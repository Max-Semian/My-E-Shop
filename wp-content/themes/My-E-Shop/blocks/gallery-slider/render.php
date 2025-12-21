<?php
/**
 * Gallery Slider Block Template
 */

// Получаем атрибуты блока
$title = !empty($attributes['title']) ? $attributes['title'] : 'Gallery of Inspiration';
$subtitle = !empty($attributes['subtitle']) ? $attributes['subtitle'] : 'Create your own archetype lookbook';
$images = !empty($attributes['images']) ? $attributes['images'] : [];
$background_color = !empty($attributes['backgroundColor']) ? $attributes['backgroundColor'] : '#ffffff';
$title_color = !empty($attributes['titleColor']) ? $attributes['titleColor'] : '#000000';
$subtitle_color = !empty($attributes['subtitleColor']) ? $attributes['subtitleColor'] : '#666666';
$animation_speed = !empty($attributes['animationSpeed']) ? $attributes['animationSpeed'] : 50;
$card_width = !empty($attributes['cardWidth']) ? $attributes['cardWidth'] : 280;
$card_height = !empty($attributes['cardHeight']) ? $attributes['cardHeight'] : 492;

// Генерируем уникальный ID для блока
$block_id = 'gallery-slider-' . wp_rand(1000, 9999);

// Если нет изображений, не выводим блок
if (empty($images)) {
    return;
}
?>

<section class="collections gallery-slider-block" id="<?php echo esc_attr($block_id); ?>" style="
    --gallery-bg-color: <?php echo esc_attr($background_color); ?>;
    --gallery-title-color: <?php echo esc_attr($title_color); ?>;
    --gallery-subtitle-color: <?php echo esc_attr($subtitle_color); ?>;
    --gallery-animation-speed: <?php echo esc_attr($animation_speed); ?>s;
    --gallery-card-width: <?php echo esc_attr($card_width); ?>px;
    --gallery-card-height: <?php echo esc_attr($card_height); ?>px;
    background-color: var(--gallery-bg-color);
">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="section-title" style="color: var(--gallery-title-color);">
                    <span><?php echo esc_html($title); ?></span>
                </h2>
                <p class="section-description" style="color: var(--gallery-subtitle-color);">
                    <span><?php echo esc_html($subtitle); ?></span>
                </p>
            </div>
        </div>
        
        <div class="collections-row">
            <div class="collections-slider-section">
                <div class="collections-horizontal-carousel">
                    <div class="collections-smooth-track" style="
                        --track-width: <?php echo esc_attr(count($images) * 2 * ($card_width + 15)); ?>px;
                    ">
                        <?php 
                        // Выводим оригинальные изображения
                        foreach ($images as $image) : 
                            // Используем URL из атрибутов, если есть, иначе получаем из медиабиблиотеки
                            $image_url = !empty($image['url']) ? $image['url'] : wp_get_attachment_image_url($image['id'], 'medium_large');
                            $image_alt = !empty($image['alt']) ? $image['alt'] : '';
                            
                            if ($image_url) :
                        ?>
                        <div class="collections-square-card">
                            <img src="<?php echo esc_url($image_url); ?>" 
                                 alt="<?php echo esc_attr($image_alt); ?>" 
                                 class="collections-square-image">
                        </div>
                        <?php 
                            endif;
                        endforeach; 
                        
                        // Выводим дублированные изображения для бесшовной петли
                        foreach ($images as $image) : 
                            // Используем URL из атрибутов, если есть, иначе получаем из медиабиблиотеки
                            $image_url = !empty($image['url']) ? $image['url'] : wp_get_attachment_image_url($image['id'], 'medium_large');
                            $image_alt = !empty($image['alt']) ? $image['alt'] : '';
                            
                            if ($image_url) :
                        ?>
                        <div class="collections-square-card">
                            <img src="<?php echo esc_url($image_url); ?>" 
                                 alt="<?php echo esc_attr($image_alt); ?>" 
                                 class="collections-square-image">
                        </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
