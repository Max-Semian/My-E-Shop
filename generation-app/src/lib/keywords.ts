/**
 * The three-level secondary keyword model.
 *
 * A secondary keyword is not "another keyword to sprinkle in". Each one does a different
 * job, and the job decides WHERE it is allowed to sit. Get the placement wrong and the
 * keyword either does nothing (a motif buried in a meta title tells no one what the print
 * shows) or actively hurts (a commercial term stuffed into five alt texts reads as spam).
 *
 *   COMMERCIAL  — a query a buyer really types. It has its own search intent and could,
 *                 in principle, rank on its own. Its home is the sentence a human reads:
 *                 the description. Once. Never in a meta field — the meta fields belong
 *                 to the primary keyword, and sharing them is how pages start competing.
 *
 *   SEMANTIC    — topical support. It carries no purchase intent; it tells a search engine
 *                 and an AI retrieval system what WORLD this product belongs to
 *                 ("witchcore aesthetic", "dark cottagecore"). It earns its place in the
 *                 description or in the tags — tags are a topical signal, not a keyword bin.
 *
 *   MOTIF       — the concrete object visible on the print: a plant, a hand, a sigil.
 *                 This is the entity layer. It is what makes the product legible to Google's
 *                 product understanding and to an LLM asked "find me a t-shirt with a moth
 *                 on it". Its home is where the image is described — the alt texts and the
 *                 tags. A motif keyword that appears nowhere near the image is dead weight.
 *
 * The tiers are enforced in validate.ts, not merely explained in the prompt.
 *
 * A note on where MOTIF keywords come from: they must be derived from the imported print
 * image itself (see /api/positions/[id]/suggest-keywords), never guessed from the print's
 * name. "Poison Garden" does not tell you whether the print shows belladonna or foxglove —
 * only the picture does, and a motif keyword that does not match the picture is a lie to
 * both the customer and the crawler.
 */

import { prisma } from './db';

export type Tier = 'COMMERCIAL' | 'SEMANTIC' | 'MOTIF';

export const TIERS: Tier[] = ['COMMERCIAL', 'SEMANTIC', 'MOTIF'];

export interface TieredKeyword {
  text: string;
  tier: Tier;
}

/** Where each tier is allowed to appear. This is the contract validate.ts enforces. */
export interface Placement {
  /** Human-readable job of this level, used in the prompt and in the UI. */
  job: string;
  /** Max occurrences inside the long description. */
  maxInDescription: number;
  /** May it appear in `tags`? */
  allowInTags: boolean;
  /** May it appear in `imagesAlt`? */
  allowInAlts: boolean;
  /**
   * Must it appear somewhere the image/product is described (description, tags or alts)?
   * Only MOTIF: a visual entity that shows up nowhere is not doing any work.
   */
  requirePlacement: boolean;
}

export const PLACEMENT: Record<Tier, Placement> = {
  COMMERCIAL: {
    job: 'A query a buyer really types. Own search intent.',
    maxInDescription: 1,
    allowInTags: false,
    allowInAlts: false,
    requirePlacement: false,
  },
  SEMANTIC: {
    job: 'Topical support — tells search engines and AI what world this belongs to.',
    maxInDescription: 1,
    allowInTags: true,
    allowInAlts: true,
    requirePlacement: false,
  },
  MOTIF: {
    job: 'The concrete object visible on the print. The entity layer.',
    maxInDescription: 1,
    allowInTags: true,
    allowInAlts: true,
    requirePlacement: true,
  },
};

/** No secondary keyword, of any tier, may enter these fields. They belong to the primary. */
export const FIELDS_CLOSED_TO_SECONDARY = ['metaTitle', 'metaDescription', 'shortDescription'] as const;

/**
 * Attach a tier to each of a position's secondary keywords by looking it up in the keyword
 * list. A keyword we have never seen defaults to COMMERCIAL — the most restrictive
 * placement — so an untagged keyword can never accidentally leak into alt texts.
 */
export async function resolveSecondaryTiers(texts: string[]): Promise<TieredKeyword[]> {
  const wanted = texts.map((t) => t.trim()).filter(Boolean);
  if (!wanted.length) return [];

  const rows = await prisma.keyword.findMany({ select: { text: true, tier: true } });
  const byText = new Map(rows.map((r) => [r.text.trim().toLowerCase(), r.tier as Tier]));

  return wanted.map((text) => ({ text, tier: byText.get(text.toLowerCase()) ?? 'COMMERCIAL' }));
}
