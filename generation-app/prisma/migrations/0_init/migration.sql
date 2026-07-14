-- CreateEnum
CREATE TYPE "PositionStatus" AS ENUM ('DRAFT', 'GENERATED', 'APPROVED');

-- CreateEnum
CREATE TYPE "KeywordType" AS ENUM ('PRIMARY', 'SECONDARY');

-- CreateTable
CREATE TABLE "Position" (
    "id" TEXT NOT NULL,
    "createdAt" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "updatedAt" TIMESTAMP(3) NOT NULL,
    "title" TEXT NOT NULL,
    "seoTitle" TEXT NOT NULL DEFAULT '',
    "imageData" BYTEA,
    "imageMime" TEXT,
    "primaryKeyword" TEXT NOT NULL,
    "secondaryKeywords" TEXT[] DEFAULT ARRAY[]::TEXT[],
    "description" TEXT NOT NULL DEFAULT '',
    "seoDescription" TEXT NOT NULL DEFAULT '',
    "keywordsUsed" JSONB,
    "warnings" TEXT[] DEFAULT ARRAY[]::TEXT[],
    "status" "PositionStatus" NOT NULL DEFAULT 'DRAFT',

    CONSTRAINT "Position_pkey" PRIMARY KEY ("id")
);

-- CreateTable
CREATE TABLE "Keyword" (
    "id" TEXT NOT NULL,
    "createdAt" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "text" TEXT NOT NULL,
    "type" "KeywordType" NOT NULL DEFAULT 'SECONDARY',
    "topic" TEXT,

    CONSTRAINT "Keyword_pkey" PRIMARY KEY ("id")
);

-- CreateIndex
CREATE UNIQUE INDEX "Position_primaryKeyword_key" ON "Position"("primaryKeyword");

-- CreateIndex
CREATE INDEX "Position_status_idx" ON "Position"("status");

-- CreateIndex
CREATE UNIQUE INDEX "Keyword_text_key" ON "Keyword"("text");

