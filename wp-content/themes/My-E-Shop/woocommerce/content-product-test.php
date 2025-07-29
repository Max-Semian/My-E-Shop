<?php
/**
 * The template for displaying product content within loops
 *
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Check if the product is valid and visible.
if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}
?>

<div <?php wc_product_class( 'modern-product-card', $product ); ?>>
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' );
	?>
	
	<div class="modern-product-image">
		<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
			<?php
			/**
			 * Hook: woocommerce_before_shop_loop_item_title.
			 * @hooked woocommerce_show_product_loop_sale_flash - 10
			 * @hooked woocommerce_template_loop_product_thumbnail - 10
			 */
			do_action( 'woocommerce_before_shop_loop_item_title' );
			?>
		</a>
	</div><!-- .modern-product-image -->

	<div class="modern-product-info">
		<h3 class="modern-product-title">
			<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
				<?php echo get_the_title(); ?>
			</a>
		</h3>
		
		<div class="modern-product-price">
			<?php echo $product->get_price_html(); ?>
		</div>
	</div><!-- .modern-product-info -->

	<?php
	/**
	 * Hook: woocommerce_after_shop_loop_item.
	 * @hooked woocommerce_template_loop_product_link_close - 5
	 * @hooked woocommerce_template_loop_add_to_cart - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item' );
	?>
</div><!-- .modern-product-card -->