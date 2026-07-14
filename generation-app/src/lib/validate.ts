/**
 * Code-level enforcement of the SEO field rules (docs/woocommerce-prompt-template.md).
 *
 * The prompt asks the model to respect hard counts and lengths. This file assumes it did
 * NOT. Every count and length is re-derived from the generated output, so a model that
 * stuffs keywords, writes a 200-char meta description, or duplicates alt texts cannot get
 * its output saved — it is rejected and regenerated with the violations fed back.
 * This, not the prompt, is what actually guarantees the rules hold.
 */

import { META_TITLE_MAX, META_DESCRIPTION_MAX } from './prompt';

export interface GeneratedCopy {
  name: string;
  slug: string;
  description: string;
  shortDescription: string;
  metaTitle: string;
  metaDescription: string;
  tags: string[];
  imagesAlt: string[];
  imageFilenames: string[];
  keywordsUsed?: { primary?: string; secondary?: string[] };
  warnings?: string[];
}

export interface RuleContext {
  primaryKeyword: string;
  secondaryKeywords: string[];
  reservedKeywords: string[];
}

export const MAX_KEYWORD_DENSITY = 0.02;
const IMAGE_VIEWS = 5;

/** Cliché openers the framework bans outright. */
const CLICHE_OPENERS = [
  'discover',
  'introducing',
  'elevate',
  'step into',
  'unleash',
  'meet the',
  'say hello to',
  'welcome to',
];

/** Fulfilment partners that must never surface in customer-facing copy. */
const SUPPLIER_NAMES = ['printful', 'printify', 'gelato', 'gooten'];

/** Count non-overlapping, case-insensitive occurrences of a phrase, on word boundaries. */
export function countPhrase(haystack: string, phrase: string): number {
  const needle = phrase.trim();
  if (!needle) return 0;
  const escaped = needle.replace(/[.*+?^${}()|[\]\\]/g, '\\$&').replace(/\s+/g, '\\s+');
  const re = new RegExp(`(?<![\\p{L}\\p{N}])${escaped}(?![\\p{L}\\p{N}])`, 'giu');
  return (haystack.match(re) || []).length;
}

function stripHtml(html: string): string {
  return html.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
}

