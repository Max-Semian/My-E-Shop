<?php
/**
 * Why Choose Us Block Template
 */

// Получаем атрибуты блока
$title = !empty($attributes['title']) ? $attributes['title'] : 'Why choose us';
$items = !empty($attributes['items']) ? $attributes['items'] : [];
$icon_size = !empty($attributes['iconSize']) ? $attributes['iconSize'] : 24;

// Если нет элементов, не выводим блок
if (empty($items)) {
    return;
}
?>

<section class="why-choose-us">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="about-section-title text-center"><?php echo esc_html($title); ?></h2>
            </div>
        </div>
        <div class="row features-row">
            <?php foreach ($items as $index => $item) : 
                $question = !empty($item['question']) ? $item['question'] : '';
                $answer = !empty($item['answer']) ? $item['answer'] : '';
                $icon = !empty($item['icon']) ? $item['icon'] : '❓';
                $icon_type = !empty($item['iconType']) ? $item['iconType'] : 'text';
                $icon_image = !empty($item['iconImage']) ? $item['iconImage'] : '';
                $icon_image_id = !empty($item['iconImageId']) ? $item['iconImageId'] : 0;
            ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-item text-center">
                    <div class="feature-icon">
                        <?php if ($icon_type === 'image' && $icon_image_id) : 
                            $image_url = wp_get_attachment_image_url($icon_image_id, 'thumbnail');
                            if ($image_url) :
                        ?>
                            <img src="<?php echo esc_url($image_url); ?>" 
                                 alt="<?php echo esc_attr($question); ?>"
                                 style="width: <?php echo esc_attr($icon_size); ?>px; height: <?php echo esc_attr($icon_size); ?>px; object-fit: cover;">
                        <?php 
                            else :
                                echo '<span style="font-size: ' . esc_attr($icon_size) . 'px;">' . esc_html($icon) . '</span>';
                            endif;
                        else : 
                            echo '<span style="font-size: ' . esc_attr($icon_size) . 'px;">' . esc_html($icon) . '</span>';
                        endif; 
                        ?>
                    </div>
                    <h3 class="feature-title"><?php echo esc_html($question); ?></h3>
                    <p class="feature-description">
                        <?php echo wp_kses_post($answer); ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
