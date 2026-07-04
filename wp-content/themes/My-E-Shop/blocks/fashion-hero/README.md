# Fashion Hero Block

Fashion hero block for the My-E-Shop theme.

## Description

This block creates a full-screen hero section with a grid of background images and centered content. It includes a title, subtitle, and a call-to-action button.

## Features

- ✅ Upload up to 7 background images
- ✅ Customizable title and subtitle
- ✅ Customizable button text and URL
- ✅ Image appearance animations
- ✅ Responsive design
- ✅ Hover effects for images

## Usage

1. Add the block in the Gutenberg editor
2. In the settings panel on the right:
   - Enter the title and subtitle
   - Configure the button text and URL
   - Upload background images (7 recommended)

## File Structure

- `block.json` - Block configuration
- `index.js` - JavaScript for the editor
- `render.php` - PHP template for the frontend
- `style.css` - Frontend styles
- `editor.css` - Editor styles
- `README.md` - Documentation

## Attributes

- `title` (string) - Section title
- `subtitle` (string) - Section subtitle
- `buttonText` (string) - Button text
- `buttonUrl` (string) - Button URL
- `images` (array) - Array of background images

## Styles

The block uses the same styles as the original markup in `main.css`:
- fadeInScale animations for images
- Gradient overlay for text readability
- Hover effects for images
- Responsive font sizes
