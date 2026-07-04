<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header(); ?>

	<?php
		// Check the selected template for the product
		$product_template = get_post_meta( get_the_ID(), '_product_template', true );

		// Disable the standard breadcrumbs for all templates (built into content-single-product.php)
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

		/**
		 * woocommerce_before_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20 (disabled - built into the templates)
		 */
		do_action( 'woocommerce_before_main_content' );
	?> 
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			
			<?php
			// Check the selected template for the product
			$product_template = get_post_meta( get_the_ID(), '_product_template', true );

			if ( $product_template === 'dark' ) {
				// Use the dark template
				wc_get_template_part( 'content', 'single-product-dark' );
			} else {
				// Use the regular template
				wc_get_template_part( 'content', 'single-product' );
			}
			?>
			
		<?php endwhile; // end of the loop. ?>
	<?php
		/**
		 * woocommerce_after_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>
<?php
get_footer();
