<?php

defined( 'ABSPATH' ) || exit;

global $product;
?>

<div class="product-page-container">
	<?php do_action( 'woocommerce_before_single_product' ); ?>
</div>

<!-- Breadcrumbs section for dark theme -->
<div class="product-dark-breadcrumbs">
    <?php
    /**
     * Output the WooCommerce Breadcrumb
     */
    woocommerce_breadcrumb();
    ?>
</div>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'product-content-wrapper product-dark-theme', $product ); ?>>
    <input type="hidden" id="product_id_field" name="product_id" value="<?php echo esc_attr(get_the_ID()); ?>">
    
    <div class="product-layout">
        <div class="product-images-section">
            <div class="product-dark-image-wrapper">
                <div id="carouselExampleFade" class="carousel slide carousel-fade" data-bs-ride="carousel">
                    <div class="carousel-inner">
						<?php
						$product_img_id = $product->get_image_id();
						if ( $product_img_id ) {
							$main_img = wp_get_attachment_url( $product_img_id );
						} else {
							$main_img = wc_placeholder_img_src( 'woocommerce_full' );
						}
						$product_img_ids = $product->get_gallery_image_ids();
						?>
                        <div class="carousel-item active">
                            <img data-fancybox="gallery" src="<?php echo $main_img; ?>" class="d-block w-100 product-main-image"
                                 alt="<?php echo $product->get_title(); ?>">
                        </div>
						<?php if ( $product_img_ids ): ?>
							<?php foreach ( $product_img_ids as $product_img_id ): ?>
                                <div class="carousel-item">
                                    <img data-fancybox="gallery" src="<?php echo wp_get_attachment_url( $product_img_id ); ?>"
                                         class="d-block w-100 product-main-image" alt="<?php echo $product->get_title(); ?>">
                                </div>
							<?php endforeach; ?>
						<?php endif; ?>
                    </div>
					<?php if ( $product_img_ids ): ?>
                        <button class="carousel-control-prev product-dark-carousel-control" type="button"
                                data-bs-target="#carouselExampleFade" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next product-dark-carousel-control" type="button"
                                data-bs-target="#carouselExampleFade" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
					<?php endif; ?>
                </div>
            </div>
        </div>

        <div class="product-info-section">
            <div class="product-dark-content">
                <?php woocommerce_show_product_sale_flash(); ?>
                
                <!-- Название товара -->
                <h1 class="product-title"><?php the_title(); ?></h1>
                
                <!-- Описание товара -->
                <div class="product-short-description">
                    <?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ); ?>
                </div>
                
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
                    <label class="product-option-label">Color</label>
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
                ?>
                <div class="product-size-picker">
                    <div class="size-picker-header">
                        <label class="product-option-label" for="size-select">Size</label>
                        <?php if ( !empty($size_guide_image) ) : ?>
                            <button type="button" class="size-guide-btn" data-bs-toggle="modal" data-bs-target="#sizeGuideModal">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                </svg>
                                Size Guide
                            </button>
                        <?php endif; ?>
                    </div>
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
                    <label class="product-option-label">Color</label>
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
                
                if (!empty($size_options)) : ?>
                <div class="product-size-picker">
                    <div class="size-picker-header">
                        <label class="product-option-label" for="size-select-var">Size</label>
                    </div>
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
                        <div class="quantity-wrapper">
                            <label for="quantity">Quantity:</label>
                            <div class="quantity-controls">
                                <button type="button" class="qty-btn minus">-</button>
                                <input type="number" id="quantity" class="input-text qty text" step="1" min="1" max="" name="quantity" value="1" title="Qty" size="4" placeholder="" inputmode="numeric">
                                <button type="button" class="qty-btn plus">+</button>
                            </div>
                        </div>
                        <button type="button" class="single_add_to_cart_button button alt custom-variation-add-to-cart">
                            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/Symbol.png' ); ?>" alt="Add to cart">
                            Add to cart
                        </button>
                    </div>
                <?php else : ?>
                    <!-- Для простых продуктов используем кастомную форму -->
                <form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
                    <div class="quantity-add-to-cart">
                        <div class="quantity-wrapper">
                            <label for="quantity">Quantity:</label>
                            <div class="quantity-controls">
                                <button type="button" class="qty-btn minus">-</button>
                                <input type="number" id="quantity" class="input-text qty text" step="1" min="1" max="" name="quantity" value="1" title="Qty" size="4" placeholder="" inputmode="numeric">
                                <button type="button" class="qty-btn plus">+</button>
                            </div>
                        </div>
                        <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt">
                            Add to cart
                        </button>
                    </div>
                </form>
                <?php endif; ?>
                
                <!-- Дополнительные опции -->
                <div class="product-extra-options">

                    <!-- Политика возврата -->
                    <?php 
                    // Try ACF field first
                    $return_policy = get_field('return_policy');
                    
                    // Fallback to post meta
                    if (empty($return_policy)) {
                        $return_policy = get_post_meta( get_the_ID(), '_product_return_policy', true );
                    }
                    
                    // Default text if nothing is set
                    if (empty($return_policy)) {
                        $return_policy = 'Free returns within 30 days';
                    }
                    ?>
                    <div class="shipping-policy">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/local_shipping.png" alt="Return Icon" class="return-icon">
                        <?php echo esc_html($return_policy); ?>
                    </div>

                    <!-- Добавить в избранное -->
                    <button class="add-to-favorite-btn" title="Add to Favorites">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                        Add To Favorite
                    </button>
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
                <div class="modal-content product-dark-modal">
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

    <div class="product-description-section">
        <div class="product-dark-description">
            <?php
            // Get ACF fields
            $title = get_field('title');
            $description = get_field('description');
            $image = get_field('image');

            // Display ACF fields if they exist
            if (!empty($title) || !empty($description) || !empty($image)) : ?>
                <div class="product-custom-description">
                    <?php if (!empty($title)) : ?>
                        <h2 class="acf-title"><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>

                    <?php if (!empty($description)) : ?>
                        <div class="acf-desc"><?php echo wpautop($description); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($image)) : ?>
                        <div class="acf-image">
                            <?php echo wp_get_attachment_image($image, 'large', false, ['class' => 'acf-custom-img']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- Product Details Sections -->
            <div class="product-details-tabs">
                <?php
                // Get detailed product information
                $product_details = get_field('product_details');
                $eco_crafto = get_field('eco_crafto');
                $shipping_return = get_field('shipping_return');
                
                // Check if any of these fields have content
                $has_details = !empty($product_details) || !empty($eco_crafto) || !empty($shipping_return);
                
                if ($has_details) : ?>
                
                <div class="details-columns">
                    <!-- Left Column -->
                    <div class="details-column details-left">
                        <!-- Product Details -->
                        <?php if (!empty($product_details)) : ?>
                        <div class="detail-section product-details-section collapsed">
                            <h3 class="detail-title accordion-toggle">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                                <span>Product Details</span>
                                <svg class="accordion-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </h3>
                            <div class="detail-content">
                                <?php echo wpautop($product_details); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Shipping And Return -->
                        <?php if (!empty($shipping_return)) : ?>
                        <div class="detail-section shipping-section collapsed">
                            <h3 class="detail-title accordion-toggle">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="1" y="3" width="15" height="13"></rect>
                                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                    <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                    <circle cx="18.5" cy="18.5" r="2.5"></circle>
                                </svg>
                                <span>Shipping And Return</span>
                                <svg class="accordion-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </h3>
                            <div class="detail-content">
                                <?php echo wpautop($shipping_return); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="details-column details-right">
                        <!-- Eco Crafto -->
                        <?php if (!empty($eco_crafto)) : ?>
                        <div class="detail-section eco-section no-accordion">
                            <h3 class="detail-title">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path>
                                    <path d="M12 6v6l4 2"></path>
                                </svg>
                                <span>Eco Crafto</span>
                            </h3>
                            <div class="detail-content">
                                <?php echo wpautop($eco_crafto); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Category Products Section -->
    <div class="category-products-section" id="category-products-section">
        <div class="product-dark-related">
            <h2><?php echo esc_html__('More Products in this Category', 'My-E-Shop'); ?></h2>
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
                                <div class="product-dark-card">
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
                                        <h5 class="product-card-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h5>
                                        <p class="product-card-price"><?php echo $product->get_price_html(); ?></p>
                                        <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" class="add-to-cart-btn add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo get_the_ID(); ?>">
                                            <?php echo esc_html__('Add to Cart', 'My-E-Shop'); ?>
                                        </a>
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
        <div class="product-dark-reviews">
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
    jQuery(document).ready(function($) {
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
        $('.add-to-favorite-btn').on('click', function() {
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
            $('.product-dark-content').prepend(messageHtml);
            
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
    
    <!-- Additional CSS for messages and interactions -->
    <style>
    .product-message {
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 15px;
        font-weight: 500;
        animation: slideInDown 0.3s ease;
    }
    
    .product-message-success {
        background-color: rgba(34, 197, 94, 0.2);
        border: 1px solid #22c55e;
        color: #22c55e;
    }
    
    .product-message-error {
        background-color: rgba(239, 68, 68, 0.2);
        border: 1px solid #ef4444;
        color: #ef4444;
    }
    
    .product-message-info {
        background-color: rgba(59, 130, 246, 0.2);
        border: 1px solid #3b82f6;
        color: #3b82f6;
    }
    
    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .single_add_to_cart_button.loading {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    /* Стилизация стандартных WooCommerce вариаций в темном стиле */
    .product-dark-theme .variations {
        background: transparent;
        border: none;
        margin: 20px 0;
    }
    
    .product-dark-theme .variations tr {
        background: transparent;
        border: none;
    }
    
    .product-dark-theme .variations td {
        padding: 10px 0;
        border: none;
        background: transparent;
    }
    
    .product-dark-theme .variations label {
        color: #e5e5e5;
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 8px;
        display: block;
    }
    
    .product-dark-theme .variations select {
        background: #3c3c3c;
        border: 2px solid #555;
        color: #e5e5e5;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        width: 100%;
        transition: all 0.3s ease;
    }
    
    .product-dark-theme .variations select:focus {
        outline: none;
        border-color: #6c5ce7;
        box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.2);
    }
    
    .product-dark-theme .variations select option {
        background: #3c3c3c;
        color: #e5e5e5;
    }
    
    .product-dark-theme .single_variation_wrap {
        margin-top: 20px;
    }
    
    .product-dark-theme .woocommerce-variation-description {
        color: #b0b0b0;
        margin: 10px 0;
        font-style: italic;
    }
    
    .product-dark-theme .woocommerce-variation-price {
        color: #6c5ce7;
        font-size: 24px;
        font-weight: 700;
        margin: 15px 0;
    }
    
    .product-dark-theme .woocommerce-variation-availability {
        color: #22c55e;
        margin: 10px 0;
        font-weight: 500;
    }
    
    /* Скрываем стандартные селекты если используем кастомные */
    .hide-default-variations .variations {
        display: none !important;
    }
    
    /* Полностью скрываем стандартную WooCommerce форму вариаций */
    .product-dark-theme .variations_form {
        display: none !important;
    }
    
    /* Показываем только наши кастомные элементы */
    /* .product-dark-theme .product-color-picker,
    .product-dark-theme .product-size-picker,
    .product-dark-theme .quantity-add-to-cart {
        display: block !important;
    } */
    
    /* Стили для выбранных по умолчанию элементов */
    .color-picker-item input[type="radio"]:checked + label.color-circle {
        border: 3px solid #6c5ce7;
        box-shadow: 0 0 0 2px rgba(108, 92, 231, 0.3);
        transform: scale(1.1);
    }
    
    .color-picker-item input[type="radio"]:checked + label.color-circle .checkmark {
        opacity: 1;
        color: #fff;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
    }
    
    .custom-select.variation-size-select {
        transition: all 0.3s ease;
    }
    
    .custom-select.variation-size-select:focus,
    .custom-select.variation-size-select.has-value {
        border-color: #6c5ce7;
        box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.2);
    }
    
    /* Фиксированная высота для карусели - 522px */
    .product-dark-image-wrapper {
        height: 522px !important;
        position: relative;
    }
    
    #carouselExampleFade {
        height: 522px !important;
    }
    
    #carouselExampleFade .carousel-inner {
        height: 522px !important;
        overflow: hidden;
    }
    
    #carouselExampleFade .carousel-item {
        height: 522px !important;
    }
    
    #carouselExampleFade .carousel-item img.product-main-image {
        height: 522px !important;
        width: 100% !important;
        object-fit: contain !important;
        object-position: center !important;
        background-color: #2c2c2c;
    }
    
    
    /* Выравнивание ширины quantity и size селектов */
    .custom-select-wrapper,
    .quantity-wrapper {
        width: 100% !important;
        max-width: 200px !important;
    }
    
    .quantity-wrapper .quantity-controls {
        width: 127px !important;
        height: 34px !important;
        display: flex !important;
        align-items: center !important;
        background: #3c3c3c !important;
        border: 2px solid #555 !important;
        border-radius: 8px !important;
        overflow: hidden !important;
    }
    
    .quantity-wrapper .qty-btn {
        width: 40px !important;
        height: 48px !important;
        background: #555 !important;
        border: none !important;
        color: #e5e5e5 !important;
        font-size: 18px !important;
        font-weight: bold !important;
        cursor: pointer !important;
        transition: background-color 0.3s ease !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    
    .quantity-wrapper .qty-btn:hover {
        background: #6c5ce7 !important;
    }
    
    .quantity-wrapper input[type="number"] {
        flex: 1 !important;
        height: 48px !important;
        border: none !important;
        background: #3c3c3c !important;
        color: #e5e5e5 !important;
        text-align: center !important;
        font-size: 16px !important;
        padding: 0 10px !important;
        outline: none !important;
    }
    
    .quantity-wrapper input[type="number"]:focus {
        background: #444 !important;
    }
    
    /* Убираем стрелки у input number */
    .quantity-wrapper input[type="number"]::-webkit-outer-spin-button,
    .quantity-wrapper input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none !important;
        margin: 0 !important;
    }
    
    .quantity-wrapper input[type="number"] {
        -moz-appearance: textfield !important;
    }
    </style>
</div>
<?php do_action( 'woocommerce_after_single_product' ); ?>