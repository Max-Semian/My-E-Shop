-- Secondary keywords are not a flat list. Each one serves one of three levels, and the
-- level decides where the keyword is allowed to be placed. See src/lib/keywords.ts.
CREATE TYPE "KeywordTier" AS ENUM ('COMMERCIAL', 'SEMANTIC', 'MOTIF');

-- COMMERCIAL is the safest default: it is the most restrictive placement (description
-- only), so pre-existing keywords keep behaving exactly as they did before.
ALTER TABLE "Keyword" ADD COLUMN "tier" "KeywordTier" NOT NULL DEFAULT 'COMMERCIAL';
