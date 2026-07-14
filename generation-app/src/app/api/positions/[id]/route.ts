import { NextResponse } from 'next/server';
import { prisma } from '@/lib/db';
import { isAuthenticated } from '@/lib/auth';

function guard() {
  return isAuthenticated() ? null : NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
}

export async function GET(_req: Request, { params }: { params: { id: string } }) {
  const denied = guard();
  if (denied) return denied;

  const p = await prisma.position.findUnique({
    where: { id: params.id },
    // Everything except the image bytes, which are served by the /image route.
    select: {
      id: true,
      title: true,
      seoTitle: true,
      primaryKeyword: true,
      secondaryKeywords: true,
      category: true,
      materials: true,
      fit: true,
      printMethod: true,
      sizes: true,
      colors: true,
      price: true,
      extraNotes: true,
      slug: true,
      description: true,
      shortDescription: true,
      metaTitle: true,
      seoDescription: true,
      tags: true,
      imagesAlt: true,
      imageFilenames: true,
      keywordsUsed: true,
      warnings: true,
      status: true,
      imageMime: true,
      updatedAt: true,
    },
  });
  if (!p) return NextResponse.json({ error: 'Not found' }, { status: 404 });
  return NextResponse.json(p);
}

export async function PATCH(req: Request, { params }: { params: { id: string } }) {
  const denied = guard();
  if (denied) return denied;

  const form = await req.formData();
  const data: Record<string, unknown> = {};

  const TEXT_FIELDS = [
    'title',
    'seoTitle',
    'category',
    'materials',
    'fit',
    'printMethod',
    'sizes',
    'colors',
    'price',
    'extraNotes',
    'slug',
    'description',
    'shortDescription',
    'metaTitle',
    'seoDescription',
  ] as const;
  for (const field of TEXT_FIELDS) {
    if (form.has(field)) data[field] = String(form.get(field) || '').trim();
  }
  if (form.has('status')) data.status = String(form.get('status'));

  const LIST_FIELDS = ['secondaryKeywords', 'tags', 'imagesAlt', 'imageFilenames'] as const;
  for (const field of LIST_FIELDS) {
    if (form.has(field)) {
      data[field] = form
        .getAll(field)
        .map((k) => String(k).trim())
        .filter(Boolean);
    }
  }

  if (form.has('primaryKeyword')) {
    const primaryKeyword = String(form.get('primaryKeyword') || '').trim();
    if (!primaryKeyword)
      return NextResponse.json({ error: 'Primary keyword is required' }, { status: 400 });

    const clash = await prisma.position.findUnique({ where: { primaryKeyword } });
    if (clash && clash.id !== params.id) {
      return NextResponse.json(
        {
          error: `Primary keyword "${primaryKeyword}" already targets "${clash.title}". One primary keyword may target only one position.`,
        },
        { status: 409 },
      );
    }
    data.primaryKeyword = primaryKeyword;
  }

  const file = form.get('image');
  if (file instanceof File && file.size > 0) {
    data.imageData = Buffer.from(await file.arrayBuffer());
    data.imageMime = file.type || 'image/png';
  }

  const updated = await prisma.position.update({
    where: { id: params.id },
    data,
    select: { id: true },
  });
  return NextResponse.json(updated);
}

export async function DELETE(_req: Request, { params }: { params: { id: string } }) {
  const denied = guard();
  if (denied) return denied;

  await prisma.position.delete({ where: { id: params.id } });
  return NextResponse.json({ ok: true });
}
