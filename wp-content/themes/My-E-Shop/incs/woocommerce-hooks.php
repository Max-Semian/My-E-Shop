<?php

add_filter('woocommerce_enqueue_styles', '__return_false');

// Enqueue custom styles for the carousel
function my_e_shop_enqueue_carousel_styles() {
    wp_enqueue_style('my-e-shop-carousel-custom', get_template_directory_uri() . '/assets/css/carousel-custom.css', array(), '1.0.0');
}
add_action('wp_enqueue_scripts', 'my_e_shop_enqueue_carousel_styles');

// product cart

remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
add_action('woocommerce_shop_loop_item_title', function () {
    global $product;
    echo '<h4>
             <a href="' . $product->get_permalink(). '">' . $product->get_title() . '</a>
          </h4>';
} );
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);

add_filter('woocommerce_product_get_rating_html', function( $html, $rating, $count ) {
    $html = '';
		/* translators: %s: rating */
		$label = sprintf( __( 'Rated %s out of 5', 'woocommerce' ), $rating );
		$html  = '<div class="star-rating" role="img" aria-label="' . esc_attr( $label ) . '">' . wc_get_star_rating_html( $rating, $count ) . '</div>';
        return $html;
}, 10, 3 );

// Custom shortcode
add_shortcode( 'my_e_shop_recent_products', 'my_e_shop_recent_products' );

function my_e_shop_recent_products( $atts ) {
	global $woocommerce_loop;

	$atts = shortcode_atts(
		array(
			'limit'      => 8,
			'columns'    => 5, // Set 5 columns by default
			'orderby'    => 'date',
			'order'      => 'DESC',
			'categories' => '',
			'tags'       => '',
		),
		$atts,
		'my_e_shop_recent_products'
	);

	$query_args = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => $atts['limit'],
		'orderby'             => $atts['orderby'],
		'order'               => $atts['order'],
	);

	$products = new WP_Query( $query_args );

	if ( ! $products->have_posts() ) {
		return '';
	}

	// Set the number of columns for WooCommerce
	$woocommerce_loop['columns'] = $atts['columns'];

	ob_start();

	// Begin the standard WooCommerce loop
	woocommerce_product_loop_start();

	while ( $products->have_posts() ) {
		$products->the_post();
		wc_get_template_part( 'content', 'product' );
	}

	// End the standard WooCommerce loop
	woocommerce_product_loop_end();

	wp_reset_postdata();

	return '<div class="woocommerce columns-' . esc_attr( $atts['columns'] ) . '">' . ob_get_clean() . '</div>';
}



add_action('templete_redirect', function () {
    if (is_product()){
        remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
    }
});

// MODIFIED breadcrumbs filter - only for the Shop page, NOT for categories
add_filter('woocommerce_breadcrumb_defaults', function() {
    // If this is a category page - do NOT apply this filter
    if (is_product_category()) {
        return array(
            'delimiter'   => ' / ',
            'wrap_before' => '',
            'wrap_after'  => '',
            'before'      => '',
            'after'       => '',
            'home'        => _x('Home', 'My-E-Shop'),
        );
    }
    
    // Apply the old styling only for the Shop page
    return array(
        'delimiter'   => '',
        'wrap_before' => '<div class="container"><div class="col-12"><nav class="breadcrumbs"><ul>',
        'wrap_after'  => '</ul></nav></div></div>',
        'before'      => '<li>',
        'after'       => '</li>',
        'home'        => _x('Home', 'My-E-Shop'),
    );
});

// Add "COLLECTION" to the breadcrumbs for products
add_filter('woocommerce_get_breadcrumb', function($crumbs) {
    if (is_product()) {
        // Create a new array with HOME, COLLECTION, and the remaining items
        $new_crumbs = array();

        // Add HOME (the first item)
        if (isset($crumbs[0])) {
            $new_crumbs[] = $crumbs[0];
        }

        // Add COLLECTION after HOME
        $new_crumbs[] = array(
            'COLLECTION',
            get_permalink(wc_get_page_id('shop'))
        );

        // Add the remaining items (categories and product)
        for ($i = 1; $i < count($crumbs); $i++) {
            $new_crumbs[] = $crumbs[$i];
        }
        
        return $new_crumbs;
    }
    return $crumbs;
});

function my_e_shop_get_shop_thumb () {
    $html = '';
    if ( is_product_category() ){
	    global $wp_query;
	    $cat = $wp_query->get_queried_object();
	    $thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
	    $image = wp_get_attachment_url( $thumbnail_id );
	    if ( $image ) {
		    $html .= '<img src="' . $image . '" alt="' . $cat->name . '" class="img-thumbnail">';
		}
	}
    return $html;
}

// Load product reviews via AJAX
add_action('wp_ajax_load_product_reviews', 'load_product_reviews_ajax');
add_action('wp_ajax_nopriv_load_product_reviews', 'load_product_reviews_ajax');