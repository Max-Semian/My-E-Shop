<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Принудительная инициализация WooCommerce шорткодов
force_woocommerce_shortcodes_init();

// Получаем текущую категорию
$current_category = get_queried_object();
$category_id = $current_category->term_id;

// Проверяем выбранный шаблон категории
$selected_template = get_term_meta($category_id, '_category_template', true);
$selected_template = !empty($selected_template) ? $selected_template : 'default';

// Ищем страницу с соответствующим slug категории
$category_page_slug = 'category-' . $current_category->slug;
$category_page = get_page_by_path($category_page_slug);

// Если нет кастомной страницы, создаем её автоматически
if (!$category_page) {
    $page_content = '<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
    <!-- wp:heading {"level":1} -->
    <h1>' . esc_html($current_category->name) . '</h1>
    <!-- /wp:heading -->
    
    <!-- wp:paragraph -->
    <p>' . esc_html($current_category->description ?: 'Добро пожаловать в категорию ' . $current_category->name) . '</p>
    <!-- /wp:paragraph -->
    
    <!-- wp:separator -->
    <hr class="wp-block-separator has-alpha-channel-opacity"/>
    <!-- /wp:separator -->
    
    <!-- wp:heading {"level":2} -->
    <h2>Товары в категории</h2>
    <!-- /wp:heading -->
    
    <!-- wp:paragraph -->
    <p>[woocommerce_products category="' . $current_category->slug . '" columns="4" limit="12"]</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->';    $page_id = wp_insert_post([
        'post_title'   => 'Категория: ' . $current_category->name,
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
        // Устанавливаем правильный контекст для WooCommerce
        global $wp_query, $woocommerce_loop;
        
        // Проверяем, есть ли блок category-hero в контенте
        $content = $category_page->post_content;
        $has_hero_block = strpos($content, 'wp:my-e-shop/category-hero') !== false;
        
        // Если есть пустой блок category-hero, убираем его
        if ($has_hero_block) {
            // Проверяем, есть ли контент в блоке hero
            preg_match('/<!-- wp:my-e-shop\/category-hero[^>]*-->(.*)<!-- \/wp:my-e-shop\/category-hero -->/s', $content, $matches);
            if (isset($matches[1])) {
                $hero_content = trim($matches[1]);
                // Если блок пустой, удаляем его
                if (empty($hero_content) || $hero_content === '\n') {
                    $content = preg_replace('/<!-- wp:my-e-shop\/category-hero[^>]*-->.*?<!-- \/wp:my-e-shop\/category-hero -->/s', '', $content);
                }
            }
        }
        
        // Выводим кастомный контент страницы с обработкой шорткодов
        

        
        // Принудительно обрабатываем шорткоды
        $content = do_shortcode($content);
        
        // Отладочная информация после обработки
        if (current_user_can('manage_options')) {
            echo '<!-- DEBUG DARK: Content after do_shortcode: ' . esc_html(substr($content, 0, 200)) . '... -->';
        }
        
        // Затем применяем остальные фильтры контента  
        $content = apply_filters('the_content', $content);
        
        echo $content;
        ?>
        
        <div class="category-edit-link">
            <?php if (current_user_can('edit_pages')): ?>
                <p><a href="<?php echo admin_url('post.php?post=' . $category_page->ID . '&action=edit'); ?>" class="button">Редактировать содержимое категории</a></p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Fallback к стандартному шаблону WooCommerce -->
        <?php wc_get_template( 'archive-product.php' ); ?>
    <?php endif; ?>
</div>

<?php get_footer( 'shop' );