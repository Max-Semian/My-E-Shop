<?php
/**
 * Render callback for the Category Hero block
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get block attributes
$category_title = isset($attributes['categoryTitle']) ? $attributes['categoryTitle'] : '';
$background_image = isset($attributes['backgroundImage']) ? $attributes['backgroundImage'] : ['id' => 0, 'url' => '', 'alt' => ''];
$text_color = isset($attributes['textColor']) ? $attributes['textColor'] : '#F4F0EB';
$overlay_opacity = isset($attributes['overlayOpacity']) ? $attributes['overlayOpacity'] : 0.5;

// If we are on a category page, get the category data
if (is_product_category()) {
    $current_category = get_queried_object();
    if ($current_category && empty($category_title)) {
        $category_title = $current_category->name;
    }
}

// Get block classes
$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'category-hero-block-frontend',
]);

?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="category-hero-container">
        <?php if (!empty($background_image['url'])): ?>
            <div 
                class="category-hero-background"
                style="background-image: url(<?php echo esc_url($background_image['url']); ?>);"
                role="img"
                aria-label="<?php echo esc_attr($background_image['alt'] ?: $category_title); ?>"
            >
                <div 
                    class="category-hero-overlay"
                    style="background-color: rgba(0, 0, 0, <?php echo esc_attr($overlay_opacity); ?>);"
                ></div>
            </div>
        <?php endif; ?>
        
        <div class="category-hero-content">
            <?php if (!empty($category_title)): ?>
            <h1 
                class="category-hero-title"
                style="color: <?php echo esc_attr($text_color); ?>;"
            >
                <?php echo wp_kses_post($category_title); ?>
            </h1>
            <?php endif; ?>
        </div>
    </div>
</div>