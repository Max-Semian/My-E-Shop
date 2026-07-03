<?php
/**
 * My Account Dashboard (My-E-Shop theme override)
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$allowed_html = array(
	'a' => array( 'href' => array() ),
);
?>

<div class="account-dashboard">

	<div class="account-dashboard__greeting">
		<h2><?php printf( esc_html__( 'Hello, %s', 'my-e-shop' ), esc_html( $current_user->display_name ) ); ?> 👋</h2>
		<p>
			<?php
			printf(
				/* translators: 1: user display name 2: logout url */
				wp_kses( __( 'Not %1$s? <a href="%2$s">Log out</a>', 'my-e-shop' ), $allowed_html ),
				esc_html( $current_user->display_name ),
				esc_url( wc_logout_url() )
			);
			?>
		</p>
	</div>

	<div class="account-dashboard__cards">
		<a class="account-card" href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>">
			<i class="fas fa-bag-shopping" aria-hidden="true"></i>
			<span><?php esc_html_e( 'Orders', 'my-e-shop' ); ?></span>
		</a>
		<a class="account-card" href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>">
			<i class="fas fa-location-dot" aria-hidden="true"></i>
			<span><?php esc_html_e( 'Addresses', 'my-e-shop' ); ?></span>
		</a>
		<a class="account-card" href="<?php echo esc_url( wc_get_endpoint_url( 'edit-account' ) ); ?>">
			<i class="fas fa-user" aria-hidden="true"></i>
			<span><?php esc_html_e( 'Account details', 'my-e-shop' ); ?></span>
		</a>
		<a class="account-card" href="<?php echo esc_url( home_url( '/wishlist' ) ); ?>">
			<i class="fas fa-heart" aria-hidden="true"></i>
			<span><?php esc_html_e( 'Wishlist', 'my-e-shop' ); ?></span>
		</a>
	</div>

</div>

<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

	// Deprecated hooks kept for compatibility.
	do_action( 'woocommerce_before_my_account' );
	do_action( 'woocommerce_after_my_account' );
