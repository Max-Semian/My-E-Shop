<?php
/**
 * Category Cards Block Template
 */

// Get block attributes with defaults
$background_color = isset($attributes['backgroundColor']) ? $attributes['backgroundColor'] : '#f8f9fa';
$block_title = isset($attributes['blockTitle']) ? $attributes['blockTitle'] : 'Collection';
$block_subtitle = isset($attributes['blockSubtitle']) ? $attributes['blockSubtitle'] : 'Wear your mark. Feel your power';
$categories = isset($attributes['categories']) ? $attributes['categories'] : array();

// Debug output (remove in production)
// echo '<!-- DEBUG: Categories data: ' . json_encode($categories) . ' -->';

// Build CSS classes
$css_classes = ['category-cards-block'];

// Build CSS custom properties
$css_vars = array(
    '--category-cards-bg-color: ' . esc_attr($background_color)
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
    <div class="category-cards-container">
        <div class="category-cards-header">
            <?php if (!empty($block_title)) : ?>
                <h2 class="category-cards-title"><?php echo esc_html($block_title); ?></h2>
            <?php endif; ?>
            
            <?php if (!empty($block_subtitle)) : ?>
                <p class="category-cards-subtitle"><?php echo esc_html($block_subtitle); ?></p>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($categories) && is_array($categories)) : ?>
            <div class="category-cards-grid">
                <?php foreach ($categories as $index => $category) : ?>
                    <div class="category-card-item">
                        <a href="<?php echo esc_url($category['url'] ?? '#'); ?>" 
                           class="category-card-link"
                           target="_blank"
                           rel="noopener noreferrer"
                           title="<?php echo esc_attr($category['title'] ?? 'Category'); ?>">
                            
                            <div class="category-card-image-container">
                                <?php if (!empty($category['imageUrl'])) : ?>
                                    <!-- DEBUG: Image URL: <?php echo esc_html($category['imageUrl']); ?> -->
                                    <img src="<?php echo esc_url($category['imageUrl']); ?>" 
                                         alt="<?php echo esc_attr($category['title'] ?? 'Category'); ?>" 
                                         class="category-card-image"
                                         onerror="console.log('Image failed to load:', this.src); this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="category-card-image-placeholder" style="display: none;">
                                        <span>📁</span>
                                        <small>Loading error</small>
                                    </div>
                                <?php else : ?>
                                    <div class="category-card-image-placeholder">
                                        <span>📁</span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="category-card-overlay"></div>
                                
                                <div class="category-card-content">
                                    <?php if (!empty($category['subtitle'])) : ?>
                                        <p class="category-card-subtitle"><?php echo esc_html($category['subtitle']); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($category['title'])) : ?>
                                        <h3 class="category-card-title" style="<?php echo !empty($category['titleColor']) ? 'color: ' . esc_attr($category['titleColor']) . ';' : ''; ?>">
                                            <?php if (!empty($category['iconUrl'])) : ?>
                                                <img src="<?php echo esc_url($category['iconUrl']); ?>" 
                                                     alt="" 
                                                     class="category-card-icon">
                                            <?php endif; ?>
                                            <?php echo esc_html($category['title']); ?>
                                        </h3>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="category-cards-grid">
                <div class="category-card-item">
                    <div class="category-card-placeholder">
                        No categories added yet - Debug: <?php echo count($categories); ?> categories
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
