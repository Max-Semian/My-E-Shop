<?php
function mytheme_add_woocommerce_support() {
    load_theme_textdomain( 'My-E-Shop', get_template_directory() . '/languages' ); // Correct syntax
    add_theme_support( 'woocommerce' );
    add_theme_support( 'title-tag' );


    register_nav_menus(
        array(
            'header-menu' => __('Header menu', 'My-E-Shop'),
        )
        );

}

add_action('after_setup_theme', 'mytheme_add_woocommerce_support', );

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('My-E-Shop-bootstrap', get_template_directory_uri(  ) . '/assets/bootstrap/css/bootstrap.min.css');
    wp_enqueue_style('My-E-Shop-fontawesome', get_template_directory_uri(  ) . '/assets/fonts/fontawesome-free-6.6.0-web/css/all.min.css');
    wp_enqueue_style('My-E-Shop-owlcarousel', get_template_directory_uri(  ) . '/assets/owlcarousel2/owl.carousel.min.css');
    wp_enqueue_style('My-E-Shop-owlcarousel-theme', get_template_directory_uri(  ) . '/assets/owlcarousel2/owl.theme.default.min.css');
    wp_enqueue_style('My-E-Shop-owlcarousel', get_template_directory_uri(  ) . '/assets/owlcarousel2/owl.carousel.min.css');
    wp_enqueue_style('My-E-Shop-main', get_template_directory_uri(  ) .'/assets/css/main.css');
    wp_enqueue_style('My-E-Shop-media', get_template_directory_uri(  ) .'/assets/css/media.css');

    wp_enqueue_script('jquery');
    wp_enqueue_script('My-E-Shop-jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js', array(), false, true);
    wp_enqueue_script('My-E-Shop-owlcarousel',get_template_directory_uri(  ) . '/assets/owlcarousel2/owl.carousel.min.js', array(), false, true);
    wp_enqueue_script('My-E-Shop-bootstrap', get_template_directory_uri(  ) . '/assets/bootstrap/js/bootstrap.bundle.min.js', array(), false, true);
    wp_enqueue_script('My-E-Shop-main', get_template_directory_uri(  ) . '/assets/js/main.js', array(), false, true);
});


require_once get_template_directory() . '\incs\woocommerce-hooks.php';
require_once get_template_directory() . '\incs\class-my-e-shop-header-menu.php';
require_once get_template_directory() . '\incs\cpt.php';

/**
 * Modify WooCommerce product description based on ACF fields
 */
add_filter('the_content', 'modify_product_description', 10, 1);

function modify_product_description($content) {
    // Only run on single product pages
    if (!is_product() || !in_the_loop()) {
        return $content;
    }
    
    // Get the current product ID
    $product_id = get_the_ID();
    
    // Get ACF fields
    $specification = get_field('title', $product_id); // Replace with your ACF field name
    $features = get_field('description', $product_id); // Replace with your ACF field name
    $technical_data = get_field('image', $product_id); // Replace with your ACF field name
    
    // Check if any of the ACF fields have content
    if (!empty($specification) || !empty($features) || !empty($technical_data)) {
        // Build new content
        $new_content = '<div class="product-custom-description">';

        $new_content .= '</div>'; // Close product-custom-description

        return $new_content;
    }

    // If no ACF fields have content, return the original WooCommerce description
    return $content;
}

// Hook this function to the WooCommerce filter
add_filter('the_content', 'modify_product_description');

// Hide variable product description if ACF Fields is active
function replace_variation_description_with_acf($variations) {
    foreach ($variations as &$variation) {
        // Check if the variation has a 'variation_id' key
        if (isset($variation['variation_id'])) {
            $variation_id = $variation['variation_id'];
            
            // Get the custom description from the ACF field (replace 'custom_description' with your field name)
            $custom_description = get_field('custom_description', $variation_id);

            // If the ACF field is not empty, replace the default variation description
            if (!empty($custom_description)) {
                $variation['variation_description'] = $custom_description;
            }
        }
    }
    return $variations;
}
add_filter('woocommerce_available_variation', 'replace_variation_description_with_acf');


/**
 * Add custom styling for the product description
 */
add_action('wp_head', 'add_product_description_styles');

function add_product_description_styles() {
    if (!is_product()) {
        return;
    }
    ?>
    <?php
}

// AJAX action to load comments
function load_product_comments() {
    // Verify nonce for security
    check_ajax_referer('load_comments_nonce', 'security');

    // Get product ID from the AJAX request
    $product_id = intval($_POST['product_id']);

    // Fetch comments for the product
    $comments = get_comments(array(
        'post_id' => $product_id,
        'status'  => 'approve',
    ));

    if ($comments) {
        foreach ($comments as $comment) {
            // Output each comment in a simple format (customize as needed)
            echo '<div class="comment">';
            echo '<p><strong>' . esc_html($comment->comment_author) . '</strong></p>';
            echo '<p>' . esc_html($comment->comment_content) . '</p>';
            echo '</div>';
        }
    } else {
        echo '<p>No comments available for this product.</p>';
    }

    wp_die(); // Always include this to properly terminate the script
}
add_action('wp_ajax_load_comments', 'load_product_comments');
add_action('wp_ajax_nopriv_load_comments', 'load_product_comments');

function enqueue_ajax_comments_script() {
    if (is_product()) {
        wp_enqueue_script('ajax-comments', get_stylesheet_directory_uri() . '/assets/js/ajax-comments.js', array('jquery'), null, true);

        // Pass necessary data to JavaScript
        wp_localize_script('ajax-comments', 'woocommerce_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'product_id' => get_the_ID(),
            'nonce' => wp_create_nonce('load_comments_nonce'),
        ));
    }
}
add_action('wp_enqueue_scripts', 'enqueue_ajax_comments_script');
