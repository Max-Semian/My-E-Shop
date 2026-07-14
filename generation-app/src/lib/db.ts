import { PrismaClient } from '@prisma/client';

// Reuse the client across hot reloads in dev, otherwise every reload opens a new pool.
const globalForPrisma = globalThis as unknown as { prisma?: PrismaClient };

export const prisma = globalForPrisma.prisma ?? new PrismaClient();

if (process.env.NODE_ENV !== 'production') globalForPrisma.prisma = prisma;

/**
 * Every query this position may NOT target. Two sources, and both matter:
 *
 *   1. Other positions' primary keywords — so two prints never compete with each other.
 *   2. Keywords owned by a listing page (`Keyword.reservedFor`) — the head terms. A product
 *      that reaches for "gothic t-shirt" is fighting its own category page for a query the
 *      category page is simply better at answering.
 *
 * Feeding this list to the model AND re-checking the finished copy against it in
 * validate.ts is what makes the rule real rather than aspirational.
 */
export async function reservedKeywordsExcept(positionId?: string): Promise<string[]> {
  const [positions, pageOwned] = await Promise.all([
    prisma.position.findMany({
      where: positionId ? { NOT: { id: positionId } } : undefined,
      select: { primaryKeyword: true },
    }),
    prisma.keyword.findMany({
      where: { NOT: { reservedFor: null } },
      select: { text: true },
    }),
  ]);

  const all = [...positions.map((p) => p.primaryKeyword), ...pageOwned.map((k) => k.text)];
  // De-duplicated: the same phrase can legitimately be reserved from both directions.
  return Array.from(new Set(all.filter((k): k is string => Boolean(k && k.trim()))));
}
