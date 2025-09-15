<?php
/**
 * Why Choose Us Block Template
 */

// Получаем атрибуты блока
$title = !empty($attributes['title']) ? $attributes['title'] : 'Why choose us';
$items = !empty($attributes['items']) ? $attributes['items'] : [];
$icon_size = !empty($attributes['iconSize']) ? $attributes['iconSize'] : 24;

// Генерируем уникальный ID для блока
$block_id = 'why-choose-us-' . wp_rand(1000, 9999);

// Если нет элементов, не выводим блок
if (empty($items)) {
    return;
}
?>

<section class="why-choose-us-block" id="<?php echo esc_attr($block_id); ?>" style="
    --why-icon-size: <?php echo esc_attr($icon_size); ?>px;
">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="about-section-title text-center">
                    <?php echo esc_html($title); ?>
                </h2>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="accordion-container">
                    <?php foreach ($items as $index => $item) : 
                        $item_id = $block_id . '-item-' . $index;
                        $question = !empty($item['question']) ? $item['question'] : '';
                        $answer = !empty($item['answer']) ? $item['answer'] : '';
                        $icon = !empty($item['icon']) ? $item['icon'] : '❓';
                        $icon_type = !empty($item['iconType']) ? $item['iconType'] : 'text';
                        $icon_image = !empty($item['iconImage']) ? $item['iconImage'] : '';
                        $icon_image_id = !empty($item['iconImageId']) ? $item['iconImageId'] : 0;
                    ?>
                    <div class="accordion-item" data-item-id="<?php echo esc_attr($item_id); ?>">
                        <div class="accordion-header" 
                             style="
                                 background-color: var(--why-question-bg);
                                 color: var(--why-question-color);
                                 border: 1px solid var(--why-border-color);
                             "
                             data-toggle="<?php echo esc_attr($item_id); ?>"
                             role="button"
                             tabindex="0"
                             aria-expanded="false"
                             aria-controls="<?php echo esc_attr($item_id); ?>">
                            <div class="accordion-icon" style="font-size: var(--why-icon-size);">
                                <?php if ($icon_type === 'image' && $icon_image_id) : 
                                    $image_url = wp_get_attachment_image_url($icon_image_id, 'thumbnail');
                                    if ($image_url) :
                                ?>
                                    <img src="<?php echo esc_url($image_url); ?>" 
                                         alt="" 
                                         class="accordion-icon-image"
                                         style="
                                             width: var(--why-icon-size);
                                             height: var(--why-icon-size);
                                             object-fit: cover;
                                             border-radius: 4px;
                                         ">
                                <?php 
                                    else :
                                        echo esc_html($icon);
                                    endif;
                                else : 
                                    echo esc_html($icon); 
                                endif; 
                                ?>
                            </div>
                            <h3 class="accordion-question">
                                <?php echo esc_html($question); ?>
                            </h3>
                            <div class="accordion-toggle">
                                <span class="accordion-plus">+</span>
                            </div>
                        </div>
                        
                        <div class="accordion-content" 
                             id="<?php echo esc_attr($item_id); ?>"
                             style="
                                 background-color: var(--why-answer-bg);
                                 color: var(--why-answer-color);
                                 border-left: 1px solid var(--why-border-color);
                                 border-right: 1px solid var(--why-border-color);
                                 border-bottom: 1px solid var(--why-border-color);
                             "
                             aria-hidden="true">
                            <div class="accordion-answer">
                                <?php echo wp_kses_post(wpautop($answer)); ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
