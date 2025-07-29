<?php

add_filter('woocommerce_enqueue_styles', '__return_false');

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

    // Set default attributes
    $atts = shortcode_atts( array(
        'limit'      => 8,                 // Number of products to display
        'order'      => 'DESC',            // Sort order
        'categories' => '',                // Product categories (comma-separated slugs)
        'tags'       => '',                // Product tags (comma-separated slugs)
    ), $atts, 'my_e_shop_recent_products' );

    $limit = intval( $atts['limit'] ); // Sanitize and cast limit to an integer

    // Build the taxonomy query (for categories and tags)
    $tax_query = array();

    if ( ! empty( $atts['categories'] ) ) {
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => explode( ',', $atts['categories'] ), // Convert comma-separated categories to an array
            'operator' => 'IN',
        );
    }

    if ( ! empty( $atts['tags'] ) ) {
        $tax_query[] = array(
            'taxonomy' => 'product_tag',
            'field'    => 'slug',
            'terms'    => explode( ',', $atts['tags'] ), // Convert comma-separated tags to an array
            'operator' => 'IN',
        );
    }

    // Query for best-selling products with sales > 10
    $args = array(
        'post_status'    => 'publish',
        'post_type'      => 'product',
        'posts_per_page' => $limit,
        'meta_key'       => 'total_sales',
        'orderby'        => 'meta_value_num',
        'order'          => sanitize_text_field( $atts['order'] ), // Sanitize the order parameter
        'meta_query'     => array(
            array(
                'key'     => 'total_sales',
                'value'   => 10,
                'compare' => '>',
                'type'    => 'NUMERIC',
            ),
        ),
        'tax_query'      => $tax_query,
    );

    // Execute the query for best-sellers
    $products = new WP_Query( $args );

    // If fewer than the limit products are found, add featured products to fill the limit
    $found_posts = $products->found_posts;
    if ( $found_posts < $limit ) {
        $remaining_limit = $limit - $found_posts;

        // Get IDs of already fetched products to avoid duplicates
        $existing_ids = wp_list_pluck( $products->posts, 'ID' );

        $featured_args = array(
            'post_status'    => 'publish',
            'post_type'      => 'product',
            'posts_per_page' => $remaining_limit, // Limit the number of featured products
            'post__not_in'   => $existing_ids, // Exclude already fetched products
            'tax_query'      => array(
                array(
                    'taxonomy' => 'product_visibility',
                    'field'    => 'name',
                    'terms'    => 'featured', // Fetch products marked as "featured"
                ),
            ),
        );

        // Query for featured products
        $featured_products = new WP_Query( $featured_args );

        if ( $featured_products->have_posts() ) {
            // Combine the posts while avoiding duplicate entries
            $products->posts = array_merge( $products->posts, $featured_products->posts );
            $products->post_count = count( $products->posts );
        }
    }

    // Ensure we never exceed the limit
    if ( count( $products->posts ) > $limit ) {
        $products->posts = array_slice( $products->posts, 0, $limit );
    }

    // Start output buffering
    ob_start();

    if ( $products->have_posts() ) : ?>
        <div class="woocommerce">
            <div class="owl-carousel owl-theme owl-carousel-full">
                <?php while ( $products->have_posts() ) : $products->the_post(); ?>
                    <?php wc_get_template_part( 'content', 'product-test' ); ?>
                <?php endwhile; ?>
            </div>
        </div>
    <?php else : ?>
        <p><?php _e( 'No best-selling products found.', 'My-E-Shop' ); ?></p>
    <?php endif;

    wp_reset_postdata();

    // Return the buffered output
    return ob_get_clean();
}



add_action('templete_redirect', function () {
    if (is_product()){
        remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
    }
});

// ИЗМЕНЕННЫЙ фильтр breadcrumbs - только для страницы Shop, НЕ для категорий
add_filter('woocommerce_breadcrumb_defaults', function() {
    // Если это страница категории - НЕ применяем этот фильтр
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
    
    // Только для страницы Shop применяем старое оформление
    return array(
        'delimiter'   => '',
        'wrap_before' => '<div class="container"><div class="col-12"><nav class="breadcrumbs"><ul>',
        'wrap_after'  => '</ul></nav></div></div>',
        'before'      => '<li>',
        'after'       => '</li>',
        'home'        => _x('Home', 'My-E-Shop'),
    );
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