<?php
/**
 * Multi-step Checkout Form Template
 * Compatible with WooCommerce Checkout with Product Images plugin
 * Place this file in: /woocommerce/checkout/form-checkout.php
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get current step
$current_step = isset($_GET['step']) ? sanitize_text_field($_GET['step']) : 'information';
$valid_steps = ['information', 'shipping', 'payment'];

if (!in_array($current_step, $valid_steps)) {
    $current_step = 'information';
}

// Define data for each step
$steps_data = [
    'information' => [
        'title' => 'Contact Information',
        'description' => 'Enter your contact details to proceed with the order',
        'number' => 1
    ],
    'shipping' => [
        'title' => 'Shipping Address',
        'description' => 'Please provide the address where you want your order delivered',
        'number' => 2
    ],
    'payment' => [
        'title' => 'Payment Method',
        'description' => 'Choose your preferred payment method to complete the order',
        'number' => 3
    ]
];

do_action('woocommerce_before_checkout_form', $checkout);

// If checkout registration is disabled and not logged in, the user cannot checkout.
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}
?>

<div class="checkout-container">
    <!-- Steps indicator -->
    <div class="checkout-steps-header">
        <div class="steps-navigation">
            <?php foreach ($steps_data as $step_key => $step_info) : ?>
                <div class="step-indicator <?php 
                    if ($step_key === $current_step) echo 'active';
                    elseif (($step_key === 'information' && in_array($current_step, ['shipping', 'payment'])) ||
                            ($step_key === 'shipping' && $current_step === 'payment')) echo 'completed';
                ?>">
                    <div class="step-number"><?php echo $step_info['number']; ?></div>
                    <div class="step-title"><?php echo $step_info['title']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Current step content -->
    <div class="checkout-step-content">
        <div class="step-header">
            <h2><?php echo $steps_data[$current_step]['title']; ?></h2>
            <p><?php echo $steps_data[$current_step]['description']; ?></p>
        </div>

        <?php if ($current_step === 'information') : ?>
            <!-- STEP 1: CONTACT INFORMATION -->
            <form class="checkout-step-form">
                <div class="form-row-half">
                    <div class="form-row">
                        <label for="billing_first_name">First Name <span style="color: var(--red-color);">*</span></label>
                        <input type="text" id="billing_first_name" name="billing_first_name" 
                               value="<?php echo esc_attr($checkout->get_value('billing_first_name')); ?>" required>
                    </div>
                    <div class="form-row">
                        <label for="billing_last_name">Last Name <span style="color: var(--red-color);">*</span></label>
                        <input type="text" id="billing_last_name" name="billing_last_name" 
                               value="<?php echo esc_attr($checkout->get_value('billing_last_name')); ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <label for="billing_email">Email Address <span style="color: var(--red-color);">*</span></label>
                    <input type="email" id="billing_email" name="billing_email" 
                           value="<?php echo esc_attr($checkout->get_value('billing_email')); ?>" required>
                </div>
                
                <div class="form-row">
                    <label for="billing_phone">Phone (optional)</label>
                    <input type="tel" id="billing_phone" name="billing_phone" 
                           value="<?php echo esc_attr($checkout->get_value('billing_phone')); ?>">
                </div>
                
                <div class="step-actions">
                    <a href="<?php echo wc_get_cart_url(); ?>" class="btn-step btn-back">
                        <i class="fas fa-arrow-left"></i> Return to Cart
                    </a>
                    <button type="button" class="btn-step btn-next" data-step="information">
                        Continue <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>

        <?php elseif ($current_step === 'shipping') : ?>
            <!-- STEP 2: SHIPPING ADDRESS -->
            <form class="checkout-step-form">
                <div class="form-row">
                    <label for="billing_country">Country <span style="color: var(--red-color);">*</span></label>
                    <select id="billing_country" name="billing_country" required>
                        <option value="">Select country</option>
                        <?php
                        $countries = WC()->countries->get_allowed_countries();
                        $selected_country = $checkout->get_value('billing_country');
                        foreach ($countries as $code => $name) {
                            echo '<option value="' . esc_attr($code) . '"' . selected($selected_country, $code, false) . '>' . esc_html($name) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-row-half">
                    <div class="form-row">
                        <label for="billing_city">City <span style="color: var(--red-color);">*</span></label>
                        <input type="text" id="billing_city" name="billing_city" 
                               value="<?php echo esc_attr($checkout->get_value('billing_city')); ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <label for="billing_postcode">Postal Code <span style="color: var(--red-color);">*</span></label>
                        <input type="text" id="billing_postcode" name="billing_postcode" 
                               value="<?php echo esc_attr($checkout->get_value('billing_postcode')); ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <label for="billing_address_1">Address <span style="color: var(--red-color);">*</span></label>
                    <input type="text" id="billing_address_1" name="billing_address_1" 
                           value="<?php echo esc_attr($checkout->get_value('billing_address_1')); ?>" 
                           placeholder="Street, house, apartment" required>
                </div>
                
                <div class="step-actions">
                    <a href="<?php echo wc_get_checkout_url(); ?>?step=information" class="btn-step btn-back">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button type="button" class="btn-step btn-next" data-step="shipping">
                        Continue <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>

<?php elseif ($current_step === 'payment') : ?>
            <!-- STEP 3: PAYMENT METHOD AND COMPLETION -->
            
            <!-- Order review summary -->
            <div class="order-review-summary">
                <h3>Order Summary</h3>
                <table class="shop_table">
                    <tbody>
                        <?php
                        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                            if ($_product && $_product->exists() && $cart_item['quantity'] > 0) {
                                ?>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #eee;">
                                        <strong><?php echo $_product->get_name(); ?></strong> × <?php echo $cart_item['quantity']; ?>
                                    </td>
                                    <td style="text-align: right; padding: 10px 0; border-bottom: 1px solid #eee;">
                                        <?php echo WC()->cart->get_product_subtotal($_product, $cart_item['quantity']); ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        <tr>
                            <td style="padding: 15px 0; font-weight: 600; font-size: 18px;">
                                <strong>Total:</strong>
                            </td>
                            <td style="text-align: right; padding: 15px 0; font-weight: 600; font-size: 18px; color: var(--main-color);">
                                <strong><?php wc_cart_totals_order_total_html(); ?></strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Final order form -->
            <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">
                
                <!-- IMPROVED: Hidden fields with saved data -->
                <?php
                // Get all saved data from session
                $information_data = WC()->session->get('checkout_step_information', array());
                $shipping_data = WC()->session->get('checkout_step_shipping', array());
                $all_saved_data = array_merge($information_data, $shipping_data);
                
                // Debug output
                if (WP_DEBUG) {
                    echo '<!-- Session data: ' . print_r($all_saved_data, true) . ' -->';
                }
                
                // Output all saved data as hidden fields
                foreach ($all_saved_data as $field_name => $field_value) {
                    if (!empty($field_value)) {
                        echo '<input type="hidden" name="' . esc_attr($field_name) . '" value="' . esc_attr($field_value) . '">';
                    }
                }
                
                // Also ensure we have the current step
                echo '<input type="hidden" name="current_checkout_step" value="payment">';
                ?>
                
                <!-- Standard WooCommerce checkout fields (hidden) -->
                <?php if ($checkout->get_checkout_fields()) : ?>
                    <div style="display: none;">
                        <?php do_action('woocommerce_checkout_billing'); ?>
                        <?php do_action('woocommerce_checkout_shipping'); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Shipping methods (if needed) -->
                <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
                <div class="shipping-methods-section">
                    <h3>Shipping Method</h3>
                    <div style="background: #f8f9fa; border-radius: 8px; margin-bottom: 10px;">
                        <?php wc_cart_totals_shipping_html(); ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Payment methods -->
                <div class="payment-methods-section">
                    <h3>Payment Method</h3>
                    
                    <?php if (WC()->cart->needs_payment()) : ?>
                        <div id="payment" class="woocommerce-checkout-payment">
                            <?php do_action('woocommerce_review_order_before_payment'); ?>
                            
                            <ul class="wc_payment_methods payment_methods methods">
                                <?php
                                $available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
                                if (!empty($available_gateways)) {
                                    foreach ($available_gateways as $gateway) {
                                        wc_get_template('checkout/payment-method.php', array('gateway' => $gateway));
                                    }
                                } else {
                                    echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . apply_filters('woocommerce_no_available_payment_methods_message', __('Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce')) . '</li>';
                                }
                                ?>
                            </ul>
                            
                            <div class="form-row place-order">
                                <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
                                
                                <!-- Terms and conditions checkbox -->
                                <?php do_action('woocommerce_checkout_terms_and_conditions'); ?>
                                
                                <!-- Additional fields before button -->
                                <?php do_action('woocommerce_review_order_before_submit'); ?>
                                
                                <div class="step-actions" style="margin-top: 30px;">
                                    <a href="<?php echo wc_get_checkout_url(); ?>?step=shipping" class="btn-step btn-back">
                                        <i class="fas fa-arrow-left"></i> Back
                                    </a>
                                    
                                    <button type="submit" class="btn-step btn-place-order" name="woocommerce_checkout_place_order" id="place_order" value="<?php esc_attr_e('Place Order', 'woocommerce'); ?>">
                                        <span class="order-button-text">Place Order</span>
                                        <span class="order-button-spinner" style="display: none;">
                                            <i class="fas fa-spinner fa-spin"></i> Processing...
                                        </span>
                                    </button>
                                </div>
                                
                                <?php do_action('woocommerce_review_order_after_submit'); ?>
                            </div>
                            
                            <?php do_action('woocommerce_review_order_after_payment'); ?>
                        </div>
                    <?php else : ?>
                        <div class="step-actions">
                            <a href="<?php echo wc_get_checkout_url(); ?>?step=shipping" class="btn-step btn-back">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            
                            <button type="submit" class="btn-step btn-place-order" name="woocommerce_checkout_place_order" id="place_order">
                                Complete Order <i class="fas fa-check"></i>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </form>

        <?php endif; ?>
    </div>
</div>

<!-- Additional scripts for payment step -->
<?php if ($current_step === 'payment') : ?>
<script>
jQuery(document).ready(function($) {
    // Handle final form submission
    $('#place_order').on('click', function() {
        var $button = $(this);
        $button.find('.order-button-text').hide();
        $button.find('.order-button-spinner').show();
        $button.prop('disabled', true);
        
        // If form doesn't submit (error), restore button state
        setTimeout(function() {
            if ($('.woocommerce-error').length > 0) {
                $button.find('.order-button-text').show();
                $button.find('.order-button-spinner').hide();
                $button.prop('disabled', false);
            }
        }, 5000);
    });
    
    // Payment method styling and radio button handling
    function initPaymentMethods() {
        $('.wc_payment_methods .wc_payment_method').each(function() {
            var $method = $(this);
            var $radio = $method.find('input[type="radio"]');
            
            // Клик по всему блоку метода оплаты
            $method.on('click', function(e) {
                // Если клик не был напрямую по радио-кнопке
                if (!$(e.target).is('input[type="radio"]')) {
                    $radio.prop('checked', true).trigger('click');
                }
            });
            
            // Обработка изменения радио-кнопки
            $radio.on('change click', function() {
                $('.wc_payment_methods .wc_payment_method').removeClass('selected');
                if ($(this).is(':checked')) {
                    $method.addClass('selected');
                }
            });
            
            // Проверяем выбранный метод при загрузке
            if ($radio.is(':checked')) {
                $method.addClass('selected');
            }
        });
    }
    
    // Инициализируем при загрузке
    initPaymentMethods();
    
    // Переинициализируем после обновления checkout
    $(document.body).on('updated_checkout payment_method_selected', function() {
        initPaymentMethods();
    });
});
</script>

<style>
/* Input error styling */
input.error,
select.error {
    border-color: var(--red-color) !important;
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
}

