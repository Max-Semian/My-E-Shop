<?php
/**
 * Newsletter Subscription Block Template
 */

// Get block attributes with defaults
$background_image = isset($attributes['backgroundImage']) ? $attributes['backgroundImage'] : '';
$main_title = isset($attributes['mainTitle']) ? $attributes['mainTitle'] : 'Data-Driven Self-Expression';
$offer_title = isset($attributes['offerTitle']) ? $attributes['offerTitle'] : 'Join our world and get 5% off your first order';
$description = isset($attributes['description']) ? $attributes['description'] : 'We send back only good vibes. Just a mix of what\'s hot and rare finds.';
$placeholder_text = isset($attributes['placeholderText']) ? $attributes['placeholderText'] : 'Enter your email here';
$button_text = isset($attributes['buttonText']) ? $attributes['buttonText'] : 'JOIN NOW';
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
    <div class="my-e-shop-newsletter-subscription-container" 
         style="<?php echo $background_image ? 'background-image: url(' . esc_url($background_image) . ');' : ''; ?> color: <?php echo esc_attr($text_color); ?>;">
        
        <?php if ($background_image) : ?>
            <div class="my-e-shop-newsletter-overlay" style="background-color: rgba(0, 0, 0, 0);"></div>
        <?php endif; ?>
        
        <div class="my-e-shop-newsletter-content">
            <div class="my-e-shop-newsletter-left">
                <?php if (!empty($main_title)) : ?>
                    <h2 class="my-e-shop-newsletter-main-title" style="color: <?php echo esc_attr($text_color); ?>;">
                        <?php echo esc_html($main_title); ?>
                    </h2>
                <?php endif; ?>
                
                <div class="my-e-shop-read-blog-button">
                    READ IN BLOG →
                </div>
            </div>
            
            <div class="my-e-shop-newsletter-right">
                <?php if (!empty($offer_title)) : ?>
                    <h3 class="my-e-shop-newsletter-offer-title" style="color: <?php echo esc_attr($text_color); ?>;">
                        <?php echo esc_html($offer_title); ?>
                    </h3>
                <?php endif; ?>
                
                <?php if (!empty($description)) : ?>
                    <p class="my-e-shop-newsletter-description" style="color: <?php echo esc_attr($text_color); ?>;">
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
            </div>
        </div>
    </div>
</div>