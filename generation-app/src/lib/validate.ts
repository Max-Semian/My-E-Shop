/**
 * Code-level enforcement of the keyword rules.
 *
 * The prompt asks the model to respect hard counts. This file assumes it did NOT.
 * Every count is re-derived from the generated text, so a model that stuffs keywords
 * cannot get its output saved — it gets rejected and regenerated with the violations
 * fed back to it. This is what actually guarantees "no keyword stuffing", not the prompt.
 */

export interface GeneratedCopy {
  description: string;
  seoDescription: string;
  keywordsUsed?: { primary?: string; secondary?: string[] };
  warnings?: string[];
}

export interface RuleContext {
  primaryKeyword: string;
  secondaryKeywords: string[];
  reservedKeywords: string[];
}

export const SEO_DESCRIPTION_MAX = 155;
/** Above this, copy reads as stuffed regardless of per-keyword counts. */
export const MAX_KEYWORD_DENSITY = 0.02;

/** Count non-overlapping, case-insensitive occurrences of a phrase, on word boundaries. */
export function countPhrase(haystack: string, phrase: string): number {
  const needle = phrase.trim();
  if (!needle) return 0;
  const escaped = needle.replace(/[.*+?^${}()|[\]\\]/g, '\\$&').replace(/\s+/g, '\\s+');
  // \b is unreliable next to non-word chars, so guard with lookarounds on word chars.
  const re = new RegExp(`(?<![\\p{L}\\p{N}])${escaped}(?![\\p{L}\\p{N}])`, 'giu');
  return (haystack.match(re) || []).length;
}

function wordCount(text: string): number {
  return (text.trim().match(/[\p{L}\p{N}'-]+/gu) || []).length;
}

export interface Violation {
  rule: string;
  detail: string;
}

/**
 * Returns [] when the copy obeys every hard rule. Any entry means: do not save,
 * regenerate and show these violations back to the model.
 */
export function validateCopy(copy: GeneratedCopy, ctx: RuleContext): Violation[] {
  const v: Violation[] = [];
  const desc = copy.description || '';
  const meta = copy.seoDescription || '';

  // --- Primary keyword: exactly once in each field ---
  const primaryInDesc = countPhrase(desc, ctx.primaryKeyword);
  if (primaryInDesc !== 1) {
    v.push({
      rule: 'primary_keyword_count',
      detail: `Primary keyword "${ctx.primaryKeyword}" appears ${primaryInDesc}x in the description. It must appear exactly 1x.`,
    });
  }
  const primaryInMeta = countPhrase(meta, ctx.primaryKeyword);
  if (primaryInMeta !== 1) {
    v.push({
      rule: 'primary_keyword_meta_count',
      detail: `Primary keyword "${ctx.primaryKeyword}" appears ${primaryInMeta}x in the meta description. It must appear exactly 1x.`,
    });
  }

  // --- Secondary keywords: at most once, and only in the description ---
  for (const kw of ctx.secondaryKeywords) {
    const n = countPhrase(desc, kw);
    if (n > 1) {
      v.push({
        rule: 'secondary_keyword_repeat',
        detail: `Secondary keyword "${kw}" appears ${n}x in the description. Max is 1x — remove the extra uses or drop the keyword entirely.`,
      });
    }
    if (countPhrase(meta, kw) > 0) {
      v.push({
        rule: 'secondary_keyword_in_meta',
        detail: `Secondary keyword "${kw}" must not appear in the meta description.`,
      });
    }
  }

  // --- Cannibalization: another page already owns these queries ---
  for (const kw of ctx.reservedKeywords) {
    const n = countPhrase(desc, kw) + countPhrase(meta, kw);
    if (n > 0) {
      v.push({
        rule: 'cannibalization',
        detail: `"${kw}" is already targeted by another position and must not be used here (found ${n}x).`,
      });
    }
  }

  // --- Meta length ---
  if (meta.length > SEO_DESCRIPTION_MAX) {
    v.push({
      rule: 'meta_too_long',
      detail: `Meta description is ${meta.length} characters. Max is ${SEO_DESCRIPTION_MAX}.`,
    });
  }
  if (meta.length < 50) {
    v.push({ rule: 'meta_too_short', detail: `Meta description is only ${meta.length} characters.` });
  }

  // --- Overall density: catches stuffing that slips past per-keyword counts ---
  const words = wordCount(desc);
  if (words > 0) {
    const allKw = [ctx.primaryKeyword, ...ctx.secondaryKeywords];
    const kwWords = allKw.reduce((sum, kw) => sum + countPhrase(desc, kw) * wordCount(kw), 0);
    const density = kwWords / words;
    if (density > MAX_KEYWORD_DENSITY) {
      v.push({
        rule: 'keyword_density',
        detail: `Keyword density is ${(density * 100).toFixed(1)}% (max ${(MAX_KEYWORD_DENSITY * 100).toFixed(0)}%). Rewrite with fewer keyword mentions.`,
      });
    }
  }

  // --- Sanity: description length ---
  if (words < 60) {
    v.push({ rule: 'description_too_short', detail: `Description is only ${words} words; aim for 90–140.` });
  }

  return v;
}
