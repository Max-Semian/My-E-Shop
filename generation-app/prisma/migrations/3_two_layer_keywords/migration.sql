-- The two-layer keyword architecture.
--
-- Layer 1 (categories / collections) owns the head terms: "gothic t-shirt", "occult
-- t-shirt", "witchcore t-shirt". Those queries carry browsing intent — the searcher wants to
-- pick from a range — so they belong on a page that shows a range.
--
-- Layer 2 (products) owns long-tail primaries anchored on the motif actually printed on the
-- shirt. Cannibalization then disappears structurally: the queries are simply different.

-- A keyword owned by a listing page is off-limits to every product.
ALTER TABLE "Keyword" ADD COLUMN "reservedFor" TEXT;

-- A product's primary can only be derived once its artwork has been imported and read, so
-- "not derived yet" must be representable. Postgres permits many NULLs under a unique index,
-- so unassigned positions coexist without fighting over an empty string.
ALTER TABLE "Position" ALTER COLUMN "primaryKeyword" DROP NOT NULL;
