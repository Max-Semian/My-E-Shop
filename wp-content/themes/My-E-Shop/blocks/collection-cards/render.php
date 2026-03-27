<?php
/**
 * Collection Cards Block Template
 */

// Get block attributes with defaults
$block_title = isset($attributes['blockTitle']) ? $attributes['blockTitle'] : 'Our Collections';
$block_subtitle = isset($attributes['blockSubtitle']) ? $attributes['blockSubtitle'] : 'Each piece tells a story — choose yours';
$collections = isset($attributes['collections']) ? $attributes['collections'] : array();

// Build CSS classes
$css_classes = ['collection-cards-block'];

// Get wrapper attributes with anchor support
$anchor_attr = array(
    'class' => implode(' ', $css_classes)
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
    <div class="collection-cards-container">
        <div class="collection-cards-header">
            <?php if (!empty($block_title)) : ?>
                <h2 class="collection-cards-title"><?php echo esc_html($block_title); ?></h2>
            <?php endif; ?>
            
            <?php if (!empty($block_subtitle)) : ?>
                <p class="collection-cards-subtitle"><?php echo esc_html($block_subtitle); ?></p>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($collections) && is_array($collections)) : ?>
            <div class="collection-cards-grid">
                <?php foreach ($collections as $index => $collection) : ?>
                    <div class="collection-card-item">
                        <a href="<?php echo esc_url($collection['url'] ?? '#'); ?>" 
                           class="collection-card-link"
                           title="<?php echo esc_attr($collection['title'] ?? 'Коллекция'); ?>">
                            
                            <div class="collection-card-image-container">
                                <?php if (!empty($collection['imageUrl'])) : ?>
                                    <img src="<?php echo esc_url($collection['imageUrl']); ?>" 
                                         alt="<?php echo esc_attr($collection['title'] ?? 'Коллекция'); ?>" 
                                         class="collection-card-image">
                                <?php else : ?>
                                    <div class="collection-card-image-placeholder">
                                        <span>📁</span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="collection-card-overlay"></div>
                                
                                <div class="collection-card-content">
                                    <?php if (!empty($collection['title'])) : ?>
                                        <h3 class="collection-card-title"><?php echo esc_html($collection['title']); ?></h3>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($collection['subtitle'])) : ?>
                                        <p class="collection-card-subtitle"><?php echo esc_html($collection['subtitle']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="no-collections-message">Коллекции не добавлены. Добавьте коллекции в редакторе блоков.</p>
        <?php endif; ?>
    </div>
</div>
