<?php
/**
 * About Section Block Template
 */

// Get block attributes with defaults
$title = isset($attributes['title']) ? $attributes['title'] : 'About the brand';
$description = isset($attributes['description']) ? $attributes['description'] : 'So familiar love for fashion, yet handmade. All of our pieces are handcrafted, down to our smallest components of dreams. Find out how it is made from us.';
$button_text = isset($attributes['buttonText']) ? $attributes['buttonText'] : 'Learn more';
$button_link = isset($attributes['buttonLink']) ? $attributes['buttonLink'] : '#';
$background_image = isset($attributes['backgroundImage']) ? $attributes['backgroundImage'] : '';
$background_color = isset($attributes['backgroundColor']) ? $attributes['backgroundColor'] : '#2c2c2c';
$text_color = isset($attributes['textColor']) ? $attributes['textColor'] : '#ffffff';
$overlay = isset($attributes['overlay']) ? $attributes['overlay'] : 0.5;

// Build CSS custom properties
$css_vars = array(
    '--about-bg-color: ' . esc_attr($background_color),
    '--about-text-color: ' . esc_attr($text_color),
    '--about-overlay: ' . (float)$overlay
);

// Get wrapper attributes
$wrapper_attributes = get_block_wrapper_attributes(array(
    'class' => 'about-section-block',
    'style' => implode('; ', $css_vars)
));

?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="about-section-container" 
         style="<?php echo $background_image ? 'background-image: url(' . esc_url($background_image) . ');' : ''; ?> background-color: <?php echo esc_attr($background_color); ?>; color: <?php echo esc_attr($text_color); ?>;">
        
        <?php if ($background_image) : ?>
            <div class="about-section-overlay" style="background-color: rgba(0, 0, 0, <?php echo esc_attr($overlay); ?>);"></div>
        <?php endif; ?>
        
        <div class="about-section-content">
            <div class="about-section-inner">
                <?php if (!empty($title)) : ?>
                    <h2 class="about-section-title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
                
                <?php if (!empty($description)) : ?>
                    <p class="about-section-description"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($button_text)) : ?>
                    <a href="<?php echo esc_url($button_link); ?>" 
                       class="about-section-button"
                       style="color: <?php echo esc_attr($text_color); ?>; border-color: <?php echo esc_attr($text_color); ?>;">
                        <?php echo esc_html($button_text); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
