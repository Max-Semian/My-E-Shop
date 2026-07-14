import { NextResponse } from 'next/server';
import { prisma, reservedKeywordsExcept } from '@/lib/db';
import { isAuthenticated } from '@/lib/auth';
import { generateCopy } from '@/lib/gemini';
import { getBrandProfile } from '@/lib/brand';
import { resolveSecondaryTiers } from '@/lib/keywords';

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
  if (!position.primaryKeyword) {
    return NextResponse.json(
      {
        error:
          'This position has no primary keyword yet. Run "Build keywords from the print" — a product primary is a long-tail query anchored on the motif, so it can only be derived once the artwork has been read.',
      },
      { status: 400 },
    );
  }

  // Every other position's primary keyword is off-limits for this one. The secondary
  // keywords are looked up in the keyword list to recover their level, because the level —
  // commercial / semantic / motif — is what decides where each one may be placed.
  const [reservedKeywords, brand, secondaryKeywords, lane] = await Promise.all([
    reservedKeywordsExcept(position.id),
    getBrandProfile(),
    resolveSecondaryTiers(position.secondaryKeywords),
    // The head terms owned by this product's listing page. Passing them in as "support, do
    // not target" is what keeps the copy inside its lane instead of drifting into the
    // category's own query.
    position.cluster
      ? prisma.keyword.findMany({ where: { reservedFor: position.cluster }, select: { text: true } })
      : Promise.resolve([]),
  ]);

  const { copy, violations, attempts } = await generateCopy(
    {
      title: position.title,
      seoTitle: position.seoTitle,
      primaryKeyword: position.primaryKeyword,
      secondaryKeywords,
      reservedKeywords,
      category: position.category,
      cluster: position.cluster,
      laneKeywords: lane.map((k) => k.text),
      materials: position.materials,
      fit: position.fit,
      printMethod: position.printMethod,
      sizes: position.sizes,
      colors: position.colors,
      price: position.price,
      extraNotes: position.extraNotes,
    },
    { data: Buffer.from(position.imageData), mimeType: position.imageMime },
    brand,
  );

  // Copy that still breaks a hard rule after the retries is NOT marked as final — it is
  // kept as a draft with the violations attached so a human decides what to do.
  const clean = violations.length === 0;

  const saved = await prisma.position.update({
    where: { id: position.id },
    data: {
      slug: copy.slug ?? '',
      description: copy.description ?? '',
      shortDescription: copy.shortDescription ?? '',
      metaTitle: copy.metaTitle ?? '',
      seoDescription: copy.metaDescription ?? '',
      tags: copy.tags ?? [],
      imagesAlt: copy.imagesAlt ?? [],
      imageFilenames: copy.imageFilenames ?? [],
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
    position: { id: saved.id, status: saved.status },
  });
}
