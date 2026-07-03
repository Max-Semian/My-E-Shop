<?php
/**
 * My Account navigation (My-E-Shop theme override)
 *
 * Adds a Font Awesome icon next to each account menu item.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );

$account_menu_icons = array(
	'dashboard'       => 'fa-gauge-high',
	'orders'          => 'fa-bag-shopping',
	'downloads'       => 'fa-download',
	'edit-address'    => 'fa-location-dot',
	'payment-methods' => 'fa-credit-card',
	'edit-account'    => 'fa-user',
	'customer-logout' => 'fa-right-from-bracket',
);
?>

<nav class="woocommerce-MyAccount-navigation" aria-label="<?php esc_attr_e( 'Account pages', 'woocommerce' ); ?>">
	<ul>
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
			<?php $icon = isset( $account_menu_icons[ $endpoint ] ) ? $account_menu_icons[ $endpoint ] : 'fa-circle-dot'; ?>
			<li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" <?php echo wc_is_current_account_menu_item( $endpoint ) ? 'aria-current="page"' : ''; ?>>
					<i class="fas <?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
					<span><?php echo esc_html( $label ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
