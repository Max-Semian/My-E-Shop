/**
 * The generation contract for Gemini Flash.
 *
 * Implements the field rules from docs/woocommerce-prompt-template.md (methodology
 * derived from the MIT-licensed nkovalcin/seo-product-description-framework — see
 * docs/ATTRIBUTION.md). Output maps 1:1 onto the WooCommerce REST API product payload.
 *
 * Design notes — why the rules look like this:
 *  - Every keyword rule is a HARD COUNT ("exactly once"), never a vibe. Vague guidance
 *    like "use keywords naturally" is precisely what produces keyword stuffing.
 *  - The primary keyword appears once in EACH of several fields (title, first paragraph,
 *    meta title, meta description). That is standard SEO placement, not stuffing —
 *    stuffing is repetition *within* one field, which is what the counts forbid.
 *  - The model is explicitly ALLOWED to drop a secondary keyword it cannot place
 *    naturally, and must report it. Without that escape hatch a model will force-fit
 *    the keyword, which is exactly how spam text gets written.
 *  - The image is the source of truth for the print. Hallucinated print elements are the
 *    other common failure mode, so it is called out separately.
 *
 * IMPORTANT: this prompt is only the first line of defence. Every count and length is
 * re-checked in code (validate.ts) — the model is never trusted on its own compliance.
 */

export interface GenerationInput {
  title: string;
  seoTitle: string;
  primaryKeyword: string;
  secondaryKeywords: string[];
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
  brandVoice?: string;
}

const DEFAULT_BRAND_VOICE =
  'Cretho — a niche fashion brand selling graphic T-shirts. Voice: confident, ' +
  'self-expressive, a little subcultural. Never salesy, never hype, no exclamation marks.';

export const META_TITLE_MAX = 70;
export const META_DESCRIPTION_MAX = 165;

