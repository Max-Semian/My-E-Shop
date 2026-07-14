/**
 * Seeds the Witchcore concept category under the two-layer keyword architecture.
 *
 * THE HIERARCHY — three things that must never be confused:
 *
 *   Layer 0 — CONCEPT CATEGORY. The brand's own taxonomy; Witchcore is one of the four.
 *             It decides what a print MEANS and how it sounds. It carries no search demand
 *             by design, and that is not a defect: nobody types "tender thoughts t-shirt".
 *
 *   Layer 1 — LISTING PAGES. The category page itself is the hub and owns the head term
 *             "witchcore t-shirt". Beneath it sit four listing pages — Gothic, Occult, Dark
 *             Botanical, Dark Romantic — each owning its own head term. Head terms carry
 *             BROWSING intent: the searcher wants to choose from a range, so they belong on
 *             a page that shows a range. Every one of the 12 supplied keywords is a head term
 *             or a synonym of one, so all 12 live here and none is left on a product.
 *             Synonyms sit on the SAME page as their head — splitting them across pages is
 *             cannibalization under another name.
 *
 *   Layer 2 — PRODUCTS. Each supports exactly one listing page (its `cluster`) and owns a
 *             long-tail primary anchored on the motif actually printed on it. That primary
 *             cannot be written down in advance: only the artwork says whether "Poison
 *             Garden" shows belladonna or foxglove. So the 14 positions are seeded WITHOUT a
 *             primary, and it is derived per product by "Build keywords from the print".
 *             Cannibalization then disappears structurally — the queries are simply different.
 *
 * The old provisional primaries are recorded in each position's warnings, so the move is
 * auditable rather than mysterious.
 *
 * Safe to re-run.
 *
 *   railway ssh --service beautiful-courage "npm run seed:witchcore"
 */

import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

/** Layer 0. */
const CATEGORY = 'Witchcore';

/**
 * Layer 1. `page` is the listing page, `head` the query it targets, `synonyms` the same
 * intent worded differently.
 *
 * `occult botanical` and `botanical gothic` are synonyms, not heads: they have no product
 * noun, so nobody types them to buy a t-shirt. They are topical support, and that is exactly
 * what they are used as here.
 */
const LISTING_PAGES = [
  {
    page: 'Witchcore',
    head: 'witchcore t-shirt',
    synonyms: ['witch graphic tee', 'witch aesthetic shirt'],
    note: 'The category page itself — the hub. The four below link up to it.',
  },
  { page: 'Gothic', head: 'gothic t-shirt', synonyms: ['gothic graphic tee'] },
  { page: 'Occult', head: 'occult t-shirt', synonyms: ['occult graphic tee'] },
  {
    page: 'Dark Botanical',
    head: 'dark botanical t-shirt',
    synonyms: ['gothic floral t-shirt', 'occult botanical', 'botanical gothic'],
  },
  { page: 'Dark Romantic', head: 'dark romantic clothing', synonyms: [] },
];

/**
 * Layer 2. `cluster` is taken from the provisional primary the original table gave each
 * print — that phrase is the table's own statement of what the product is about, so it is
 * evidence rather than invention. WC-11 and WC-14 had primaries with no cluster of their own
 * ("dark aesthetic shirt", "dark symbolic tee"), so their lane is inferred from the Poison
 * Garden capsule and is flagged for review.
 */
