# Cretho — Print Copy Generator

Controlled generation of product descriptions and SEO meta descriptions for T-shirt prints,
from the print image + SEO inputs, using **Gemini Flash**.

Runs on `generation.cretho.com` as a separate Railway service.

## How keyword spam and cannibalization are actually prevented

Two layers, because a prompt alone does not hold:

1. **Prompt** (`src/lib/prompt.ts`) — every rule is a hard count, not a suggestion
   ("primary keyword: exactly once"). The model is explicitly allowed to *drop* a keyword
   it cannot place naturally and report it in `warnings`. Removing that escape hatch is
   what makes models force-fit keywords and produce spam.

2. **Code** (`src/lib/validate.ts`) — every count is re-derived from the generated text.
   The model is never trusted. If it stuffed a keyword, exceeded 2% density, ran the meta
   description past 155 chars, or touched a keyword reserved by another page, the answer is
   **rejected** and regenerated with the exact violated rules fed back (up to 3 attempts).
   Copy that still breaks a rule is stored as `DRAFT` with the violations attached — never
   silently published.

**Cannibalization** is enforced at the database level: `Position.primaryKeyword` is `@unique`,
so two prints can never target the same query. Every other position's primary keyword is
passed to the model as `reservedKeywords` and re-checked in code.

## Data model

- `Position` — print image (stored inline), title, SEO title, primary keyword (unique),
  secondary keywords, generated description + meta, warnings, status.
- `Keyword` — the reusable keyword list; the UI marks keywords already claimed as a primary.

## Environment variables

| Variable | Notes |
|---|---|
| `DATABASE_URL` | Injected by Railway when you attach the Postgres service |
| `GEMINI_API_KEY` | Google AI Studio key |
| `GEMINI_MODEL` | defaults to `gemini-2.5-flash` |
| `APP_PASSWORD` | shared login password |
| `SESSION_SECRET` | long random string (signs the session cookie) |

## Deploy on Railway

1. New service in the existing project → deploy from this repo, **Root Directory = `generation-app`**.
2. Add a **Postgres** service in the same project; attach it so `DATABASE_URL` is injected.
3. Set `GEMINI_API_KEY`, `APP_PASSWORD`, `SESSION_SECRET` in the service Variables.
4. Settings → Networking → Custom Domain → `generation.cretho.com`, then add the CNAME +
   TXT records Railway shows to Cloudflare (**CNAME, not an A record — Railway has no static IP**).
   Keep the CNAME on **DNS only** (grey cloud) until the certificate is issued.

`npm run build` runs `prisma migrate deploy`, so the schema is applied on every deploy.

## Local

```bash
cp .env.example .env   # fill in the values
npm install
npx prisma migrate dev --name init
npm run dev
```
