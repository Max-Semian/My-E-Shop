<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Force WooCommerce shortcodes initialization
force_woocommerce_shortcodes_init();

// Get the current category
$current_category = get_queried_object();
$category_id = $current_category->term_id;

// Check the selected category template
$selected_template = get_term_meta($category_id, '_category_template', true);
$selected_template = !empty($selected_template) ? $selected_template : 'default';

// Look for a page with the matching category slug
$category_page_slug = 'category-' . $current_category->slug;
$category_page = get_page_by_path($category_page_slug);

// If there is no custom page, create it automatically
if (!$category_page) {
    $page_content = '<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
    <!-- wp:heading {"level":1} -->
    <h1>' . esc_html($current_category->name) . '</h1>
    <!-- /wp:heading -->
    
    <!-- wp:paragraph -->
    <p>' . esc_html($current_category->description ?: 'Welcome to the ' . $current_category->name . ' category') . '</p>
    <!-- /wp:paragraph -->
    
    <!-- wp:separator -->
    <hr class="wp-block-separator has-alpha-channel-opacity"/>
    <!-- /wp:separator -->
    
    <!-- wp:heading {"level":2} -->
    <h2>Products in this category</h2>
    <!-- /wp:heading -->
    
    <!-- wp:paragraph -->
    <p>[woocommerce_products category="' . $current_category->slug . '" columns="4" limit="12"]</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->';

    $page_id = wp_insert_post([
        'post_title'   => 'Category: ' . $current_category->name,
        'post_name'    => $category_page_slug,
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_content' => $page_content,
        'meta_input'   => [
            '_category_page_for' => $category_id,
        ]
    ]);
    
    if ($page_id && !is_wp_error($page_id)) {
        $category_page = get_post($page_id);
    }
}

get_header( 'shop' ); ?>

<div class="woocommerce-category-page woocommerce-category-page--<?php echo esc_attr($selected_template); ?>">
    <?php if ($category_page): ?>
        <?php
        // Set the correct context for WooCommerce
        global $wp_query, $woocommerce_loop;

        // Check whether the content contains a category-hero block
        $content = $category_page->post_content;
        $has_hero_block = strpos($content, 'wp:my-e-shop/category-hero') !== false;

        // If the category-hero block is empty, remove it
        if ($has_hero_block) {
            // Check whether the hero block has content
            preg_match('/<!-- wp:my-e-shop\/category-hero[^>]*-->(.*)<!-- \/wp:my-e-shop\/category-hero -->/s', $content, $matches);
            if (isset($matches[1])) {
                $hero_content = trim($matches[1]);
                // If the block is empty, remove it
                if (empty($hero_content) || $hero_content === '\n') {
                    $content = preg_replace('/<!-- wp:my-e-shop\/category-hero[^>]*-->.*?<!-- \/wp:my-e-shop\/category-hero -->/s', '', $content);
                }
            }
        }

        // Output the custom page content with shortcode processing

        // Force shortcode processing
        $content = do_shortcode($content);

        // Debug information after processing
        if (current_user_can('manage_options')) {
            echo '<!-- DEBUG: Content after do_shortcode: ' . esc_html(substr($content, 0, 200)) . '... -->';
        }
        
        // Then apply the remaining content filters
        $content = apply_filters('the_content', $content);
        
        echo $content;
        ?>
        
        <div class="category-edit-link">
            <?php if (current_user_can('edit_pages')): ?>
                <p><a href="<?php echo admin_url('post.php?post=' . $category_page->ID . '&action=edit'); ?>" class="button">Edit category content</a></p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Fallback to the standard WooCommerce template -->
        <?php wc_get_template( 'archive-product.php' ); ?>
    <?php endif; ?>
</div>

<style>
.category-custom-content {
    margin-bottom: 2rem;
}

.category-edit-link {
    text-align: center;
    margin: 1rem 0;
    padding: 1rem;
    background: #f9f9f9;
    border-radius: 5px;
}

.category-edit-link .button {
    background: #0073aa;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 3px;
    display: inline-block;
}

.category-edit-link .button:hover {
    background: #005a87;
}
</style>

<?php get_footer( 'shop' );
