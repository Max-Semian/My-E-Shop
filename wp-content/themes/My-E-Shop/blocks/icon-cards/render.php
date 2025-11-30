<?php
/**
 * Icon Cards Block Template
 */

$mainTitle = $attributes['mainTitle'] ?? '';
$cards = $attributes['cards'] ?? [];

if (empty($cards)) {
    return;
}
?>

<div class="icon-cards-wrapper">
    <?php if (!empty($mainTitle)) : ?>
        <h2 class="icon-cards-main-title"><?php echo esc_html($mainTitle); ?></h2>
    <?php endif; ?>
    
    <div class="icon-cards-block">
    <?php foreach ($cards as $index => $card) : 
        $icon = $card['icon'] ?? [];
        $iconUrl = $icon['url'] ?? '';
        $iconAlt = $icon['alt'] ?? '';
        $title = $card['title'] ?? '';
        $description = $card['description'] ?? '';
    ?>
        <div class="icon-card">
            <?php if (!empty($iconUrl)) : ?>
                <img src="<?php echo esc_url($iconUrl); ?>" alt="<?php echo esc_attr($iconAlt); ?>" class="icon-card-icon">
            <?php endif; ?>
            
            <?php if (!empty($title)) : ?>
                <h3 class="icon-card-title"><?php echo esc_html($title); ?></h3>
            <?php endif; ?>
            
            <?php if (!empty($description)) : ?>
                <p class="icon-card-description"><?php echo nl2br(esc_html($description)); ?></p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    </div>
</div>
