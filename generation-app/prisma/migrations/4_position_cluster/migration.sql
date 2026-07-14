-- The listing page a product supports inside its concept category ("Dark Botanical").
--
-- Fixed before generation, not inferred during it. It pins the trajectory: which head terms
-- are off-limits (the listing page owns them) and which siblings this print must stay
-- distinguishable from. Without it the model re-guesses its lane on every run.
ALTER TABLE "Position" ADD COLUMN "cluster" TEXT NOT NULL DEFAULT '';
