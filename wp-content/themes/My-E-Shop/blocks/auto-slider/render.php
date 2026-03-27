<?php
/**
 * Auto Slider Block Template
 */

$images = $attributes['images'] ?? [];

if (empty($images)) {
    return;
}
?>

<div class="auto-slider-wrapper">
    <div class="auto-slider-track">
        <?php foreach ($images as $image) : 
            $url = $image['url'] ?? '';
            $alt = $image['alt'] ?? '';
            
            if (empty($url)) continue;
        ?>
            <div class="auto-slider-item">
                <img src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy">
            </div>
        <?php endforeach; ?>
    </div>
</div>
