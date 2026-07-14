-- CreateTable
CREATE TABLE "BrandProfile" (
    "id" TEXT NOT NULL DEFAULT 'default',
    "updatedAt" TIMESTAMP(3) NOT NULL,
    "brandName" TEXT NOT NULL DEFAULT 'Cretho',
    "concept" TEXT NOT NULL DEFAULT '',
    "archetype" TEXT NOT NULL DEFAULT '',
    "archetypeNotes" TEXT NOT NULL DEFAULT '',
    "audience" TEXT NOT NULL DEFAULT '',
    "toneOfVoice" TEXT NOT NULL DEFAULT '',
    "valueProps" TEXT NOT NULL DEFAULT '',
    "vocabulary" TEXT NOT NULL DEFAULT '',
    "bannedWords" TEXT[] DEFAULT ARRAY[]::TEXT[],

    CONSTRAINT "BrandProfile_pkey" PRIMARY KEY ("id")
);

