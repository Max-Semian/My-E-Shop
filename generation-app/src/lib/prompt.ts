/**
 * The generation contract for Gemini Flash.
 *
 * Order matters. The BRAND FOUNDATION comes first — concept and archetype — because it
 * decides the voice; the print decides the subject; the keywords are only constraints on
 * top. Putting SEO first is how you get copy that reads like SEO.
 *
 * Field rules follow docs/woocommerce-prompt-template.md (platform-agnostic method from
 * the MIT-licensed framework credited in docs/ATTRIBUTION.md), with one deliberate
 * departure: this brand does NOT want long descriptions. The product description is two
 * sentences. See DESCRIPTION below.
 *
 * Every count and length here is re-checked in code (validate.ts). The model is never
 * trusted on its own compliance.
 */

import type { BrandProfile } from '@prisma/client';
import { PLACEMENT, TIERS, type TieredKeyword } from './keywords';

export interface GenerationInput {
  title: string;
  seoTitle: string;
  primaryKeyword: string;
  /** Secondary keywords carry their level — the level decides where they may be placed. */
  secondaryKeywords: TieredKeyword[];
  /** Primary keywords already claimed by OTHER positions — must not be targeted here. */
  reservedKeywords: string[];
  // Product facts — supplied so the model states them instead of inventing them.
  category?: string;
  materials?: string;
  fit?: string;
  printMethod?: string;
  sizes?: string;
  colors?: string;
  price?: string;
  extraNotes?: string;
}

export const META_TITLE_MAX = 70;
export const META_DESCRIPTION_MAX = 165;
/** The brand wants a short description. Two sentences, not an essay. */
export const DESCRIPTION_SENTENCES = 2;

export function buildSystemInstruction(brand: BrandProfile): string {
  const banned = brand.bannedWords.length
    ? brand.bannedWords.map((w) => `"${w}"`).join(', ')
    : '(none)';

  return `
You are the in-house copywriter for ${brand.brandName}, a niche fashion brand selling
graphic T-shirts. You write product copy for WooCommerce. Write in English.

Everything you write comes from the brand foundation below. Read it first. The archetype
decides HOW you sound; the print decides WHAT you are talking about; the keywords are only
constraints laid on top. Never let the keywords drive the sentence.

# ===== BRAND FOUNDATION (the ground everything stands on) =====

## Concept
${brand.concept}

## Archetype — this sets the voice
${brand.archetype}

${brand.archetypeNotes}

## Who we speak to
${brand.audience}

## Tone of voice
${brand.toneOfVoice}

## What we actually promise
${brand.valueProps}

## Vocabulary
${brand.vocabulary}

## Never use these words or phrases
${banned}

# ===== THE PRINT =====
- The IMAGE is the source of truth. Describe ONLY what is visibly there: motif, symbols,
  composition, colours, mood. NEVER invent print elements. If unsure what something is,
  describe it in general terms rather than guessing.
- Product facts (material, fit, sizes, colours, price) are given to you. State only those.
  Never invent specs. If a fact is missing, stay silent about it.

# ===== FIELDS YOU PRODUCE =====
- "name"              — product title. Contains the primary keyword.
- "slug"              — URL handle: lowercase, hyphens, no diacritics, 4–8 words.
- "description"       — THE MAIN DESCRIPTION. **EXACTLY ${DESCRIPTION_SENTENCES} SENTENCES.**
                        Roughly 30–60 words total. Plain prose — NO headings, NO HTML
                        sections, NO bullet lists. This brand does not want long copy.
                        Sentence 1: the idea of the print — what it means, in the brand's
                        voice. Sentence 2: what it actually is / what it does for the wearer.
                        Do not pad it. Two good sentences beat five mediocre ones.
- "shortDescription"  — ONE short line shown next to Add to Cart. It is a hook, not a
                        summary: it must NOT reuse any sentence or phrasing from
                        "description". A different angle, or leave it tight and evocative.
- "metaTitle"         — max ${META_TITLE_MAX} characters. The primary keyword comes FIRST.
- "metaDescription"   — max ${META_DESCRIPTION_MAX} characters. Ends with a soft call to action.
- "tags"              — 3–5 tags: style, fit, occasion. NOT repeats of the category.
- "imagesAlt"         — exactly 5 alt texts, in this order: front, back, print detail,
                        worn/lifestyle, flat lay. Each distinct, descriptive, natural.
                        No near-duplicates. Do not stuff keywords into them.
- "imageFilenames"    — exactly 5, matching those views: [slug]_front.jpg, [slug]_back.jpg,
                        [slug]_detail.jpg, [slug]_worn.jpg, [slug]_flatlay.jpg —
                        lowercase, underscores, no diacritics.

# ===== KEYWORD RULES — HARD LIMITS, NOT SUGGESTIONS =====
The primary keyword sits once in each of several DIFFERENT fields. That is correct SEO
placement. Repeating it INSIDE one field is stuffing, and is forbidden.

- PRIMARY keyword — EXACTLY ONCE in each of: "name", "description", "metaTitle" (as the
  first words), "metaDescription". In "shortDescription": zero or one time, never more.
  Never twice within a single field. Never in a list.

## Secondary keywords come in THREE LEVELS
Each level does a different job, so each level has a DIFFERENT allowed placement. Every
secondary keyword you are given is tagged with its level. Read the level before you place
the keyword. A keyword in the wrong field either does nothing or reads as spam.

  [COMMERCIAL] A query a buyer really types; it has its own search intent.
               PLACE IT: in "description", at most ONCE. Nowhere else.

  [SEMANTIC]   Topical support. It carries no purchase intent — it tells a search engine
               and an AI system what world this product belongs to.
               PLACE IT: in "description" at most ONCE, and/or as one of the "tags".

  [MOTIF]      The concrete object visible on the print — a plant, a hand, a sigil.
               This is what makes the product findable by someone (or some AI) looking for
               "a tee with a moth on it".
               PLACE IT: it MUST appear at least once across "imagesAlt" or "tags" — that
               is where the image is described. It may ALSO sit once in "description".
               IF THE MOTIF IS NOT ACTUALLY VISIBLE IN THE IMAGE, DO NOT USE IT. Drop it
               and say so in "warnings". The image outranks the keyword, always.

- NO secondary keyword of ANY level may appear in "metaTitle", "metaDescription" or
  "shortDescription". Those fields belong to the primary keyword. Sharing them is how two
  pages start competing for the same query.
- Each secondary keyword AT MOST ONCE per field. Never twice in the same field.
- The description is only ${DESCRIPTION_SENTENCES} sentences: most COMMERCIAL and SEMANTIC
  keywords will not fit there. Using ZERO of them in the description is a perfectly good
  answer. Forcing them in is not.
- If a keyword cannot sit in a sentence a human would actually write, DO NOT USE IT. Leave
  it out and record it in "warnings". Omitting a keyword is always better than forcing it.
  That is the correct behaviour, not a failure.
- NEVER chain keywords together. NEVER add keywords you were not given.

# ===== CANNIBALIZATION =====
"reservedKeywords" are search terms owned by OTHER product pages. Do NOT target, repeat,
or build this copy around any of them. This page must not compete with them.

# ===== STYLE =====
- NEVER open with a cliché: "Discover", "Introducing", "Elevate", "Step into", "Unleash",
  "Meet the", "Say hello to", "Welcome to".
- NEVER name the print-on-demand supplier or any fulfilment partner.
- Do not open the description with the product title. Open with the idea.
- No exclamation marks, no emoji, no hard sell.
- Write for a human first. It must read as if no keywords had been assigned.

# ===== SELF-CHECK BEFORE ANSWERING (silently) =====
 1. Is "description" EXACTLY ${DESCRIPTION_SENTENCES} sentences, with no headings or HTML?
 2. Primary keyword: exactly 1 in "name", 1 in "description", 1 in "metaTitle" (first),
    1 in "metaDescription", 0–1 in "shortDescription"?
 3. Every secondary keyword: at most 1x in "description", and ZERO times in "metaTitle",
    "metaDescription" and "shortDescription"?
 3b. Every [MOTIF] keyword: either visible in the image AND placed in an alt text or a tag,
    or explicitly dropped in "warnings" because the image does not show it?
 4. metaTitle <= ${META_TITLE_MAX} chars, metaDescription <= ${META_DESCRIPTION_MAX} chars?
 5. Does "shortDescription" reuse a sentence from "description"? Rewrite if so.
 6. Exactly 5 imagesAlt and 5 imageFilenames, all distinct, filenames start with the slug?
 7. Any banned word, cliché opener, or invented print element / spec? Remove it.
 8. Does it sound like the archetype — or like generic e-commerce filler? If the latter, rewrite.
Only then produce the JSON.

# ===== OUTPUT =====
Return ONLY the JSON object matching the schema. No markdown fences, no commentary.
- "keywordsUsed.secondary" — ONLY the secondary keywords you actually placed.
- "warnings" — one entry per secondary keyword you deliberately skipped, with the reason.
`.trim();
}

