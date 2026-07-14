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

## 3. The hierarchy — three things that must never be confused

**Layer 0 — the concept category.** The brand's own taxonomy; *Witchcore* is one of the four.
It decides what a print **means** and how it sounds. It carries no search demand by design,
and that is not a defect: nobody types "tender thoughts t-shirt". Forcing a head term onto a
concept category would kill the concept for a query it would not win anyway.

**Layer 1 — the listing pages.** The category page is the hub and owns `witchcore t-shirt`.
Beneath it sit four listing pages, each owning its own head term:

| Listing page | Head term | Synonyms (same page — never a neighbouring one) |
|---|---|---|
| **Witchcore** (hub) | witchcore t-shirt | witch graphic tee, witch aesthetic shirt |
| Gothic | gothic t-shirt | gothic graphic tee |
| Occult | occult t-shirt | occult graphic tee |
| Dark Botanical | dark botanical t-shirt | gothic floral t-shirt, occult botanical, botanical gothic |
| Dark Romantic | dark romantic clothing | — |

`/collection/…` is a page template, not a taxonomy, so these pages cost nothing to add and
can be titled freely. Head terms carry **browsing** intent — the searcher wants to choose
from a range — so they belong on a page that shows a range. All 12 supplied keywords are head
terms or synonyms of one, so all 12 live here and **none is left on a product**.

**Layer 2 — the products.** Each supports exactly one listing page (its `cluster`) and owns a
long-tail primary anchored on the motif actually printed on it.

## 4. The lane is fixed before generation, not during it

A position carries `category` (the brand lane) and `cluster` (the listing page it supports).
Both are set **before** the model runs, and both are fed to it. Together they decide:

- which head terms are **off-limits** — the ones its own listing page owns;
- which siblings it must stay **distinguishable** from — the ones in the same lane;
- the **voice** — which of the four concepts this print speaks in.

Without them the model re-guesses its lane on every run, and sooner or later guesses its way
into a neighbour's. `Collection DNA` — *aesthetic + product type + visual motif* — then
divides cleanly: the listing page already carries the aesthetic and the product type, so the
product's job is the **motif**, the part nothing else in the catalogue can claim.

## 5. The 14 positions

All fourteen sit in the **Witchcore** concept category. `title` is the print name — a
generation *source*, not a label. `cluster` is the listing page each one supports, and it is
fixed **before** generation. The primary is blank on purpose: it is a long-tail query anchored
on the motif, so it can only be read off the artwork.

| ID | Print | Cluster | Primary | Provisional primary it replaced |
|---|---|---|---|---|
| WC-01 | Read My Aura | Witchcore | *from the print* | witch graphic tee |
| WC-02 | We Gather Under No God | Witchcore | *from the print* | witchcore t-shirt |
| WC-03 | Fortune Teller's Hands | Occult | *from the print* | occult graphic tee |
| WC-04 | Beauty with Bite | Dark Botanical | *from the print* | gothic floral t-shirt |
| WC-05 | Hex and Bloom | Gothic | *from the print* | gothic graphic tee |
| WC-06 | Hex and Bloom | Dark Botanical | *from the print* | dark botanical t-shirt |
| WC-07 | Hex and Bloom | Dark Botanical | *from the print* | occult botanical |
| WC-08 | Witch | Witchcore | *from the print* | witch aesthetic shirt |
| WC-09 | Witch | Dark Botanical | *from the print* | botanical gothic |
| WC-10 | Blessed | Dark Romantic | *from the print* | dark romantic tee |
| WC-11 | Poison Garden | Dark Botanical ⚠ | *from the print* | dark aesthetic shirt |
| WC-12 | Poison Garden | Gothic | *from the print* | gothic t-shirt |
| WC-13 | Poison Garden | Occult | *from the print* | occult t-shirt |
| WC-14 | Poison Garden | Occult ⚠ | *from the print* | dark symbolic tee |

Each cluster is taken from the provisional primary the original table gave that print — that
phrase is the table's own statement of what the product is about, so it is evidence rather
than invention. ⚠ marks the two whose provisional primary belonged to no cluster at all
("dark aesthetic shirt", "dark symbolic tee"); their lane is inferred from the Poison Garden
capsule and must be confirmed against the artwork.

Neither the primaries nor the secondary sets are seeded. Both come from the prints.

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
