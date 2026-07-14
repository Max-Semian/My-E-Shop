import { NextResponse } from 'next/server';
import { GoogleGenerativeAI, SchemaType } from '@google/generative-ai';
import { prisma, reservedKeywordsExcept } from '@/lib/db';
import { isAuthenticated } from '@/lib/auth';
import { countPhrase } from '@/lib/validate';
import { PLACEMENT, type Tier } from '@/lib/keywords';

export const maxDuration = 60;

/**
 * Builds the secondary keyword set for a position from THREE sources, in this order:
 *
 *   1. THE PRINT IMAGE   — the only honest source of the MOTIF level. A print called
 *                          "Poison Garden" does not tell you whether it shows belladonna
 *                          or foxglove; only the picture does. Motif keywords guessed from
 *                          a name are fiction, and fiction in an alt text is a lie to both
 *                          the customer and the crawler.
 *   2. THE PRINT TITLE   — the concept behind the artwork. The image says what is drawn,
 *                          the title says what it means.
 *   3. THE PRIMARY KEYWORD — the cluster this page owns. Every secondary must sit close to
 *                          it thematically, or it is pulling the page apart.
 *
 * Nothing is written to the database here: the editor picks what to accept. Suggestions
 * that would cannibalize another position, or that merely repeat another position's
 * secondaries, are flagged in code — the model is not trusted to police that itself.
 */

interface Suggestion {
  text: string;
  tier: Tier;
  rationale: string;
  /** Set when the suggestion collides with the rest of the catalogue. */
  conflict?: string;
}

const responseSchema = {
  type: SchemaType.OBJECT,
  properties: {
    primary: {
      type: SchemaType.OBJECT,
      properties: {
        text: { type: SchemaType.STRING },
        rationale: { type: SchemaType.STRING },
      },
      required: ['text', 'rationale'],
      description: 'The proposed long-tail primary keyword for this product page.',
    },
    entity: {
      type: SchemaType.STRING,
      description: 'One sentence: what this product IS, as an entity, after reading the image.',
    },
    visibleMotifs: {
      type: SchemaType.ARRAY,
      items: { type: SchemaType.STRING },
      description: 'Every concrete object you can actually see in the print.',
    },
    keywords: {
      type: SchemaType.ARRAY,
      items: {
        type: SchemaType.OBJECT,
        properties: {
          text: { type: SchemaType.STRING },
          tier: { type: SchemaType.STRING, enum: ['COMMERCIAL', 'SEMANTIC', 'MOTIF'] },
          rationale: { type: SchemaType.STRING },
        },
        required: ['text', 'tier', 'rationale'],
      },
    },
    rejected: {
      type: SchemaType.ARRAY,
      items: {
        type: SchemaType.OBJECT,
        properties: {
          text: { type: SchemaType.STRING },
          reason: { type: SchemaType.STRING },
        },
        required: ['text', 'reason'],
      },
      description: 'Candidates you considered and threw away, and why.',
    },
  },
  required: ['primary', 'entity', 'visibleMotifs', 'keywords', 'rejected'],
} as const;

