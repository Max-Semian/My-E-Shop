# Scrambled Text Block (GSAP)

An interactive WordPress Gutenberg text block with a scramble effect on cursor hover, based on the GSAP ScrambleTextPlugin.

## 🎨 Description

This block creates an interactive text effect where characters "scramble" as the mouse cursor approaches. Based on the work of Tom Miller from the GSAP community.

**Inspiration source:** [CodePen - Tom Miller](https://codepen.io/creativeocean/pen/NPWLwJM)

## ✨ Features

- 🎯 **Interactivity**: Reacts to cursor movement
- 🎛️ **Configurable parameters**: Radius, duration, speed, characters
- 🎨 **Full customization**: Text and background colors, font sizes
- 📱 **Responsive**: Works on desktop and mobile devices
- ⚡ **GSAP animation**: Smooth, performant animation
- 🔤 **Monospace font**: For a better visual effect

## 📦 File structure

```
blocks/scrambled-text/
├── block.json          # Block configuration
├── index.js            # WordPress editor (Edit & Save)
├── script.js           # Frontend JavaScript with GSAP
├── style.css           # Frontend styles
├── editor.css          # Editor styles
└── README.md           # Documentation
```

## 🚀 Usage

### In the WordPress editor

1. Open the page/post editor
2. Add the **"Scrambled Text (GSAP)"** block
3. Enter your text
4. Configure the parameters in the right panel

### Block settings

#### Text
- **Font size**: Small / Medium / Large
- **Alignment**: Left / Center / Right

#### GSAP Animation
- **Effect radius** (50-300px): Distance from the cursor to activate the effect
- **Duration** (0.3-3.0 sec): Scramble animation time
- **Scramble speed** (0.1-1.0): Character change speed
- **Scramble characters**: Set of characters for the effect (default: `.:`)

#### Colors
- **Text color**: White by default (#ffffff)
- **Background color**: Transparent by default

## 🎯 Usage examples

### Basic example
```
Text: "Lorem ipsum dolor sit amet"
Radius: 100px
Duration: 1.2 sec
Speed: 0.5
Characters: .:
```

### Fast effect
```
Radius: 80px
Duration: 0.5 sec
Speed: 0.8
Characters: !@#$%
```

### Slow effect
```
Radius: 150px
Duration: 2.0 sec
Speed: 0.3
Characters: ░▒▓█
```

## 🔧 Technical details

### Dependencies

**GSAP libraries** (already included in `functions.php`):
- GSAP Core 3.12.5+
- ScrambleTextPlugin

### Block attributes

| Attribute | Type | Default | Description |
|---------|-----|--------------|----------|
| `content` | string | Lorem ipsum... | Text content |
| `radius` | number | 100 | Effect radius in pixels |
| `duration` | number | 1.2 | Animation duration in seconds |
| `speed` | number | 0.5 | Scramble speed (0.1-1.0) |
| `scrambleChars` | string | .: | Characters for the effect |
| `textColor` | string | #ffffff | Text color |
| `backgroundColor` | string | "" | Background color |
| `fontSize` | string | medium | Size: small/medium/large |
| `textAlign` | string | left | Alignment: left/center/right |

### Data attributes

The block passes parameters via data attributes:
```html
<div class="scrambled-text-wrapper"
     data-radius="100"
     data-duration="1.2"
     data-speed="0.5"
     data-scramble-chars=".:">
```

### JavaScript API

Function for manual re-initialization:
```javascript
window.reinitScrambledText();
```

## 📱 Responsiveness

- **Desktop**: Full interactivity with the mouse cursor
- **Tablet**: Support for touch events
- **Mobile**: Optimized font sizes, hint for touch control

### Breakpoints
- `< 768px`: Mobile devices
- `< 480px`: Small screens

## 🎨 CSS Classes

- `.scrambled-text-block` - Root container
- `.scrambled-text-wrapper` - Wrapper with data attributes
- `.text-block` - Text block
- `.char` - Individual character
- `.font-small`, `.font-medium`, `.font-large` - Font sizes
- `.align-left`, `.align-center`, `.align-right` - Alignment

## ⚠️ Important notes

### GSAP ScrambleTextPlugin
- Required for the full scramble effect
- Available in GSAP 3.x
- A license may be required for commercial use

### Fallback
If ScrambleTextPlugin is unavailable, the block uses a simplified animation:
- Random character replacement
- Opacity and scale animation
- Without smooth scrambling

### Performance
- Optimized for modern browsers
- Uses `will-change` for GPU acceleration
- Limited by the action radius to save resources

## 🐛 Troubleshooting

**Problem**: Animation does not work
- ✅ Check the browser console for errors
- ✅ Make sure GSAP is loaded (check Network in DevTools)
- ✅ Check that `viewScript` is specified in block.json

**Problem**: Text does not display
- ✅ Clear the WordPress cache
- ✅ Re-save the page in the editor
- ✅ Check that the `script.js` file loads

**Problem**: The effect is weak or not noticeable
- ✅ Increase the effect radius
- ✅ Decrease the scramble speed
- ✅ Increase the animation duration
- ✅ Use more contrasting characters

## 📋 Block registration

The block is automatically registered in `functions.php`:

```php
// Block registration
register_block_type(get_template_directory() . '/blocks/scrambled-text/block.json');

// Editor scripts and styles
wp_enqueue_script('my-e-shop-scrambled-text-editor', ...);
wp_enqueue_style('my-e-shop-scrambled-text-editor-style', ...);

// Frontend scripts and styles
wp_enqueue_style('my-e-shop-scrambled-text-style', ...);
wp_enqueue_script('my-e-shop-scrambled-text-script', ['gsap', 'gsap-scramble-text'], ...);
```

## 🔄 Version

**v1.0.0** - First release

## 📄 License

GPL v2 or later

---

**Author**: My-E-Shop Theme  
**Inspired by**: Tom Miller (GSAP Community)  
**Technologies**: WordPress Gutenberg, GSAP 3.x, ScrambleTextPlugin
