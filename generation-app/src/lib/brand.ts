import { prisma } from './db';

/**
 * The brand foundation. Everything the generator writes is downstream of this:
 * the archetype decides the voice, the concept decides what the copy is even about.
 * It lives in the DB (one row) so it can be edited in the UI without a deploy.
 *
 * Seeded from the Cretho brand document. In English, because the storefront is English.
 */
export const DEFAULT_BRAND = {
  brandName: 'Cretho',
  concept: [
    'Trend-Led Identity Wear. We make designer T-shirts that fuse runway aesthetics with',
    'personal identity — clothing for people who want to be on trend, express themselves and',
    'belong to a niche community without paying luxury prices. A print is never just a',
    'graphic: it is a small manifesto. Fashion plus meaning, at an honest price.',
  ].join(' '),
  archetype: 'The Magician + The Creator (hybrid)',
  archetypeNotes: [
    'THE MAGICIAN turns the wearer\'s inner world into style. It speaks in symbolism, energy,',
    'signs and a witchy undercurrent — transformation, personal power, quiet ritual.',
    'Register: "Your aura, in cotton", "Fashioned for your energy", "Wear your sign. Feel your power."',
    '',
    'THE CREATOR helps the wearer author their own aesthetic. It speaks in runway references,',
    'visual storytelling, prints as art manifestos.',
    '',
    'Together: every drop is both a spell (the message) and an artefact (the design).',
    'It does NOT sound like: horoscope-app filler, generic "spiritual" platitudes, mall-brand hype,',
    'or a fortune teller. The esoteric note is a spark, never a costume.',
  ].join('\n'),
  audience: [
    'Ages 18–35, mostly in US cities. Predominantly women and gender-fluid people.',
    'They value individuality, style, mindfulness and self-expression. They follow fashion but',
    'are not chasing luxury. They live on Pinterest and Reels, and are drawn to astrology,',
    'symbolism and aesthetics. They buy when a piece feels like "this is about me".',
  ].join(' '),
  toneOfVoice: [
    'Inspiring, intuitive, visual, softly provocative.',
    'Talk like a friend who is equal parts witch and stylist: knowledgeable, stylish, with a',
    'light esoteric spark — and absolutely no pathos. Never preachy, never mystical cosplay.',
    'Confident, never salesy. No hype, no exclamation marks, no emoji.',
  ].join(' '),
  valueProps: [
    'Runway-inspired prints, released in small themed drops.',
    'Premium aesthetic at an accessible price ($27–35).',
    'Meaning, not just a graphic — mental health, personal power, symbolism, activism.',
    '100% cotton, unisex and women\'s fits.',
  ].join('\n'),
  vocabulary: [
    'Say "tee" or "T-shirt", not "shirt". Say "print", not "design" when referring to the artwork.',
    'Say "drop" or "collection", not "product line".',
    'Collection themes: Witchy core, Astrology signs, Mental health affirmations, AI & Techwear, Fashion activism.',
  ].join('\n'),
  bannedWords: [
    'Printful',
    'Printify',
    'cheap',
    'must-have',
    'game-changer',
    'unleash',
    'elevate your wardrobe',
  ],
};

export type BrandProfile = typeof DEFAULT_BRAND & { id: string };

/** One row, id="default". Created from DEFAULT_BRAND the first time it is requested. */
export async function getBrandProfile() {
  return prisma.brandProfile.upsert({
    where: { id: 'default' },
    update: {},
    create: { id: 'default', ...DEFAULT_BRAND },
  });
}