function systemInstruction(hasPrimary: boolean): string {
  const primaryTask = hasPrimary
    ? `
# THE PRIMARY KEYWORD
This page already has a primary keyword. Do not change it. Build the secondary set around it.
Still return it in "primary", unchanged, with a one-line rationale.
`
    : `
# YOUR FIRST JOB: THE PRIMARY KEYWORD
This page has no primary keyword yet. Derive one FROM THE PRINT.

This catalogue is built in TWO LAYERS:
  - Category and collection pages own the HEAD TERMS ("gothic t-shirt", "occult t-shirt").
    Those queries carry BROWSING intent: the searcher wants to choose from a range. They are
    RESERVED. You may never propose one, or a synonym of one, for a product.
  - A PRODUCT page owns a LONG-TAIL query anchored on THE MOTIF ACTUALLY PRINTED ON IT.
    That is a query no other product in the catalogue can claim, so cannibalization does not
    arise — the queries are simply different things.

Shape it as:  [motif] (+ aesthetic) + [product noun]
  good:  "tarot hands t-shirt", "moth graphic tee", "belladonna print shirt"
  bad:   "gothic t-shirt"      (a head term — reserved for the category)
  bad:   "dark symbolic tee"   (nobody types this; it names nothing)
  bad:   "beautiful witchy top" (not a query, just adjectives)

It must contain a product noun (t-shirt / tee / shirt), name something a person can actually
SEE in the image, and be a phrase a real person would plausibly type. Specific and
low-volume beats broad and fabricated: a query that matches the shirt exactly will convert,
a head term the shirt cannot win will not.
`;

  return `
You are a technical SEO strategist for an e-commerce catalogue of graphic T-shirts on
WooCommerce. Your job is to build the keyword set for ONE product page.

You are given the print image and the print title. Use both. The image is the source of
truth for anything visual: never name an object you cannot see.
${primaryTask}

# THE SECONDARY SET — three levels, and the set must contain all three
[COMMERCIAL] ${PLACEMENT.COMMERCIAL.job}
    A noun phrase a buyer would really type. Strong, natural, purchasable.
[SEMANTIC]   ${PLACEMENT.SEMANTIC.job}
    An aesthetic, subculture or topical entity. It gives the product its context.
[MOTIF]      ${PLACEMENT.MOTIF.job}
    Read it off the image. A plant, an animal, a hand, a symbol. Be specific: "belladonna",
    not "flower"; "palmistry hand", not "hand". If you cannot identify the species or symbol
    with confidence, describe it precisely instead of guessing a name.

# HARD RULES
- NO ARTIFICIAL PHRASES. If a human would never type it into a search box and it does not
  help a machine understand the product, it does not belong. Kill it. Put it in "rejected".
- Commercial keywords must be real NOUN PHRASES with buying intent — strengthen them.
  "witchy vibes" is not a keyword. "tarot t-shirt" is.
- NOT EVERY KEYWORD HAS TO BE HIGH-VOLUME. A low-volume motif entity that nails what is on
  the shirt is worth more than a fat head term that says nothing specific. What matters is
  that THE SET AS A WHOLE draws a sharp, unambiguous picture of this one product.
- Every keyword must EITHER have standalone search intent, OR make the product easier for a
  search engine / an AI to understand. If it does neither, it is noise.
- THEMATIC PROXIMITY: every keyword must sit close to the primary keyword. You are
  reinforcing one cluster, not opening new ones.
- SEPARATION: other products in this collection are close cousins. Your set must make THIS
  product distinguishable from them. Do not reach for the collection's generic vocabulary —
  reach for what only this print has.
- RESERVED keywords belong to other product pages. Never suggest one, and never suggest a
  phrase that means the same thing. That is cannibalization.
- NO REPEATS. Do not restate the primary keyword, or a near-synonym of it, as a secondary.

# SIZE
Aim for 3 to 5 keywords: at least one of each level, and at most two of any one level.
A tight set beats a long one. Quality is measured by how precisely the set defines the
product, not by how many keywords it contains.

Return ONLY the JSON object.
`.trim();
}

