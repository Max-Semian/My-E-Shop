import { NextResponse } from 'next/server';
import { prisma, reservedKeywordsExcept } from '@/lib/db';
import { isAuthenticated } from '@/lib/auth';
import { generateCopy } from '@/lib/gemini';

export const maxDuration = 60;

export async function POST(_req: Request, { params }: { params: { id: string } }) {
  if (!isAuthenticated()) {
    return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
  }

  const position = await prisma.position.findUnique({ where: { id: params.id } });
  if (!position) return NextResponse.json({ error: 'Not found' }, { status: 404 });
  if (!position.imageData || !position.imageMime) {
    return NextResponse.json(
      { error: 'Upload the print image first — the copy is written from the image.' },
      { status: 400 },
    );
  }

  // Every other position's primary keyword is off-limits for this one.
  const reservedKeywords = await reservedKeywordsExcept(position.id);

  const { copy, violations, attempts } = await generateCopy(
    {
      title: position.title,
      seoTitle: position.seoTitle,
      primaryKeyword: position.primaryKeyword,
      secondaryKeywords: position.secondaryKeywords,
      reservedKeywords,
    },
    { data: Buffer.from(position.imageData), mimeType: position.imageMime },
  );

  // Copy that still breaks a hard rule after the retries is NOT saved as final — it is
  // stored as a draft with the violations attached so a human decides what to do.
  const clean = violations.length === 0;

  const saved = await prisma.position.update({
    where: { id: position.id },
    data: {
      description: copy.description,
      seoDescription: copy.seoDescription,
      keywordsUsed: (copy.keywordsUsed ?? {}) as any,
      warnings: [
        ...(copy.warnings ?? []),
        ...violations.map((v) => `RULE BROKEN [${v.rule}]: ${v.detail}`),
      ],
      status: clean ? 'GENERATED' : 'DRAFT',
    },
  });

  return NextResponse.json({
    ok: clean,
    attempts,
    violations,
    position: {
      id: saved.id,
      description: saved.description,
      seoDescription: saved.seoDescription,
      warnings: saved.warnings,
      status: saved.status,
    },
  });
}
