/**
 * Seeds the keyword architecture of the WITCHCORE category.
 *
 * Witchcore is ONE of the brand's four concept categories. Its architecture is 14 keyword
 * positions (WC-01…WC-14): each is a primary keyword paired with a print concept and a
 * unified H1. There are no sub-categories and no extra listing pages — "gothic", "occult",
 * "dark botanical" and "dark romantic" are KEYWORDS of Witchcore, not pages beneath it.
 *
 * What this script does NOT seed: secondary keywords. Those are built per position from the
 * imported print image (`/api/positions/[id]/suggest-keywords`). The motif level in
 * particular can only be read off the artwork — "Poison Garden" does not say whether the
 * print shows belladonna or foxglove, and a motif invented from a name ends up in an alt
 * text describing something that is not in the picture.
 *
 * `cluster` is the thematic lane inside the category. It owns no keyword and is not a page.
 * Its only job is separation: fourteen positions in one category are close cousins, and the
 * ones that can actually collide are the ones in the same lane. It tells the model which
 * siblings it must stay distinguishable from, instead of weighing thirteen equally.
 *
 * Safe to re-run — every write is an upsert keyed on the unique primary keyword.
 *
 *   railway ssh --service beautiful-courage "npm run seed:witchcore"
 */

import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

const CATEGORY = 'Witchcore';

/** The supplied keyword list. Each is claimed as a primary by exactly one position below. */
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
 * The 14 positions. `title` is the print concept — a generation SOURCE, not a label: it says
 * what the artwork MEANS, while the image says what it shows.
 */
const POSITIONS = [
  { ref: 'WC-01', title: 'Read My Aura',           primary: 'witch graphic tee',     h1: 'Witch Graphic T-Shirt — Read My Aura',            lane: 'Witch',          type: 'Stand-alone concept' },
  { ref: 'WC-02', title: 'We Gather Under No God', primary: 'witchcore t-shirt',     h1: 'Witchcore T-Shirt — We Gather Under No God',      lane: 'Witch',          type: 'Stand-alone concept' },
  { ref: 'WC-03', title: "Fortune Teller's Hands", primary: 'occult graphic tee',    h1: "Occult Graphic T-Shirt — Fortune Teller's Hands", lane: 'Occult',         type: 'Stand-alone concept' },
  { ref: 'WC-04', title: 'Beauty with Bite',       primary: 'gothic floral t-shirt', h1: 'Gothic Floral T-Shirt — Beauty with Bite',        lane: 'Dark Botanical', type: 'Stand-alone concept' },
  { ref: 'WC-05', title: 'Hex and Bloom',          primary: 'gothic graphic tee',    h1: 'Gothic Graphic T-Shirt — Hex and Bloom',          lane: 'Gothic',         type: 'Capsule: Hex and Bloom' },
  { ref: 'WC-06', title: 'Hex and Bloom',          primary: 'dark botanical t-shirt', h1: 'Dark Botanical T-Shirt — Hex and Bloom',         lane: 'Dark Botanical', type: 'Capsule: Hex and Bloom' },
  { ref: 'WC-07', title: 'Hex and Bloom',          primary: 'occult botanical',      h1: 'Occult Botanical T-Shirt — Hex and Bloom',        lane: 'Dark Botanical', type: 'Capsule: Hex and Bloom' },
  { ref: 'WC-08', title: 'Witch',                  primary: 'witch aesthetic shirt', h1: 'Witch Aesthetic T-Shirt — Witch',                 lane: 'Witch',          type: 'Capsule: Witch' },
  { ref: 'WC-09', title: 'Witch',                  primary: 'botanical gothic',      h1: 'Botanical Gothic T-Shirt — Witch',                lane: 'Dark Botanical', type: 'Capsule: Witch' },
  { ref: 'WC-10', title: 'Blessed',                primary: 'dark romantic tee',     h1: 'Dark Romantic T-Shirt — Blessed',                 lane: 'Dark Romantic',  type: 'Stand-alone concept' },
  { ref: 'WC-11', title: 'Poison Garden',          primary: 'dark aesthetic shirt',  h1: 'Dark Aesthetic T-Shirt — Poison Garden',          lane: 'Dark Botanical', type: 'Capsule: Poison Garden' },
  { ref: 'WC-12', title: 'Poison Garden',          primary: 'gothic t-shirt',        h1: 'Gothic T-Shirt — Poison Garden',                  lane: 'Gothic',         type: 'Capsule: Poison Garden' },
  { ref: 'WC-13', title: 'Poison Garden',          primary: 'occult t-shirt',        h1: 'Occult T-Shirt — Poison Garden',                  lane: 'Occult',         type: 'Capsule: Poison Garden' },
  { ref: 'WC-14', title: 'Poison Garden',          primary: 'dark symbolic tee',     h1: 'Dark Symbolic T-Shirt — Poison Garden',           lane: 'Occult',         type: 'Capsule: Poison Garden' },
];

async function main() {
  // Nothing is reserved to a listing page: Witchcore has no sub-pages, so no keyword is
  // withheld from a position.
  await prisma.keyword.updateMany({ data: { reservedFor: null } });

  for (const text of KEYWORD_LIST) {
    await prisma.keyword.upsert({
      where: { text },
      create: { text, type: 'PRIMARY', topic: CATEGORY },
      update: { type: 'PRIMARY', topic: CATEGORY, reservedFor: null },
    });
  }
  console.log(`Keyword list: ${KEYWORD_LIST.length} keywords, category "${CATEGORY}".`);

  for (const p of POSITIONS) {
    const existing = await prisma.position.findFirst({
      where: { extraNotes: { startsWith: `${p.ref}.` } },
    });

    const data = {
      title: p.title,
      primaryKeyword: p.primary,
      seoTitle: p.h1,
      category: CATEGORY,
      cluster: p.lane,
      extraNotes: `${p.ref}. ${p.type}.`,
      warnings: [],
    };

    if (existing) await prisma.position.update({ where: { id: existing.id }, data });
    else await prisma.position.create({ data });
  }
  console.log(`Positions: ${POSITIONS.length} in "${CATEGORY}", primary keywords restored.`);
  console.log('\nNext: import each print, then "Build keywords from the print" — it derives the');
  console.log('three-level secondary set from the artwork, the title and the primary keyword.');
}

main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  })
  .finally(() => prisma.$disconnect());
