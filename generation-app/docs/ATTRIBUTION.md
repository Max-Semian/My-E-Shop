# Attribution & provenance

The SEO methodology this generator implements is derived from an MIT-licensed
open-source framework. This file records exactly what came from where, so the
licence obligation is met and nobody has to guess later.

## Upstream source (MIT)

**Repository:** [nkovalcin/seo-product-description-framework](https://github.com/nkovalcin/seo-product-description-framework)
**Licence:** MIT

The following files from that repository are the source of the *methodology*
(keyword placement, field rules, meta length limits, anti-cliché rules). They are
**not** vendored into this repo — they are referenced:

| File | Upstream |
|---|---|
| `seo-rules.md` | https://github.com/nkovalcin/seo-product-description-framework/blob/main/seo-rules.md |
| `prompt-templates.md` | https://github.com/nkovalcin/seo-product-description-framework/blob/main/prompt-templates.md |
| `shopify-technical-seo.md` | https://github.com/nkovalcin/seo-product-description-framework/blob/main/shopify-technical-seo.md |

The MIT licence permits this use, and requires the copyright notice and permission
notice be preserved. See `LICENSE-UPSTREAM-MIT` in this directory.

## Written for this project (derivative work)

These two files are **not** copies from the upstream repo. They were written from
scratch for the Cretho stack, following the structure of the upstream Shopify
documents:

| File | Notes |
|---|---|
| `woocommerce-technical-seo.md` | WooCommerce/WordPress adaptation of the Shopify technical-SEO checklist, plus Rank Math / Yoast and WP REST API specifics. |
| `woocommerce-prompt-template.md` | Adaptation of the prompt templates to JSON output that maps onto the WooCommerce REST API payload. |

## How the methodology is actually enforced here

The documents are the specification. The enforcement lives in code:

- `src/lib/prompt.ts` — turns the field rules into hard, countable instructions for the model.
- `src/lib/validate.ts` — re-derives every count and length from the generated output and
  **rejects** anything that breaks a rule. The model is never trusted on its own compliance.

So the docs can be read as "why", and those two files are "what is actually enforced".
If you change a rule in the docs, change it in `validate.ts` too or it does not exist.
