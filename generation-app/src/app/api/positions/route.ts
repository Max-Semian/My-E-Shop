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
  const title = String(form.get('title') || '').trim();
  const seoTitle = String(form.get('seoTitle') || '').trim();
  const primaryKeyword = String(form.get('primaryKeyword') || '').trim();
  const secondaryKeywords = form
    .getAll('secondaryKeywords')
    .map((k) => String(k).trim())
    .filter(Boolean);

  if (!title) return NextResponse.json({ error: 'Title is required' }, { status: 400 });
  if (!primaryKeyword)
    return NextResponse.json({ error: 'Primary keyword is required' }, { status: 400 });

  // Cannibalization guard, surfaced as a friendly error before we ever hit the DB constraint.
  const clash = await prisma.position.findUnique({ where: { primaryKeyword } });
  if (clash) {
    return NextResponse.json(
      {
        error: `Primary keyword "${primaryKeyword}" is already used by "${clash.title}". One primary keyword may target only one position (no cannibalization).`,
      },
      { status: 409 },
    );
  }

  const file = form.get('image');
  let imageData: Buffer | undefined;
  let imageMime: string | undefined;
  if (file instanceof File && file.size > 0) {
    imageData = Buffer.from(await file.arrayBuffer());
    imageMime = file.type || 'image/png';
  }

  const created = await prisma.position.create({
    data: { title, seoTitle, primaryKeyword, secondaryKeywords, imageData, imageMime },
    select: { id: true },
  });
  return NextResponse.json(created, { status: 201 });
}
