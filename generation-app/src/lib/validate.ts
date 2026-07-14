/**
 * Code-level enforcement of the field rules.
 *
 * The prompt states hard counts and lengths. This file assumes the model ignored them.
 * Every count is re-derived from the generated output, so copy that stuffs keywords,
 * overruns the meta description, or pads the description past two sentences cannot be
 * saved — it is rejected and regenerated with the violations fed back. This, not the
 * prompt, is what actually guarantees the rules hold.
 *
 * Note on keyword density: the old 2% density ceiling was removed on purpose. With a
 * two-sentence description (~40 words) a single three-word keyword is already ~7%, so the
 * metric is meaningless at this length and would reject every valid answer. The hard
 * per-field counts ("exactly once") do the work instead.
 */

import { META_TITLE_MAX, META_DESCRIPTION_MAX, DESCRIPTION_SENTENCES } from './prompt';
import { PLACEMENT, type TieredKeyword } from './keywords';

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
  /** Each secondary keyword carries its level; the level decides its allowed placement. */
  secondaryKeywords: TieredKeyword[];
  reservedKeywords: string[];
  /** From the brand profile — words that must never appear in customer-facing copy. */
  bannedWords: string[];
}

const IMAGE_VIEWS = 5;

/** Cliché openers banned outright. */
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

/** Split into sentences. Abbreviations are rare in this copy, so the naive split is fine. */
function splitSentences(text: string): string[] {
  return stripHtml(text)
    .split(/(?<=[.!?])\s+/)
    .map((s) => s.trim())
    .filter(Boolean);
}

export interface Violation {
  rule: string;
  detail: string;
}