/* Additional styles for payment methods */
.wc_payment_methods {
    list-style: none;
    padding: 0;
    margin: 20px 0;
}

.wc_payment_method {
    margin-bottom: 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: #fff;
}

.wc_payment_method:hover,
.wc_payment_method.selected {
    border-color: var(--main-color);
    box-shadow: 0 4px 15px rgba(66, 149, 228, 0.1);
}

.wc_payment_method input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.wc_payment_method label {
    display: block;
    padding: 5px 10px;
    cursor: pointer;
    font-weight: 500;
    margin: 0;
    position: relative;
    transition: all 0.3s ease;
}

.wc_payment_method label::before {
    content: '';
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    width: 18px;
    height: 18px;
    border: 2px solid #ddd;
    border-radius: 50%;
    background: #fff;
    transition: all 0.3s ease;
}

.wc_payment_method label::after {
    content: '';
    position: absolute;
    right: 26px;
    top: 50%;
    transform: translateY(-50%);
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--main-color);
    opacity: 0;
    transition: all 0.3s ease;
}

.wc_payment_method.selected label {
    background: rgba(66, 149, 228, 0.08);
    color: var(--main-color);
    font-weight: 600;
}

.wc_payment_method.selected label::before {
    border-color: var(--main-color);
}

.wc_payment_method.selected label::after {
    opacity: 1;
}

