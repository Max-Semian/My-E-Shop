# Category Cards Block

## Description

The "Category Cards" block displays product categories as a nice grid with images and text.

## Features

- Display categories in a responsive grid (4 columns on desktop, 2 on mobile)
- Upload images through the WordPress media library
- Customizable block title and subtitle
- Customizable background color
- Hover effects and animations
- Link support for each category

## Block settings

### Main settings
- **Background color**: Customizable block background color
- **Block title**: Main title (for example, "Collection")
- **Block subtitle**: Additional text below the title

### Category settings
For each category you can configure:
- **Image**: Upload through the media library
- **Category name**: Main category text
- **Subtitle**: Additional text (displayed in a small font)
- **Link**: URL to open on click

## File structure

```
category-cards/
├── block.json          # Block configuration
├── index.js           # JavaScript for the editor
├── render.php         # PHP template for the frontend
├── style.css          # Styles for the frontend
├── editor.css         # Styles for the editor
├── script.js          # JavaScript for the frontend
└── README.md          # Documentation
```

## Usage

1. In the WordPress editor, find the "Category Cards Block"
2. Add the block to a page
3. Configure the title and background color in the sidebar
4. Add categories using the "+ Add category" button
5. For each category, upload an image and fill in the text fields

## Responsiveness

- **Desktop (>1024px)**: 4 columns
- **Tablet (768-1024px)**: 3 columns  
- **Mobile (480-768px)**: 2 columns
- **Small mobile (<480px)**: 1 column

## Compatibility

- WordPress 5.0+
- Gutenberg editor
- Modern browsers (Chrome, Firefox, Safari, Edge)

## Development

The block uses:
- ES5/ES6 JavaScript for compatibility
- WordPress Block API
- CSS Grid for the responsive grid
- PHP for server-side rendering
