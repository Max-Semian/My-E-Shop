<?php
/**
 * Category Products Block Template
 */

// Проверяем, что WooCommerce активен
if (!class_exists('WooCommerce')) {
    return;
}

$category_id = isset($attributes['categoryId']) ? intval($attributes['categoryId']) : 0;
$category_slug = isset($attributes['categorySlug']) ? sanitize_text_field($attributes['categorySlug']) : '';
$show_breadcrumbs = isset($attributes['showBreadcrumbs']) ? $attributes['showBreadcrumbs'] : true;
$show_search = isset($attributes['showSearch']) ? $attributes['showSearch'] : true;
$products_per_slide = isset($attributes['productsPerSlide']) ? intval($attributes['productsPerSlide']) : 4;
$show_navigation = isset($attributes['showNavigation']) ? $attributes['showNavigation'] : true;
$show_pagination = isset($attributes['showPagination']) ? $attributes['showPagination'] : true;
$autoplay = isset($attributes['autoplay']) ? $attributes['autoplay'] : false;
$autoplay_speed = isset($attributes['autoplaySpeed']) ? intval($attributes['autoplaySpeed']) : 3000;

// Цвета с проверкой и значениями по умолчанию
$background_color = !empty($attributes['backgroundColor']) ? $attributes['backgroundColor'] : '#ffffff';
$title_color = !empty($attributes['titleColor']) ? $attributes['titleColor'] : '#333333';
$price_color = !empty($attributes['priceColor']) ? $attributes['priceColor'] : '#e74c3c';
$product_info_bg_color = !empty($attributes['productInfoBackgroundColor']) ? $attributes['productInfoBackgroundColor'] : '#f8f9fa';

// Отладка цветов (удалить после тестирования)
// error_log('Category Products Colors: BG=' . $background_color . ', Title=' . $title_color . ', Price=' . $price_color);

if ($category_id === 0) {
    return;
}

// Получаем категорию
$category = get_term($category_id, 'product_cat');
if (is_wp_error($category) || !$category) {
    return;
}

// Генерируем уникальный ID для слайдера
$slider_id = 'category-products-slider-' . uniqid();

// Создаем инлайн стили только для динамических цветов
$custom_styles = '';

if ($background_color && $background_color !== '#ffffff') {
    $custom_styles .= '.wp-block-my-e-shop-category-products .category-products-block { background-color: ' . esc_attr($background_color) . '; }';
}
if ($title_color && $title_color !== '#333333') {
    $custom_styles .= '.wp-block-my-e-shop-category-products .category-products-block .product-title a { color: ' . esc_attr($title_color) . '; }';
}
if ($price_color && $price_color !== '#e74c3c') {
    $custom_styles .= '.wp-block-my-e-shop-category-products .category-products-block .product-price, .wp-block-my-e-shop-category-products .category-products-block .product-price .woocommerce-Price-amount { color: ' . esc_attr($price_color) . '; }';
}
if ($product_info_bg_color && $product_info_bg_color !== '#f8f9fa') {
    $custom_styles .= '.wp-block-my-e-shop-category-products .category-products-block .product-info { background-color: ' . esc_attr($product_info_bg_color) . '; }';
}

// Добавляем стили для центрирования поиска
$custom_styles .= '
.wp-block-my-e-shop-category-products .category-products-content .category-top-section .category-search {
    flex: 1 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: end !important;
    margin-bottom: 0 !important;
}
.wp-block-my-e-shop-category-products .search-input-wrapper {
    margin: 0 auto !important;
}
';
?>

<?php if ($custom_styles): ?>
    <style><?php echo $custom_styles; ?></style>
<?php endif; ?>

<div class="wp-block-my-e-shop-category-products">
<div class="category-products-block" 
     data-category-id="<?php echo esc_attr($category_id); ?>"
     style="<?php echo $background_color ? 'background-color: ' . esc_attr($background_color) . ';' : ''; ?>">
    <div class="category-products-content">
    <?php if ($show_breadcrumbs || $show_search): ?>
        <div class="category-top-section">
            <?php if ($show_breadcrumbs): ?>