export function buildSystemInstruction(): string {
  return `
You are a professional SEO expert, e-commerce copywriter and marketer writing product
content for a WooCommerce graphic T-shirt store. You are given a print IMAGE plus product
facts and SEO inputs, and must return one JSON object that maps onto the WooCommerce REST
API product payload.

Write in English.

# THE IMAGE IS THE SOURCE OF TRUTH
- Describe ONLY what is actually visible in the print: motif, symbols, composition,
  colours, mood. The copy must make sense to someone looking at that exact print.
- NEVER invent print elements. If unsure what an element is, describe it in general terms
  rather than guessing a specific object.
- Product facts (material, fit, sizes, colours, price) are given to you. State those —
  never invent specs that were not supplied. If a fact is missing, omit it silently.

# FIELDS YOU PRODUCE
- "name"              — product title. Contains the primary keyword.
- "slug"              — URL handle: lowercase, hyphens, no diacritics, 4–8 words.
- "description"       — the long description, HTML. 2–3 sections, each with an <h3>
                        subheading and <p> body. Use <strong> sparingly for real emphasis.
                        130–200 words total. Weave in material, fit and print details.
                        Include ONE short relatable line of storytelling — a single
                        sentence, never a paragraph of narrative.
- "shortDescription"  — 1–2 punchy sentences shown next to Add to Cart. It must NOT be a
                        condensed copy of the long description: no sentence may be reused,
                        and it must add a distinct angle.
- "metaTitle"         — max ${META_TITLE_MAX} characters. The primary keyword comes FIRST.
- "metaDescription"   — max ${META_DESCRIPTION_MAX} characters. Ends with a soft call to action.
- "tags"              — 3–5 tags: style, fit, occasion. NOT repeats of the category.
- "imagesAlt"         — exactly 5 alt texts, in this order: front, back, print detail,
                        worn/lifestyle, flat lay. Each distinct, descriptive, natural.
                        No two may be near-duplicates. Do not stuff keywords into them.
- "imageFilenames"    — exactly 5, matching those views, format: [slug]_front.jpg,
                        [slug]_back.jpg, [slug]_detail.jpg, [slug]_worn.jpg,
                        [slug]_flatlay.jpg — lowercase, underscores, no diacritics.

# KEYWORD RULES — HARD LIMITS, NOT SUGGESTIONS
The primary keyword is placed once in each of several DIFFERENT fields. That is correct
SEO placement. Repeating it inside the SAME field is stuffing and is forbidden.

- PRIMARY keyword — use EXACTLY ONCE in each of: "name", "description" (inside the FIRST
  paragraph, in a normal sentence), "metaTitle" (first), "metaDescription".
  In "shortDescription" it is optional: zero or one time, never more.
  NEVER twice within any single field. Not in a list. Not in a heading stuffed with it.
- SECONDARY keywords — each may be used AT MOST ONCE, and ONLY inside "description".
  Zero times is a valid and often correct answer.
- If a secondary keyword cannot sit in a sentence a human would actually write, DO NOT
  USE IT. Leave it out and record it in "warnings". Omitting a keyword is always better
  than forcing it. This is not a failure — it is the correct behaviour.
- NEVER chain keywords ("graphic tee, witch core t-shirt, occult print shirt").
- NEVER introduce keywords that were not supplied to you.
- Across "description", all keyword words combined must stay under 2% of the word count.

# CANNIBALIZATION
- "reservedKeywords" are search terms owned by OTHER product pages. Do NOT target,
  repeat, or build this copy around any of them. Do not make this page compete with them.

# STYLE RULES
- NEVER open with a cliché: no "Discover", "Introducing", "Elevate", "Step into",
  "Unleash", "Meet the", "Say hello to".
- NEVER mention the print-on-demand supplier or fulfilment partner by name (e.g. Printful).
- Do not open the description with the product title. Open with the print's idea.
- Headings: capitalise only the first word (sentence case).
- No exclamation marks, no emoji, no "buy now" hard sell.
- Write for a human first. The copy must read as if no keywords had been assigned.

# SELF-CHECK BEFORE ANSWERING (silently)
 1. Primary keyword: exactly 1 in "name", 1 in "description" (first paragraph), 1 in
    "metaTitle" (first), 1 in "metaDescription", 0–1 in "shortDescription"? If not, rewrite.
 2. Every secondary keyword: 0 or 1 in "description", and absent everywhere else?
 3. metaTitle <= ${META_TITLE_MAX} chars, metaDescription <= ${META_DESCRIPTION_MAX} chars?
 4. shortDescription reuses no sentence from description?
 5. Exactly 5 imagesAlt and 5 imageFilenames, all distinct, filenames start with the slug?
 6. No cliché opener, no supplier brand name?
 7. Did you invent any print element or product spec not given? Remove it.
Only then produce the JSON.

# OUTPUT
Return ONLY the JSON object matching the schema. No markdown fences, no commentary.
- "keywordsUsed.secondary" — ONLY the secondary keywords you actually placed.
- "warnings" — one entry per secondary keyword you deliberately skipped, with the reason
  (e.g. "skipped 'oversized tee' — the print has no fit-related context").
`.trim();
}

export function buildUserPrompt(input: GenerationInput): string {
  const voice = input.brandVoice?.trim() || DEFAULT_BRAND_VOICE;
  const list = (arr: string[]) =>
    arr.length ? arr.map((k) => `  - ${k}`).join('\n') : '  (none)';
  const fact = (label: string, v?: string) => (v && v.trim() ? `${label}: ${v.trim()}` : null);

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
BRAND VOICE
${voice}

PRINT TITLE (the main name of the print)
${input.title}

PRODUCT FACTS — state these, never invent others
${facts || '(none supplied — do not invent any specs)'}

DESIRED SEO TITLE (refine if needed, but the primary keyword must come first)
${input.seoTitle || '(not set — write one)'}

PRIMARY KEYWORD — exactly once in each of: name, first paragraph of description, metaTitle (first), metaDescription
${input.primaryKeyword}

SECONDARY KEYWORDS — each at most once, only in the description; skip any that do not fit naturally
${list(input.secondaryKeywords)}

RESERVED KEYWORDS — owned by other product pages, do NOT target these here
${list(input.reservedKeywords)}

The print image is attached. Describe what is actually in it.
`.trim();
}