const POSITIONS = [
  { ref: 'WC-01', title: 'Read My Aura',           capsule: 'Stand-alone concept',    cluster: 'Witchcore',      was: 'witch graphic tee' },
  { ref: 'WC-02', title: 'We Gather Under No God', capsule: 'Stand-alone concept',    cluster: 'Witchcore',      was: 'witchcore t-shirt' },
  { ref: 'WC-03', title: "Fortune Teller's Hands", capsule: 'Stand-alone concept',    cluster: 'Occult',         was: 'occult graphic tee' },
  { ref: 'WC-04', title: 'Beauty with Bite',       capsule: 'Stand-alone concept',    cluster: 'Dark Botanical', was: 'gothic floral t-shirt' },
  { ref: 'WC-05', title: 'Hex and Bloom',          capsule: 'Capsule: Hex and Bloom', cluster: 'Gothic',         was: 'gothic graphic tee' },
  { ref: 'WC-06', title: 'Hex and Bloom',          capsule: 'Capsule: Hex and Bloom', cluster: 'Dark Botanical', was: 'dark botanical t-shirt' },
  { ref: 'WC-07', title: 'Hex and Bloom',          capsule: 'Capsule: Hex and Bloom', cluster: 'Dark Botanical', was: 'occult botanical' },
  { ref: 'WC-08', title: 'Witch',                  capsule: 'Capsule: Witch',         cluster: 'Witchcore',      was: 'witch aesthetic shirt' },
  { ref: 'WC-09', title: 'Witch',                  capsule: 'Capsule: Witch',         cluster: 'Dark Botanical', was: 'botanical gothic' },
  { ref: 'WC-10', title: 'Blessed',                capsule: 'Stand-alone concept',    cluster: 'Dark Romantic',  was: 'dark romantic tee' },
  { ref: 'WC-11', title: 'Poison Garden',          capsule: 'Capsule: Poison Garden', cluster: 'Dark Botanical', was: 'dark aesthetic shirt', inferred: true },
  { ref: 'WC-12', title: 'Poison Garden',          capsule: 'Capsule: Poison Garden', cluster: 'Gothic',         was: 'gothic t-shirt' },
  { ref: 'WC-13', title: 'Poison Garden',          capsule: 'Capsule: Poison Garden', cluster: 'Occult',         was: 'occult t-shirt' },
  { ref: 'WC-14', title: 'Poison Garden',          capsule: 'Capsule: Poison Garden', cluster: 'Occult',         was: 'dark symbolic tee', inferred: true },
];

const NOT_DERIVED =
  'Primary keyword not derived yet. A product primary is a long-tail query anchored on the ' +
  'motif actually printed on the shirt, so it can only be read off the artwork. Import the ' +
  'print, then run "Build keywords from the print".';

async function main() {
  for (const { page, head, synonyms } of LISTING_PAGES) {
    for (const text of [head, ...synonyms]) {
      await prisma.keyword.upsert({
        where: { text },
        create: { text, type: 'PRIMARY', tier: 'SEMANTIC', topic: CATEGORY, reservedFor: page },
        update: { type: 'PRIMARY', topic: CATEGORY, reservedFor: page },
      });
    }
    console.log(`${page.padEnd(16)} ← ${head}${synonyms.length ? `  (+${synonyms.length} synonyms)` : ''}`);
  }

  for (const p of POSITIONS) {
    const existing = await prisma.position.findFirst({
      where: { extraNotes: { startsWith: `${p.ref}.` } },
    });

    const warnings = [
      NOT_DERIVED,
      `Provisional primary "${p.was}" moved to the "${p.cluster}" listing page, which owns it.`,
    ];
    if (p.inferred) {
      warnings.push(
        `Lane inferred from the capsule, not from a keyword — "${p.was}" belongs to no cluster. Confirm "${p.cluster}" against the artwork.`,
      );
    }

    const data = {
      title: p.title,
      // Cleared on purpose: an invented head term here is worse than an honest blank.
      primaryKeyword: null,
      seoTitle: '',
      category: CATEGORY,
      cluster: p.cluster,
      extraNotes: `${p.ref}. ${p.capsule}.`,
      warnings,
    };

    if (existing) await prisma.position.update({ where: { id: existing.id }, data });
    else await prisma.position.create({ data });
  }

  const keywords = LISTING_PAGES.reduce((n, p) => n + 1 + p.synonyms.length, 0);
  console.log(`\nLayer 1: ${keywords} keywords on ${LISTING_PAGES.length} listing pages — off-limits to every product.`);
  console.log(`Layer 2: ${POSITIONS.length} positions in "${CATEGORY}", lane fixed, no primary yet.`);
  console.log('\nNext: import each print, then "Build keywords from the print" — it derives the');
  console.log('long-tail primary AND the three-level secondary set from the artwork itself.');
}

main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  })
  .finally(() => prisma.$disconnect());
