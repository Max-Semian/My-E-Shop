import { NextResponse } from 'next/server';
import { prisma } from '@/lib/db';
import { isAuthenticated } from '@/lib/auth';

function guard() {
  return isAuthenticated() ? null : NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
}

/** Table of all positions. Image bytes are excluded — they are served separately. */
export async function GET() {
  const denied = guard();
  if (denied) return denied;

  const positions = await prisma.position.findMany({
    orderBy: { updatedAt: 'desc' },
    select: {
      id: true,
      title: true,
      seoTitle: true,
      primaryKeyword: true,
      secondaryKeywords: true,
      category: true,
      cluster: true,
      status: true,
      warnings: true,
      updatedAt: true,
      imageMime: true,
    },
  });
  return NextResponse.json(positions);
}

export async function POST(req: Request) {
  const denied = guard();
  if (denied) return denied;

  const form = await req.formData();
  const str = (k: string) => String(form.get(k) || '').trim();

  const title = str('title');
  const seoTitle = str('seoTitle');
  const primaryKeyword = str('primaryKeyword');
  const secondaryKeywords = form
    .getAll('secondaryKeywords')
    .map((k) => String(k).trim())
    .filter(Boolean);

  // Product facts — supplied so the model states them instead of inventing specs.
  const facts = {
    category: str('category'),
    cluster: str('cluster'),
    materials: str('materials'),
    fit: str('fit'),
    printMethod: str('printMethod'),
    sizes: str('sizes'),
    colors: str('colors'),
    price: str('price'),
    extraNotes: str('extraNotes'),
  };

  if (!title) return NextResponse.json({ error: 'Title is required' }, { status: 400 });

  // The primary keyword is OPTIONAL here on purpose. A product primary is a long-tail query
  // anchored on the motif that is actually printed on the shirt, so it cannot honestly be
  // typed in before the artwork has been read — it is derived by "Build keywords from the
  // print". Demanding one up front is what produces invented head terms.
  if (primaryKeyword) {
    const clash = await prisma.position.findUnique({ where: { primaryKeyword } });
    if (clash) {
      return NextResponse.json(
        {
          error: `Primary keyword "${primaryKeyword}" is already used by "${clash.title}". One primary keyword may target only one position (no cannibalization).`,
        },
        { status: 409 },
      );
    }

    const owned = await prisma.keyword.findFirst({
      where: { text: primaryKeyword, NOT: { reservedFor: null } },
      select: { reservedFor: true },
    });
    if (owned) {
      return NextResponse.json(
        {
          error: `"${primaryKeyword}" is a head term owned by ${owned.reservedFor}. Head terms carry browsing intent and belong on a listing page — a product cannot target one.`,
        },
        { status: 409 },
      );
    }
  }

  const file = form.get('image');
  let imageData: Buffer | undefined;
  let imageMime: string | undefined;
  if (file instanceof File && file.size > 0) {
    imageData = Buffer.from(await file.arrayBuffer());
    imageMime = file.type || 'image/png';
  }

  const created = await prisma.position.create({
    data: {
      title,
      seoTitle,
      // null, not '' — many positions can legitimately be "not derived yet", and Postgres
      // allows many NULLs under a unique index but only one empty string.
      primaryKeyword: primaryKeyword || null,
      secondaryKeywords,
      imageData,
      imageMime,
      ...facts,
    },
    select: { id: true },
  });
  return NextResponse.json(created, { status: 201 });
}
