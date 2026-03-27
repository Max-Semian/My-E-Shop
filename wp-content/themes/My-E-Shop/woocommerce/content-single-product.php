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
                    <!-- WooCommerce форма вариаций - скрываем только селекты, но оставляем форму активной -->
                    <div class="woocommerce-variation-form-wrapper">
                        <style>
                            .woocommerce-variation-form-wrapper .variations { display: none !important; }
                            .woocommerce-variation-form-wrapper .single_variation_wrap .woocommerce-variation { display: none !important; }
                            .woocommerce-variation-form-wrapper .single_variation_wrap .woocommerce-variation-add-to-cart { display: none !important; }
                        </style>
                        <?php woocommerce_variable_add_to_cart(); ?>
                    </div>
                    
                    <!-- Кастомная кнопка add to cart для вариативных продуктов -->
                    <div class="quantity-add-to-cart">
                        <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
                        <button type="button" class="button alt custom-variation-add-to-cart custom-atc-btn" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>" data-icon-src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/Cart-icon.svg' ); ?>" data-original-text="Add to cart">
                            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/Cart-icon.svg' ); ?>" alt="Add to cart">
                            Add to cart
                        </button>
                        <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
                    </div>
                <?php else : ?>
                    <!-- Для простых продуктов используем кастомную форму -->
                <form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
                    <div class="quantity-add-to-cart">
                        <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
                        <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt custom-atc-btn" data-icon-src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/Cart-icon.svg' ); ?>" data-original-text="Add to cart">
                            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/Cart-icon.svg' ); ?>" alt="Add to cart">
                            Add to cart
                        </button>
                        <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
                    </div>
                </form>
                <?php endif; ?>

                <!-- Accordion Content Area (shows selected content) -->
                <div class="product-accordion-content" id="accordionContent"></div>

                <!-- Product Information Accordion Navigation -->
                <?php
                // Get product description (standard WooCommerce field)
                $description = $product->get_description();
                // Get ACF fields
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
                        'posts_per_page' => 4, // Show 4 products
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
</div>
<?php do_action( 'woocommerce_after_single_product' ); ?>