<?php
/**
 * Fashion Hero Block Template
 */

// Извлекаем атрибуты
$title = $attributes['title'] ?? 'Fashioned for Your Energy';
$subtitle = $attributes['subtitle'] ?? 'Designer T-shirts inspired by the catwalk and made for self-expression';
$button_text = $attributes['buttonText'] ?? 'Shop Now';
$button_url = $attributes['buttonUrl'] ?? '#';
$images_data = $attributes['images'] ?? array();
$background_image = $attributes['backgroundImage'] ?? array();
$overlay_opacity = isset($attributes['overlayOpacity']) ? $attributes['overlayOpacity'] : 50;

// Проверяем, есть ли фоновое изображение
$has_background_image = !empty($background_image['url']);

// Строим массив изображений для 7 позиций (всегда показываем их)
$images = array();
for ($i = 1; $i <= 7; $i++) {
    $image_key = 'image' . $i;
    if (isset($images_data[$image_key]) && !empty($images_data[$image_key])) {
        $images[] = $images_data[$image_key];
    } else {
        // Изображения по умолчанию
        $images[] = array(
            'url' => get_template_directory_uri() . '/assets/img/fashion-' . $i . '.jpg',
            'alt' => 'Fashion Model ' . $i
        );
    }
}

// Если URL кнопки не задан, используем URL магазина WooCommerce
if ($button_url === '#' && function_exists('wc_get_page_id')) {
    $button_url = get_permalink(wc_get_page_id('shop'));
}

// Получаем атрибуты блока
$wrapper_attributes = get_block_wrapper_attributes();
?>

<section <?php echo $wrapper_attributes; ?> class="fashion-hero-section">
    <div class="fashion-hero-container">
        <?php if ($has_background_image) : ?>
            <!-- Main Background Image (under 7 photos) -->
            <div class="fashion-hero-background" style="background-image: url('<?php echo esc_url($background_image['url']); ?>');">
            </div>
        <?php endif; ?>
        
        <!-- 7 Fashion Images Grid (always visible) -->
        <div class="fashion-images-grid">
            <?php foreach ($images as $index => $image) : ?>
                <?php 
                // На мобильных устройствах показываем только первые 6 изображений
                $mobile_class = ($index >= 6) ? 'fashion-mobile-hidden' : '';
                ?>
                <div class="fashion-image-card fashion-image-<?php echo ($index + 1); ?> <?php echo $mobile_class; ?>">
                    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt'] ?: 'Fashion Model ' . ($index + 1)); ?>">
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Dark Overlay -->
        <div class="fashion-hero-overlay" style="opacity: <?php echo esc_attr($overlay_opacity / 100); ?>;"></div>

        <!-- Content -->
        <div class="fashion-hero-content">
            <h1 class="fashion-hero-title"><?php echo esc_html($title); ?></h1>
            <p class="fashion-hero-subtitle"><?php echo esc_html($subtitle); ?></p>
            <?php if (!empty($button_url) && $button_url !== '#') : ?>
                <a href="<?php echo esc_url($button_url); ?>" class="fashion-hero-btn">
                    <?php echo esc_html($button_text); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
