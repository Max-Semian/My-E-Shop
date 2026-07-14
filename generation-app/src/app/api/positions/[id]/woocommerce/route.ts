import { NextResponse } from 'next/server';
import { prisma } from '@/lib/db';
import { isAuthenticated } from '@/lib/auth';

/**
 * The generated copy as a ready-to-POST WooCommerce REST API product payload
 * (see docs/woocommerce-prompt-template.md).
 *
 * Meta keys default to Rank Math; pass ?seo=yoast for the Yoast field names.
 * Image alt text / filenames are returned alongside the payload — they are applied when
 * uploading media via /wp-json/wp/v2/media, then attached in a follow-up PUT.
 */
export async function GET(req: Request, { params }: { params: { id: string } }) {
  if (!isAuthenticated()) {
    return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
  }

  const p = await prisma.position.findUnique({ where: { id: params.id } });
  if (!p) return NextResponse.json({ error: 'Not found' }, { status: 404 });

  if (p.status === 'DRAFT') {
    return NextResponse.json(
      { error: 'This position is still a DRAFT — it has unresolved rule violations. Fix and regenerate before exporting.' },
      { status: 409 },
    );
  }

  const seo = new URL(req.url).searchParams.get('seo') === 'yoast' ? 'yoast' : 'rankmath';
  const metaKeys =
    seo === 'yoast'
      ? { title: '_yoast_wpseo_title', desc: '_yoast_wpseo_metadesc' }
      : { title: 'rank_math_title', desc: 'rank_math_description' };

  return NextResponse.json({
    payload: {
      name: p.title,
      slug: p.slug,
      type: 'variable',
      description: p.description,
      short_description: p.shortDescription,
      categories: p.category ? [{ name: p.category }] : [],
      tags: p.tags.map((name) => ({ name })),
      meta_data: [
        { key: metaKeys.title, value: p.metaTitle },
        { key: metaKeys.desc, value: p.seoDescription },
      ],
    },
    // Applied separately when uploading media, then attached via the product `images` array.
    media: p.imagesAlt.map((alt, i) => ({ alt, filename: p.imageFilenames[i] ?? null })),
  });
}
