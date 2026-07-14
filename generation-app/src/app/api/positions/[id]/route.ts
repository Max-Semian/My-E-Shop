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
    select: {
      id: true,
      title: true,
      seoTitle: true,
      primaryKeyword: true,
      secondaryKeywords: true,
      description: true,
      seoDescription: true,
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

  for (const field of ['title', 'seoTitle', 'description', 'seoDescription'] as const) {
    if (form.has(field)) data[field] = String(form.get(field) || '').trim();
  }
  if (form.has('status')) data.status = String(form.get('status'));

  if (form.has('secondaryKeywords')) {
    data.secondaryKeywords = form
      .getAll('secondaryKeywords')
      .map((k) => String(k).trim())
      .filter(Boolean);
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
