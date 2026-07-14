import { NextResponse } from 'next/server';
import { GoogleGenerativeAI, SchemaType } from '@google/generative-ai';
import { prisma } from '@/lib/db';
import { isAuthenticated } from '@/lib/auth';

export const maxDuration = 60;

/**
 * Russian preview of the generated copy — for the editor to sanity-check the meaning.
 *
 * This is a READ-ONLY view. The translation is never written to the database and never
 * leaves for WooCommerce: the storefront is English, so English stays the source of truth.
 * Nothing here touches the Position row.
 */
export async function POST(_req: Request, { params }: { params: { id: string } }) {
  if (!isAuthenticated()) {
    return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
  }

  const p = await prisma.position.findUnique({
    where: { id: params.id },
    select: {
      description: true,
      shortDescription: true,
      metaTitle: true,
      seoDescription: true,
      tags: true,
      imagesAlt: true,
    },
  });
  if (!p) return NextResponse.json({ error: 'Not found' }, { status: 404 });

  const source = {
    description: p.description,
    shortDescription: p.shortDescription,
    metaTitle: p.metaTitle,
    metaDescription: p.seoDescription,
    tags: p.tags,
    imagesAlt: p.imagesAlt,
  };

  if (!source.description && !source.metaDescription) {
    return NextResponse.json({ error: 'Nothing to translate yet — generate the copy first.' }, { status: 400 });
  }

  const key = process.env.GEMINI_API_KEY;
  if (!key) return NextResponse.json({ error: 'GEMINI_API_KEY is not set' }, { status: 500 });

  const model = new GoogleGenerativeAI(key).getGenerativeModel({
    model: process.env.GEMINI_MODEL || 'gemini-2.5-flash',
    systemInstruction:
      'Translate the given e-commerce copy from English into Russian. This is a reading aid ' +
      'for an editor, so translate faithfully: keep the meaning, tone and length, do not ' +
      'improve, shorten or "fix" the text, and do not add anything. Keep brand names as they ' +
      'are. Return only the JSON object.',
    generationConfig: {
      temperature: 0.2,
      responseMimeType: 'application/json',
      responseSchema: {
        type: SchemaType.OBJECT,
        properties: {
          description: { type: SchemaType.STRING },
          shortDescription: { type: SchemaType.STRING },
          metaTitle: { type: SchemaType.STRING },
          metaDescription: { type: SchemaType.STRING },
          tags: { type: SchemaType.ARRAY, items: { type: SchemaType.STRING } },
          imagesAlt: { type: SchemaType.ARRAY, items: { type: SchemaType.STRING } },
        },
        required: ['description', 'shortDescription', 'metaTitle', 'metaDescription', 'tags', 'imagesAlt'],
      } as any,
    },
  });

  const result = await model.generateContent(JSON.stringify(source));
  try {
    return NextResponse.json(JSON.parse(result.response.text()));
  } catch {
    return NextResponse.json({ error: 'Translation failed' }, { status: 502 });
  }
}
