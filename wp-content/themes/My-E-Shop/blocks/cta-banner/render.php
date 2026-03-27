<?php
$background_image = isset($attributes['backgroundImage']) ? esc_url($attributes['backgroundImage']) : '';
$title = isset($attributes['title']) ? esc_html($attributes['title']) : 'Ready to find your magical T-shirt?';
$subtitle = isset($attributes['subtitle']) ? esc_html($attributes['subtitle']) : 'Explore our collections and let your intuition choose for you';
$button_text = isset($attributes['buttonText']) ? esc_html($attributes['buttonText']) : 'Shop Now';
$button_link = isset($attributes['buttonLink']) ? esc_url($attributes['buttonLink']) : '#';

$banner_style = $background_image ? 'background-image: url(' . $background_image . ');' : '';
?>

<div class="cta-banner-wrapper">
    <div class="cta-banner" style="<?php echo $banner_style; ?>">
        <div class="cta-banner-content">
            <h2 class="cta-banner-title"><?php echo $title; ?></h2>
            <p class="cta-banner-subtitle"><?php echo $subtitle; ?></p>
            <a href="<?php echo $button_link; ?>" class="cta-banner-button"><?php echo $button_text; ?></a>
        </div>
    </div>
</div>
