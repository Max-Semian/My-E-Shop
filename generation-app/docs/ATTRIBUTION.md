# Attribution & provenance

The SEO methodology this generator implements is derived from an MIT-licensed
open-source framework. This file records exactly what came from where, so the
licence obligation is met and nobody has to guess later.

## Upstream source (MIT)

**Repository:** [nkovalcin/seo-product-description-framework](https://github.com/nkovalcin/seo-product-description-framework)
**Licence:** MIT — full text preserved in `LICENSE-UPSTREAM-MIT` (© 2025 nkovalcin).

We use only the **platform-agnostic** parts — the general e-commerce SEO method that
holds regardless of the shop platform:

| Upstream file | What we take from it |
|---|---|
| [`seo-rules.md`](https://github.com/nkovalcin/seo-product-description-framework/blob/main/seo-rules.md) | Keyword placement rules, meta length limits, anti-cliché rules, keyword-density ceiling. |
| [`prompt-templates.md`](https://github.com/nkovalcin/seo-product-description-framework/blob/main/prompt-templates.md) | Field structure of the product-copy prompt and its output contract. |

These files are **referenced, not vendored** — we implement the method, we don't ship copies.

### Deliberately NOT used

The upstream `shopify-technical-seo.md` is **not** used and not vendored here. It is
Shopify-platform plumbing (Liquid templates, Shopify admin settings, Shopify's URL
handling) with no bearing on this stack. Cretho runs on **WooCommerce / WordPress**, so
the technical layer is written for that instead — see below.

## Written for this project (derivative work)

These two files are **our own work**, not copies from the upstream repo. They follow the
same document structure but the content is written for the WooCommerce stack:

| File | Notes |
|---|---|
| `woocommerce-technical-seo.md` | Technical SEO checklist for WooCommerce/WordPress: Rank Math / Yoast field mapping, permalinks, variable-product canonicals, WP REST API specifics. |
| `woocommerce-prompt-template.md` | Prompt template producing JSON that maps onto the WooCommerce REST API product payload. |

## How the methodology is actually enforced here

The documents are the specification. The enforcement lives in code:

- `src/lib/prompt.ts` — turns the field rules into hard, countable instructions for the model.
- `src/lib/validate.ts` — re-derives every count and length from the generated output and
  **rejects** anything that breaks a rule. The model is never trusted on its own compliance.

So the docs are the "why", and those two files are "what is actually enforced".
If you change a rule in the docs, change it in `validate.ts` too — otherwise it does not exist.
