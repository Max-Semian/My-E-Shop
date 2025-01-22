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

<div <?php wc_product_class( 'col-lg-3 col-md-4 col-sm-6 mb-3', $product ); ?>>
	<div class="product-card">
		<?php
		/**
		 * Hook: woocommerce_before_shop_loop_item.
		 * @hooked woocommerce_template_loop_product_link_open - 10
		 */
		do_action( 'woocommerce_before_shop_loop_item' );
		?>
		
		<div class="product-thumb">
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
		</div><!-- .product-thumb -->

		<div class="products-details">
			<?php
			/**
			 * Hook: woocommerce_shop_loop_item_title.
			 * @hooked woocommerce_template_loop_product_title - 10
			 */
			do_action( 'woocommerce_shop_loop_item_title' );
			?>
			
			<div class="product-excerpt mb-2">
				<?php the_content()
				?>
			</div>
<!-- .product-excerpt -->

			<div class="product-bottom-details">
				<div class="My-E-Shop-rating">
					<?php
					woocommerce_template_loop_rating();
					$rating_cnt = $product->get_rating_count();
					echo '<div class="woostudy-rating-count"> <small>(' . esc_html( $rating_cnt ) . ')</small> </div>';
					?>
				</div>

				<?php
				/**
				 * Hook: woocommerce_after_shop_loop_item_title.
				 * @hooked woocommerce_template_loop_rating - 5
				 * @hooked woocommerce_template_loop_price - 10
				 */
				do_action( 'woocommerce_after_shop_loop_item_title' );

				/**
				 * Hook: woocommerce_after_shop_loop_item.
				 * @hooked woocommerce_template_loop_product_link_close - 5
				 * @hooked woocommerce_template_loop_add_to_cart - 10
				 */
				do_action( 'woocommerce_after_shop_loop_item' );
				?>
			</div><!-- .product-bottom-details -->
		</div><!-- .products-details -->
	</div><!-- .product-card -->
</div><!-- col-lg-3 col-md-4 col-sm-6 mb-3 -->
