<?php
/**
 * Order details
 * Custom template for My-E-Shop theme
 * 
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.6.0
 */

defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if ( ! $order ) {
	return;
}

$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads             = $order->get_downloadable_items();
$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();

if ( $show_downloads ) {
	wc_get_template(
		'order/order-downloads.php',
		array(
			'downloads'  => $downloads,
			'show_title' => true,
		)
	);
}
?>

<!-- Custom Order Confirmation Styles -->
<style>
/* Order Confirmation Page Styles */
body.woocommerce-order-received {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    font-family: 'SF Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.woocommerce-order-received .container {
    max-width: 900px;
    margin: 0 auto;
    padding: 40px 20px;
}

/* Order Summary Cards */
.order-summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 30px 0;
}

.summary-card {
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    padding: 25px;
    border-radius: 12px;
    text-align: center;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.summary-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--main-color), var(--main-light-color));
}

.summary-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.summary-card .label {
    font-size: 14px;
    color: var(--grey-color);
    margin-bottom: 8px;
    text-transform: uppercase;
    font-weight: 500;
    letter-spacing: 0.5px;
}

.summary-card .value {
    font-size: 18px;
    font-weight: 600;
    color: var(--black-color);
}

.summary-card.total .value {
    color: var(--main-color);
    font-size: 24px;
}

/* Order Details Section */
.custom-order-details {
    background: white;
    border-radius: 16px;
    padding: 40px;
    margin-bottom: 40px;
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.8);
}

.custom-order-details h2 {
    color: var(--black-color);
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 30px;
    text-align: center;
    position: relative;
    padding-bottom: 15px;
}

.custom-order-details h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, var(--main-color), var(--main-light-color));
    border-radius: 2px;
}

/* Order Items Table */
.shop_table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    margin-bottom: 30px;
}

.shop_table thead th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    color: var(--black-color);
    font-weight: 600;
    padding: 20px;
    text-align: left;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e9ecef;
}

.shop_table tbody td {
    padding: 20px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
}

.shop_table tbody tr:last-child td {
    border-bottom: none;
}

.shop_table tbody tr:hover {
    background: #f8f9fa;
}

.shop_table .product-name {
    font-weight: 600;
    color: var(--black-color);
}

.shop_table .product-total {
    font-weight: 600;
    color: var(--main-color);
    text-align: right;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .custom-order-details {
        padding: 25px 20px;
    }

    .order-summary-cards {
        grid-template-columns: 1fr;
    }

    .shop_table thead th,
    .shop_table tbody td {
        padding: 15px 10px;
        font-size: 14px;
    }

    .summary-card {
        padding: 20px 15px;
    }
}
</style>

<div class="custom-order-details">
    <!-- Order Summary Cards -->
    <div class="order-summary-cards">
        <div class="summary-card">
            <div class="label">Order Number</div>
            <div class="value">#<?php echo $order->get_order_number(); ?></div>
        </div>
        
        <div class="summary-card">
            <div class="label">Order Date</div>
            <div class="value"><?php echo wc_format_datetime($order->get_date_created(), 'M j, Y'); ?></div>
        </div>
        
        <div class="summary-card">
            <div class="label">Payment Method</div>
            <div class="value"><?php echo $order->get_payment_method_title(); ?></div>
        </div>
        
        <div class="summary-card total">
            <div class="label">Total</div>
            <div class="value"><?php echo $order->get_formatted_order_total(); ?></div>
        </div>
    </div>

    <h2 class="woocommerce-order-details__title"><?php esc_html_e( 'Order details', 'woocommerce' ); ?></h2>

    <table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
        <thead>
            <tr>
                <th class="woocommerce-table__product-name product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
                <th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php
            do_action( 'woocommerce_order_details_before_order_table_items', $order );

            foreach ( $order_items as $item_id => $item ) {
                $product = $item->get_product();

                wc_get_template(
                    'order/order-details-item.php',
                    array(
                        'order'              => $order,
                        'item_id'            => $item_id,
                        'item'               => $item,
                        'show_purchase_note' => $show_purchase_note,
                        'purchase_note'      => $product ? $product->get_purchase_note() : '',
                        'product'            => $product,
                    )
                );
            }

            do_action( 'woocommerce_order_details_after_order_table_items', $order );
            ?>
        </tbody>

        <tfoot>
            <?php
            foreach ( $order->get_order_item_totals() as $key => $total ) {
                ?>
                <tr>
                    <th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
                    <td><?php echo wp_kses_post( $total['value'] ); ?></td>
                </tr>
                <?php
            }
            ?>
            <?php if ( $order->get_customer_note() ) : ?>
                <tr>
                    <th><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
                    <td><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
                </tr>
            <?php endif; ?>
        </tfoot>
    </table>

    <?php if ( $order->get_payment_method() !== 'cod' ) : ?>
    <div style="background: linear-gradient(135deg, #e8f4fd 0%, #f0f8ff 100%); padding: 25px; border-radius: 12px; border: 1px solid rgba(66, 149, 228, 0.2); margin: 30px 0; text-align: center;">
        <h3 style="color: var(--main-color); margin-bottom: 15px; font-size: 18px; font-weight: 600;">💳 Payment Processed</h3>
        <p style="margin: 0;">Your payment has been successfully processed. You will receive a confirmation email shortly.</p>
    </div>
    <?php endif; ?>
</div>

<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>