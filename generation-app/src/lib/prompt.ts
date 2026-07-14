/**
 * The generation contract for Gemini Flash.
 *
 * Design notes — why the rules look like this:
 *  - Every rule is stated as a HARD COUNT, not a vibe ("exactly once", "at most once").
 *    Vague guidance like "use keywords naturally" is what produces keyword stuffing.
 *  - The model is explicitly ALLOWED to drop a keyword it cannot place naturally, and
 *    must report it in `warnings`. Without that escape hatch a model will force-fit a
 *    keyword — which is exactly how spam text gets written.
 *  - The image is the source of truth for what the print depicts. Hallucinated print
 *    elements are the other common failure, so it is called out separately.
 *  - Output is a strict JSON schema, so nothing has to be parsed out of prose.
 *
 * IMPORTANT: the prompt is only the first line of defence. Everything here is
 * re-checked in code (see validate.ts) — the model is never trusted on counts.
 */

export interface GenerationInput {
  title: string;
  seoTitle: string;
  primaryKeyword: string;
  secondaryKeywords: string[];
  /** Keywords already claimed by OTHER positions — must not be targeted here. */
  reservedKeywords: string[];
  brandVoice?: string;
}

const DEFAULT_BRAND_VOICE =
  'Cretho — a niche fashion brand selling graphic T-shirts. Voice: confident, ' +
  'self-expressive, a little subcultural. Never salesy, never hype, no exclamation marks.';

export function buildSystemInstruction(): string {
  return `
You are an SEO copywriter for a fashion e-commerce brand. You write product copy for
graphic T-shirt prints. You will be given a print IMAGE plus SEO inputs, and you must
produce two pieces of copy.

# WHAT YOU PRODUCE
1. "description"     — the product description shown on the product page.
                       3 short paragraphs, 90–140 words TOTAL.
2. "seoDescription"  — the meta description tag. ONE sentence, 140–155 characters MAX.

# THE IMAGE IS THE SOURCE OF TRUTH
- Describe ONLY what is actually visible in the print image: its motif, symbols,
  composition, colours, mood.
- NEVER invent elements that are not in the image. If you are unsure what an element
  is, describe it in general terms instead of guessing a specific object.
- The copy must make sense to someone looking at that exact print.

# KEYWORD RULES — THESE ARE HARD LIMITS, NOT SUGGESTIONS
These rules exist to prevent keyword stuffing. Violating a count is a failed answer.

- PRIMARY keyword: use it EXACTLY ONCE in "description", placed inside the first two
  sentences, inside a normal grammatical sentence. Not in a list. Not repeated later.
- PRIMARY keyword in "seoDescription": use it EXACTLY ONCE.
- SECONDARY keywords: use each one AT MOST ONCE, and ONLY in "description".
  A secondary keyword may be used ZERO times.
- If a secondary keyword cannot be placed in a sentence that a human would actually
  write, DO NOT USE IT. Leave it out and list it in "warnings". Omitting a keyword is
  always better than forcing it. This is not a failure — it is the correct behaviour.
- NEVER repeat any keyword beyond its allowed count, in any inflected form.
- NEVER chain keywords together ("graphic tee, witch core t-shirt, occult print shirt").
- NEVER add keywords that were not supplied to you.
- Combined, all keywords must stay under ~2% of the total word count.

# CANNIBALIZATION RULES
- You will be given "reservedKeywords": search terms that already belong to OTHER
  product pages. Do NOT target, repeat, or build the copy around any of them.
  Incidental common words are fine; targeting the phrase is not.
- Do not make this page compete with those pages.

# STYLE RULES
- Do not reuse the SEO title verbatim inside the description.
- Do not open with the product title. Open with the print's idea.
- No clickbait, no "buy now", no exclamation marks, no emoji.
- Write for a human first. The copy must read as if no keywords were assigned.

# BEFORE YOU ANSWER — SELF-CHECK (do this silently)
Count the occurrences of every keyword in your own draft.
 1. Is the primary keyword in "description" exactly 1? If not, rewrite.
 2. Is the primary keyword in "seoDescription" exactly 1? If not, rewrite.
 3. Is every secondary keyword 0 or 1? If any is 2+, rewrite.
 4. Is "seoDescription" <= 155 characters? If not, shorten.
 5. Did you invent any print element not in the image? If so, remove it.
Only then produce the JSON.

# OUTPUT
Return ONLY the JSON object matching the provided schema. No markdown, no commentary.
- "keywordsUsed.primary"   — the primary keyword if you used it, else omit.
- "keywordsUsed.secondary" — ONLY the secondary keywords you actually placed.
- "warnings"               — one entry for each secondary keyword you deliberately
                             skipped, and why (e.g. "skipped 'oversized tee' — the
                             print has no fit-related context").
`.trim();
}

export function buildUserPrompt(input: GenerationInput): string {
  const voice = input.brandVoice?.trim() || DEFAULT_BRAND_VOICE;
  const secondary = input.secondaryKeywords.length
    ? input.secondaryKeywords.map((k) => `  - ${k}`).join('\n')
    : '  (none)';
  const reserved = input.reservedKeywords.length
    ? input.reservedKeywords.map((k) => `  - ${k}`).join('\n')
    : '  (none)';

  return `
BRAND VOICE
${voice}

PRINT TITLE (main name of the print)
${input.title}

SEO TITLE (do not repeat verbatim in the description)
${input.seoTitle || '(not set)'}

PRIMARY KEYWORD — use exactly once in the description, exactly once in the meta description
${input.primaryKeyword}

SECONDARY KEYWORDS — each at most once, skip any that do not fit naturally
${secondary}

RESERVED KEYWORDS — already targeted by other pages, do NOT target these here
${reserved}

The print image is attached. Describe what is actually in it.
`.trim();
}

/** Gemini structured-output schema — forces valid, parseable JSON. */
export const RESPONSE_SCHEMA = {
  type: 'object',
  properties: {
    description: { type: 'string' },
    seoDescription: { type: 'string' },
    keywordsUsed: {
      type: 'object',
      properties: {
        primary: { type: 'string' },
        secondary: { type: 'array', items: { type: 'string' } },
      },
    },
    warnings: { type: 'array', items: { type: 'string' } },
  },
  required: ['description', 'seoDescription', 'keywordsUsed', 'warnings'],
} as const;
