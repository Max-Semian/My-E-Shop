import { NextResponse } from 'next/server';
import { prisma } from '@/lib/db';
import { isAuthenticated } from '@/lib/auth';
import { TIERS } from '@/lib/keywords';

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

  // Positions whose primary has not been derived from the artwork yet claim nothing.
  const claimedBy = new Map(
    positions
      .filter((p) => p.primaryKeyword)
      .map((p) => [p.primaryKeyword!.toLowerCase(), p.title]),
  );

  return NextResponse.json(
    keywords.map((k) => ({
      ...k,
      claimedBy: claimedBy.get(k.text.toLowerCase()) ?? null,
    })),
  );
}

/**
 * Bulk-add keywords: one per line, or a comma-separated list.
 *
 * `tier` is not cosmetic — it decides where the keyword may be placed in the copy
 * (see lib/keywords.ts), so an untiered keyword defaults to COMMERCIAL, the most
 * restrictive placement. Upserting rather than skipping duplicates means re-adding a
 * keyword with a corrected tier actually corrects it.
 */
export async function POST(req: Request) {
  const denied = guard();
  if (denied) return denied;

  const body = await req.json().catch(() => ({ text: '' }));
  const { text, type = 'SECONDARY', tier = 'COMMERCIAL', topic, reservedFor } = body;

  const items = String(text || '')
    .split(/[\n,]/)
    .map((s) => s.trim())
    .filter(Boolean);

  if (!items.length) return NextResponse.json({ error: 'No keywords supplied' }, { status: 400 });

  const kwType = type === 'PRIMARY' ? 'PRIMARY' : 'SECONDARY';
  const kwTier = TIERS.includes(tier) ? tier : 'COMMERCIAL';
  // Naming an owner puts the keyword in layer 1: it becomes a head term that belongs to a
  // listing page, and every product page is then forbidden from targeting it.
  const owner = String(reservedFor || '').trim() || null;
  // Only touch the owner when the caller actually said something about it. Accepting a
  // suggested secondary from a position card posts no `reservedFor`, and that must not
  // silently strip a head term of the page that owns it.
  const ownerUpdate = reservedFor === undefined ? {} : { reservedFor: owner };

  await Promise.all(
    items.map((t) =>
      prisma.keyword.upsert({
        where: { text: t },
        create: { text: t, type: kwType, tier: kwTier, topic: topic || null, reservedFor: owner },
        update: { type: kwType, tier: kwTier, ...ownerUpdate, ...(topic ? { topic } : {}) },
      }),
    ),
  );

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
