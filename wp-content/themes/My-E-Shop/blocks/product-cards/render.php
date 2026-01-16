<?php
/**
 * Product Cards Block Template
 */

// Get block attributes with defaults
$background_color = isset($attributes['backgroundColor']) ? $attributes['backgroundColor'] : '#f8f9fa';
$block_title = isset($attributes['blockTitle']) ? $attributes['blockTitle'] : 'Best Sellers';
$block_subtitle = isset($attributes['blockSubtitle']) ? $attributes['blockSubtitle'] : 'Your aura in cotton';
$columns = isset($attributes['columns']) ? $attributes['columns'] : 4;
$card_style = isset($attributes['cardStyle']) ? $attributes['cardStyle'] : 'classic';
$custom_images = isset($attributes['customImages']) ? $attributes['customImages'] : array();
$show_cart_button = isset($attributes['showCartButton']) ? $attributes['showCartButton'] : true;
$cart_button_text = isset($attributes['cartButtonText']) ? $attributes['cartButtonText'] : 'Add to Cart';
$cart_button_link = isset($attributes['cartButtonLink']) ? $attributes['cartButtonLink'] : '#';

// Debug output (remove in production)
// echo '<!-- DEBUG: Products data: ' . json_encode($custom_images) . ' -->';

// Build CSS classes
$css_classes = ['product-cards-block', 'card-style-' . $card_style, 'columns-' . $columns];

// Build CSS custom properties
$css_vars = array(
    '--product-cards-bg-color: ' . esc_attr($background_color),
    '--product-cards-columns: ' . (int)$columns
);

// Get wrapper attributes with anchor support
$anchor_attr = array(
    'class' => implode(' ', $css_classes),
    'style' => implode('; ', $css_vars)
);

// Add anchor ID if present
if (!empty($attributes['anchor'])) {
    $anchor_attr['id'] = $attributes['anchor'];
} elseif (!empty($block->anchor)) {
    $anchor_attr['id'] = $block->anchor;
}

$wrapper_attributes = get_block_wrapper_attributes($anchor_attr);

?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="product-cards-container">
        <div class="product-cards-header">
            <div class="product-cards-header-content">
                <div class="product-cards-titles">
                    <?php if (!empty($block_title)) : ?>
                        <h2 class="product-cards-title"><?php echo esc_html($block_title); ?></h2>
                    <?php endif; ?>
                    
                    <?php if (!empty($block_subtitle)) : ?>
                        <p class="product-cards-subtitle"><?php echo esc_html($block_subtitle); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if (!empty($custom_images) && is_array($custom_images)) : ?>
            <div class="product-cards-grid">
                <?php foreach ($custom_images as $index => $product) : ?>
                    <div class="product-card-item">
                        <a href="<?php echo esc_url($product['link'] ?? '#'); ?>" 
                           class="product-card-link"
                           target="_blank"
                           rel="noopener noreferrer"
                           title="<?php echo esc_attr($product['title'] ?? 'Товар'); ?>">
                            
                            <div class="product-card-image-container">
                                <?php if (!empty($product['imageUrl'])) : ?>
                                    <!-- DEBUG: Image URL: <?php echo esc_html($product['imageUrl']); ?> -->
                                    <img src="<?php echo esc_url($product['imageUrl']); ?>" 
                                         alt="<?php echo esc_attr($product['title'] ?? 'Товар'); ?>" 
                                         class="product-card-image"
                                         onerror="console.log('Image failed to load:', this.src); this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="product-card-image-placeholder" style="display: none;">
                                        <span>🛍️</span>
                                        <small>Ошибка загрузки</small>
                                    </div>
                                <?php else : ?>
                                    <div class="product-card-image-placeholder">
                                        <span>🛍️</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-card-content">
                                <?php if (!empty($product['title'])) : ?>
                                    <h3 class="product-card-title">
                                        <?php echo esc_html($product['title']); ?>
                                    </h3>
                                <?php endif; ?>
                                
                                <!-- Брейкер между названием и ценой -->
                                <div class="product-card-breaker"></div>
                                
                                <div class="product-card-bottom">
                                    <?php if (!empty($product['price'])) : ?>
                                        <div class="product-card-price-section">
                                            <?php if (!empty($product['originalPrice']) && $product['originalPrice'] !== $product['price']) : ?>
                                                <span class="product-card-original-price"><?php echo esc_html($product['originalPrice']); ?></span>
                                                <span class="product-card-sale-price"><?php echo esc_html($product['price']); ?></span>
                                            <?php else : ?>
                                                <span class="product-card-price"><?php echo esc_html($product['price']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($show_cart_button) : ?>
                                        <div class="product-card-actions">
                                            <a href="<?php echo esc_url($cart_button_link); ?>" 
                                               class="product-cart-button"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               title="<?php echo esc_attr($cart_button_text); ?>">
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="product-cards-grid">
                <div class="product-card-item">
                    <div class="product-card-placeholder">
                        Товары еще не добавлены - Debug: <?php echo count($custom_images); ?> товаров
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>