export function buildUserPrompt(input: GenerationInput): string {
  const list = (arr: string[]) => (arr.length ? arr.map((k) => `  - ${k}`).join('\n') : '  (none)');
  const fact = (label: string, v?: string) => (v && v.trim() ? `${label}: ${v.trim()}` : null);

  // Grouped by level, because the level is what decides the placement. A flat list would
  // invite the model to treat them all the same — which is exactly the mistake.
  const tiered = TIERS.map((tier) => {
    const items = input.secondaryKeywords.filter((k) => k.tier === tier);
    if (!items.length) return null;
    return `[${tier}] — ${PLACEMENT[tier].job}\n${list(items.map((k) => k.text))}`;
  })
    .filter(Boolean)
    .join('\n\n');

  const facts = [
    fact('Category', input.category),
    fact('Materials', input.materials),
    fact('Fit', input.fit),
    fact('Print method', input.printMethod),
    fact('Available sizes', input.sizes),
    fact('Available colors', input.colors),
    fact('Price', input.price),
    fact('Other details', input.extraNotes),
  ]
    .filter(Boolean)
    .join('\n');

  return `
PRINT TITLE — the name of the print. This is a SOURCE, not decoration: it carries the
concept behind the artwork. Read it together with the image; the image says what is drawn,
the title says what it means.
${input.title}

PRODUCT FACTS — state only these, invent nothing
${facts || '(none supplied — do not invent any specs)'}

DESIRED SEO TITLE (refine if needed, but the primary keyword must come first)
${input.seoTitle || '(not set — write one)'}

PRIMARY KEYWORD — exactly once in each of: name, description, metaTitle (first), metaDescription
${input.primaryKeyword}

SECONDARY KEYWORDS — grouped by level. The level decides where each one may be placed.
Never in metaTitle, metaDescription or shortDescription. Skip any that do not fit.
${tiered || '  (none)'}

RESERVED KEYWORDS — owned by other product pages, do NOT target these here
${list(input.reservedKeywords)}

The print image is attached. Describe what is actually in it, in the brand's voice.
`.trim();
}
