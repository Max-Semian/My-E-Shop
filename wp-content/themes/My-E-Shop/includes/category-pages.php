<?php
/**
 * Functions for managing category pages
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize WooCommerce so shortcodes work correctly
 */
add_action('init', function() {
    if (class_exists('WooCommerce')) {
        // Make sure WooCommerce is initialized
        WC();

        // Load WooCommerce shortcodes if they are not loaded
        if (!shortcode_exists('woocommerce_products')) {
            WC()->frontend_includes();
        }
    }
});

/**
 * Improved handling of WooCommerce shortcodes on category pages
 */
add_filter('the_content', function($content) {
    // Check that we are on a category page or a related page
    if (is_product_category() || (is_page() && strpos(get_post()->post_name, 'category-') === 0)) {
        // Load WooCommerce styles if they are not loaded
        if (function_exists('wc_enqueue_js')) {
            wp_enqueue_style('woocommerce-layout');
            wp_enqueue_style('woocommerce-smallscreen'); 
            wp_enqueue_style('woocommerce-general');
            
            wc_enqueue_js("
                jQuery(document).ready(function($) {
                    if ($('body').hasClass('woocommerce-no-js')) {
                        $('body').removeClass('woocommerce-no-js');
                    }
                });
            ");
        }
        
        // Force shortcode processing
        $content = do_shortcode($content);
    }
    return $content;
}, 20);

/**
 * Adds a metabox to the category editor for quick access to the page
 */
add_action('product_cat_edit_form_fields', 'add_category_page_metabox');
function add_category_page_metabox($term) {
    $category_page_slug = 'category-' . $term->slug;
    $category_page = get_page_by_path($category_page_slug);
    $selected_template = get_term_meta($term->term_id, '_category_template', true);
    $selected_template = !empty($selected_template) ? $selected_template : 'default';
    ?>
    <tr class="form-field">
        <th scope="row">
            <label for="category-template">Category template</label>
        </th>
        <td>
            <select name="category_template" id="category-template">
                <option value="default" <?php selected($selected_template, 'default'); ?>>Light template</option>
                <option value="dark" <?php selected($selected_template, 'dark'); ?>>Dark template</option>
            </select>
            <p class="description">Choose the display template for this category.</p>
        </td>
    </tr>
    
    <tr class="form-field">
        <th scope="row">
            <label>Custom category page</label>
        </th>
        <td>
            <?php if ($category_page): ?>
                <p>
                    <strong>Page created:</strong>
                    <a href="<?php echo get_permalink($category_page->ID); ?>" target="_blank">View</a> |
                    <a href="<?php echo admin_url('post.php?post=' . $category_page->ID . '&action=edit'); ?>">Edit</a>
                </p>
                <p class="description">You can edit this category's content in the Gutenberg editor.</p>
            <?php else: ?>
                <p>
                    <button type="button" class="button" onclick="createCategoryPage(<?php echo $term->term_id; ?>, '<?php echo $term->slug; ?>', '<?php echo addslashes($term->name); ?>')">
                        Create custom page
                    </button>
                </p>
                <p class="description">Create a custom page for this category so you can edit its content.</p>
            <?php endif; ?>
        </td>
    </tr>
    
    <script>
    function createCategoryPage(termId, slug, name) {
        if (confirm('Create a custom page for the "' + name + '" category?')) {
            var data = {
                action: 'create_category_page',
                term_id: termId,
                slug: slug,
                name: name,
                nonce: '<?php echo wp_create_nonce('create_category_page'); ?>'
            };
            
            jQuery.post(ajaxurl, data, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error creating the page: ' + response.data);
                }
            });
        }
    }
    </script>
    <?php
}

/**
 * AJAX handler for creating a category page
 */
add_action('wp_ajax_create_category_page', 'handle_create_category_page');
function handle_create_category_page() {
    check_ajax_referer('create_category_page', 'nonce');
    
    if (!current_user_can('edit_pages')) {
        wp_die('Insufficient permissions');
    }
    
    $term_id = intval($_POST['term_id']);
    $slug = sanitize_title($_POST['slug']);
    $name = sanitize_text_field($_POST['name']);
    
    $term = get_term($term_id, 'product_cat');
    if (!$term) {
        wp_send_json_error('Category not found');
    }
    
    $page_slug = 'category-' . $slug;
    $existing_page = get_page_by_path($page_slug);
    
    if ($existing_page) {
        wp_send_json_error('Page already exists');
    }
    
    $page_content = '<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
    <!-- wp:heading {"level":1} -->
    <h1>' . esc_html($name) . '</h1>
    <!-- /wp:heading -->
    
    <!-- wp:paragraph -->
    <p>' . esc_html($term->description ?: 'Welcome to the ' . $name . ' category') . '</p>
    <!-- /wp:paragraph -->
    
    <!-- wp:separator -->
    <hr class="wp-block-separator has-alpha-channel-opacity"/>
    <!-- /wp:separator -->
    
    <!-- wp:heading {"level":2} -->
    <h2>Our products</h2>
    <!-- /wp:heading -->
    
    <!-- wp:paragraph -->
    <p>[woocommerce_products category="' . $slug . '" columns="4" limit="12"]</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->';
    
    $page_id = wp_insert_post([
        'post_title'   => 'Category: ' . $name,
        'post_name'    => $page_slug,
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_content' => $page_content,
        'meta_input'   => [
            '_category_page_for' => $term_id,
        ]
    ]);
    
    if ($page_id && !is_wp_error($page_id)) {
        wp_send_json_success([
            'page_id' => $page_id,
            'edit_url' => admin_url('post.php?post=' . $page_id . '&action=edit')
        ]);
    } else {
        wp_send_json_error('Error creating the page');
    }
}

