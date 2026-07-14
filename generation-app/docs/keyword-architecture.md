# Keyword architecture — Witch Core

## 1. The model

A secondary keyword is not "one more keyword to sprinkle in". Each serves one of three
levels, and **the level decides where the keyword is allowed to sit**. That placement rule
is enforced in `src/lib/validate.ts`, not merely requested in the prompt.

| Level | Job | Allowed placement | Forbidden |
|---|---|---|---|
| **COMMERCIAL** | A query a buyer really types. Own search intent. | `description`, once | tags, alts, all meta fields |
| **SEMANTIC** | Topical context — tells Google and an AI what world this belongs to. | `description` (once) and/or `tags` | all meta fields |
| **MOTIF** | The concrete object visible on the print. The entity layer. | `tags`, `imagesAlt` — **must land in at least one** — and optionally once in `description` | all meta fields |

**No secondary keyword of any level may enter `metaTitle`, `metaDescription` or
`shortDescription`.** Those belong to the primary keyword alone. Sharing them is precisely
how two product pages begin competing for one query.

The set is judged as a set, not keyword by keyword. A low-volume motif that names exactly
what is on the shirt is worth more than a fat head term that says nothing specific. The
test is: **does this set draw a sharp, unambiguous picture of this one product?**

## 2. Where the keywords come from

Three sources, and all three are required:

1. **The print image** — the only honest source of the MOTIF level.
2. **The print title** — the concept behind the artwork. The image says what is *drawn*;
   the title says what it *means*.
3. **The primary keyword** — the cluster this page owns. Every secondary must reinforce it.

Motif keywords are read off the artwork by `POST /api/positions/[id]/suggest-keywords`,
never guessed from the name. "Poison Garden" does not tell you whether the print shows
belladonna or foxglove — and a motif keyword invented from a title ends up in an alt text
describing something that is not in the picture. That is a lie to the customer and to the
crawler, and it is the one failure mode the code refuses to allow: the endpoint returns
`400` if the image has not been imported yet.

## 3. Collection DNA

    aesthetic + product type + visual motif
    (witchcore  +  graphic tee  +  botanical gothic)

The primary keyword usually carries **aesthetic + product type**. The secondary set must
therefore supply the part the primary does not: the **visual motif** — plus the commercial
and semantic reinforcement around it.

## 4. Anti-cannibalization — enforced in three places

1. **Database** — `Position.primaryKeyword` is `@unique`. Two prints cannot target one query.
2. **Suggestion time** — a suggested keyword that contains, or is contained by, another
   position's primary is returned **blocked**, with the colliding product named. Same for a
   keyword already used as a secondary elsewhere (mass repeats blur both pages), and for a
   restatement of this page's own primary.
3. **Generation time** — every other position's primary is passed to the model as a reserved
   list, and `validate.ts` re-checks the finished copy for each one. A violation rejects the
   copy and regenerates it (×3), then leaves it as `DRAFT` with the rule named.

## 5. The 14 positions

`title` = print name (a generation source, not a label). `H1` = unified SEO title.

| ID | Print | Primary keyword | H1 | Capsule |
|---|---|---|---|---|
| WC-01 | Read My Aura | witch graphic tee | Witch Graphic T-Shirt — Read My Aura | stand-alone |
| WC-02 | We Gather Under No God | witchcore t-shirt | Witchcore T-Shirt — We Gather Under No God | stand-alone |
| WC-03 | Fortune Teller's Hands | occult graphic tee | Occult Graphic T-Shirt — Fortune Teller's Hands | stand-alone |
| WC-04 | Beauty with Bite | gothic floral t-shirt | Gothic Floral T-Shirt — Beauty with Bite | stand-alone |
| WC-05 | Hex and Bloom | gothic graphic tee | Gothic Graphic T-Shirt — Hex and Bloom | Hex and Bloom |
| WC-06 | Hex and Bloom | dark botanical t-shirt | Dark Botanical T-Shirt — Hex and Bloom | Hex and Bloom |
| WC-07 | Hex and Bloom | occult botanical | Occult Botanical T-Shirt — Hex and Bloom | Hex and Bloom |
| WC-08 | Witch | witch aesthetic shirt | Witch Aesthetic T-Shirt — Witch | Witch |
| WC-09 | Witch | botanical gothic | Botanical Gothic T-Shirt — Witch | Witch |
| WC-10 | Blessed | dark romantic tee | Dark Romantic T-Shirt — Blessed | stand-alone |
| WC-11 | Poison Garden | dark aesthetic shirt | Dark Aesthetic T-Shirt — Poison Garden | Poison Garden |
| WC-12 | Poison Garden | gothic t-shirt | Gothic T-Shirt — Poison Garden | Poison Garden |
| WC-13 | Poison Garden | occult t-shirt | Occult T-Shirt — Poison Garden | Poison Garden |
| WC-14 | Poison Garden | dark symbolic tee | Dark Symbolic T-Shirt — Poison Garden | Poison Garden |

