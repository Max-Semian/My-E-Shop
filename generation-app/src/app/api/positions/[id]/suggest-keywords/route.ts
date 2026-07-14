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
  required: ['entity', 'visibleMotifs', 'keywords', 'rejected'],
} as const;

function systemInstruction(): string {
  return `
You are a technical SEO strategist for an e-commerce catalogue of graphic T-shirts on
WooCommerce. Your job is to build the SECONDARY KEYWORD SET for one product.

You are given the print image, the print title and the primary keyword. Use all three.
The image is the source of truth for anything visual: never name an object you cannot see.

# THE THREE LEVELS — the set must contain all three
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
  if (!position.primaryKeyword) {
    return NextResponse.json({ error: 'Set the primary keyword first — the set is built around it.' }, { status: 400 });
  }

  const key = process.env.GEMINI_API_KEY;
  if (!key) return NextResponse.json({ error: 'GEMINI_API_KEY is not set' }, { status: 500 });

  // The rest of the catalogue: what this position may not touch, and what it must not echo.
  const [reserved, siblings] = await Promise.all([
    reservedKeywordsExcept(position.id),
    prisma.position.findMany({
      where: { NOT: { id: position.id } },
      select: { title: true, primaryKeyword: true, secondaryKeywords: true, category: true },
    }),
  ]);

  const collection = position.category || '(not set)';
  const cousins = siblings
    .filter((s) => !position.category || s.category === position.category)
    .map((s) => `  - "${s.title}" → primary: ${s.primaryKeyword}`)
    .join('\n');

  const model = new GoogleGenerativeAI(key).getGenerativeModel({
    model: process.env.GEMINI_MODEL || 'gemini-2.5-flash',
    systemInstruction: systemInstruction(),
    generationConfig: {
      temperature: 0.3,
      responseMimeType: 'application/json',
      responseSchema: responseSchema as any,
    },
  });

  const prompt = `
PRINT TITLE — the concept of the artwork
${position.title}

PRIMARY KEYWORD — the cluster this page owns. Every secondary must reinforce it.
${position.primaryKeyword}

COLLECTION
${collection}

COLLECTION DNA — how a product in this catalogue is identified:
  aesthetic + product type + visual motif  (e.g. witchcore + graphic tee + botanical gothic)
Your set must supply the parts the primary keyword does not already carry.

OTHER PRODUCTS IN THIS COLLECTION — you must stay distinguishable from these
${cousins || '  (none yet)'}

RESERVED KEYWORDS — owned by other product pages. Never suggest these, or synonyms of them.
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
      if (!conflict && countPhrase(text, position.primaryKeyword) > 0) {
        conflict = 'Restates the primary keyword. A secondary must add something new.';
      }

      // Mass repeats are what turn a catalogue into mush.
      const owner = usedElsewhere.get(text.toLowerCase());
      if (!conflict && owner) conflict = `Already a secondary on "${owner}" — reusing it blurs both pages.`;

      return { text, tier, rationale: k.rationale || '', conflict };
    });

  return NextResponse.json({
    entity: parsed.entity || '',
    visibleMotifs: parsed.visibleMotifs || [],
    suggestions,
    rejected: parsed.rejected || [],
  });
}
