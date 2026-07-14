import { NextResponse } from 'next/server';
import { prisma } from '@/lib/db';
import { isAuthenticated } from '@/lib/auth';
import { getBrandProfile } from '@/lib/brand';

function guard() {
  return isAuthenticated() ? null : NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
}

export async function GET() {
  const denied = guard();
  if (denied) return denied;
  return NextResponse.json(await getBrandProfile());
}

/**
 * Editing the brand changes the voice of every future generation — it is the system
 * instruction, not a decoration. Nothing already generated is rewritten.
 */
export async function PUT(req: Request) {
  const denied = guard();
  if (denied) return denied;

  await getBrandProfile(); // ensure the row exists

  const form = await req.formData();
  const data: Record<string, unknown> = {};

  for (const field of [
    'brandName',
    'concept',
    'archetype',
    'archetypeNotes',
    'audience',
    'toneOfVoice',
    'valueProps',
    'vocabulary',
  ] as const) {
    if (form.has(field)) data[field] = String(form.get(field) || '').trim();
  }

  if (form.has('bannedWordsRaw')) {
    data.bannedWords = String(form.get('bannedWordsRaw') || '')
      .split(/[\n,]/)
      .map((s) => s.trim())
      .filter(Boolean);
  }

  const saved = await prisma.brandProfile.update({ where: { id: 'default' }, data });
  return NextResponse.json(saved);
}
