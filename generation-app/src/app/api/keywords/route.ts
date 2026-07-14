import { NextResponse } from 'next/server';
import { prisma } from '@/lib/db';
import { isAuthenticated } from '@/lib/auth';

function guard() {
  return isAuthenticated() ? null : NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
}

/**
 * The keyword list secondary keywords are picked from.
 * `claimed` marks keywords already used as a PRIMARY by some position — the UI greys
 * those out so you cannot accidentally cannibalize an existing page.
 */
export async function GET() {
  const denied = guard();
  if (denied) return denied;

  const [keywords, positions] = await Promise.all([
    prisma.keyword.findMany({ orderBy: [{ type: 'asc' }, { text: 'asc' }] }),
    prisma.position.findMany({ select: { primaryKeyword: true, title: true } }),
  ]);

  const claimedBy = new Map(positions.map((p) => [p.primaryKeyword.toLowerCase(), p.title]));

  return NextResponse.json(
    keywords.map((k) => ({
      ...k,
      claimedBy: claimedBy.get(k.text.toLowerCase()) ?? null,
    })),
  );
}

/** Bulk-add keywords: one per line, or a comma-separated list. */
export async function POST(req: Request) {
  const denied = guard();
  if (denied) return denied;

  const { text, type = 'SECONDARY', topic } = await req.json().catch(() => ({ text: '' }));
  const items = String(text || '')
    .split(/[\n,]/)
    .map((s) => s.trim())
    .filter(Boolean);

  if (!items.length) return NextResponse.json({ error: 'No keywords supplied' }, { status: 400 });

  await prisma.keyword.createMany({
    data: items.map((t) => ({
      text: t,
      type: type === 'PRIMARY' ? 'PRIMARY' : 'SECONDARY',
      topic: topic || null,
    })),
    skipDuplicates: true,
  });

  return NextResponse.json({ added: items.length });
}

export async function DELETE(req: Request) {
  const denied = guard();
  if (denied) return denied;

  const { id } = await req.json().catch(() => ({ id: '' }));
  if (!id) return NextResponse.json({ error: 'id required' }, { status: 400 });
  await prisma.keyword.delete({ where: { id: String(id) } });
  return NextResponse.json({ ok: true });
}
