<?php
/**
 * Why Choose Us Block Template
 */

// Get block attributes
$title = !empty($attributes['title']) ? $attributes['title'] : 'Why choose us';
$items = !empty($attributes['items']) ? $attributes['items'] : [];
$icon_size = !empty($attributes['iconSize']) ? $attributes['iconSize'] : 24;

// Temporary debug (remove after checking)
if (current_user_can('administrator')) {
    echo '<!-- DEBUG: ' . json_encode($items) . ' -->';
}

// If there are no items, do not render the block
if (empty($items)) {
    return;
}
?>

<section class="why-choose-us">
    <div class="container">
        <div class="row">
                <h2 class="about-section-title text-center"><?php echo esc_html($title); ?></h2>
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
                        <?php 
                        // Check different image options
                        $image_displayed = false;
                        
                        // Option 1: Use image ID
                        if ($icon_type === 'image' && $icon_image_id) {
                            $image_url = wp_get_attachment_image_url($icon_image_id, 'thumbnail');
                            if ($image_url) {
                                echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($question) . '" style="width: ' . esc_attr($icon_size) . 'px; height: auto; object-fit: cover; border-radius: 4px;">';
                                $image_displayed = true;
                            }
                        }
                        
                        // Option 2: Use direct image link
                        if (!$image_displayed && $icon_type === 'image' && $icon_image) {
                            echo '<img src="' . esc_url($icon_image) . '" alt="' . esc_attr($question) . '" style="width: ' . esc_attr($icon_size) . 'px; height: auto; object-fit: cover; border-radius: 4px;">';
                            $image_displayed = true;
                        }
                        
                        // Option 3: Fallback to text/emoji
                        if (!$image_displayed) {
                            $fallback_icon = !empty($icon) ? $icon : '🔧';
                            echo '<span style="font-size: ' . esc_attr($icon_size) . 'px; display: inline-block;">' . esc_html($fallback_icon) . '</span>';
                        }
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
