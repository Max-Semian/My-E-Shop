import { PrismaClient } from '@prisma/client';

// Reuse the client across hot reloads in dev, otherwise every reload opens a new pool.
const globalForPrisma = globalThis as unknown as { prisma?: PrismaClient };

export const prisma = globalForPrisma.prisma ?? new PrismaClient();

if (process.env.NODE_ENV !== 'production') globalForPrisma.prisma = prisma;

/**
 * Primary keywords already claimed by other positions.
 * Feeding these to the model (and re-checking in validate.ts) is what stops two
 * prints from being optimised for the same query.
 */
export async function reservedKeywordsExcept(positionId?: string): Promise<string[]> {
  const rows = await prisma.position.findMany({
    where: positionId ? { NOT: { id: positionId } } : undefined,
    select: { primaryKeyword: true },
  });
  return rows.map((r) => r.primaryKeyword).filter(Boolean);
}
