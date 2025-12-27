<?php

defined( 'ABSPATH' ) || exit;

global $product;
?>

<div class="product-page-container">
	<?php do_action( 'woocommerce_before_single_product' ); ?>
</div>

<!-- Breadcrumbs section for dark theme -->
<div class="product-light-breadcrumbs">
    <?php
    /**
     * Output the WooCommerce Breadcrumb
     */
    woocommerce_breadcrumb();
    ?>
</div>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'product-content-wrapper product-light-theme', $product ); ?>>
    <input type="hidden" id="product_id_field" name="product_id" value="<?php echo esc_attr(get_the_ID()); ?>">
    
    <div class="product-layout">
        <div class="product-images-section">
            <div class="product-light-image-wrapper">
                <div id="carouselExampleFade" class="carousel slide carousel-fade">
                    <div class="carousel-inner">
						<?php
						$product_img_id = $product->get_image_id();
						if ( $product_img_id ) {
							$main_img = wp_get_attachment_url( $product_img_id );
						} else {
							$main_img = wc_placeholder_img_src( 'woocommerce_full' );
						}
						$product_img_ids = $product->get_gallery_image_ids();
						$has_gallery = !empty($product_img_ids);
						?>
                        <div class="carousel-item active">
                            <img data-fancybox="gallery" data-src="<?php echo $main_img; ?>" src="<?php echo $main_img; ?>" class="d-block w-100 product-main-image product-gallery-image"
                                 alt="<?php echo $product->get_title(); ?>">
                        </div>
						<?php if ( $has_gallery ): ?>
							<?php foreach ( $product_img_ids as $product_img_id ): ?>
                                <div class="carousel-item">
                                    <img data-fancybox="gallery" data-src="<?php echo wp_get_attachment_url( $product_img_id ); ?>" src="<?php echo wp_get_attachment_url( $product_img_id ); ?>"
                                         class="d-block w-100 product-main-image product-gallery-image" alt="<?php echo $product->get_title(); ?>">
                                </div>
							<?php endforeach; ?>
						<?php endif; ?>
                    </div>
                </div>
                <?php if ( $has_gallery ): ?>
                <button class="carousel-control-prev product-light-carousel-control" type="button" id="carouselPrevBtn" onclick="window.handleCarouselNav('prev'); return false;">
                    <img class="carousel-control-prev-icon" aria-hidden="true" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/left_arrow.png' ); ?>" alt="Previous">
                </button>
                <button class="carousel-control-next product-light-carousel-control" type="button" id="carouselNextBtn" onclick="window.handleCarouselNav('next'); return false;">
                    <img class="carousel-control-next-icon" aria-hidden="true" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/right_arrow.png' ); ?>" alt="Next">
                </button>
                <?php endif; ?>
                </div>
        </div>
        <div class="product-info-section">
            <div class="product-light-content">
                <?php woocommerce_show_product_sale_flash(); ?>
                
                <!-- Название товара -->
                <h1 class="product-title"><?php the_title(); ?></h1>
                
                <!-- Цена товара -->
                <div class="product-price">
                    <?php echo $product->get_price_html(); ?>
                </div>
                
                <!-- Выбор цвета через кружочки -->
                <?php 
                // Для вариативных продуктов не показываем кастомные селекты
                if ( !$product->is_type( 'variable' ) ) {
                    // Сначала проверяем атрибуты WooCommerce
                    $wc_colors = $product->get_attribute('Color');
                    $colors_array = array();
                    
                    // Отладочная информация (можно удалить после тестирования)
                    // echo '<!-- Debug: WC Colors = ' . $wc_colors . ' -->';
                    
                    if ( !empty($wc_colors) ) {
                        // Обрабатываем WooCommerce атрибуты
                        $wc_colors_list = array_map('trim', explode(',', $wc_colors));
                        foreach ($wc_colors_list as $color_name) {
                            $colors_array[] = array(
                                'name' => $color_name,
                                'color' => get_color_hex_by_name($color_name)
                            );
                        }
                    } else {
                        // Fallback на ACF поля
                        $colors = get_post_meta( get_the_ID(), '_product_colors', true );
                        if ( !empty($colors) ) {
                            $colors_array = json_decode($colors, true);
                        }
                    }
                    
                    if ( is_array($colors_array) && !empty($colors_array) ) {
                ?>
                <div class="product-color-picker">
                    <label class="product-option-label">Colors</label>
                    <div class="color-picker-options">
                        <?php foreach ($colors_array as $index => $color) : ?>
                            <div class="color-picker-item" data-color="<?php echo esc_attr($color['color']); ?>" title="<?php echo esc_attr($color['name']); ?>">
                                <input type="radio" name="product_color" value="<?php echo esc_attr($color['name']); ?>" id="color_<?php echo $index; ?>" <?php echo $index === 0 ? 'checked' : ''; ?>>
                                <label for="color_<?php echo $index; ?>" class="color-circle" style="background-color: <?php echo esc_attr($color['color']); ?>">
                                    <span class="checkmark">✓</span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php } } ?>
                
                <!-- Выбор размера выпадающим списком -->
                <?php 
                // Для вариативных продуктов не показываем кастомные селекты
                if ( !$product->is_type( 'variable' ) ) {
                    // Сначала проверяем атрибуты WooCommerce для размеров
                    $wc_sizes = $product->get_attribute('Size');
                    $sizes_array = array();
                    
                    // Отладочная информация (можно удалить после тестирования)
                    // echo '<!-- Debug: WC Sizes = ' . $wc_sizes . ' -->';
                    
                    if ( !empty($wc_sizes) ) {
                        // Обрабатываем WooCommerce атрибуты
                        $sizes_array = array_map('trim', explode(',', $wc_sizes));
                    } else {
                        // Fallback на ACF поля
                        $sizes = get_post_meta( get_the_ID(), '_product_sizes', true );
                        if ( !empty($sizes) ) {
                            $sizes_array = array_map('trim', explode(',', $sizes));
                        }
                    }
                    
                    // Get size guide image from ACF
                    $size_guide_image = get_field('size_guide_image');
                    
                    if ( !empty($sizes_array) ) {
                        // Получаем атрибут Fit
                        $wc_fit = $product->get_attribute('Fit');
                        $fit_array = array();
                        if ( !empty($wc_fit) ) {
                            $fit_array = array_map('trim', explode(',', $wc_fit));
                        }
                ?>
                <div class="product-size-picker">
                    <?php if ( !empty($fit_array) ) : ?>
                    <div class="custom-select-wrapper">
                        <select name="product_fit" id="fit-select" class="custom-select">
                            <option value="">Select Fit</option>
                            <?php foreach ($fit_array as $fit) : ?>
                                <option value="<?php echo esc_attr($fit); ?>"><?php echo esc_html($fit); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="select-arrow">
                            <svg width="12" height="8" viewBox="0 0 12 8" fill="none">
                                <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="custom-select-wrapper">
                        <select name="product_size" id="size-select" class="custom-select">
                            <option value="">Select Size</option>
                            <?php foreach ($sizes_array as $size) : ?>
                                <option value="<?php echo esc_attr($size); ?>"><?php echo esc_html($size); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="select-arrow">
                            <svg width="12" height="8" viewBox="0 0 12 8" fill="none">
                                <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <?php } } ?>
                
                <!-- Кастомные селекты для вариативных продуктов -->
                <?php if ( $product->is_type( 'variable' ) ) : ?>
                
                <!-- Color Picker для вариативных продуктов -->
                <?php 
                // Получаем атрибуты из product data
                $product_attributes = $product->get_attributes();
                $color_options = array();
                $color_attribute_name = '';
                $default_color = '';
                
                // Ищем атрибут цвета
                foreach ($product_attributes as $attribute_name => $attribute) {
                    if (strpos(strtolower($attribute_name), 'color') !== false || strpos(strtolower($attribute_name), 'colour') !== false) {
                        if ($attribute->get_variation()) {
                            $color_attribute_name = 'attribute_' . $attribute_name;
                            
                            // Получаем дефолтное значение
                            $default_attributes = $product->get_default_attributes();
                            $default_color = isset($default_attributes[str_replace('attribute_', '', $color_attribute_name)]) 
                                ? $default_attributes[str_replace('attribute_', '', $color_attribute_name)] 
                                : '';
                            
                            $terms = $attribute->get_terms();
                            if ($terms) {
                                foreach ($terms as $term) {
                                    $color_options[] = $term->slug;
                                }
                            } else {
                                // Если это кастомный атрибут
                                $color_options = $attribute->get_options();
                            }
                            break;
                        }
                    }
                }
                
                if (!empty($color_options)) : ?>
                <div class="product-color-picker">
                    <label class="product-option-label">Colors</label>
                    <div class="color-picker-options">
                        <?php foreach ($color_options as $index => $color_slug) : 
                            $color_name = ucfirst(str_replace('-', ' ', $color_slug));
                            $color_hex = get_color_hex_by_name($color_name);
                            
                            // Проверяем, является ли этот цвет дефолтным
                            $is_default = ($default_color === $color_slug) || ($default_color === '' && $index === 0);
                        ?>
                            <div class="color-picker-item" data-color="<?php echo esc_attr($color_hex); ?>" title="<?php echo esc_attr($color_name); ?>">
                                <input type="radio" name="<?php echo esc_attr($color_attribute_name); ?>" class="variation-color-input" value="<?php echo esc_attr($color_slug); ?>" id="color_var_<?php echo $index; ?>" <?php echo $is_default ? 'checked' : ''; ?>>
                                <label for="color_var_<?php echo $index; ?>" class="color-circle" style="background-color: <?php echo esc_attr($color_hex); ?>">
                                    <span class="checkmark">✓</span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Size Picker для вариативных продуктов -->
                <?php 
                $size_options = array();
                $size_attribute_name = '';
                $default_size = '';
                
                // Ищем атрибут размера
                foreach ($product_attributes as $attribute_name => $attribute) {
                    if (strpos(strtolower($attribute_name), 'size') !== false) {
                        if ($attribute->get_variation()) {
                            $size_attribute_name = 'attribute_' . $attribute_name;
                            
                            // Получаем дефолтное значение для размера
                            $default_attributes = $product->get_default_attributes();
                            $default_size = isset($default_attributes[str_replace('attribute_', '', $size_attribute_name)]) 
                                ? $default_attributes[str_replace('attribute_', '', $size_attribute_name)] 
                                : '';
                            
                            $terms = $attribute->get_terms();
                            if ($terms) {
                                foreach ($terms as $term) {
                                    $size_options[] = $term->slug;
                                }
                            } else {
                                // Если это кастомный атрибут
                                $size_options = $attribute->get_options();
                            }
                            break;
                        }
                    }
                }
                
                if (!empty($size_options)) : 
                    // Получаем атрибут Fit для вариативных продуктов
                    $fit_options = array();
                    $fit_attribute_name = '';
                    $default_fit = '';
                    
                    foreach ($product_attributes as $attribute_name => $attribute) {
                        if (strpos(strtolower($attribute_name), 'fit') !== false) {
                            if ($attribute->get_variation()) {
                                $fit_attribute_name = 'attribute_' . $attribute_name;
                                
                                $default_attributes = $product->get_default_attributes();
                                $default_fit = isset($default_attributes[str_replace('attribute_', '', $fit_attribute_name)]) 
                                    ? $default_attributes[str_replace('attribute_', '', $fit_attribute_name)] 
                                    : '';
                                
                                $terms = $attribute->get_terms();
                                if ($terms) {
                                    foreach ($terms as $term) {
                                        $fit_options[] = $term->slug;
                                    }
                                } else {
                                    $fit_options = $attribute->get_options();
                                }
                                break;
                            }
                        }
                    }
                ?>
                <div class="product-size-picker">
                    <?php if (!empty($fit_options)) : ?>
                    <div class="custom-select-wrapper">
                        <select name="<?php echo esc_attr($fit_attribute_name); ?>" id="fit-select-var" class="custom-select variation-fit-select">
                            <option value="">Select Fit</option>
                            <?php foreach ($fit_options as $fit_slug) : 
                                $fit_name = ucfirst(str_replace('-', ' ', $fit_slug));
                                $is_default_fit = ($default_fit === $fit_slug);
                            ?>
                                <option value="<?php echo esc_attr($fit_slug); ?>" <?php echo $is_default_fit ? 'selected' : ''; ?>><?php echo esc_html($fit_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="select-arrow">
                            <svg width="12" height="8" viewBox="0 0 12 8" fill="none">
                                <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="custom-select-wrapper">
                        <select name="<?php echo esc_attr($size_attribute_name); ?>" id="size-select-var" class="custom-select variation-size-select">
                            <option value="">Select Size</option>
                            <?php foreach ($size_options as $size_slug) : 
                                $size_name = ucfirst(str_replace('-', ' ', $size_slug));
                                $is_default_size = ($default_size === $size_slug);
                            ?>
                                <option value="<?php echo esc_attr($size_slug); ?>" <?php echo $is_default_size ? 'selected' : ''; ?>><?php echo esc_html($size_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="select-arrow">
                            <svg width="12" height="8" viewBox="0 0 12 8" fill="none">
                                <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php endif; ?>
                
                <!-- Форма добавления в корзину -->
                <?php if ( $product->is_type( 'variable' ) ) : ?>
                    <!-- Скрытая стандартная WooCommerce форма вариаций для функциональности -->
                    <div style="display: none;">
                        <?php woocommerce_variable_add_to_cart(); ?>
                    </div>
                    
                    <!-- Кастомная форма с quantity и add to cart для вариативных продуктов -->
                    <div class="quantity-add-to-cart">
                        <!-- Добавить в избранное -->
                        <button class="add-to-favorite-btn-light" title="Add to Favorites">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                            Add To Favorite
                        </button>
                        <button type="button" class="single_add_to_cart_button-light button alt custom-variation-add-to-cart">
                            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/cart-icon.svg' ); ?>" alt="Add to cart">
                            Add to cart
                        </button>
                    </div>
                <?php else : ?>
                    <!-- Для простых продуктов используем кастомную форму -->
                <form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
                    <div class="quantity-add-to-cart">
                            <!-- Добавить в избранное -->
                        <button class="add-to-favorite-btn-light" title="Add to Favorites">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                            Add To Favorite
                        </button>
                        <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt">
                            Add to cart
                        </button>
                    </div>
                </form>
                <?php endif; ?>

                <!-- Accordion Content Area (shows selected content) -->
                <div class="product-accordion-content" id="accordionContent"></div>

                <!-- Product Information Accordion Navigation -->
                <?php
                // Get ACF fields
                $description = get_field('description');
                $product_details = get_field('product_details');
                $shipping_return = get_field('shipping_return');
                ?>
                <div class="product-info-accordion">
                    <?php if (!empty($description)) : ?>
                    <button class="accordion-nav-btn" type="button" data-target="description">Description</button>
                    <?php endif; ?>
                    <?php if (!empty($product_details)) : ?>
                    <button class="accordion-nav-btn" type="button" data-target="details">Details</button>
                    <?php endif; ?>
                    <?php if (!empty($shipping_return)) : ?>
                    <button class="accordion-nav-btn" type="button" data-target="shipping">Shipping</button>
                    <?php endif; ?>
                    <button class="accordion-nav-btn" type="button" data-target="enquiries">Enquiries</button>
                </div>

                <!-- Hidden content blocks -->
                <div class="accordion-hidden-content">
                    <?php if (!empty($description)) : ?>
                    <div id="content-description"><?php echo wpautop($description); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($product_details)) : ?>
                    <div id="content-details"><?php echo wpautop($product_details); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($shipping_return)) : ?>
                    <div id="content-shipping"><?php echo wpautop($shipping_return); ?></div>
                    <?php endif; ?>
                    <div id="content-enquiries">
                        <p>For any questions about this product, please contact us at:</p>
                        <p>Email: support@example.com</p>
                        <p>Phone: +1 234 567 890</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
        <!-- Size Guide Modal -->
        <?php 
        // Get size guide image from ACF
        $size_guide_image = get_field('size_guide_image');
        if ( !empty($size_guide_image) ) : 
        ?>
        <div class="modal fade" id="sizeGuideModal" tabindex="-1" aria-labelledby="sizeGuideModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content product-light-modal">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sizeGuideModalLabel">Size Guide</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body size-guide-modal-body">
                        <?php 
                        // If $size_guide_image is an array (ACF image field returns array)
                        if (is_array($size_guide_image)) {
                            echo '<img src="' . esc_url($size_guide_image['url']) . '" alt="' . esc_attr($size_guide_image['alt']) . '" class="size-guide-image" />';
                        } 
                        // If it's an image ID
                        else if (is_numeric($size_guide_image)) {
                            echo wp_get_attachment_image($size_guide_image, 'full', false, ['class' => 'size-guide-image']);
                        }
                        // If it's a URL
                        else {
                            echo '<img src="' . esc_url($size_guide_image) . '" alt="Size Guide" class="size-guide-image" />';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Category Products Section -->
    <div class="category-products-section" id="category-products-section">
        <div class="product-light-related">
            <h2><?php echo esc_html__('YOU MAY ALSO LIKE', 'My-E-Shop'); ?></h2>
            <div class="category-products-container">
                <?php
                // Get the product categories
                $product_categories = wp_get_post_terms(get_the_ID(), 'product_cat');
                
                if (!empty($product_categories)) {
                    // Get the first category ID (you can modify this to use a specific category)
                    $category_id = $product_categories[0]->term_id;
                    $current_product_id = get_the_ID();
                    
                    // Query for products in the same category
                    $args = array(
                        'post_type' => 'product',
                        'posts_per_page' => 3, // Show only 3 products
                        'post__not_in' => array($current_product_id), // Exclude current product
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'product_cat',
                                'field' => 'term_id',
                                'terms' => $category_id,
                            ),
                        ),
                        'orderby' => 'rand', // Random order
                    );
                    
                    $category_products = new WP_Query($args);
                    
                    if ($category_products->have_posts()) {
                        echo '<div class="related-products-grid">';
                        
                        while ($category_products->have_posts()) {
                            $category_products->the_post();
                            global $product;
                            ?>
                            <div class="related-product-item">
                                <div class="product-light-card">
                                    <a href="<?php the_permalink(); ?>" class="product-image-link">
                                        <?php 
                                        if (has_post_thumbnail()) {
                                            $thumbnail_id = get_post_thumbnail_id();
                                            $image_url = wp_get_attachment_image_url($thumbnail_id, 'woocommerce_thumbnail');
                                            echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr(get_the_title()) . '" class="related-product-image">';
                                        } else {
                                            echo wc_placeholder_img('woocommerce_thumbnail'); 
                                        }
                                        ?>
                                    </a>
                                    <div class="product-card-body">
                                        <div class="product-card-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </div>
                                        <div class="product-card-price"><?php echo $product->get_price_html(); ?></div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        
                        echo '</div>';
                        wp_reset_postdata();
                    } else {
                        echo '<p class="no-products-message">' . esc_html__('No related products found.', 'My-E-Shop') . '</p>';
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="reviews-section" id="reviews-section">
        <div class="product-light-reviews">
            <h2><?php echo esc_html__('Reviews', 'My-E-Shop'); ?></h2>
            <div id="reviews-container" data-loaded="false">
                <div class="reviews-loading">
                    <p><?php echo esc_html__('Loading reviews...', 'My-E-Shop'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced JavaScript for Product Interactions -->
    <script>
    // Function to handle carousel navigation
    function handleCarouselNav(direction) {
        var carousel = window.productCarousel;
        if (!carousel) {
            var el = document.getElementById('carouselExampleFade');
            if (el && typeof bootstrap !== 'undefined') {
                carousel = bootstrap.Carousel.getInstance(el);
                if (!carousel) {
                    carousel = new bootstrap.Carousel(el, {
                        interval: false,
                        wrap: true
                    });
                    window.productCarousel = carousel;
                }
            }
        }
        
        if (carousel) {
            if (direction === 'prev') {
                carousel.prev();
            } else if (direction === 'next') {
                carousel.next();
            }
        }
    }
    
    // Make function global
    window.handleCarouselNav = handleCarouselNav;
    
    // Block ALL events on carousel buttons at capture phase
    ['click', 'mousedown', 'pointerdown', 'touchstart'].forEach(function(eventType) {
        document.addEventListener(eventType, function(e) {
            var button = e.target.closest('.product-light-carousel-control');
            if (button) {
                e.stopImmediatePropagation();
                e.preventDefault();
                
                // Trigger navigation on click (desktop) or touchstart (mobile)
                if (eventType === 'click') {
                    var direction = button.classList.contains('carousel-control-prev') ? 'prev' : 'next';
                    handleCarouselNav(direction);
                }
                return false;
            }
        }, true); // capture phase
    });
    
    jQuery(document).ready(function($) {
        // Initialize Bootstrap Carousel with delay to ensure Bootstrap is loaded
        setTimeout(function() {
            var myCarousel = document.getElementById('carouselExampleFade');
            if (myCarousel && typeof bootstrap !== 'undefined') {
                // Dispose existing instance if any
                var existingInstance = bootstrap.Carousel.getInstance(myCarousel);
                if (existingInstance) {
                    existingInstance.dispose();
                }
                
                var carousel = new bootstrap.Carousel(myCarousel, {
                    interval: false,
                    wrap: true,
                    touch: true,
                    keyboard: true
                });
                
                // Store carousel instance globally for event handlers
                window.productCarousel = carousel;
            }
        }, 300);
        
        // Quantity controls
        $('.qty-btn.plus').on('click', function() {
            var input = $(this).siblings('input[type="number"]');
            var currentVal = parseInt(input.val()) || 1;
            input.val(currentVal + 1);
        });
        
        $('.qty-btn.minus').on('click', function() {
            var input = $(this).siblings('input[type="number"]');
            var currentVal = parseInt(input.val()) || 1;
            if (currentVal > 1) {
                input.val(currentVal - 1);
            }
        });
        
        // Color picker functionality
        $('.color-picker-item input[type="radio"]').on('change', function() {
            var selectedColor = $(this).val();
            var colorHex = $(this).closest('.color-picker-item').data('color');
            
            console.log('Selected color: ' + selectedColor + ' (' + colorHex + ')');
            
            // Add visual feedback
            $('.color-picker-item').removeClass('selected');
            $(this).closest('.color-picker-item').addClass('selected');
            
            // Here you can add logic to change product images based on color
        });
        
        // Size picker functionality
        $('#size-select').on('change', function() {
            var selectedSize = $(this).val();
            
            if (selectedSize) {
                console.log('Selected size: ' + selectedSize);
                
                // Add visual feedback
                $(this).addClass('selected');
                
                // Enable add to cart button if it was disabled
                $('.single_add_to_cart_button').prop('disabled', false);
            } else {
                $(this).removeClass('selected');
            }
        });
        
        // Add to favorite functionality
        $('.add-to-favorite-btn-light').on('click', function() {
            var button = $(this);
            var productId = <?php echo get_the_ID(); ?>;
            
            // Toggle visual state
            button.toggleClass('favorited');
            
            if (button.hasClass('favorited')) {
                button.find('svg').attr('fill', '#ff6b9d');
                button.find('path').attr('fill', '#ff6b9d');
                
                console.log('Added to favorites: ' + productId);
                showMessage('Added to favorites!', 'success');
            } else {
                button.find('svg').attr('fill', 'none');
                button.find('path').attr('fill', 'none');
                
                console.log('Removed from favorites: ' + productId);
                showMessage('Removed from favorites!', 'info');
            }
        });
        
        // Form validation before submit
        $('form.cart').on('submit', function(e) {
            var selectedColor = $('input[name="product_color"]:checked').val();
            var selectedSize = $('#size-select').val();
            
            // Check if color is required and selected
            if ($('.product-color-picker').length > 0 && !selectedColor) {
                e.preventDefault();
                showMessage('Please select a color!', 'error');
                return false;
            }
            
            // Check if size is required and selected
            if ($('.product-size-picker').length > 0 && !selectedSize) {
                e.preventDefault();
                showMessage('Please select a size!', 'error');
                return false;
            }
            
            // Add loading state to button
            $('.single_add_to_cart_button').addClass('loading').text('Adding...');
        });
        
        // Helper function to show messages
        function showMessage(message, type) {
            var messageClass = 'product-message-' + type;
            var messageHtml = '<div class="product-message ' + messageClass + '">' + message + '</div>';
            
            // Remove existing messages
            $('.product-message').remove();
            
            // Add new message
            $('.product-light-content').prepend(messageHtml);
            
            // Auto remove after 3 seconds
            setTimeout(function() {
                $('.product-message').fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }
        
        // Accordion functionality for product details sections
        $('.accordion-toggle').on('click', function() {
            const $section = $(this).closest('.detail-section');
            const $content = $section.find('.detail-content');
            
            // Toggle collapsed class
            $section.toggleClass('collapsed');
            
            // Slide toggle content with animation
            if ($section.hasClass('collapsed')) {
                $content.slideUp(300);
            } else {
                $content.slideDown(300);
            }
        });

        // Product Info Accordion Navigation functionality
        $('.product-info-accordion .accordion-nav-btn').on('click', function() {
            const $btn = $(this);
            const target = $btn.data('target');
            const $contentArea = $('#accordionContent');
            const $sourceContent = $('#content-' + target);
            
            // Check if already active
            if ($btn.hasClass('active')) {
                // Close
                $btn.removeClass('active');
                $contentArea.hide().html('');
            } else {
                // Switch content
                $('.accordion-nav-btn').removeClass('active');
                $btn.addClass('active');
                $contentArea.html($sourceContent.html()).show();
            }
        });
        
        // Load initial reviews
        const productId = <?php echo get_the_ID(); ?>;
        console.log("Loading reviews for product:", productId);
        
        // Simple check to make sure we're not loading twice
        if ($('#reviews-container').attr('data-loaded') === 'false') {
            $.ajax({
                url: '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    'action': 'load_product_reviews',
                    'product_id': productId
                },
                success: function(response) {
                    $('#reviews-container').html(response).attr('data-loaded', 'true');
                    console.log("Reviews loaded successfully");
                },
                error: function(xhr, status, error) {
                    console.error("Error loading reviews:", error);
                    $('#reviews-container').html('<p class="error-message">Error loading reviews: ' + error + '</p>');
                }
            });
        }
        
        // Event delegation for load more button
        $(document).on('click', '#load-more-reviews', function(e) {
            e.preventDefault();
            
            console.log("Load more button clicked");
            
            const $button = $(this);
            $button.text('Loading...').prop('disabled', true);
            
            const productId = $button.data('product-id');
            const nextPage = $button.data('page');
            
            $.ajax({
                url: '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    'action': 'load_product_reviews',
                    'product_id': productId,
                    'page': nextPage
                },
                success: function(response) {
                    // Create temporary div to parse the HTML
                    const tempDiv = $('<div>').html(response);
                    
                    // Find new reviews and append them
                    const newReviews = tempDiv.find('.commentlist li');
                    $('#reviews-container .commentlist').append(newReviews);
                    
                    // Replace old button with new one
                    $button.remove();
                    
                    const newButton = tempDiv.find('#load-more-reviews');
                    if (newButton.length) {
                        $('#reviews-container').append(newButton);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error loading more reviews:", error);
                    $button.text('Error. Try again').prop('disabled', false);
                }
            });
        });
        
        // WooCommerce variations image switching for custom carousel
        $(document).ready(function() {
            // Инициализация дефолтных значений для вариативных продуктов
            function initializeDefaultVariations() {
                // Для цветных кружочков - находим checked элемент и синхронизируем
                const checkedColorInput = $('.variation-color-input:checked');
                if (checkedColorInput.length) {
                    const selectedColor = checkedColorInput.val();
                    const hiddenSelect = $('form.variations_form select[name="' + checkedColorInput.attr('name') + '"]');
                    if (hiddenSelect.length) {
                        hiddenSelect.val(selectedColor).trigger('change');
                    }
                }
                
                // Для размеров - находим selected элемент и синхронизируем
                const selectedSizeOption = $('.variation-size-select option:selected');
                if (selectedSizeOption.length && selectedSizeOption.val() !== '') {
                    const selectedSize = selectedSizeOption.val();
                    const sizeSelect = $('.variation-size-select');
                    const hiddenSelect = $('form.variations_form select[name="' + sizeSelect.attr('name') + '"]');
                    if (hiddenSelect.length) {
                        hiddenSelect.val(selectedSize).trigger('change');
                    }
                    // Добавляем класс для стилизации
                    sizeSelect.addClass('has-value');
                }
            }
            
            // Функция для обновления визуального состояния селектов
            function updateSelectState() {
                $('.variation-size-select').each(function() {
                    if ($(this).val() !== '') {
                        $(this).addClass('has-value');
                    } else {
                        $(this).removeClass('has-value');
                    }
                });
            }
            
            // Запускаем инициализацию после загрузки страницы
            setTimeout(function() {
                initializeDefaultVariations();
                updateSelectState();
            }, 500);
            
            // Если это вариативный продукт, слушаем изменения формы вариаций
            $('form.variations_form').on('found_variation', function(event, variation) {
                console.log('Variation found:', variation);
                
                // Получаем изображение вариации
                if (variation.image && variation.image.src) {
                    // Находим активный слайд карусели
                    const activeSlide = $('#carouselExampleFade .carousel-item.active img');
                    
                    // Плавно меняем изображение
                    activeSlide.fadeOut(200, function() {
                        $(this).attr('src', variation.image.src)
                               .attr('alt', variation.image.alt || '')
                               .fadeIn(200);
                    });
                    
                    // Также обновляем изображения в fancybox если используется
                    activeSlide.attr('data-fancybox', 'gallery');
                }
            });
            
            // Сброс к оригинальному изображению при сбросе вариации
            $('form.variations_form').on('reset_data', function() {
                // Возвращаем основное изображение продукта
                const activeSlide = $('#carouselExampleFade .carousel-item.active img');
                const originalImage = $('#carouselExampleFade .carousel-item:first-child img').attr('src');
                
                if (originalImage) {
                    activeSlide.fadeOut(200, function() {
                        $(this).attr('src', originalImage)
                               .fadeIn(200);
                    });
                }
            });
            
            // Дополнительная обработка для кастомных цветных кружочков
            $('.color-picker-item input[type="radio"]').on('change', function() {
                const selectedColor = $(this).val();
                console.log('Custom color selected:', selectedColor);
                
                // Синхронизируем со скрытой формой WooCommerce
                const hiddenSelect = $('form.variations_form select[name="' + $(this).attr('name') + '"]');
                if (hiddenSelect.length) {
                    hiddenSelect.val(selectedColor).trigger('change');
                }
            });
            
            // Обработка для кастомного селекта размеров
            $('.custom-select.variation-size-select').on('change', function() {
                const selectedSize = $(this).val();
                console.log('Custom size selected:', selectedSize);
                
                // Обновляем визуальное состояние
                updateSelectState();
                
                // Синхронизируем со скрытой формой WooCommerce
                const hiddenSelect = $('form.variations_form select[name="' + $(this).attr('name') + '"]');
                if (hiddenSelect.length) {
                    hiddenSelect.val(selectedSize).trigger('change');
                }
            });
            
            // Кастомная кнопка Add to Cart для вариаций
            $('.custom-variation-add-to-cart').on('click', function(e) {
                e.preventDefault();
                
                const $button = $(this);
                const $form = $('form.variations_form');
                
                if ($form.length) {
                    // Проверяем что все вариации выбраны
                    const isValid = $form.find('select[name^="attribute_"]').toArray().every(select => {
                        return $(select).val() !== '';
                    });
                    
                    if (!isValid) {
                        alert('Please select all product options before adding to cart.');
                        return;
                    }
                    
                    // Обновляем quantity в скрытой форме
                    const quantity = $('#quantity').val();
                    $form.find('input[name="quantity"]').val(quantity);
                    
                    // Симулируем клик по скрытой кнопке WooCommerce
                    $form.find('.single_add_to_cart_button').click();
                } else {
                    console.error('Variations form not found');
                }
            });
            
            // Обработка для простых продуктов
            $('.color-picker-item input[type="radio"]:not(.variation-color-input)').on('change', function() {
                const selectedColor = $(this).val();
                console.log('Custom color selected:', selectedColor);
                
                // Триггерим изменение в стандартной форме WooCommerce если она есть
                const wooSelect = $('select[name="attribute_pa_color"], select[name="attribute_color"]');
                if (wooSelect.length) {
                    wooSelect.val(selectedColor.toLowerCase()).trigger('change');
                }
            });
            
            // Обработка для кастомного селекта размеров (простые продукты)
            $('.custom-select:not(.variation-size-select)').on('change', function() {
                const selectedSize = $(this).val();
                console.log('Custom size selected:', selectedSize);
                
                // Триггерим изменение в стандартной форме WooCommerce если она есть
                const wooSelect = $('select[name="attribute_pa_size"], select[name="attribute_size"]');
                if (wooSelect.length) {
                    wooSelect.val(selectedSize.toLowerCase()).trigger('change');
                }
            });
        });
    });
    </script>
</div>
<?php do_action( 'woocommerce_after_single_product' ); ?>