export function validateCopy(copy: GeneratedCopy, ctx: RuleContext): Violation[] {
  const v: Violation[] = [];
  const push = (rule: string, detail: string) => v.push({ rule, detail });

  const rawDesc = copy.description || '';
  const descText = stripHtml(rawDesc);
  const shortText = stripHtml(copy.shortDescription || '');
  const name = copy.name || '';
  const metaTitle = copy.metaTitle || '';
  const metaDesc = copy.metaDescription || '';
  const kw = ctx.primaryKeyword;

  // ---------- description: exactly two sentences, plain prose ----------
  const descSentences = splitSentences(rawDesc);
  if (descSentences.length !== DESCRIPTION_SENTENCES) {
    push(
      'description_sentence_count',
      `Description must be exactly ${DESCRIPTION_SENTENCES} sentences; got ${descSentences.length}. This brand does not want long copy.`,
    );
  }
  if (/<(h[1-6]|ul|ol|li)\b/i.test(rawDesc)) {
    push(
      'description_has_sections',
      'Description must be plain prose — no headings, lists or HTML sections.',
    );
  }
  const descWords = wordCount(descText);
  if (descWords > 0 && (descWords < 20 || descWords > 80)) {
    push('description_length', `Description is ${descWords} words; two sentences should land around 30–60.`);
  }

  // ---------- shortDescription: one line, not a restatement ----------
  const shortSentences = splitSentences(copy.shortDescription || '');
  if (shortSentences.length > 2) {
    push('short_description_too_long', `shortDescription should be one short line; got ${shortSentences.length} sentences.`);
  }
  const longNormalized = new Set(descSentences.map((s) => s.toLowerCase()));
  for (const s of shortSentences) {
    if (longNormalized.has(s.toLowerCase())) {
      push(
        'short_description_duplicates',
        'shortDescription reuses a sentence from the description. It must be a different angle.',
      );
      break;
    }
  }

  // ---------- primary keyword: exactly once per field ----------
  for (const [field, text] of [
    ['name', name],
    ['description', descText],
    ['metaTitle', metaTitle],
    ['metaDescription', metaDesc],
  ] as Array<[string, string]>) {
    const n = countPhrase(text, kw);
    if (n !== 1) {
      push(
        'primary_keyword_count',
        `Primary keyword "${kw}" appears ${n}x in "${field}". It must appear exactly 1x there.`,
      );
    }
  }
  if (countPhrase(shortText, kw) > 1) {
    push('primary_keyword_short', `Primary keyword "${kw}" appears more than once in "shortDescription". Max 1x.`);
  }
  if (metaTitle && !metaTitle.toLowerCase().trim().startsWith(kw.toLowerCase().trim())) {
    push('meta_title_keyword_first', `"metaTitle" must start with the primary keyword "${kw}".`);
  }

  // ---------- secondary keywords: placement is decided by the level ----------
  // A flat "description only" rule would waste the whole model: a MOTIF keyword belongs in
  // the alt texts (that is where the image is described), while a COMMERCIAL one there
  // would just be stuffing. So each level is checked against its own placement contract.
  const tagsText = (copy.tags || []).join(' \n ');
  const altsText = (copy.imagesAlt || []).join(' \n ');
  const modelWarnings = (copy.warnings || []).join(' ').toLowerCase();

  for (const k of ctx.secondaryKeywords) {
    const rule = PLACEMENT[k.tier];
    const inDesc = countPhrase(descText, k.text);
    const inTags = countPhrase(tagsText, k.text);
    const inAlts = countPhrase(altsText, k.text);

    if (inDesc > rule.maxInDescription) {
      push(
        'secondary_keyword_repeat',
        `[${k.tier}] "${k.text}" appears ${inDesc}x in the description; max ${rule.maxInDescription}x. Remove the extra use or drop the keyword.`,
      );
    }

    // The meta fields and the short description belong to the primary keyword. Letting a
    // secondary in is where two product pages start competing for the same query.
    if (countPhrase(metaTitle, k.text) + countPhrase(metaDesc, k.text) + countPhrase(shortText, k.text) > 0) {
      push(
        'secondary_keyword_in_reserved_field',
        `[${k.tier}] "${k.text}" was found in metaTitle / metaDescription / shortDescription. Those fields belong to the primary keyword only.`,
      );
    }

    if (!rule.allowInTags && inTags > 0) {
      push(
        'secondary_keyword_wrong_field',
        `[${k.tier}] "${k.text}" is a commercial query, not a topical signal — it must not be used as a tag.`,
      );
    }
    if (!rule.allowInAlts && inAlts > 0) {
      push(
        'secondary_keyword_wrong_field',
        `[${k.tier}] "${k.text}" must not be pushed into the alt texts. Alt texts describe the image, not the search query.`,
      );
    }

    // A motif is the thing in the picture. If it is never used to describe the picture, it
    // is doing no work at all — so it must either land somewhere, or be consciously
    // dropped with a reason (the image genuinely does not show it).
    if (rule.requirePlacement && inDesc + inTags + inAlts === 0) {
      const dropped = modelWarnings.includes(k.text.toLowerCase());
      if (!dropped) {
        push(
          'motif_keyword_unused',
          `[MOTIF] "${k.text}" is the object shown on the print, so it must appear in at least one alt text or tag. If the image does not actually show it, drop it and say so in "warnings".`,
        );
      }
    }
  }

  // ---------- cannibalization ----------
  for (const k of ctx.reservedKeywords) {
    const n =
      countPhrase(descText, k) + countPhrase(metaDesc, k) + countPhrase(metaTitle, k) + countPhrase(name, k);
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
    push('meta_description_too_long', `metaDescription is ${metaDesc.length} chars, max ${META_DESCRIPTION_MAX}.`);
  }
  if (metaDesc.length < 70) {
    push('meta_description_too_short', `metaDescription is only ${metaDesc.length} chars.`);
  }

  // ---------- brand: banned words and cliché openers ----------
  const haystack = [name, descText, shortText, metaTitle, metaDesc].join(' ').toLowerCase();
  for (const w of ctx.bannedWords) {
    const t = w.trim().toLowerCase();
    if (t && haystack.includes(t)) {
      push('banned_word', `"${w}" is on the brand's banned list and must never appear in copy.`);
    }
  }
  const opener = descText.toLowerCase();
  for (const c of CLICHE_OPENERS) {
    if (opener.startsWith(c)) {
      push('cliche_opener', `The description opens with the banned cliché "${c}…". Rewrite the opening.`);
      break;
    }
  }

  // ---------- slug ----------
  const slug = copy.slug || '';
  if (!/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(slug)) {
    push('slug_format', `slug "${slug}" must be lowercase words separated by single hyphens, no diacritics.`);
  } else {
    const parts = slug.split('-').length;
    if (parts < 3 || parts > 8) {
      push('slug_length', `slug has ${parts} words; aim for 4–8.`);
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
  const normalizedAlts = alts.map((a) => a.trim().toLowerCase());
  if (new Set(normalizedAlts).size !== normalizedAlts.length) {
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
