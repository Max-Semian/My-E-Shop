<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header(); ?>

	<?php
		// Проверяем выбранный шаблон для товара
		$product_template = get_post_meta( get_the_ID(), '_product_template', true );
		
		// Отключаем стандартные breadcrumbs для всех шаблонов (встроены в content-single-product.php)
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		
		/**
		 * woocommerce_before_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20 (отключены - встроены в шаблоны)
		 */
		do_action( 'woocommerce_before_main_content' );
	?> 
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			
			<?php
			// Проверяем выбранный шаблон для товара
			$product_template = get_post_meta( get_the_ID(), '_product_template', true );
			
			if ( $product_template === 'dark' ) {
				// Используем темный шаблон
				wc_get_template_part( 'content', 'single-product-dark' );
			} else {
				// Используем обычный шаблон
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
