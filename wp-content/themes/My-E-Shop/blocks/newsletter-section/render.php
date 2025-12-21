<?php
/**
 * Newsletter Section Block Template
 */

// Извлекаем атрибуты
$title = $attributes['title'] ?? 'Join our world and get 5% off your first order';
$subtitle = $attributes['subtitle'] ?? 'Stay up to date with our news and get exclusive promotional content';
$button_text = $attributes['buttonText'] ?? 'SUBSCRIBE';
$placeholder_text = $attributes['placeholderText'] ?? 'Enter your email here...';
$background_image = $attributes['backgroundImage'] ?? '';
$background_color = $attributes['backgroundColor'] ?? '#2c2c2c';
$text_color = $attributes['textColor'] ?? '#ffffff';
$overlay = $attributes['overlay'] ?? 0.7;

// Получаем атрибуты блока
$wrapper_attributes = get_block_wrapper_attributes(array(
    'class' => 'newsletter-section'
));

// Генерируем уникальный ID для формы
$form_id = 'newsletter-form-' . wp_rand(1000, 9999);
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="newsletter-background" style="
        background-image: <?php echo $background_image ? 'url(' . esc_url($background_image) . ')' : 'none'; ?>;
        background-color: <?php echo esc_attr($background_color); ?>;
        color: <?php echo esc_attr($text_color); ?>;
    ">
        <?php if ($background_image) : ?>
           
        <?php endif; ?>
        
        <div class="container">
            <div class="newsletter-content">
                <h2 class="newsletter-title"><?php echo esc_html($title); ?></h2>
                <p class="newsletter-subtitle"><?php echo esc_html($subtitle); ?></p>
                
                <form class="newsletter-form" id="<?php echo esc_attr($form_id); ?>" method="post" action="">
                    <?php wp_nonce_field('newsletter_subscription', 'newsletter_nonce'); ?>
                    <div class="newsletter-form-group">
                        <input 
                            type="email" 
                            name="subscriber_email" 
                            class="newsletter-input" 
                            placeholder="<?php echo esc_attr($placeholder_text); ?>"
                            required
                        >
                        <button type="submit" class="newsletter-button">
                            <?php echo esc_html($button_text); ?>
                        </button>
                    </div>
                    <div class="newsletter-message" id="newsletter-message-<?php echo esc_attr($form_id); ?>"></div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('<?php echo esc_js($form_id); ?>');
    const messageDiv = document.getElementById('newsletter-message-<?php echo esc_js($form_id); ?>');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            formData.append('action', 'newsletter_subscription');
            
            // Показываем загрузку
            messageDiv.innerHTML = '<div class="newsletter-loading">Subscribing...</div>';
            
            // Отправляем AJAX запрос
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageDiv.innerHTML = '<div class="newsletter-success">' + data.data.message + '</div>';
                    form.reset();
                } else {
                    messageDiv.innerHTML = '<div class="newsletter-error">' + data.data.message + '</div>';
                }
            })
            .catch(error => {
                messageDiv.innerHTML = '<div class="newsletter-error">An error occurred. Please try again.</div>';
            });
        });
    }
});
</script>