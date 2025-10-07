<?php
/**
 * Функции для управления страницами категорий
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Инициализация WooCommerce для правильной работы шорткодов
 */
add_action('init', function() {
    if (class_exists('WooCommerce')) {
        // Убеждаемся что WooCommerce инициализирован
        WC();
        
        // Подключаем шорткоды WooCommerce если они не подключены
        if (!shortcode_exists('woocommerce_products')) {
            WC()->frontend_includes();
        }
    }
});

/**
 * Улучшенная обработка шорткодов WooCommerce на страницах категорий
 */
add_filter('the_content', function($content) {
    // Проверяем что мы на странице категории или связанной странице
    if (is_product_category() || (is_page() && strpos(get_post()->post_name, 'category-') === 0)) {
        // Подключаем стили WooCommerce если они не подключены
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
        
        // Принудительно обрабатываем шорткоды
        $content = do_shortcode($content);
    }
    return $content;
}, 20);

/**
 * Добавляет метабокс в редактор категорий для быстрого доступа к странице
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
            <label for="category-template">Шаблон категории</label>
        </th>
        <td>
            <select name="category_template" id="category-template">
                <option value="default" <?php selected($selected_template, 'default'); ?>>Светлый шаблон</option>
                <option value="dark" <?php selected($selected_template, 'dark'); ?>>Темный шаблон</option>
            </select>
            <p class="description">Выберите шаблон отображения для этой категории.</p>
        </td>
    </tr>
    
    <tr class="form-field">
        <th scope="row">
            <label>Кастомная страница категории</label>
        </th>
        <td>
            <?php if ($category_page): ?>
                <p>
                    <strong>Страница создана:</strong> 
                    <a href="<?php echo get_permalink($category_page->ID); ?>" target="_blank">Просмотреть</a> | 
                    <a href="<?php echo admin_url('post.php?post=' . $category_page->ID . '&action=edit'); ?>">Редактировать</a>
                </p>
                <p class="description">Вы можете редактировать содержимое этой категории в редакторе Gutenberg.</p>
            <?php else: ?>
                <p>
                    <button type="button" class="button" onclick="createCategoryPage(<?php echo $term->term_id; ?>, '<?php echo $term->slug; ?>', '<?php echo addslashes($term->name); ?>')">
                        Создать кастомную страницу
                    </button>
                </p>
                <p class="description">Создайте кастомную страницу для этой категории, чтобы редактировать её содержимое.</p>
            <?php endif; ?>
        </td>
    </tr>
    
    <script>
    function createCategoryPage(termId, slug, name) {
        if (confirm('Создать кастомную страницу для категории "' + name + '"?')) {
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
                    alert('Ошибка при создании страницы: ' + response.data);
                }
            });
        }
    }
    </script>
    <?php
}

/**
 * AJAX обработчик для создания страницы категории
 */
add_action('wp_ajax_create_category_page', 'handle_create_category_page');
function handle_create_category_page() {
    check_ajax_referer('create_category_page', 'nonce');
    
    if (!current_user_can('edit_pages')) {
        wp_die('Недостаточно прав');
    }
    
    $term_id = intval($_POST['term_id']);
    $slug = sanitize_title($_POST['slug']);
    $name = sanitize_text_field($_POST['name']);
    
    $term = get_term($term_id, 'product_cat');
    if (!$term) {
        wp_send_json_error('Категория не найдена');
    }
    
    $page_slug = 'category-' . $slug;
    $existing_page = get_page_by_path($page_slug);
    
    if ($existing_page) {
        wp_send_json_error('Страница уже существует');
    }
    
    $page_content = '<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
    <!-- wp:heading {"level":1} -->
    <h1>' . esc_html($name) . '</h1>
    <!-- /wp:heading -->
    
    <!-- wp:paragraph -->
    <p>' . esc_html($term->description ?: 'Добро пожаловать в категорию ' . $name) . '</p>
    <!-- /wp:paragraph -->
    
    <!-- wp:separator -->
    <hr class="wp-block-separator has-alpha-channel-opacity"/>
    <!-- /wp:separator -->
    
    <!-- wp:heading {"level":2} -->
    <h2>Наши товары</h2>
    <!-- /wp:heading -->
    
    <!-- wp:paragraph -->
    <p>[woocommerce_products category="' . $slug . '" columns="4" limit="12"]</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->';
    
    $page_id = wp_insert_post([
        'post_title'   => 'Категория: ' . $name,
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
        wp_send_json_error('Ошибка при создании страницы');
    }
}

/**
 * Добавляет колонки в список категорий для быстрого доступа к страницам
 */
add_filter('manage_edit-product_cat_columns', 'add_category_page_columns');
function add_category_page_columns($columns) {
    $columns['category_template'] = 'Шаблон';
    $columns['category_page'] = 'Кастомная страница';
    return $columns;
}

add_action('manage_product_cat_custom_column', 'display_category_page_columns', 10, 3);
function display_category_page_columns($content, $column_name, $term_id) {
    if ($column_name === 'category_template') {
        $template = get_term_meta($term_id, '_category_template', true);
        $template = !empty($template) ? $template : 'default';
        
        if ($template === 'dark') {
            echo '<span style="color: #2C2C2C; background: #F4F0EB; padding: 3px 8px; border-radius: 3px; font-size: 11px;">🌙 Темный</span>';
        } else {
            echo '<span style="color: #333; background: #f0f0f0; padding: 3px 8px; border-radius: 3px; font-size: 11px;">☀️ Светлый</span>';
        }
    }
    
    if ($column_name === 'category_page') {
        $term = get_term($term_id, 'product_cat');
        $category_page_slug = 'category-' . $term->slug;
        $category_page = get_page_by_path($category_page_slug);
        
        if ($category_page) {
            echo '<a href="' . admin_url('post.php?post=' . $category_page->ID . '&action=edit') . '" class="button button-small">Редактировать</a>';
            echo '<br><a href="' . get_permalink($category_page->ID) . '" target="_blank" class="button button-small">Просмотр</a>';
        } else {
            echo '<span style="color: #999;">Не создана</span>';
        }
    }
}

/**
 * Удаляет страницу категории при удалении самой категории
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
 * Сохраняет выбранный шаблон категории
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
 * Обновляет slug страницы при изменении slug категории
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
                'post_title' => 'Категория: ' . $term->name
            ]);
        }
    }
}

/**
 * Добавляет уведомление о возможности создания кастомных страниц
 */
add_action('admin_notices', 'category_pages_admin_notice');
function category_pages_admin_notice() {
    $screen = get_current_screen();
    if ($screen && $screen->id === 'edit-product_cat') {
        ?>
        <div class="notice notice-info">
            <p><strong>Кастомные страницы категорий:</strong> Вы можете создать индивидуальные страницы для каждой категории с возможностью редактирования в Gutenberg. Выберите шаблон (светлый/темный) и используйте колонки "Шаблон" и "Кастомная страница" для управления.</p>
        </div>
        <?php
    }
}