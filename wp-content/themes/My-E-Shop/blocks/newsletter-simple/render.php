<?php
/**
 * Newsletter Simple Block Template
 */

// Extract attributes
$title = $attributes['title'] ?? 'Join our world and get 5% off your first order';
$subtitle = $attributes['subtitle'] ?? 'Stay updated with new drops, visual stories & rare finds before anyone else.';
$button_text = $attributes['buttonText'] ?? 'JOIN NOW';
$placeholder_text = $attributes['placeholderText'] ?? 'Enter your email';
$disclaimer_text = $attributes['disclaimerText'] ?? 'We only send thoughtful emails - no spam, just style. By subscribing, you agree to receive inspiration and exclusive privileges. Your data is safe with us.';

// Get the block attributes
$wrapper_attributes = get_block_wrapper_attributes(array(
    'class' => 'newsletter-simple-block'
));

// Generate a unique ID for the form
$form_id = 'newsletter-simple-' . wp_rand(1000, 9999);
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="newsletter-simple-container">
        <div class="newsletter-simple-content">
            <h2 class="newsletter-simple-title"><?php echo esc_html($title); ?></h2>
            <p class="newsletter-simple-subtitle"><?php echo esc_html($subtitle); ?></p>
            
            <form class="newsletter-simple-form" id="<?php echo esc_attr($form_id); ?>" method="post" action="">
                <?php wp_nonce_field('newsletter_subscription', 'newsletter_nonce'); ?>
                <div class="newsletter-simple-form-group">
                    <input 
                        type="email" 
                        name="subscriber_email" 
                        class="newsletter-simple-input" 
                        placeholder="<?php echo esc_attr($placeholder_text); ?>"
                        required
                    >
                    <button type="submit" class="newsletter-simple-button">
                        <?php echo esc_html($button_text); ?>
                    </button>
                </div>
                <div class="newsletter-simple-message" id="newsletter-message-<?php echo esc_attr($form_id); ?>"></div>
            </form>
            
            <?php if ($disclaimer_text) : ?>
                <p class="newsletter-simple-disclaimer"><?php echo esc_html($disclaimer_text); ?></p>
            <?php endif; ?>
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
            
            messageDiv.innerHTML = '<div class="newsletter-loading">Subscribing...</div>';
            
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
