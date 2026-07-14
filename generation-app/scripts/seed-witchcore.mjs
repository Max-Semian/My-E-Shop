/**
 * Seeds the Witch Core collection: the keyword list and the 14 positions.
 *
 * What it deliberately does NOT seed: secondary keywords. Those are built per position
 * from the imported print image (see /api/positions/[id]/suggest-keywords) — the motif
 * level in particular can only be read off the artwork. A print titled "Poison Garden"
 * does not tell you whether it shows belladonna or foxglove, and a motif keyword invented
 * from a name would end up in an alt text describing something that is not there.
 *
 * Safe to re-run: every write is an upsert keyed on a natural unique column.
 *
 *   npm run seed:witchcore          (local, uses .env)
 *   railway run npm run seed:witchcore   (against the deployed database)
 */

import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

const COLLECTION = 'Witch Core';

/**
 * The keyword list, as supplied. Every one of these is claimed as a PRIMARY by exactly one
 * position below — which is why none of them can serve as a secondary anywhere: doing so
 * would point two pages at one query.
 */
const KEYWORD_LIST = [
  'witchcore t-shirt',
  'witch aesthetic shirt',
  'occult t-shirt',
  'gothic t-shirt',
  'witch graphic tee',
  'dark romantic clothing',
  'occult graphic tee',
  'gothic graphic tee',
  'dark botanical t-shirt',
  'gothic floral t-shirt',
  'occult botanical',
  'botanical gothic',
];

/**
 * The 14 positions. `title` is the print name — a SOURCE for generation, not a label: it
 * carries the concept the artwork is about. `seoTitle` is the unified H1.
 */
const POSITIONS = [
  { ref: 'WC-01', title: 'Read My Aura',            primary: 'witch graphic tee',    h1: 'Witch Graphic T-Shirt — Read My Aura',            capsule: 'Stand-alone concept' },
  { ref: 'WC-02', title: 'We Gather Under No God',  primary: 'witchcore t-shirt',    h1: 'Witchcore T-Shirt — We Gather Under No God',      capsule: 'Stand-alone concept' },
  { ref: 'WC-03', title: "Fortune Teller's Hands",  primary: 'occult graphic tee',   h1: "Occult Graphic T-Shirt — Fortune Teller's Hands", capsule: 'Stand-alone concept' },
  { ref: 'WC-04', title: 'Beauty with Bite',        primary: 'gothic floral t-shirt', h1: 'Gothic Floral T-Shirt — Beauty with Bite',       capsule: 'Stand-alone concept' },
  { ref: 'WC-05', title: 'Hex and Bloom',           primary: 'gothic graphic tee',   h1: 'Gothic Graphic T-Shirt — Hex and Bloom',          capsule: 'Capsule: Hex and Bloom' },
  { ref: 'WC-06', title: 'Hex and Bloom',           primary: 'dark botanical t-shirt', h1: 'Dark Botanical T-Shirt — Hex and Bloom',        capsule: 'Capsule: Hex and Bloom' },
  { ref: 'WC-07', title: 'Hex and Bloom',           primary: 'occult botanical',     h1: 'Occult Botanical T-Shirt — Hex and Bloom',        capsule: 'Capsule: Hex and Bloom' },
  { ref: 'WC-08', title: 'Witch',                   primary: 'witch aesthetic shirt', h1: 'Witch Aesthetic T-Shirt — Witch',                capsule: 'Capsule: Witch' },
  { ref: 'WC-09', title: 'Witch',                   primary: 'botanical gothic',     h1: 'Botanical Gothic T-Shirt — Witch',                capsule: 'Capsule: Witch' },
  { ref: 'WC-10', title: 'Blessed',                 primary: 'dark romantic tee',    h1: 'Dark Romantic T-Shirt — Blessed',                 capsule: 'Stand-alone concept' },
  { ref: 'WC-11', title: 'Poison Garden',           primary: 'dark aesthetic shirt', h1: 'Dark Aesthetic T-Shirt — Poison Garden',          capsule: 'Capsule: Poison Garden' },
  { ref: 'WC-12', title: 'Poison Garden',           primary: 'gothic t-shirt',       h1: 'Gothic T-Shirt — Poison Garden',                  capsule: 'Capsule: Poison Garden' },
  { ref: 'WC-13', title: 'Poison Garden',           primary: 'occult t-shirt',       h1: 'Occult T-Shirt — Poison Garden',                  capsule: 'Capsule: Poison Garden' },
  { ref: 'WC-14', title: 'Poison Garden',           primary: 'dark symbolic tee',    h1: 'Dark Symbolic T-Shirt — Poison Garden',           capsule: 'Capsule: Poison Garden' },
];

async function main() {
  for (const text of KEYWORD_LIST) {
    await prisma.keyword.upsert({
      where: { text },
      create: { text, type: 'PRIMARY', topic: COLLECTION },
      update: { type: 'PRIMARY', topic: COLLECTION },
    });
  }
  console.log(`Keyword list: ${KEYWORD_LIST.length} keywords, all claimed as PRIMARY.`);

  for (const p of POSITIONS) {
    await prisma.position.upsert({
      // primaryKeyword is unique, so it is the natural key — and re-running can never
      // create a second position competing for the same query.
      where: { primaryKeyword: p.primary },
      create: {
        title: p.title,
        primaryKeyword: p.primary,
        seoTitle: p.h1,
        category: COLLECTION,
        extraNotes: `${p.ref}. ${p.capsule}.`,
      },
      update: { seoTitle: p.h1, category: COLLECTION, extraNotes: `${p.ref}. ${p.capsule}.` },
    });
  }
  console.log(`Positions: ${POSITIONS.length} seeded.`);
  console.log('\nNext: import the print image into each position, then "Build keywords from');
  console.log('the print" — the secondary set is read off the artwork, not off the name.');
}

main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  })
  .finally(() => prisma.$disconnect());
