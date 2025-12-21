<?php
/**
 * Newsletter Subscription Block Template
 */

// Get block attributes with defaults
$background_image = isset($attributes['backgroundImage']) ? $attributes['backgroundImage'] : '';
$offer_title = isset($attributes['offerTitle']) ? $attributes['offerTitle'] : 'Join our world and get 5% off your first order';
$description = isset($attributes['description']) ? $attributes['description'] : 'Stay updated with new drops, visual stories & rare finds before anyone else.';
$placeholder_text = isset($attributes['placeholderText']) ? $attributes['placeholderText'] : 'Enter your email here';
$button_text = isset($attributes['buttonText']) ? $attributes['buttonText'] : 'JOIN NOW';
$disclaimer_text = isset($attributes['disclaimerText']) ? $attributes['disclaimerText'] : 'We send style, not spam. Subscribe for inspiration and exclusive perks—your data is safe.';
$button_color = isset($attributes['buttonColor']) ? $attributes['buttonColor'] : '#AA2DD0';
$text_color = isset($attributes['textColor']) ? $attributes['textColor'] : '#000000';

// Build CSS custom properties
$css_vars = array(
    '--newsletter-button-color: ' . esc_attr($button_color),
    '--newsletter-text-color: ' . esc_attr($text_color)
);

// Get wrapper attributes
$wrapper_attributes = get_block_wrapper_attributes(array(
    'class' => 'my-e-shop-newsletter-subscription-block',
    'style' => implode('; ', $css_vars)
));

?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="my-e-shop-newsletter-subscription-container">
        
        <!-- Левая часть - изображение -->
        <div class="my-e-shop-newsletter-left">
            <?php if ($background_image) : ?>
                <img src="<?php echo esc_url($background_image); ?>" alt="Newsletter" class="my-e-shop-newsletter-image">
            <?php endif; ?>
        </div>
        
        <!-- Правая часть - форма -->
        <div class="my-e-shop-newsletter-right">
            <?php if (!empty($offer_title)) : ?>
                <h3 class="my-e-shop-newsletter-offer-title">
                    <?php echo esc_html($offer_title); ?>
                </h3>
            <?php endif; ?>
            
            <?php if (!empty($description)) : ?>
                <p class="my-e-shop-newsletter-description">
                    <?php echo esc_html($description); ?>
                </p>
            <?php endif; ?>
            
            <form class="my-e-shop-newsletter-form" method="post" action="">
                <?php wp_nonce_field('newsletter_subscription', 'newsletter_nonce'); ?>
                <div class="my-e-shop-newsletter-form-group">
                    <input type="email" 
                           name="newsletter_email" 
                           placeholder="<?php echo esc_attr($placeholder_text); ?>" 
                           class="my-e-shop-newsletter-email-input" 
                           required>
                    <button type="submit" 
                            name="newsletter_submit"
                            class="my-e-shop-newsletter-submit-button">
                        <?php echo esc_html($button_text); ?>
                    </button>
                </div>
            </form>
            
            <?php if (!empty($disclaimer_text)) : ?>
                <p class="my-e-shop-newsletter-disclaimer">
                    <?php echo esc_html($disclaimer_text); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>