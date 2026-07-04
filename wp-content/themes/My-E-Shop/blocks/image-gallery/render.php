<?php
/**
 * Render callback for the Image Gallery block
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get the block attributes
$rows = isset($attributes['rows']) ? $attributes['rows'] : [];

// Get the block classes
$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'image-gallery-frontend',
]);

if (empty($rows)) {
    return '';
}
?>

<div <?php echo $wrapper_attributes; ?>>
    <?php foreach ($rows as $row_index => $row) : ?>
        <div class="image-gallery-row">
            <?php if (!empty($row['image1']['url'])) : ?>
                <div class="gallery-image">
                    <img 
                        src="<?php echo esc_url($row['image1']['url']); ?>" 
                        alt="<?php echo esc_attr($row['image1']['alt']); ?>"
                        loading="lazy"
                    />
                </div>
            <?php endif; ?>
            
            <?php if (!empty($row['image2']['url'])) : ?>
                <div class="gallery-image">
                    <img 
                        src="<?php echo esc_url($row['image2']['url']); ?>" 
                        alt="<?php echo esc_attr($row['image2']['alt']); ?>"
                        loading="lazy"
                    />
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
