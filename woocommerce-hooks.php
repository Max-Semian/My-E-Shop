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

//custom shortcode
add_shortcode( 'my_e_shop_recent_products', 'my_e_shop_recent_products' );
function my_e_shop_recent_products( $atts ) {
    global $woocommerce_loop, $woocommerce;

    // Set default attributes
    extract( shortcode_atts( array(
        'limit' => '8',
        'order' => 'DESC',
    ), $atts ) );

    // Define date query for the last 2 months
    $date_query = array(
        array(
            'after'  => '2 months ago',
            'column' => 'post_date',
        ),
    );

    // Query for best-selling products in the last 2 months with sales > 10
    $args = array(
        'post_status'    => 'publish',
        'post_type'      => 'product',
        'posts_per_page' => $limit,
        'meta_key'       => 'total_sales',
        'orderby'        => 'meta_value_num',
        'order'          => $order,
        'meta_query'     => array(
            array(
                'key'     => 'total_sales',
                'value'   => 10,
                'compare' => '>',
                'type'    => 'NUMERIC',
            ),
        ),
        'date_query'     => $date_query,
    );

    // Execute the query
    $products = new WP_Query( $args );
    $found_posts = $products->found_posts;

    // If fewer than 8 products are found, add featured products to fill the limit
    if ( $found_posts < $limit ) {
        $remaining_limit = $limit - $found_posts;

        $featured_args = array(
            'post_status'    => 'publish',
            'post_type'      => 'product',
            'posts_per_page' => $remaining_limit,
            'meta_query'     => array(
                array(
                    'key'   => '_featured',
                    'value' => 'yes',
                ),
            ),
        );

        $featured_products = new WP_Query( $featured_args );

        // Merge results from both queries
        if ( $featured_products->have_posts() ) {
            $products->posts = array_merge( $products->posts, $featured_products->posts );
            $products->post_count += $featured_products->post_count;
        }
        wp_reset_postdata();
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
        <p><?php _e( 'No best-selling products found for the past two months.', 'My-E-Shop' ); ?></p>
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

add_filter ( 'woocommerce_breadcrumb_defaults', function() {
            return array(
                    'delimiter'   => '',
                    'wrap_before' => '<div class="container"><div class="row"><div class="col-12"><nav class="breadcrumbs"><ul>',
                    'wrap_after'  => '</ul></nav></div></div></div>',
                    'before'      => '<li>',
                    'after'       => '</li>',
                    'home'        => _x( 'Home', 'My-E-Shop', ),
                         );
});
