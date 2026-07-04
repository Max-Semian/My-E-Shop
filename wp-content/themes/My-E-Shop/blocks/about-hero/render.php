<?php
/**
 * Render callback for the About Hero block
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get block attributes
$title = isset($attributes['title']) ? $attributes['title'] : 'ABOUT US';
$button_text = isset($attributes['buttonText']) ? $attributes['buttonText'] : 'Learn more';
$button_url = isset($attributes['buttonUrl']) ? $attributes['buttonUrl'] : '';
$background_image = isset($attributes['backgroundImage']) ? $attributes['backgroundImage'] : ['id' => 0, 'url' => '', 'alt' => ''];
$text_color = isset($attributes['textColor']) ? $attributes['textColor'] : '#F4F0EB';
$overlay_opacity = isset($attributes['overlayOpacity']) ? $attributes['overlayOpacity'] : 0.5;

// Get block classes
$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'about-hero-block-frontend',
]);

?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="about-hero-container">
        <?php if (!empty($background_image['url'])): ?>
            <div 
                class="about-hero-background"
                style="background-image: url(<?php echo esc_url($background_image['url']); ?>);"
                role="img"
                aria-label="<?php echo esc_attr($background_image['alt'] ?: $title); ?>"
            >
                <div class="about-hero-overlay"></div>
            </div>
        <?php endif; ?>
        
        <div class="about-hero-content">
            <?php if (!empty($title)): ?>
            <h1 
                class="about-hero-title"
                style="color: <?php echo esc_attr($text_color); ?>;"
            >
                <?php echo wp_kses_post($title); ?>
            </h1>
            <?php endif; ?>
            
            <?php if (!empty($button_text)): ?>
            <div class="about-hero-button-wrapper">
                <a 
                    href="<?php echo esc_url($button_url); ?>" 
                    class="about-hero-button btn btn-primary"
                >
                    <?php echo esc_html($button_text); ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
