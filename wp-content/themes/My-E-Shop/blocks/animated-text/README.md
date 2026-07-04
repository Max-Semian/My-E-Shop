# Animated Text Block

A custom Gutenberg block for the My-E-Shop WordPress theme that lets you create eye-catching animated text blocks.

## Features

- ✅ Fully editable text with RichText support
- ✅ 4 animation types:
  - Typewriter (typewriter)
  - Word appearance one by one (fadeInWords)
  - Fade in (fadeIn)
  - Slide up (slideUp)
- ✅ Animation speed control
- ✅ 4 font sizes
- ✅ Text alignment control
- ✅ Text and background color control
- ✅ Responsive design
- ✅ Animation starts when the block comes into view

## Replacing an existing section

This block is designed to replace a static section:
```html
<section class="animated-text-section">
    <div class="text-block" id="textBlock">
        <div class="text-content" id="textContent">
            T-shirts that combine runway aesthetics with individuality...
        </div>
    </div>
</section>
```

## Usage

1. In the WordPress editor, add a new block
2. Find the "Animated Text Block" block in the "Text" category
3. Enter or edit the text
4. Configure it in the sidebar:
   - **Font size**: small, normal, large, extra large
   - **Animation type**: choose a suitable effect
   - **Animation speed**: from 10 to 200 ms
   - **Colors**: text and background color
5. Use the toolbar to align the text

## Settings

### Basic settings
- **Text**: Fully editable with bold and italic support
- **Alignment**: Left, center, right

### Text settings
- **Font size**: 4 preset sizes
- **Text color**: Choose any color with transparency support
- **Background color**: Configure the block background

### Animation settings
- **Animation type**:
  - **Typewriter**: Simulates typing text with a cursor
  - **Word appearance**: Words appear one by one
  - **Fade in**: The whole text fades in smoothly
  - **Slide up**: The text rises from below
- **Speed**: Configure the animation timing

## File structure

```
blocks/animated-text/
├── block.json          # Block configuration
├── block.js           # Block JavaScript code
├── style.css          # Frontend styles
├── editor.css         # Editor styles
├── script.js          # JavaScript for animations
└── README.md          # Documentation
```

## CSS classes

- `.animated-text-section` - Main container
- `.text-block` - Text block container
- `.text-content` - Text container
- `.font-small`, `.font-normal`, `.font-large`, `.font-extra-large` - Font sizes
- `.align-left`, `.align-center`, `.align-right` - Alignment
- `.typewriter`, `.fadeInWords`, `.fadeIn`, `.slideUp` - Animation classes

## JavaScript API

### Functions
- `restartTextAnimations()` - Restart all animations (for debugging)

### Data attributes
- `data-animation` - animation type
- `data-speed` - animation speed in milliseconds

## Responsiveness

- Automatically disables the typewriter animation on mobile devices
- Responsive font sizes
- Optimized for different screens

## Compatibility

- WordPress 5.0+
- Support for all modern browsers
- Intersection Observer API for performance optimization

## Migration

To replace the old section with the new block:
1. Remove the section's HTML code from `front-page.php`
2. Add the new block in the Gutenberg editor
3. Copy the text from the old section
4. Configure the animation and styles to your taste