export async function POST(_req: Request, { params }: { params: { id: string } }) {
  if (!isAuthenticated()) {
    return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
  }

  const position = await prisma.position.findUnique({ where: { id: params.id } });
  if (!position) return NextResponse.json({ error: 'Not found' }, { status: 404 });

  if (!position.imageData || !position.imageMime) {
    return NextResponse.json(
      {
        error:
          'Upload the print image first. Motif keywords are read off the image — deriving them from the title alone would be guesswork.',
      },
      { status: 400 },
    );
  }
  const key = process.env.GEMINI_API_KEY;
  if (!key) return NextResponse.json({ error: 'GEMINI_API_KEY is not set' }, { status: 500 });

  // The rest of the catalogue: what this position may not touch, and what it must not echo.
  // `laneKeywords` are the head terms owned by THIS product's listing page — the lane it is
  // supposed to reinforce, and precisely the terms it must not try to take for itself.
  const [reserved, siblings, laneKeywords] = await Promise.all([
    reservedKeywordsExcept(position.id),
    prisma.position.findMany({
      where: { NOT: { id: position.id } },
      select: {
        title: true,
        primaryKeyword: true,
        secondaryKeywords: true,
        category: true,
        cluster: true,
      },
    }),
    position.cluster
      ? prisma.keyword.findMany({
          where: { reservedFor: position.cluster },
          select: { text: true },
        })
      : Promise.resolve([]),
  ]);

  const category = position.category || '(not set)';
  const cluster = position.cluster || '(not set)';

  // The products it is actually at risk of colliding with are the ones in the same lane.
  // Listing every position in the catalogue would just be noise.
  const nearest = siblings.filter((s) =>
    position.cluster ? s.cluster === position.cluster : s.category === position.category,
  );
  const cousins = (nearest.length ? nearest : siblings)
    .map((s) => `  - "${s.title}" → primary: ${s.primaryKeyword || '(not derived yet)'}`)
    .join('\n');

  const model = new GoogleGenerativeAI(key).getGenerativeModel({
    model: process.env.GEMINI_MODEL || 'gemini-2.5-flash',
    systemInstruction: systemInstruction(Boolean(position.primaryKeyword)),
    generationConfig: {
      temperature: 0.3,
      responseMimeType: 'application/json',
      responseSchema: responseSchema as any,
    },
  });

  const prompt = `
PRINT TITLE — the concept of the artwork
${position.title}

PRIMARY KEYWORD
${
  position.primaryKeyword
    ? `${position.primaryKeyword}  (already set — keep it, and build the secondary set around it)`
    : '(not set — derive it from the print, as a long-tail motif-anchored query)'
}

CONCEPT CATEGORY — the brand's own lane. It decides what this print MEANS and how it sounds.
It is not a search term and is not meant to be one.
${category}

LISTING PAGE THIS PRODUCT SUPPORTS — its cluster inside the category
${cluster}

THE HEAD TERMS OWNED BY THAT LISTING PAGE
${laneKeywords.length ? laneKeywords.map((k) => `  - ${k.text}`).join('\n') : '  (none registered)'}
This is the topic you are inside. Your keywords must sit CLOSE to these — and must never BE
one of them. The listing page ranks for them; this product page supports it from underneath
with a query of its own that no listing page would ever target.

COLLECTION DNA — how a product here is identified:
  aesthetic + product type + visual motif  (e.g. witchcore + graphic tee + botanical gothic)
The listing page already carries the aesthetic and the product type. Your job is the MOTIF —
the part nothing else in the catalogue can claim.

THE PRODUCTS IN THE SAME LANE — these are the ones you can actually collide with
${cousins || '  (none yet)'}

RESERVED KEYWORDS — owned by a category/collection page, or by another product. These are
OFF-LIMITS, for the primary and for every secondary. Never suggest one, or a synonym of one.
${reserved.length ? reserved.map((k) => `  - ${k}`).join('\n') : '  (none)'}

The print image is attached. Read the motifs off the image, not off the title.
`.trim();

  const result = await model.generateContent([
    { text: prompt },
    {
      inlineData: {
        data: Buffer.from(position.imageData).toString('base64'),
        mimeType: position.imageMime,
      },
    },
  ]);

  let parsed: {
    primary: { text: string; rationale: string };
    entity: string;
    visibleMotifs: string[];
    keywords: Suggestion[];
    rejected: { text: string; reason: string }[];
  };
  try {
    parsed = JSON.parse(result.response.text());
  } catch {
    return NextResponse.json({ error: 'The model did not return usable JSON.' }, { status: 502 });
  }

  // ---- code-level policing: the model is not trusted to enforce this on itself ----

  // The proposed primary. The head terms belong to the listing pages, and a model that has
  // just been shown a long list of gothic keywords is exactly the kind of thing that
  // reaches for one — so the reserved list is re-checked here, in code.
  const primaryText = (parsed.primary?.text || '').trim();
  let primaryConflict: string | undefined;
  if (!position.primaryKeyword) {
    const head = reserved.find((r) => countPhrase(primaryText, r) > 0 || countPhrase(r, primaryText) > 0);
    if (!primaryText) {
      primaryConflict = 'No primary was proposed.';
    } else if (head) {
      primaryConflict = `"${head}" is a head term owned by a listing page (or another product). A product page cannot target it.`;
    } else if (!/\b(t-?shirt|tee|shirt|top)\b/i.test(primaryText)) {
      // Without a product noun it is not a purchase query, it is a mood.
      primaryConflict = 'A product primary must contain a product noun (t-shirt / tee / shirt).';
    }
  }

  const usedElsewhere = new Map<string, string>();
  for (const s of siblings) {
    for (const k of s.secondaryKeywords) usedElsewhere.set(k.trim().toLowerCase(), s.title);
  }

  const suggestions: Suggestion[] = (parsed.keywords || [])
    .filter((k) => k?.text?.trim())
    .map((k) => {
      const text = k.text.trim();
      const tier: Tier = PLACEMENT[k.tier as Tier] ? (k.tier as Tier) : 'COMMERCIAL';
      let conflict: string | undefined;

      // Cannibalization: a suggestion that contains — or is contained by — another page's
      // primary keyword is targeting that page's query, whatever the model claims.
      const clash = reserved.find(
        (r) => countPhrase(text, r) > 0 || countPhrase(r, text) > 0,
      );
      if (clash) conflict = `Cannibalizes "${clash}" — that query belongs to another product.`;

      // Echoing this page's own primary adds nothing and dilutes the set.
      const ownPrimary = position.primaryKeyword || primaryText;
      if (!conflict && ownPrimary && countPhrase(text, ownPrimary) > 0) {
        conflict = 'Restates the primary keyword. A secondary must add something new.';
      }

      // Mass repeats are what turn a catalogue into mush.
      const owner = usedElsewhere.get(text.toLowerCase());
      if (!conflict && owner) conflict = `Already a secondary on "${owner}" — reusing it blurs both pages.`;

      return { text, tier, rationale: k.rationale || '', conflict };
    });

  return NextResponse.json({
    primary: {
      text: primaryText,
      rationale: parsed.primary?.rationale || '',
      conflict: primaryConflict,
      /** True when the page already had one and the model was told to leave it alone. */
      alreadySet: Boolean(position.primaryKeyword),
    },
    entity: parsed.entity || '',
    visibleMotifs: parsed.visibleMotifs || [],
    suggestions,
    rejected: parsed.rejected || [],
  });
}