Secondary sets are built per position from the imported artwork. They are deliberately not
seeded here.

## 6. Audit of the primary layer

The requested checks were run against the primary layer as supplied. Four problems are real
and no secondary set can paper over them, because they sit **above** the secondary layer.

### 6.1 Two "primaries" are not commercial keywords at all
`occult botanical` (WC-07) and `botanical gothic` (WC-09) have **no product head noun**.
Nobody types them to buy a t-shirt; they are modifiers. As primaries they carry weak intent
and they sit inside the same query family as `dark botanical t-shirt` (WC-06) and
`gothic floral t-shirt` (WC-04).

> **Recommendation:** demote both to **SEMANTIC** secondaries — which is exactly what they
> are — and give WC-07 and WC-09 real commercial primaries anchored on the motif their prints
> actually show (readable once the artwork is imported).

### 6.2 A four-way botanical collision
WC-04 / WC-06 / WC-07 / WC-09 all target "dark + plant + shirt". Google will treat
`dark botanical t-shirt`, `occult botanical` and `botanical gothic` as near-synonyms
regardless of what the DB thinks. This is the collection's biggest cannibalization risk and
it exists **in the primaries**, not in the secondaries.

> **Recommendation:** separate them by the motif that is actually drawn (different plant, or
> plant vs. plant+symbol), and let the motif carry into each primary. Four botanical prints
> can coexist — four botanical *queries* cannot.

### 6.3 Two primaries have no real search demand
`dark aesthetic shirt` (WC-11) and `dark symbolic tee` (WC-14) are not in the keyword list;
they were invented to fill the table. `dark symbolic tee` in particular is a phrase
essentially nobody searches.

> **Recommendation:** replace with motif-anchored noun phrases from the actual prints. A
> specific, low-volume query that matches the shirt beats a fabricated phrase with no volume
> at all.

### 6.4 The head terms sit on capsule variants
`gothic t-shirt` (WC-12) and `occult t-shirt` (WC-13) are the two biggest head terms in the
collection — and both are parked on the 2nd and 3rd variant inside the Poison Garden capsule.
The strongest queries are pointing at the least representative pages.

> **Recommendation:** head terms belong on the collection/category page, or on the single
> strongest print. Give WC-12 and WC-13 long-tail primaries of their own.

### 6.5 Duplicate titles are a content-level collision
WC-05/06/07 share the title "Hex and Bloom"; WC-11–14 share "Poison Garden"; WC-08/09 share
"Witch". Since the title is a **generation source**, three positions with the same title and
near-identical primaries will produce near-identical copy — cannibalization at the content
level even though the primaries differ in the database.

> **Recommendation:** give every print its real, distinct name before generating. The motif
> keywords read off each image will then pull the copy apart on their own.

### 6.6 A free keyword worth using
`dark romantic clothing` is in the keyword list but was never claimed (WC-10 took
`dark romantic tee`). It is the natural **COMMERCIAL** secondary for WC-10 — same page, same
cluster, so it is reinforcement, not cannibalization.

## 7. Compliance with the required checks

| Requirement | How it is met |
|---|---|
| No artificial phrases | Suggester must reject them, and lists what it threw away with reasons. |
| Commercial noun phrases strengthened | The COMMERCIAL level is defined as a noun phrase with buying intent; "witchy vibes" is rejected, "tarot t-shirt" is not. |
| Concrete visual entities added | The MOTIF level, read off the image; the endpoint refuses to run without the artwork. |
| Own search intent **or** better product understanding | Stated as the acceptance test for every keyword; anything that does neither is noise. |
| Not every keyword must be high-volume | Stated explicitly; the set, not the individual keyword, is what must define the product. |
| Thematic proximity to the primary | The primary is a required input; every secondary must reinforce that one cluster. |
| Separation within the collection | Sibling products and their primaries are passed in, with the instruction to stay distinguishable. |
| No repeats that weaken the architecture | Reuse of a sibling's secondary is blocked in code; restating the page's own primary is blocked in code. |
| Classic SEO | Per-field placement rules; primary keyword owns the meta fields exclusively. |
| Semantic search | The SEMANTIC level supplies the topical world. |
| Google Shopping / product understanding | The MOTIF level lands in the alt texts and tags — the fields that describe the item. |
| AI retrieval / LLM recommendations | The set is required to define the product as an **entity**; the suggester returns that entity statement explicitly. |
