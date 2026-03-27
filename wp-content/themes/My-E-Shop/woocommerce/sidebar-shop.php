<?php
/**
 * Shop Sidebar Template - Final Version
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<div class="sidebar">

    <!-- WordPress Widgets (если есть) -->
    <?php if (is_active_sidebar('sidebar-1')) : ?>
        <div class="filter-block">
            <?php dynamic_sidebar('sidebar-1'); ?>
        </div>
    <?php endif; ?>

    <!-- Фильтр по цене - ОБНОВЛЕННАЯ ВЕРСИЯ -->
    <div class="filter-block">
        <h3 class="section-title"><span><?php _e('Filter by Price', 'My-E-Shop'); ?></span></h3>
        
        <?php 
        // Пробуем использовать виджет WooCommerce
        if (class_exists('WC_Widget_Price_Filter')) {
            echo '<div class="woocommerce-price-filter-widget">';
            
            // Создаем экземпляр виджета
            $price_filter_widget = new WC_Widget_Price_Filter();
            $args = array(
                'before_widget' => '<div class="widget woocommerce widget_price_filter">',
                'after_widget' => '</div>',
                'before_title' => '<h3 class="widget-title">',
                'after_title' => '</h3>'
            );
            $instance = array('title' => '');
            
            // Выводим виджет
            $price_filter_widget->widget($args, $instance);
            
            echo '</div>';
        } else {
            // Простой HTML фильтр цены как fallback
            ?>
            <form method="get" class="price-filter-form">
                <div class="price-filter-inputs">
                    <input type="number" 
                           name="min_price" 
                           placeholder="<?php _e('Min', 'My-E-Shop'); ?>" 
                           value="<?php echo isset($_GET['min_price']) ? esc_attr($_GET['min_price']) : ''; ?>" 
                           min="0"
                           class="form-control">
                    <span>-</span>
                    <input type="number" 
                           name="max_price" 
                           placeholder="<?php _e('Max', 'My-E-Shop'); ?>" 
                           value="<?php echo isset($_GET['max_price']) ? esc_attr($_GET['max_price']) : ''; ?>" 
                           min="0"
                           class="form-control">
                </div>
                <button type="submit" class="btn btn-primary btn-sm filter-btn">
                    <i class="fas fa-filter"></i> <?php _e('Filter', 'My-E-Shop'); ?>
                </button>
                <?php
                // Сохраняем другие параметры поиска
                foreach ($_GET as $key => $value) {
                    if (!in_array($key, ['min_price', 'max_price'])) {
                        echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '">';
                    }
                }
                ?>
            </form>
            <?php
        }
        ?>
    </div>

    <!-- Категории товаров -->
    <?php if (!is_product_category()): ?>
    <div class="filter-block">
        <h3 class="section-title"><span><?php _e('Categories', 'My-E-Shop'); ?></span></h3>
        <?php
        $categories = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
            'exclude' => array(get_option('default_product_cat')),
        ));

        if (!empty($categories) && !is_wp_error($categories)) {
            echo '<ul class="category-filter-list">';
            foreach ($categories as $category) {
                $category_link = get_term_link($category);
                $count = $category->count;
                
                echo '<li class="category-item">';
                echo '<a href="' . esc_url($category_link) . '" class="category-link">';
                echo '<span class="category-name">' . esc_html($category->name) . '</span>';
                echo '<span class="category-count">' . $count . '</span>';
                echo '</a>';
                echo '</li>';
            }
            echo '</ul>';
        }
        ?>
    </div>
    <?php endif; ?>

    <!-- Фильтры по атрибутам товаров -->
    <?php
    $attribute_taxonomies = wc_get_attribute_taxonomies();
    if (!empty($attribute_taxonomies)) {
        foreach ($attribute_taxonomies as $attribute) {
            $taxonomy = wc_attribute_taxonomy_name($attribute->attribute_name);
            $terms = get_terms(array(
                'taxonomy' => $taxonomy,
                'hide_empty' => true,
            ));

            if (!empty($terms) && !is_wp_error($terms)) {
                ?>
                <div class="filter-block">
                    <h3 class="section-title"><span><?php echo esc_html($attribute->attribute_label); ?></span></h3>
                    <ul class="attribute-filter-list">
                        <?php foreach ($terms as $term) { 
                            $current_filters = isset($_GET['filter_' . $attribute->attribute_name]) ? 
                                explode(',', $_GET['filter_' . $attribute->attribute_name]) : array();
                            $is_checked = in_array($term->slug, $current_filters);
                        ?>
                            <li class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input attribute-filter" 
                                       id="<?php echo esc_attr($taxonomy . '_' . $term->term_id); ?>"
                                       data-taxonomy="<?php echo esc_attr($taxonomy); ?>"
                                       data-term="<?php echo esc_attr($term->slug); ?>"
                                       data-attribute="<?php echo esc_attr($attribute->attribute_name); ?>"
                                       value="<?php echo esc_attr($term->slug); ?>"
                                       <?php checked($is_checked); ?>>
                                <label class="form-check-label" for="<?php echo esc_attr($taxonomy . '_' . $term->term_id); ?>">
                                    <span class="attribute-name"><?php echo esc_html($term->name); ?></span>
                                    <span class="attribute-count"><?php echo $term->count; ?></span>
                                </label>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <?php
            }
        }
    }
    ?>

    <!-- Фильтр по наличию -->
    <div class="filter-block">
        <h3 class="section-title"><span><?php _e('Availability', 'My-E-Shop'); ?></span></h3>
        <?php
        $current_stock_filter = isset($_GET['stock_status']) ? $_GET['stock_status'] : '';
        $stock_statuses = explode(',', $current_stock_filter);
        ?>
        <div class="form-check">
            <input type="checkbox" 
                   class="form-check-input stock-filter" 
                   id="in-stock-filter" 
                   value="instock"
                   <?php checked(in_array('instock', $stock_statuses)); ?>>
            <label class="form-check-label" for="in-stock-filter">
                <i class="fas fa-check-circle" style="color: #28a745;"></i>
                <?php _e('In Stock', 'My-E-Shop'); ?>
            </label>
        </div>
        <div class="form-check">
            <input type="checkbox" 
                   class="form-check-input stock-filter" 
                   id="out-of-stock-filter" 
                   value="outofstock"
                   <?php checked(in_array('outofstock', $stock_statuses)); ?>>
            <label class="form-check-label" for="out-of-stock-filter">
                <i class="fas fa-times-circle" style="color: #dc3545;"></i>
                <?php _e('Out of Stock', 'My-E-Shop'); ?>
            </label>
        </div>
    </div>

    <!-- Фильтр "Распродажа" -->
    <div class="filter-block">
        <h3 class="section-title"><span><?php _e('Special Offers', 'My-E-Shop'); ?></span></h3>
        <div class="form-check">
            <input type="checkbox" 
                   class="form-check-input sale-filter" 
                   id="on-sale-filter"
                   value="1"
                   <?php checked(isset($_GET['on_sale']) && $_GET['on_sale'] == '1'); ?>>
            <label class="form-check-label" for="on-sale-filter">
                <i class="fas fa-tag" style="color: var(--red-color);"></i>
                <?php _e('On Sale Only', 'My-E-Shop'); ?>
            </label>
        </div>
    </div>

    <!-- Кнопка очистки фильтров -->
    <?php 
    $has_filters = false;
    foreach ($_GET as $key => $value) {
        if (in_array($key, ['min_price', 'max_price', 'stock_status', 'on_sale']) || strpos($key, 'filter_') === 0) {
            $has_filters = true;
            break;
        }
    }
    
    if ($has_filters): ?>
    <div class="filter-block">
        <a href="<?php echo esc_url(strtok($_SERVER['REQUEST_URI'], '?')); ?>" 
           class="btn btn-outline-secondary btn-sm clear-filters-btn">
            <i class="fas fa-eraser"></i>
            <?php _e('Clear All Filters', 'My-E-Shop'); ?>
        </a>
    </div>
    <?php endif; ?>

    <!-- Рекомендуемые товары (только на страницах категорий) -->
    <?php if (is_product_category()) : ?>
    <div class="recommend-section">
        <h3 class="section-title"><span><?php _e('Recommended', 'My-E-Shop'); ?></span></h3>
        
        <?php
        // Get current category
        $current_category = get_queried_object();
        
        // Get recommended products from the same category
        $recommended_args = array(
            'post_type' => 'product',
            'posts_per_page' => 4,
            'post_status' => 'publish',
            'orderby' => 'rand',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $current_category->term_id,
                ),
            ),
            'meta_query' => array(
                array(
                    'key' => '_stock_status',
                    'value' => 'instock',
                ),
            ),
        );
        
        $recommended_products = new WP_Query($recommended_args);
        
        if ($recommended_products->have_posts()) : ?>
            <div class="recommend-items">
                <?php while ($recommended_products->have_posts()) : $recommended_products->the_post(); ?>
                    <?php 
                    global $product;
                    $product_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'thumbnail');
                    ?>
                    <div class="recommend-item">
                        <div class="recommend-item-image">
                            <a href="<?php the_permalink(); ?>">
                                <?php if ($product_image) : ?>
                                    <img src="<?php echo esc_url($product_image[0]); ?>" 
                                         alt="<?php echo esc_attr(get_the_title()); ?>">
                                <?php else : ?>
                                    <div class="no-image-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="recommend-item-info">
                            <div class="recommend-item-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </div>
                            <div class="recommend-item-price">
                                <?php echo $product->get_price_html(); ?>
                            </div>
                            <?php if ($product->is_on_sale()) : ?>
                                <div class="recommend-item-sale">
                                    <span class="sale-badge"><?php _e('Sale!', 'My-E-Shop'); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <div class="no-recommendations">
                <i class="fas fa-info-circle"></i>
                <p><?php _e('No recommended products found.', 'My-E-Shop'); ?></p>
            </div>
        <?php endif; ?>
        
        <?php wp_reset_postdata(); ?>
    </div>
    <?php endif; ?>

</div> <!--SideBar-->