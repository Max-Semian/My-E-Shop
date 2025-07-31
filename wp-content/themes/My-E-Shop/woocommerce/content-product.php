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
<li <?php wc_product_class( '', $product ); ?>>
	<div class="product-card">
		<?php
		/**
		 * Hook: woocommerce_before_shop_loop_item.
		 *
		 * @hooked woocommerce_template_loop_product_link_open - 10
		 */
		do_action( 'woocommerce_before_shop_loop_item' );
		?>
		
		<div class="product-thumb">
			<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
				<?php
				/**
				 * Hook: woocommerce_before_shop_loop_item_title.
				 *
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
			 *
			 * @hooked woocommerce_template_loop_product_title - 10
			 */
			do_action( 'woocommerce_shop_loop_item_title' );
			?>
			
			<div class="product-excerpt mb-2">
				<?php echo $product->get_short_description(); ?>
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

			<div class="product-price-cart">
				<span class="price"><?php echo $product->get_price_html(); ?></span>
				<div class="custom-add-to-cart">
					<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" 
					class="button add_to_cart_button ajax_add_to_cart" 
					data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" 
					data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>" 
					aria-label="<?php echo esc_attr( $product->add_to_cart_text() ); ?>" 
					rel="nofollow">
					<i class="fas fa-shopping-cart"></i> <!-- Cart Icon -->
					</a>
				</div>
			</div>
		</div><!-- .product-bottom-details -->
		</div><!-- .products-details -->
	</div><!-- .product-card -->
</li>