.wc_payment_method .payment_box {
    padding: 5px 10px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
    display: none;
}

.wc_payment_method.selected .payment_box {
    display: block;
}

/* Payment security info */
.payment-security-info {
    background: #e8f5e8;
    padding: 15px 20px;
    border-radius: 6px;
    margin-top: 20px;
    text-align: center;
    border: 1px solid #d4edda;
}

.payment-security-info i {
    color: #28a745;
    margin-right: 8px;
}

.payment-security-info span {
    color: #155724;
    font-size: 14px;
    font-weight: 500;
}

/* Order button improvements */
.btn-place-order {
    position: relative;
    min-width: 200px;
}

.btn-place-order:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none !important;
}

/* Terms and conditions styling */
.woocommerce-terms-and-conditions-wrapper {
    margin: 20px 0;
    padding: 5px 10px;
    background: #f8f9fa;
    border-radius: 6px;
    border: 1px solid #e9ecef;
}

.woocommerce-terms-and-conditions-checkbox-text {
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.woocommerce-terms-and-conditions-checkbox-text input[type="checkbox"] {
    margin-top: 4px;
    accent-color: var(--main-color);
}

/* Shipping methods styling */
.shipping-methods-section h3 {
    color: var(--black-color);
    font-weight: 600;
    font-family: 'Montserrat', sans-serif;
    font-size: 18px;
}

.woocommerce-shipping-methods {
    list-style: none;
    padding: 0;
}

.woocommerce-shipping-methods li {
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.woocommerce-shipping-methods li:last-child {
    border-bottom: none;
}

.woocommerce-shipping-methods input[type="radio"] {
    margin-right: 10px;
    accent-color: var(--main-color);
}
</style>
<?php endif; ?>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>