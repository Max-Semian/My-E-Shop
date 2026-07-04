# Custom category pages with Gutenberg

This system lets you create individual pages for each WooCommerce category, editable in the Gutenberg editor.

## Features

- ✅ Automatic creation of custom category pages
- ✅ Full Gutenberg editor support
- ✅ Management from the WordPress admin panel
- ✅ Automatic synchronization with category changes
- ✅ Clean design and responsiveness
- ✅ Quick edit links

## How to use

### 1. Automatic creation
On the first visit to a category page, the system automatically creates a custom page with default content.

### 2. Creating from the admin panel
1. Go to **Products → Categories**
2. Click **Edit** on the desired category
3. In the "Custom category page" section, click **Create custom page**
4. The page will be created and you'll be able to edit it

### 3. Managing from the category list
A new "Custom page" column appears in the category list with buttons:
- **Edit** — opens the page in the Gutenberg editor
- **View** — opens the page on the site

### 4. Editing the content
1. Find the desired category and click "Edit" in the "Custom page" column
2. Use the full power of the Gutenberg editor:
   - Add text, image, and video blocks
   - Use ready-made WooCommerce blocks
   - Build complex layouts with columns
   - Add shortcodes and widgets

## File structure

```
wp-content/themes/My-E-Shop/
├── woocommerce/
│   └── taxonomy-product-cat.php     # Category template
├── includes/
│   └── category-pages.php           # Core logic
├── assets/css/
│   └── category-pages.css          # Styles
└── functions.php                    # Feature registration
```

## Details

### Automatic synchronization
- When a category name changes — the page title is updated
- When a category slug changes — the page URL is updated
- When a category is deleted — the linked page is deleted

### SEO optimization
- Page URL: `/category-{category-slug}/`
- Title: `Category: {Category name}`
- Metadata is inherited automatically from WordPress

### WooCommerce shortcodes
You can use these in the editor:
```
[woocommerce_products category="category-slug" columns="4" limit="12"]
[woocommerce_products category="category-slug" columns="4" limit="12" paginate="true"]
```

## Technical information

### Meta fields
Each custom page has a `_category_page_for` meta field holding the category ID for linking.

### AJAX handlers
- `create_category_page` — creates a new category page
- Protected via nonce and user capability check

### WordPress hooks
- `product_cat_edit_form_fields` — adds a field to the category editor
- `manage_edit-product_cat_columns` — adds a column to the list
- `delete_product_cat` — deletes the page when a category is deleted
- `edited_product_cat` — synchronizes changes

## Styling

All styles live in `assets/css/category-pages.css` and include:
- Modern design with gradients and shadows
- Full responsiveness for mobile devices
- Styles for buttons and interactive elements
- WooCommerce compatibility

## Access rights

Creating and editing custom pages requires the `edit_pages` capability.
The management buttons are only visible to users with the appropriate rights.