function wordCount(text: string): number {
  return (text.trim().match(/[\p{L}\p{N}'-]+/gu) || []).length;
}

/** First <p> of the HTML description, or the first sentence if there is no markup. */
function firstParagraph(html: string): string {
  const m = html.match(/<p[^>]*>([\s\S]*?)<\/p>/i);
  return stripHtml(m ? m[1] : html.split(/\n\n/)[0] || '');
}

function sentences(text: string): string[] {
  return stripHtml(text)
    .split(/(?<=[.!?])\s+/)
    .map((s) => s.trim().toLowerCase())
    .filter((s) => s.length > 25);
}

export interface Violation {
  rule: string;
  detail: string;
}

export function validateCopy(copy: GeneratedCopy, ctx: RuleContext): Violation[] {
  const v: Violation[] = [];
  const push = (rule: string, detail: string) => v.push({ rule, detail });

  const descText = stripHtml(copy.description || '');
  const shortText = stripHtml(copy.shortDescription || '');
  const name = copy.name || '';
  const metaTitle = copy.metaTitle || '';
  const metaDesc = copy.metaDescription || '';
  const kw = ctx.primaryKeyword;

  // ---------- primary keyword: exactly once per field ----------
  const exactlyOnce: Array<[string, string]> = [
    ['name', name],
    ['description', descText],
    ['metaTitle', metaTitle],
    ['metaDescription', metaDesc],
  ];
  for (const [field, text] of exactlyOnce) {
    const n = countPhrase(text, kw);
    if (n !== 1) {
      push(
        'primary_keyword_count',
        `Primary keyword "${kw}" appears ${n}x in "${field}". It must appear exactly 1x there.`,
      );
    }
  }
  if (countPhrase(shortText, kw) > 1) {
    push(
      'primary_keyword_short',
      `Primary keyword "${kw}" appears more than once in "shortDescription". Max is 1x.`,
    );
  }
  // Placement: it has to be in the opening paragraph, not buried at the end.
  if (countPhrase(firstParagraph(copy.description || ''), kw) !== 1) {
    push(
      'primary_keyword_placement',
      `Primary keyword "${kw}" must sit in the FIRST paragraph of the description, exactly once.`,
    );
  }
  // metaTitle must lead with it.
  if (metaTitle && !metaTitle.toLowerCase().trim().startsWith(kw.toLowerCase().trim())) {
    push('meta_title_keyword_first', `"metaTitle" must start with the primary keyword "${kw}".`);
  }

  // ---------- secondary keywords: at most once, description only ----------
  for (const k of ctx.secondaryKeywords) {
    const n = countPhrase(descText, k);
    if (n > 1) {
      push(
        'secondary_keyword_repeat',
        `Secondary keyword "${k}" appears ${n}x in the description. Max is 1x — remove the extra uses or drop it.`,
      );
    }
    const elsewhere =
      countPhrase(metaTitle, k) + countPhrase(metaDesc, k) + countPhrase(shortText, k);
    if (elsewhere > 0) {
      push(
        'secondary_keyword_outside_description',
        `Secondary keyword "${k}" may only appear in the description, but was found in the meta/short fields.`,
      );
    }
  }

  // ---------- cannibalization ----------
  for (const k of ctx.reservedKeywords) {
    const n =
      countPhrase(descText, k) +
      countPhrase(metaDesc, k) +
      countPhrase(metaTitle, k) +
      countPhrase(name, k);
    if (n > 0) {
      push(
        'cannibalization',
        `"${k}" is already targeted by another position and must not be used here (found ${n}x).`,
      );
    }
  }

  // ---------- lengths ----------
  if (metaTitle.length > META_TITLE_MAX) {
    push('meta_title_too_long', `metaTitle is ${metaTitle.length} chars, max ${META_TITLE_MAX}.`);
  }
  if (metaDesc.length > META_DESCRIPTION_MAX) {
    push(
      'meta_description_too_long',
      `metaDescription is ${metaDesc.length} chars, max ${META_DESCRIPTION_MAX}.`,
    );
  }
  if (metaDesc.length < 70) {
    push('meta_description_too_short', `metaDescription is only ${metaDesc.length} chars.`);
  }
  const descWords = wordCount(descText);
  if (descWords < 110) {
    push('description_too_short', `Description is ${descWords} words; aim for 130–200.`);
  }

  // ---------- shortDescription must not restate the long one ----------
  const longSentences = new Set(sentences(copy.description || ''));
  for (const s of sentences(copy.shortDescription || '')) {
    if (longSentences.has(s)) {
      push(
        'short_description_duplicates',
        'shortDescription reuses a sentence from the long description. It must add a distinct angle.',
      );
      break;
    }
  }

  // ---------- keyword density ----------
  if (descWords > 0) {
    const all = [kw, ...ctx.secondaryKeywords];
    const kwWords = all.reduce((sum, k) => sum + countPhrase(descText, k) * wordCount(k), 0);
    const density = kwWords / descWords;
    if (density > MAX_KEYWORD_DENSITY) {
      push(
        'keyword_density',
        `Keyword density is ${(density * 100).toFixed(1)}% (max ${(MAX_KEYWORD_DENSITY * 100).toFixed(0)}%). Rewrite with fewer keyword mentions.`,
      );
    }
  }

  // ---------- style: cliché openers and supplier names ----------
  const opener = (descText + ' ').trim().toLowerCase();
  for (const c of CLICHE_OPENERS) {
    if (opener.startsWith(c)) {
      push('cliche_opener', `The description opens with the banned cliché "${c}…". Rewrite the opening.`);
      break;
    }
  }
  const haystack = [name, descText, shortText, metaTitle, metaDesc].join(' ').toLowerCase();
  for (const s of SUPPLIER_NAMES) {
    if (haystack.includes(s)) {
      push('supplier_mentioned', `The fulfilment partner "${s}" must never appear in customer-facing copy.`);
    }
  }

  // ---------- slug ----------
  const slug = copy.slug || '';
  if (!/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(slug)) {
    push('slug_format', `slug "${slug}" must be lowercase words separated by single hyphens, no diacritics.`);
  } else {
    const parts = slug.split('-').length;
    if (parts < 3 || parts > 8) {
      push('slug_length', `slug has ${parts} words; it should be 4–8 (3 accepted at a push).`);
    }
  }

  // ---------- tags ----------
  const tags = copy.tags || [];
  if (tags.length < 3 || tags.length > 5) {
    push('tags_count', `Expected 3–5 tags, got ${tags.length}.`);
  }

  // ---------- images ----------
  const alts = copy.imagesAlt || [];
  const files = copy.imageFilenames || [];
  if (alts.length !== IMAGE_VIEWS) {
    push('images_alt_count', `Expected exactly ${IMAGE_VIEWS} alt texts, got ${alts.length}.`);
  }
  if (files.length !== IMAGE_VIEWS) {
    push('image_filenames_count', `Expected exactly ${IMAGE_VIEWS} filenames, got ${files.length}.`);
  }
  const normalized = alts.map((a) => a.trim().toLowerCase());
  if (new Set(normalized).size !== normalized.length) {
    push('images_alt_duplicate', 'Alt texts must all be distinct — duplicates found.');
  }
  for (const f of files) {
    if (!/^[a-z0-9_]+\.(jpg|jpeg|png|webp)$/.test(f)) {
      push('image_filename_format', `Filename "${f}" must be lowercase with underscores, e.g. my_print_front.jpg.`);
      break;
    }
  }

  return v;
}
