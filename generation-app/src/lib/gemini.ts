import { GoogleGenerativeAI, SchemaType } from '@google/generative-ai';
import type { BrandProfile } from '@prisma/client';
import { buildSystemInstruction, buildUserPrompt, type GenerationInput } from './prompt';
import { validateCopy, type GeneratedCopy, type Violation } from './validate';

const MODEL = process.env.GEMINI_MODEL || 'gemini-2.5-flash';
/** How many times we hand the violations back and demand a rewrite. */
const MAX_ATTEMPTS = 3;

function client() {
  const key = process.env.GEMINI_API_KEY;
  if (!key) throw new Error('GEMINI_API_KEY is not set');
  return new GoogleGenerativeAI(key);
}

/** Structured output — the model cannot return anything but this shape. */
const responseSchema = {
  type: SchemaType.OBJECT,
  properties: {
    name: { type: SchemaType.STRING },
    slug: { type: SchemaType.STRING },
    description: { type: SchemaType.STRING },
    shortDescription: { type: SchemaType.STRING },
    metaTitle: { type: SchemaType.STRING },
    metaDescription: { type: SchemaType.STRING },
    tags: { type: SchemaType.ARRAY, items: { type: SchemaType.STRING } },
    imagesAlt: { type: SchemaType.ARRAY, items: { type: SchemaType.STRING } },
    imageFilenames: { type: SchemaType.ARRAY, items: { type: SchemaType.STRING } },
    keywordsUsed: {
      type: SchemaType.OBJECT,
      properties: {
        primary: { type: SchemaType.STRING },
        secondary: { type: SchemaType.ARRAY, items: { type: SchemaType.STRING } },
      },
    },
    warnings: { type: SchemaType.ARRAY, items: { type: SchemaType.STRING } },
  },
  required: [
    'name',
    'slug',
    'description',
    'shortDescription',
    'metaTitle',
    'metaDescription',
    'tags',
    'imagesAlt',
    'imageFilenames',
    'keywordsUsed',
    'warnings',
  ],
} as const;

export interface GenerateResult {
  copy: GeneratedCopy;
  /** Rules still broken after the last attempt — empty means the copy is clean. */
  violations: Violation[];
  attempts: number;
}

export async function generateCopy(
  input: GenerationInput,
  image: { data: Buffer; mimeType: string },
  brand: BrandProfile,
): Promise<GenerateResult> {
  const model = client().getGenerativeModel({
    model: MODEL,
    // The brand foundation is the system instruction: it is the ground the copy stands on,
    // not an afterthought appended to the request.
    systemInstruction: buildSystemInstruction(brand),
    generationConfig: {
      // Low temperature: this is a rules-following task, not a creative free-for-all.
      temperature: 0.4,
      responseMimeType: 'application/json',
      responseSchema: responseSchema as any,
    },
  });

  const ctx = {
    primaryKeyword: input.primaryKeyword,
    secondaryKeywords: input.secondaryKeywords,
    reservedKeywords: input.reservedKeywords,
    bannedWords: brand.bannedWords,
  };

  const imagePart = {
    inlineData: { data: image.data.toString('base64'), mimeType: image.mimeType },
  };

  let lastCopy = {} as GeneratedCopy;
  let violations: Violation[] = [];

  for (let attempt = 1; attempt <= MAX_ATTEMPTS; attempt++) {
    // On a retry, name the exact counts it broke. A generic "try again" does not fix
    // stuffing — quoting the violated rule does.
    const correction =
      violations.length > 0
        ? `\n\nYOUR PREVIOUS ANSWER WAS REJECTED. It broke these hard rules:\n` +
          violations.map((x) => `  - [${x.rule}] ${x.detail}`).join('\n') +
          `\n\nRewrite from scratch. Obey every count and length exactly. If a keyword` +
          ` cannot be placed naturally, drop it and say so in "warnings" — never force it in.`
        : '';

    const result = await model.generateContent([
      { text: buildUserPrompt(input) + correction },
      imagePart,
    ]);

    try {
      lastCopy = JSON.parse(result.response.text()) as GeneratedCopy;
    } catch {
      violations = [{ rule: 'invalid_json', detail: 'Model did not return valid JSON.' }];
      continue;
    }

    violations = validateCopy(lastCopy, ctx);
    if (violations.length === 0) {
      return { copy: lastCopy, violations: [], attempts: attempt };
    }
  }

  // Still dirty after the retries: hand it back flagged rather than silently saving spam.
  return { copy: lastCopy, violations, attempts: MAX_ATTEMPTS };
}