/**
 * Adds columns to the category list for quick access to pages
 */
add_filter('manage_edit-product_cat_columns', 'add_category_page_columns');
function add_category_page_columns($columns) {
    $columns['category_template'] = 'Template';
    $columns['category_page'] = 'Custom page';
    return $columns;
}

add_action('manage_product_cat_custom_column', 'display_category_page_columns', 10, 3);
function display_category_page_columns($content, $column_name, $term_id) {
    if ($column_name === 'category_template') {
        $template = get_term_meta($term_id, '_category_template', true);
        $template = !empty($template) ? $template : 'default';
        
        if ($template === 'dark') {
            echo '<span style="color: #2C2C2C; background: #F4F0EB; padding: 3px 8px; border-radius: 3px; font-size: 11px;">🌙 Dark</span>';
        } else {
            echo '<span style="color: #333; background: #f0f0f0; padding: 3px 8px; border-radius: 3px; font-size: 11px;">☀️ Light</span>';
        }
    }
    
    if ($column_name === 'category_page') {
        $term = get_term($term_id, 'product_cat');
        $category_page_slug = 'category-' . $term->slug;
        $category_page = get_page_by_path($category_page_slug);
        
        if ($category_page) {
            echo '<a href="' . admin_url('post.php?post=' . $category_page->ID . '&action=edit') . '" class="button button-small">Edit</a>';
            echo '<br><a href="' . get_permalink($category_page->ID) . '" target="_blank" class="button button-small">View</a>';
        } else {
            echo '<span style="color: #999;">Not created</span>';
        }
    }
}

/**
 * Deletes the category page when the category itself is deleted
 */
add_action('delete_product_cat', 'delete_category_page_on_term_delete');
function delete_category_page_on_term_delete($term_id) {
    $pages = get_posts([
        'post_type' => 'page',
        'meta_query' => [
            [
                'key' => '_category_page_for',
                'value' => $term_id,
                'compare' => '='
            ]
        ],
        'posts_per_page' => 1
    ]);
    
    if (!empty($pages)) {
        wp_delete_post($pages[0]->ID, true);
    }
}

/**
 * Saves the selected category template
 */
add_action('edited_product_cat', 'save_category_template');
add_action('created_product_cat', 'save_category_template');
function save_category_template($term_id) {
    if (isset($_POST['category_template'])) {
        $template = sanitize_text_field($_POST['category_template']);
        if (in_array($template, ['default', 'dark'])) {
            update_term_meta($term_id, '_category_template', $template);
        }
    }
}

/**
 * Updates the page slug when the category slug changes
 */
add_action('edited_product_cat', 'update_category_page_on_term_edit');
function update_category_page_on_term_edit($term_id) {
    $term = get_term($term_id, 'product_cat');
    if (!$term) return;
    
    $pages = get_posts([
        'post_type' => 'page',
        'meta_query' => [
            [
                'key' => '_category_page_for',
                'value' => $term_id,
                'compare' => '='
            ]
        ],
        'posts_per_page' => 1
    ]);
    
    if (!empty($pages)) {
        $page = $pages[0];
        $new_slug = 'category-' . $term->slug;
        
        if ($page->post_name !== $new_slug) {
            wp_update_post([
                'ID' => $page->ID,
                'post_name' => $new_slug,
                'post_title' => 'Category: ' . $term->name
            ]);
        }
    }
}

/**
 * Adds a notice about the option to create custom pages
 */
add_action('admin_notices', 'category_pages_admin_notice');
function category_pages_admin_notice() {
    $screen = get_current_screen();
    if ($screen && $screen->id === 'edit-product_cat') {
        ?>
        <div class="notice notice-info">
            <p><strong>Custom category pages:</strong> You can create individual pages for each category that can be edited in Gutenberg. Choose a template (light/dark) and use the "Template" and "Custom page" columns to manage them.</p>
        </div>
        <?php
    }
}