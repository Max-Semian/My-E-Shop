# Print Copy Generator — operator's guide

`https://generation.cretho.com`

A tool for writing product copy for print T-shirts under control. It reads the print, writes
the copy in the brand's voice, and then **refuses to save copy that breaks the rules** —
which is the part that matters. An LLM will happily stuff keywords and invent motifs; this
one is not allowed to.

---

## 1. The idea in one paragraph

Three things go in, in this order, and the order is the whole design:

1. **The brand** decides *how it sounds*.
2. **The print** decides *what it is about*.
3. **The keywords** are constraints laid on top — they never drive the sentence.

Put SEO first and you get copy that reads like SEO. So the brand foundation is the model's
system instruction — it comes before the image and before the keywords, every single time.

---

## 2. The three pages

| Page | What it is for |
|---|---|
| **/** (Positions) | The catalogue. Every position, its status, its keywords. Click one to open it. |
| **/brand** | The brand foundation. Concept, archetype, tone, banned words. |
| **/keywords** | The keyword list. Each secondary keyword and the **level** it serves. |

---

## 3. Set the brand up first — everything stands on it

Open **/brand**. It is already filled in from the brand concept document, but read it before
you generate anything, because it is the ground the copy stands on:

- **Concept** — the core idea. Decides what the copy is *about*.
- **Archetype** — decides how it *sounds*. Currently *The Magician + The Creator*.
- **Tone of voice**, **audience**, **what we promise**, **vocabulary**.
- **Banned words** — never appear in copy. These are enforced **in code**: copy containing
  one is rejected and regenerated, not merely discouraged.

Changing the brand changes every **future** generation. Copy already written is left alone.

---

## 4. The workflow, per position

### Step 1 — Create the position, or open a seeded one

The Witchcore category is already seeded: 14 positions (WC-01…WC-14), each with its title,
primary keyword, H1 and thematic lane.

Fields that matter before you generate:

- **Title** — the print name. This is a **source**, not a label. The image says what is
  *drawn*; the title says what it *means*. "Poison Garden" is doing real work here.
- **Concept category** — the brand lane (Witchcore). Decides meaning and voice. It is not a
  search term and is not meant to be one.
- **Thematic lane** (`cluster`) — Dark Botanical, Occult, Gothic… Not a page, owns no
  keyword. Its only job is **separation**: it tells the model which siblings it can actually
  collide with, instead of weighing all thirteen equally. **Set it before generating.**
- **Primary keyword** — the query this position owns. Unique across the whole catalogue; the
  database will not let two positions share one.
- **Product facts** — materials, fit, sizes, colours, price. Supplied so the model *states*
  them instead of inventing them. Leave a field blank and it stays silent about it — it will
  not guess.

### Step 2 — Import the print image

Everything visual is read off this image. Without it the tool will not build keywords, and
will not generate copy. That refusal is deliberate.

### Step 3 — Build keywords from the print

Press **“Build keywords from the print”**. It reads three sources together — the **image**,
the **title** and the **primary keyword** — and proposes a secondary set on three levels:

| Level | Job | Where it is allowed |
|---|---|---|
| **COMMERCIAL** | A query a buyer really types. Own search intent. | the description, once |
| **SEMANTIC** | Topical context — tells Google and an AI what world this belongs to. | the description or the tags |
| **MOTIF** | The concrete object drawn on the print. | **must** land in an alt text or a tag |

**No secondary keyword of any level may enter the meta title, meta description or short
description.** Those belong to the primary keyword. Sharing them is how two pages start
competing for one query.

You also get:

- **What this product is** — the model's one-sentence read of the product as an *entity*.
  This is what AI retrieval and Google's product understanding actually consume. If this
  sentence is wrong, the keywords under it will be wrong too. Read it first.
- **Visible in the image** — every object it can actually see. Sanity-check this against the
  artwork. It is the ground truth for the motif level.
- **Considered and thrown away** — candidates it rejected, with reasons.

Suggestions marked **Blocked** cannot be added. A suggestion is blocked when it would
cannibalize another position's primary, restate this page's own primary, or repeat a keyword
already used by a neighbouring position. That check runs **in code**, not on the model's
word.

Press **Add** on what is worth keeping — it lands in the Secondary keywords box and is
registered in the keyword list **with its level**. Then press **Save**.