<!-- Хлебные крошки -->
<?php
// Определяем темную или светлую тему
$is_dark_theme = ($background_color === '#2C2C2C' || $background_color === '#2c2c2c');
$breadcrumb_class = $is_dark_theme ? 'product-dark-breadcrumbs' : 'product-light-breadcrumbs';
?>
<div class="<?php echo esc_attr($breadcrumb_class); ?>">
    <div class="container">
        <nav class="woocommerce-breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>">HOME</a> / <?php
            global $post;
            if ($post->post_parent) {
                $parent_id = $post->post_parent;
                $breadcrumbs_array = array();
                while ($parent_id) {
                    $page = get_post($parent_id);
                    $breadcrumbs_array[] = array(
                        'title' => $page->post_title,
                        'url' => get_permalink($page->ID)
                    );
                    $parent_id = $page->post_parent;
                }
                $breadcrumbs_array = array_reverse($breadcrumbs_array);
                foreach ($breadcrumbs_array as $crumb) {
                    ?><a href="<?php echo esc_url($crumb['url']); ?>"><?php echo strtoupper(esc_html($crumb['title'])); ?></a> / <?php
                }
            }
            $page_title = get_the_title();
            if (empty($page_title)) {
                $page_title = get_the_title($post->ID);
            }
            if (empty($page_title)) {
                $page_title = $post->post_title;
            }
            echo strtoupper(esc_html($page_title));
            ?></nav>
    </div>
</div>
            <?php endif; ?>

            <?php if ($show_search): ?>
                <div class="category-search">
                    <form class="category-search-form" role="search">
                        <div class="search-input-wrapper">
                            <input type="search" 
                                   class="category-search-input" 
                                   placeholder="Search <?php echo esc_attr($category->name); ?>..."
                                   name="category_search"
                                   data-category-id="<?php echo esc_attr($category_id); ?>">
                            <button type="submit" class="category-search-button">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21 21L16.514 16.506L21 21ZM19 10.5C19 15.194 15.194 19 10.5 19C5.806 19 2 15.194 2 10.5C2 5.806 5.806 2 10.5 2C15.194 2 19 5.806 19 10.5Z" 
                                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="category-products-slider-wrapper">
        <div class="category-products-slider" 
             id="<?php echo esc_attr($slider_id); ?>"
             data-slides-per-view="<?php echo esc_attr($products_per_slide); ?>"
             data-autoplay="<?php echo $autoplay ? 'true' : 'false'; ?>"
             data-autoplay-speed="<?php echo esc_attr($autoplay_speed); ?>"
             data-show-navigation="<?php echo $show_navigation ? 'true' : 'false'; ?>"
             data-show-pagination="<?php echo $show_pagination ? 'true' : 'false'; ?>">
            
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <?php
                    // Получаем товары категории
                    $products = wc_get_products(array(
                        'category' => array($category->slug),
                        'limit' => 20, // Ограничиваем количество для демо
                        'status' => 'publish',
                        'orderby' => 'menu_order',
                        'order' => 'ASC'
                    ));

                    if ($products):
                        foreach ($products as $product):
                            $product_id = $product->get_id();
                            $product_name = $product->get_name();
                            $product_price = $product->get_price_html();
                            $product_image = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'full');
                            $product_link = get_permalink($product_id);
                            ?>
                            <div class="swiper-slide">
                                <div class="product-card">
                                    <div class="product-image-wrapper">
                                        <a href="<?php echo esc_url($product_link); ?>">
                                            <?php if ($product_image): ?>
                                                <img src="<?php echo esc_url($product_image[0]); ?>" 
                                                     alt="<?php echo esc_attr($product_name); ?>"
                                                     class="product-image">
                                            <?php else: ?>
                                                <div class="product-no-image">
                                                    <span>Нет изображения</span>
                                                </div>
                                            <?php endif; ?>
                                        </a>
                                        <button class="add-to-wishlist" data-product-id="<?php echo esc_attr($product_id); ?>">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/HeartFavorit.png" alt="Добавить в избранное">
                                        </button>
                                        <button class="quick-view-btn" data-product-id="<?php echo esc_attr($product_id); ?>">
                                            QUIK VIEW
                                        </button>
                                    </div>
                                    <div class="product-info">
                                        <h3 class="product-title">
                                            <a href="<?php echo esc_url($product_link); ?>">
                                                <?php echo esc_html($product_name); ?>
                                            </a>
                                        </h3>
                                        <div class="product-price">
                                            <?php echo $product_price; ?>
                                        </div>
                                        <button class="add-to-cart-btn" 
                                                data-product-id="<?php echo esc_attr($product_id); ?>"
                                                data-product-type="<?php echo esc_attr($product->get_type()); ?>">
                                            Добавить в корзину
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php
                        endforeach;
                    else:
                        ?>
                        <div class="swiper-slide">
                            <div class="no-products-message">
                                <p>В этой категории пока нет товаров</p>
                            </div>
                        </div>
                        <?php
                    endif;
                    ?>
                </div>

            <?php if ($show_pagination): ?>
                    <div class="swiper-pagination"></div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($show_navigation): ?>
            <div class="slider-navigation">
                <div class="swiper-button-prev">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="swiper-button-next">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        <?php endif; ?>
        </div>
    </div>
    </div> <!-- /.category-products-content -->
</div>
</div>