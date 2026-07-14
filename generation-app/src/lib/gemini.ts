import { GoogleGenerativeAI, SchemaType } from '@google/generative-ai';
import {
  buildSystemInstruction,
  buildUserPrompt,
  type GenerationInput,
} from './prompt';
import { validateCopy, type GeneratedCopy, type Violation } from './validate';

const MODEL = process.env.GEMINI_MODEL || 'gemini-2.5-flash';
/** How many times we hand the violations back and ask for a rewrite. */
const MAX_ATTEMPTS = 3;

function client() {
  const key = process.env.GEMINI_API_KEY;
  if (!key) throw new Error('GEMINI_API_KEY is not set');
  return new GoogleGenerativeAI(key);
}

const responseSchema = {
  type: SchemaType.OBJECT,
  properties: {
    description: { type: SchemaType.STRING },
    seoDescription: { type: SchemaType.STRING },
    keywordsUsed: {
      type: SchemaType.OBJECT,
      properties: {
        primary: { type: SchemaType.STRING },
        secondary: { type: SchemaType.ARRAY, items: { type: SchemaType.STRING } },
      },
    },
    warnings: { type: SchemaType.ARRAY, items: { type: SchemaType.STRING } },
  },
  required: ['description', 'seoDescription', 'keywordsUsed', 'warnings'],
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
): Promise<GenerateResult> {
  const model = client().getGenerativeModel({
    model: MODEL,
    systemInstruction: buildSystemInstruction(),
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
  };

  const imagePart = {
    inlineData: { data: image.data.toString('base64'), mimeType: image.mimeType },
  };

  let lastCopy: GeneratedCopy = { description: '', seoDescription: '' };
  let violations: Violation[] = [];

  for (let attempt = 1; attempt <= MAX_ATTEMPTS; attempt++) {
    // On a retry, show the model exactly which counts it broke. Generic "try again"
    // prompts do not fix stuffing — naming the violated rule does.
    const correction =
      violations.length > 0
        ? `\n\nYOUR PREVIOUS ANSWER WAS REJECTED. It broke these hard rules:\n` +
          violations.map((v) => `  - [${v.rule}] ${v.detail}`).join('\n') +
          `\n\nRewrite from scratch. Obey every count exactly. If a keyword cannot be` +
          ` placed naturally, drop it and say so in "warnings" — do not force it in.`
        : '';

    const result = await model.generateContent([
      { text: buildUserPrompt(input) + correction },
      imagePart,
    ]);

    const raw = result.response.text();
    try {
      lastCopy = JSON.parse(raw) as GeneratedCopy;
    } catch {
      violations = [{ rule: 'invalid_json', detail: 'Model did not return valid JSON.' }];
      continue;
    }

    violations = validateCopy(lastCopy, ctx);
    if (violations.length === 0) {
      return { copy: lastCopy, violations: [], attempts: attempt };
    }
  }

  // Still dirty after retries: hand it back flagged rather than silently saving spam.
  return { copy: lastCopy, violations, attempts: MAX_ATTEMPTS };
}