> The level is not a label. It is what the code uses to decide where the keyword may be
> placed. A motif keyword registered as commercial will never be allowed into the alt texts
> where it belongs.

### Step 4 — Generate

Press **“Generate with Gemini Flash”**. Out comes:

- **name**, **slug**
- **description** — exactly **2 sentences**. This brand does not want long copy.
- **short description** — one line, a hook, and it may not reuse a sentence from the
  description.
- **meta title** (≤ 70 chars, primary keyword first) and **meta description** (≤ 165 chars)
- **3–5 tags**
- **5 alt texts** and **5 filenames** — front, back, detail, worn, flat lay

### Step 5 — Check, then approve

**View in Russian** shows a faithful translation so you can check the meaning. It is a
reading aid only: it is never saved and never exported. English stays the source of truth.

**Copy WooCommerce JSON** puts a ready REST payload on the clipboard (Rank Math fields by
default). It **refuses to export a DRAFT** — see below.

---

## 5. What happens when it breaks a rule

This is the part worth understanding, because it is what makes the tool trustworthy.

Every rule is re-checked **in code** after the model answers (`src/lib/validate.ts`). The
model's own claim of compliance is ignored. If a rule is broken:

1. The exact violated rule is handed back to the model, quoted, and it rewrites from scratch.
2. Up to **three** attempts.
3. If it still breaks a rule, the copy is saved as **DRAFT**, not `GENERATED`, with each
   violation listed as `RULE BROKEN [rule]: detail`. It will not export.

A `DRAFT` with warnings is the tool telling you something is genuinely wrong — usually the
inputs, not the model. The two most common causes:

- **A keyword that cannot be placed naturally.** The model is explicitly *allowed* to drop
  it and say so in the warnings. That is correct behaviour, not failure. If a keyword keeps
  getting dropped, the keyword is the problem.
- **A motif keyword that is not in the picture.** The image always outranks the keyword.

Checks enforced in code, in brief: the description is exactly two sentences and plain prose;
the primary keyword appears exactly once in each of name / description / meta title (first
words) / meta description; every secondary keyword respects its level's placement; no
secondary in a meta field; nothing cannibalizes another position; meta lengths; no banned
words; no cliché openers ("Discover", "Elevate", "Step into"…); slug format; 3–5 tags; 5
distinct alt texts.

There is deliberately **no keyword-density check**. With a two-sentence description a single
three-word keyword is already ~7% — the metric is meaningless at this length and would
reject every valid answer. The hard per-field counts do the work instead.

---

## 6. The keyword list

**/keywords** holds every keyword and its level. Two things to know:

- Re-adding an existing keyword **updates** its level. That is how you correct a mistiered
  one.
- A keyword already taken as a **primary** by some position is shown in red with the owner's
  name. Do not target it again — that is cannibalization, and the database will refuse it
  anyway.

---

## 7. Statuses

| Status | Meaning |
|---|---|
| **DRAFT** | Not generated yet, or generated and still breaking a rule. Will not export. |
| **GENERATED** | Passed every check. Ready to review. |
| **APPROVED** | You have read it and signed it off. |

---

## 8. Operations

Sign in with the password stored as `APP_PASSWORD` in Railway Variables. Change it there.

Re-seed the Witchcore keyword architecture (safe to re-run; it never creates a duplicate
position, since the primary keyword is the natural key):

```bash
railway ssh --service beautiful-courage "npm run seed:witchcore"
```

Environment: `GEMINI_API_KEY`, `GEMINI_MODEL`, `DATABASE_URL`, `APP_PASSWORD`,
`SESSION_SECRET` — all in Railway Variables, none in git.

Print images are stored in Postgres, not on a disk volume. They survive redeploys and are
covered by the database backups. See the comment on `Position.imageData` for why a volume
would be a step backwards.

---

## 9. The short version

1. Read **/brand** once.
2. Open a position. Set **title**, **thematic lane**, **product facts**.
3. **Import the print.**
4. **Build keywords from the print** → add the ones that are not blocked → **Save**.
5. **Generate**.
6. If it comes back `DRAFT` with warnings — fix the **inputs**, not the text.
7. **View in Russian** to check the meaning, then **Copy WooCommerce JSON**.

Related reading: [keyword-architecture.md](keyword-architecture.md) — why the levels exist
and how cannibalization is prevented.
