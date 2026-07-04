# T-Shirt Designer Block - T-shirt print designer

## Description
A full-featured designer for creating print designs on T-shirts with an interactive interface.

## Features

### Tools:
1. **Upload** - upload your own images
2. **Add text** - add editable text
3. **Graphics** - library of ready-made graphic elements
4. **My library** - saved elements (in development)
5. **My templates** - design templates (in development)
6. **Shutterstock** - Shutterstock integration (in development)
7. **Fiverr** - Fiverr integration (in development)

### Editor functions:
- Switching between the front and back side of the T-shirt
- Zoom (in/out) of the workspace
- Moving, scaling and rotating objects
- Deleting selected objects (Delete/Backspace key)
- Print area with configurable dimensions
- Changing the T-shirt color

## Block settings in the WordPress editor

### Colors:
- **Background color** - background of the entire block (#F5F5F0 by default)
- **Default T-shirt color** - color of the T-shirt model (#FFFFFF by default)

### Print area:
- **Area width** - 200-400px (280px by default)
- **Area height** - 250-500px (350px by default)

### Tools:
- **Text tool** - enable/disable
- **Image upload** - enable/disable
- **Graphics library** - enable/disable

### Graphics for the library:
Add images that will be available to users in the graphics library

## Usage on the frontend

### For the user:
1. Select a tool in the left panel
2. Add text or an image
3. Move and scale elements
4. Switch between the front and back side
5. Click "Save product" to save the design

### Keyboard shortcuts:
- `Delete` or `Backspace` - delete the selected object

## Technical details

### Libraries used:
- **Fabric.js 5.3.0** - for working with canvas and objects
- **jQuery** - for DOM manipulation
- **SVG** - for rendering the T-shirt model

### Design data structure:
```javascript
{
    front: {canvas JSON},  // Front side design
    back: {canvas JSON},   // Back side design
    frontImage: "data:image/png;base64,...",  // Front side PNG
    backImage: "data:image/png;base64,...",   // Back side PNG
    tshirtColor: "#FFFFFF"  // T-shirt color
}
```

## Extending functionality

### Adding new tools:
1. Add a button in `render.php`
2. Create a method in the `TShirtDesigner` class in `script.js`
3. Bind the event in the `activateTool()` method

### Saving the design on the server:
Uncomment and configure the AJAX request in the `saveDesign()` method:
```javascript
$.ajax({
    url: '/wp-admin/admin-ajax.php',
    method: 'POST',
    data: {
        action: 'save_tshirt_design',
        design: JSON.stringify(designs)
    },
    success: function(response) {
        alert('Design saved!');
    }
});
```

### Adding a handler in functions.php:
```php
add_action('wp_ajax_save_tshirt_design', 'save_tshirt_design_handler');
add_action('wp_ajax_nopriv_save_tshirt_design', 'save_tshirt_design_handler');

function save_tshirt_design_handler() {
    $design = json_decode(stripslashes($_POST['design']), true);
    // Save to the database or product meta fields
    wp_send_json_success(['message' => 'Design saved']);
}
```

## Responsive design

The block adapts to different screen sizes:
- Desktop (> 1200px) - full functionality
- Tablet (768px - 1200px) - horizontal tools panel
- Mobile (< 768px) - vertical layout, simplified navigation

## Compatibility

- WordPress 5.8+
- PHP 7.4+
- Modern browsers (Chrome, Firefox, Safari, Edge)
- Mobile browsers (iOS Safari, Chrome Mobile)
