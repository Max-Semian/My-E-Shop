<?php
/**
 * Category Products Block Template
 */

// Check that WooCommerce is active
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

// Colors with validation and default values
$background_color = !empty($attributes['backgroundColor']) ? $attributes['backgroundColor'] : '#ffffff';
$title_color = !empty($attributes['titleColor']) ? $attributes['titleColor'] : '#333333';
$price_color = !empty($attributes['priceColor']) ? $attributes['priceColor'] : '#e74c3c';
$product_info_bg_color = !empty($attributes['productInfoBackgroundColor']) ? $attributes['productInfoBackgroundColor'] : '#f8f9fa';

// Color debugging (remove after testing)
// error_log('Category Products Colors: BG=' . $background_color . ', Title=' . $title_color . ', Price=' . $price_color);

if ($category_id === 0) {
    return;
}

// Get the category
$category = get_term($category_id, 'product_cat');
if (is_wp_error($category) || !$category) {
    return;
}

// Generate a unique ID for the slider
$slider_id = 'category-products-slider-' . uniqid();

// Create inline styles only for dynamic colors
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

// Add styles to center the search
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

// Determine dark or light theme for the button styles
$is_dark_theme = ($background_color === '#2C2C2C' || $background_color === '#2c2c2c');
$is_light_beige = ($background_color === '#F4F0EB' || $background_color === '#f4f0eb');

// Add button styles depending on the theme
if (!$is_dark_theme) {
    $button_bg = $is_light_beige ? '#F4F0EB' : '#ffffff';
    $custom_styles .= '
    .wp-block-my-e-shop-category-products .slider-navigation .swiper-button-prev,
    .wp-block-my-e-shop-category-products .slider-navigation .swiper-button-next {
        background-color: ' . $button_bg . ' !important;
        border-color: #AA2DD0 !important;
        color: #AA2DD0 !important;
    }
    
    .wp-block-my-e-shop-category-products .slider-navigation .swiper-button-prev:hover,
    .wp-block-my-e-shop-category-products .slider-navigation .swiper-button-next:hover {
        background-color: #AA2DD0 !important;
        color: ' . $button_bg . ' !important;
    }
    ';
}

// Add animation for the slider navigation buttons
$custom_styles .= '
.wp-block-my-e-shop-category-products .slider-navigation .swiper-button-prev,
.wp-block-my-e-shop-category-products .slider-navigation .swiper-button-next {
    overflow: hidden !important;
    opacity: 1 !important;
    pointer-events: auto !important;
}

.wp-block-my-e-shop-category-products .slider-navigation .swiper-button-prev .nav-icon,
.wp-block-my-e-shop-category-products .slider-navigation .swiper-button-next .nav-icon {
    position: relative;
    z-index: 10;
    transition: opacity 0.1s ease;
    pointer-events: none;
}

.wp-block-my-e-shop-category-products .slider-navigation .swiper-button-prev:hover .nav-icon,
.wp-block-my-e-shop-category-products .slider-navigation .swiper-button-next:hover .nav-icon {
    opacity: 0;
    transition: opacity 1s ease;
}

.wp-block-my-e-shop-category-products .slider-navigation .swiper-button-prev .nav-hover-icon,
.wp-block-my-e-shop-category-products .slider-navigation .swiper-button-next .nav-hover-icon {
    opacity: 0;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 10;
    transition: opacity 0.1s ease;
    pointer-events: none;
}

.wp-block-my-e-shop-category-products .slider-navigation .swiper-button-prev:hover .nav-hover-icon,
.wp-block-my-e-shop-category-products .slider-navigation .swiper-button-next:hover .nav-hover-icon {
    opacity: 1;
    transition: opacity 1s ease;
}

.wp-block-my-e-shop-category-products .slider-navigation .anim-layer {
    position: absolute;
    width: 144px;
    height: 128px;
    top: -32px;
    left: -8px;
    transform: rotate(12deg) scaleX(0);
    transform-origin: left;
    transition: transform 1s ease;
    pointer-events: none;
    z-index: 1;
}

.wp-block-my-e-shop-category-products .slider-navigation .swiper-button-prev .anim-layer {
    left: auto;
    right: -8px;
    transform: rotate(-12deg) scaleX(0);
    transform-origin: right;
}

.wp-block-my-e-shop-category-products .slider-navigation .anim-layer-1 {
    background-color: #000000;
    transition: transform 0.5s ease;
}

.wp-block-my-e-shop-category-products .slider-navigation .anim-layer-2 {
    background-color: #c084fc;
    transition: transform 0.7s ease;
}

.wp-block-my-e-shop-category-products .slider-navigation .anim-layer-3 {
    background-color: #9333ea;
    transition: transform 1s ease;
}

.wp-block-my-e-shop-category-products .slider-navigation .swiper-button-next:hover .anim-layer {
    transform: rotate(12deg) scaleX(1);
}

.wp-block-my-e-shop-category-products .slider-navigation .swiper-button-prev:hover .anim-layer {
    transform: rotate(-12deg) scaleX(1);
}

.wp-block-my-e-shop-category-products .slider-navigation .swiper-button-prev:hover .anim-layer-1,
.wp-block-my-e-shop-category-products .slider-navigation .swiper-button-next:hover .anim-layer-1 {
    transition: transform 0.5s ease;
}

.wp-block-my-e-shop-category-products .slider-navigation .swiper-button-prev:hover .anim-layer-2,
.wp-block-my-e-shop-category-products .slider-navigation .swiper-button-next:hover .anim-layer-2 {
    transition: transform 0.7s ease;
}

.wp-block-my-e-shop-category-products .slider-navigation .swiper-button-prev:hover .anim-layer-3,
.wp-block-my-e-shop-category-products .slider-navigation .swiper-button-next:hover .anim-layer-3 {
    transition: transform 1s ease;
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
<!-- Breadcrumbs -->
<?php
// Determine dark or light theme
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
                    // Get the category products
                    $products = wc_get_products(array(
                        'category' => array($category->slug),
                        'limit' => 20, // Limit the number for the demo
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
                                                    <span>No image</span>
                                                </div>
                                            <?php endif; ?>
                                        </a>
                                        <button class="add-to-wishlist" data-product-id="<?php echo esc_attr($product_id); ?>">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/HeartFavorit.png" alt="Add to wishlist">
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
                                            Add to cart
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
                                <p>There are no products in this category yet</p>
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
                    <svg class="nav-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <svg class="nav-hover-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="anim-layer anim-layer-1"></span>
                    <span class="anim-layer anim-layer-2"></span>
                    <span class="anim-layer anim-layer-3"></span>
                </div>
                <div class="swiper-button-next">
                    <svg class="nav-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <svg class="nav-hover-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="anim-layer anim-layer-1"></span>
                    <span class="anim-layer anim-layer-2"></span>
                    <span class="anim-layer anim-layer-3"></span>
                </div>
            </div>
        <?php endif; ?>
        </div>
    </div>
    </div> <!-- /.category-products-content -->
</div>
</div>