<?php defined( 'ABSPATH' ) || exit; ?>

<?php get_header(); ?>

<?php do_action( 'woocommerce_before_main_content' );?>

<!-- Hero Banner Section ONLY for categories -->
<?php if (is_product_category()): ?>
<div class="category-hero-section">
    <?php 
    $category_image = '';
    $category_title = '';
    
    // Get the current category
    $current_category = get_queried_object();
    $thumbnail_id = get_term_meta($current_category->term_id, 'thumbnail_id', true);
    $category_title = $current_category->name;
    
    if ($thumbnail_id) {
        $category_image = wp_get_attachment_image_src($thumbnail_id, 'full');
    }
    
    // If there is a category image, use it, otherwise use the default
    if (!empty($category_image)) {
        $hero_bg = $category_image[0];
    } else {
        // Default image or gradient
        $hero_bg = 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 400"><defs><linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:%23667eea;stop-opacity:1" /><stop offset="100%" style="stop-color:%23764ba2;stop-opacity:1" /></linearGradient></defs><rect fill="url(%23grad)" width="1200" height="400"/></svg>';
    }
    ?>
    
    <div class="hero-banner" style="background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('<?php echo esc_url($hero_bg); ?>');">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="hero-content">
                <!-- Category Title in hero banner -->
                <?php if (apply_filters('woocommerce_show_page_title', true)): ?>
                <h1 class="hero-title">
                    <?php echo esc_html($category_title); ?>
                </h1>
                <?php endif; ?>
                
                <!-- Category Description -->
                <div class="hero-description">
                    <?php 
                    if (category_description()) {
                        echo '<p>' . wp_trim_words(category_description(), 20) . '</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Breadcrumbs below hero banner ONLY for categories -->
<div class="breadcrumbs-section">
    <div class="breadcrumbs-container">
        <nav class="breadcrumbs">
            <?php woocommerce_breadcrumb(array(
                'delimiter'   => '',
                'wrap_before' => '<ul class="breadcrumb-list">',
                'wrap_after'  => '</ul>',
                'before'      => '<li>',
                'after'       => '</li>',
                'home'        => _x('Home', 'breadcrumb', 'woocommerce'),
            )); ?>
        </nav>
    </div>
</div>
<?php endif; ?>

<div class="container">
	<div class="row">
		<div class="col-lg-3 col-md-4">
		    <?php 
		    // Include the shop sidebar
		    $sidebar_path = get_template_directory() . '/woocommerce/sidebar-shop.php';
		    
		    if (file_exists($sidebar_path)) {
		        include $sidebar_path;
		    } else {
		        // Fallback to the standard sidebar
		        get_sidebar('shop');
		    }
		    ?>
		</div><!-- ./col-lg-3 col-md-4-->
		<div class="col-lg-9 col-md-8">
			<div class="row">
				<!-- Page title for Shop (if NOT a category) -->
				<?php if (!is_product_category()): ?>
				<div class="col-12">
					<?php if (apply_filters('woocommerce_show_page_title', true)): ?>
						<h1 class="woocommerce-products-header__title page-title section-title h3">
							<span><?php woocommerce_page_title(); ?></span>
						</h1>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<?php 
					if ( woocommerce_product_loop() ) { ?>
						
						<?php 
						/**
						 * Hook: woocommerce_before_shop_loop.
						 *
						 * @hooked woocommerce_output_all_notices - 10
						 * @hooked woocommerce_result_count - 20
						 * @hooked woocommerce_catalog_ordering - 30
						 */
						do_action( 'woocommerce_before_shop_loop' );
					
						woocommerce_product_loop_start();
					
						if ( wc_get_loop_prop( 'total' ) ) {
							while ( have_posts() ) {
								the_post();
					
								/**
								 * Hook: woocommerce_shop_loop.
								 */
								do_action( 'woocommerce_shop_loop' );
					
								wc_get_template_part( 'content', 'product' );
							}
						}
					
						woocommerce_product_loop_end();
					
						/**
						 * Hook: woocommerce_after_shop_loop.
						 *
						 * @hooked woocommerce_pagination - 10
						 */
						do_action( 'woocommerce_after_shop_loop' );
					} else {
						/**
						 * Hook: woocommerce_no_products_found.
						 *
						 * @hooked wc_no_products_found - 10
						 */
						do_action( 'woocommerce_no_products_found' );
					}
				?>
			</div><!--./row-->
		</div> <!-- ./col-lg-9 col-md-8-->
	</div> <!-- ./row-->
</div> <!-- ./container-->

<?php do_action( 'woocommerce_after_main_content' );?>

<?php get_footer(); ?>