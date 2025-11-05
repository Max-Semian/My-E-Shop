<?php
/**
 * Thankyou page
 * Custom template for My-E-Shop theme
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.1.0
 */

defined( 'ABSPATH' ) || exit;
?>

<style>
/* Thankyou Page Global Styles */
body.woocommerce-order-received {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    font-family: 'SF Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    margin: 0;
    padding: 0;
}

.woocommerce-order-received .container {
    max-width: 900px;
    margin: 0 auto;
}

/* Success Message */
.woocommerce-notice--success {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    color: #2d3748;
    padding: 40px;
    border-radius: 20px;
    text-align: center;
    margin-bottom: 40px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    position: relative;
    overflow: hidden;
}

.woocommerce-notice--success::before {
    content: '✓';
    position: absolute;
    top: -30px;
    right: -30px;
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #48bb78, #38a169);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 50px;
    font-weight: bold;
    color: white;
    animation: successCheckmark 1s ease 0.5s both;
}

.woocommerce-notice--success::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 20%, rgba(72, 187, 120, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(102, 126, 234, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 60%, rgba(118, 75, 162, 0.05) 0%, transparent 50%);
    pointer-events: none;
}

.woocommerce-notice--success h1,
.woocommerce-notice--success p {
    position: relative;
    z-index: 2;
}

.woocommerce-notice--success h1 {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 15px;
    color: #2d3748;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.woocommerce-notice--success p {
    font-size: 18px;
    margin-bottom: 0;
    color: #4a5568;
    line-height: 1.6;
}

/* Content Container */
.thankyou-content {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 0;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    overflow: hidden;
    animation: fadeInUp 0.8s ease 0.3s both;
}

/* Animation Keyframes */
@keyframes successCheckmark {
    0% {
        opacity: 0;
        transform: scale(0) rotate(-45deg);
    }
    50% {
        opacity: 1;
        transform: scale(1.2) rotate(-10deg);
    }
    100% {
        opacity: 1;
        transform: scale(1) rotate(0deg);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .container {
        padding: 20px 15px;
    }

    .woocommerce-notice--success {
        padding: 30px 20px;
    }

    .woocommerce-notice--success h1 {
        font-size: 24px;
    }

    .woocommerce-notice--success p {
        font-size: 16px;
    }

    .woocommerce-notice--success::before {
        width: 70px;
        height: 70px;
        font-size: 35px;
        top: -20px;
        right: -20px;
    }
}

/* Hide default customer details to prevent duplication */
.woocommerce-customer-details {
    background: transparent;
    padding: 0;
}

/* Override any theme conflicts */
.woocommerce-order-received .woocommerce-order {
    background: transparent;
    padding: 0;
    margin: 0;
}
</style>

<div class="woocommerce-order">

    <?php
    if ( $order ) :

        do_action( 'woocommerce_before_thankyou', $order->get_id() );
        ?>

        <?php if ( $order->has_status( 'failed' ) ) : ?>

            <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

            <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
                <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
                <?php if ( is_user_logged_in() ) : ?>
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
                <?php endif; ?>
            </p>

        <?php else : ?>

            <div class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received" style="animation: slideInDown 0.6s ease;">
                <h1><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ), $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
                <p><?php esc_html_e( 'We are getting started on your order right away, and you will receive an order confirmation email shortly.', 'woocommerce' ); ?></p>
            </div>

            

        <?php endif; ?>

        <?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

    <?php else : ?>

        <div class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">
            <h1><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ), null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
            <p><?php esc_html_e( 'Your order details will appear here once we have processed your request.', 'woocommerce' ); ?></p>
        </div>

    <?php endif; ?>

</div>

<script>
// Add some interactive effects
document.addEventListener('DOMContentLoaded', function() {
    // Add floating animation to success message
    const successMessage = document.querySelector('.woocommerce-notice--success');
    if (successMessage) {
        let startTime = Date.now();
        function animate() {
            const elapsed = Date.now() - startTime;
            const float = Math.sin(elapsed / 1000) * 2;
            successMessage.style.transform = `translateY(${float}px)`;
            requestAnimationFrame(animate);
        }
        setTimeout(animate, 2000); // Start floating after initial animations
    }

    // Add sparkle effect on hover for action buttons
    const buttons = document.querySelectorAll('.order-actions .button');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.05)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(-2px) scale(1)';
        });
    });
});
</script>