# WooCommerce Product Prompt — REST API JSON Output

Adapted from `prompt-templates.md` to output a single JSON object that maps
directly onto the WooCommerce REST API `POST /wp-json/wc/v3/products` (or
`PUT .../products/{id}`) payload, so it can be pushed straight from your
pipeline without manual reformatting.

## Master Prompt

```
Create SEO-optimized product content for my WooCommerce t-shirt store.

Requirements:
- Write as a professional SEO expert, e-commerce copywriter, and marketer
- Use [TARGET LANGUAGE] language
- Split the long description into sections with subheadings (use HTML: <h3>, <p>, <strong>)
- Implement light storytelling (one short relatable line, not a full story)
- Include material, fit, and print details naturally in the copy
- Never start with "Discover..." or other cliché openings
- Never mention the print-on-demand supplier's brand name (e.g. Printful)
- Keep short_description to 1-2 punchy sentences — it must NOT repeat the long description
- Only capitalize the first word of headings (for languages where this applies)
- Primary keyword must appear in the title, first paragraph, meta_title, and meta_description

Product data:
Name: [ORIGINAL PRODUCT NAME]
Category: [e.g. Graphic Tees / Oversized Fits / Basics]
Target keyword: [PRIMARY KEYWORD]
Materials: [e.g. 100% combed cotton, 180gsm]
Fit: [e.g. unisex, oversized, true-to-size]
Print method: [e.g. DTG, screen print]
Available sizes: [S-XXL etc.]
Available colors: [list]
Price: [amount + currency]
Other details: [anything else — care instructions, print placement, etc.]

Output ONLY a single valid JSON object (no markdown fences, no commentary) with this exact structure:

{
  "name": "",
  "slug": "",
  "type": "variable",
  "short_description": "",
  "description": "",
  "meta_title": "",
  "meta_description": "",
  "categories": [{"name": ""}],
  "tags": [{"name": ""}],
  "images_alt": ["", "", "", "", ""],
  "image_filenames": ["", "", "", "", ""]
}

Field rules:
- slug: lowercase, hyphens, no diacritics, 4-8 keywords, matches URL handle rules
- description: full HTML description per the requirements above
- short_description: plain text or minimal HTML, 1-2 sentences
- meta_title: max 70 characters, primary keyword first
- meta_description: max 165 characters, no cliché openings, includes a soft CTA
- images_alt: 5 distinct alt text variations (front, back, detail, worn/lifestyle, flat lay) — each keyword-rich but natural, no duplicates
- image_filenames: matching filenames, lowercase, underscores, no diacritics, format [slug]_[view].jpg
- tags: 3-5 relevant tags (style, fit, occasion — not just repeats of the category)
```

## Batch Version (multiple t-shirts in one category)

```
I have [N] t-shirt products in the "[CATEGORY]" category. I will provide
each product one at a time. For each one, output ONLY the JSON object
described in the schema below — no markdown fences, no commentary between
products.

Schema:
{
  "name": "",
  "slug": "",
  "type": "variable",
  "short_description": "",
  "description": "",
  "meta_title": "",
  "meta_description": "",
  "categories": [{"name": ""}],
  "tags": [{"name": ""}],
  "images_alt": ["", "", "", "", ""],
  "image_filenames": ["", "", "", "", ""]
}

Rules that apply to ALL products in this batch:
- Target language: [LANGUAGE]
- Always use the term "[PREFERRED TERM]" not "[TERM TO AVOID]" (brand terminology)
- Never mention the print-on-demand supplier's name
- Primary keyword per product: use the "Target keyword" I give with each item
- Consistent tone: [e.g. casual, streetwear, playful / minimal, premium, understated]

Ready? Here is product 1:
Name: [...]
Target keyword: [...]
Materials: [...]
Fit: [...]
Colors: [...]
Sizes: [...]
Price: [...]
```

## Pushing to WooCommerce REST API

Once you have the JSON, map it into the create/update payload:

```python
import requests

payload = {
    "name": item["name"],
    "slug": item["slug"],
    "type": "variable",
    "description": item["description"],
    "short_description": item["short_description"],
    "categories": item["categories"],
    "tags": item["tags"],
    "meta_data": [
        {"key": "rank_math_title", "value": item["meta_title"]},
        {"key": "rank_math_description", "value": item["meta_description"]}
        # swap keys for "_yoast_wpseo_title" / "_yoast_wpseo_metadesc" if using Yoast
    ]
}

resp = requests.post(
    "https://cretho.com/wp-json/wc/v3/products",
    json=payload,
    auth=(CONSUMER_KEY, CONSUMER_SECRET)
)
```

Image alt text / filenames get applied separately when uploading media via
`/wp-json/wp/v2/media`, then attached to the product via the `images` array
(`src` + `alt` per image object) in a follow-up `PUT`.

## Tips

- Feed the batch prompt through your existing Telegram review flow before
  pushing to WooCommerce — same pattern you already use for the Threads
  outreach pipeline, just swapped to a "review JSON → approve → push to WC"
  step.
- Cache `categories` and `tags` name→ID mappings once per run so you're not
  hitting the WooCommerce taxonomy endpoints on every product.
