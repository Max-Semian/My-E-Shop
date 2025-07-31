<?php
function mytheme_add_woocommerce_support() {
    load_theme_textdomain( 'My-E-Shop', get_template_directory() . '/languages' );
    add_theme_support( 'woocommerce' );
    add_theme_support( 'title-tag' );

    register_nav_menus(
        array(
            'header-menu' => __('Header menu', 'My-E-Shop'),
        )
    );
}

add_action('widgets_init', function () {
    register_sidebar(
        array(
            'name' => esc_html__('Sidebar', 'My-E-Shop'),
            'id' => 'sidebar-1',
            'description' => esc_html__('Add widgets here to appear in your sidebar.', 'My-E-Shop'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h2 class="widget-title">',
            'after_title' => '</h2>',
        )
    );
});

add_action('after_setup_theme', 'mytheme_add_woocommerce_support');


add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('My-E-Shop-bootstrap', get_template_directory_uri() . '/assets/bootstrap/css/bootstrap.min.css');
    wp_enqueue_style('My-E-Shop-fontawesome', get_template_directory_uri() . '/assets/fonts/fontawesome-free-6.6.0-web/css/all.min.css');
    wp_enqueue_style('My-E-Shop-owlcarousel', get_template_directory_uri() . '/assets/owlcarousel2/owl.carousel.min.css');
    wp_enqueue_style('My-E-Shop-owlcarousel-theme', get_template_directory_uri() . '/assets/owlcarousel2/owl.theme.default.min.css');
    wp_enqueue_style('My-E-Shop-fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css');
    wp_enqueue_style('My-E-Shop-main', get_template_directory_uri() .'/assets/css/main.css');
    wp_enqueue_style('My-E-Shop-media', get_template_directory_uri() .'/assets/css/media.css');

    // Подключаем jQuery первым
    wp_deregister_script('jquery');
    wp_enqueue_script('jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js', array(), '3.7.1', false);
    
    // Затем основные скрипты
    wp_enqueue_script('My-E-Shop-owlcarousel',get_template_directory_uri() . '/assets/owlcarousel2/owl.carousel.min.js', array('jquery'), false, true);
    wp_enqueue_script('My-E-Shop-bootstrap', get_template_directory_uri() . '/assets/bootstrap/js/bootstrap.bundle.min.js', array('jquery'), false, true);
    wp_enqueue_script('My-E-Shop-fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js', array('jquery'), false, true);
    wp_enqueue_script('My-E-Shop-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), false, true);
    wp_localize_script('My-E-Shop-main', 'my_e_shop_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));
    
    // Подключаем jQuery UI только на страницах магазина
    if (is_shop() || is_product_category() || is_product_tag()) {
        // jQuery UI из CDN для надежности
        wp_enqueue_script('jquery-ui-core', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js', array('jquery'), '1.13.2', false);
        wp_enqueue_style('jquery-ui-theme', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/ui-lightness/jquery-ui.min.css', array(), '1.13.2');
        
        // WooCommerce скрипты
        if (function_exists('is_woocommerce')) {
            wp_enqueue_script('wc-price-slider', WC()->plugin_url() . '/assets/js/frontend/price-slider.min.js', array('jquery', 'jquery-ui-core'), WC()->version, true);
            wp_enqueue_style('woocommerce-layout');
            wp_enqueue_style('woocommerce-smallscreen');
            wp_enqueue_style('woocommerce-general');
        }
    }
}, 20); // Повышаем приоритет

require_once get_template_directory() . '/incs/woocommerce-hooks.php';
require_once get_template_directory() . '/incs/class-my-e-shop-header-menu.php';
require_once get_template_directory() . '/incs/cpt.php';

/**
 * Modify WooCommerce product description based on ACF fields
 */
function modify_product_description($content) {
    if (!is_product() || !in_the_loop()) {
        return $content;
    }

    $product_id = get_the_ID();
    $title = get_field('title', $product_id);
    $description = get_field('description', $product_id);
    $image = get_field('image', $product_id);

    if (empty($title) && empty($description) && empty($image)) {
        return $content;
    }

    $new_content = '<div class="product-custom-description">';

    if (!empty($title)) {
        $new_content .= '<h2 class="acf-title">' . esc_html($title) . '</h2>';
    }

    if (!empty($description)) {
        $new_content .= '<p class="acf-desc">' . esc_html($description) . '</p>';
    }

    if (!empty($image)) {
        $new_content .= wp_get_attachment_image($image, 'large', false, ['class' => 'acf-custom-img']);
    }

    $new_content .= '</div>';

    return $content . $new_content;
}
add_filter('the_content', 'modify_product_description', 10, 1);

/**
 * Hide variable product description if ACF Fields is active
 */
function replace_variation_description_with_acf($variations) {
    foreach ($variations as &$variation) {
        if (isset($variation['variation_id'])) {
            $variation_id = $variation['variation_id'];
            $custom_description = get_field('custom_description', $variation_id);

            if (!empty($custom_description)) {
                $variation['variation_description'] = esc_html($custom_description);
            }
        }
    }
    return $variations;
}
add_filter('woocommerce_available_variation', 'replace_variation_description_with_acf');

/**
 * Add custom styling for the product description
 */
add_action('wp_head', 'add_product_description_styles');
function add_product_description_styles() {
    if (!is_product()) {
        return;
    }
    ?>
    <style>
        .product-custom-description {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
        }

        .acf-title {
            font-size: 24px;
            font-weight: bold;
        }

        .acf-desc {
            font-size: 16px;
            color: #555;
        }

        .acf-custom-img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
    </style>
    <?php
}

// Removing related products from product page 
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

// Add category products instead of related products
add_action( 'woocommerce_after_single_product_summary', 'display_category_products', 20 );
function display_category_products() {
    if ( ! is_product() ) {
        return;
    }

    global $post;

    $categories = wp_get_post_terms( $post->ID, 'product_cat', array( 'fields' => 'ids' ) );

    if ( empty( $categories ) ) {
        return;
    }

    $args = array(
        'post_type'           => 'product',
        'posts_per_page'      => 4,
        'post__not_in'        => array( $post->ID ),
        'tax_query'           => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $categories,
            ),
        ),
    );

    $category_products = new WP_Query( $args );

    if ( $category_products->have_posts() ) {
        echo '<section class="category-products">';
        echo '<h2>' . __( 'Products from the Same Category', 'My-E-Shop' ) . '</h2>';
        echo '<div class="row products">';

        while ( $category_products->have_posts() ) {
            $category_products->the_post();
            wc_get_template_part( 'content', 'product' );
        }

        echo '</div>';
        echo '</section>';
    }

    wp_reset_postdata();
}

// Load product reviews via AJAX
add_action('wp_ajax_load_product_reviews', 'load_product_reviews_ajax');
add_action('wp_ajax_nopriv_load_product_reviews', 'load_product_reviews_ajax');
function load_product_reviews_ajax() {
    if (isset($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 5;
        
        $offset = ($page - 1) * $per_page;
        
        $args = array(
            'post_id' => $product_id,
            'status' => 'approve',
            'number' => $per_page,
            'offset' => $offset,
        );
        
        $comments = get_comments($args);
        
        $total_comments = get_comments(array(
            'post_id' => $product_id,
            'status' => 'approve',
            'count' => true
        ));
        
        $has_more = ($offset + $per_page) < $total_comments;
        
        ob_start();
        
        if ($comments) {
            echo '<ul class="commentlist">';
            foreach ($comments as $comment) {
                echo '<li class="review">';
                echo '<div class="comment-text">';
                
                $rating = get_comment_meta($comment->comment_ID, 'rating', true);
                if ($rating) {
                    echo wc_get_rating_html($rating);
                }
                
                echo '<p class="meta">';
                echo '<strong>' . esc_html($comment->comment_author) . '</strong>';
                echo ' - <time>' . date('F j, Y', strtotime($comment->comment_date)) . '</time>';
                echo '</p>';
                echo '<div class="description">';
                echo wpautop($comment->comment_content);
                echo '</div>';
                echo '</div>';
                echo '</li>';
            }
            echo '</ul>';
            
            echo '<div class="reviews-pagination" 
                 data-total="' . esc_attr($total_comments) . '" 
                 data-page="' . esc_attr($page) . '" 
                 data-has-more="' . ($has_more ? 'true' : 'false') . '"
                 data-product-id="' . esc_attr($product_id) . '"
                 style="display:none;"></div>';
            
            if ($has_more) {
                echo '<button type="button" id="load-more-reviews" class="load-more-reviews-btn"
                      data-product-id="' . esc_attr($product_id) . '" 
                      data-page="' . esc_attr($page + 1) . '">
                      Load More Reviews
                      </button>';
            }
        } else {
            if ($page === 1) {
                echo '<p>No reviews yet for this product.</p>';
            } else {
                echo '<p>No more reviews to load.</p>';
            }
        }
        
        $output = ob_get_clean();
        echo $output;
    } else {
        echo '<p>Error: No product ID provided</p>';
    }
    
    wp_die();
}

/**
 * MULTI-STEP CHECKOUT FUNCTIONALITY
 */

function get_checkout_step() {
    if (isset($_GET['step'])) {
        return sanitize_text_field($_GET['step']);
    }
    return 'information';
}

add_filter('woocommerce_checkout_fields', 'customize_checkout_fields_by_step');
function customize_checkout_fields_by_step($fields) {
    $current_step = get_checkout_step();
    
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_state']);
    unset($fields['order']['order_comments']);
    
    $fields['billing']['billing_first_name']['label'] = 'First Name';
    $fields['billing']['billing_last_name']['label'] = 'Last Name';
    $fields['billing']['billing_email']['label'] = 'Email Address';
    $fields['billing']['billing_phone']['label'] = 'Phone (optional)';
    $fields['billing']['billing_phone']['required'] = false;
    $fields['billing']['billing_country']['label'] = 'Country';
    $fields['billing']['billing_city']['label'] = 'City';
    $fields['billing']['billing_address_1']['label'] = 'Address';
    $fields['billing']['billing_postcode']['label'] = 'Postal Code';
    
    switch ($current_step) {
        case 'information':
            $allowed_fields = ['billing_first_name', 'billing_last_name', 'billing_email', 'billing_phone'];
            foreach ($fields['billing'] as $key => $field) {
                if (!in_array($key, $allowed_fields)) {
                    unset($fields['billing'][$key]);
                }
            }
            unset($fields['shipping']);
            break;
            
        case 'shipping':
            $allowed_fields = ['billing_country', 'billing_city', 'billing_address_1', 'billing_postcode'];
            foreach ($fields['billing'] as $key => $field) {
                if (!in_array($key, $allowed_fields)) {
                    unset($fields['billing'][$key]);
                }
            }
            unset($fields['shipping']);
            break;
            
        case 'payment':
            unset($fields['billing']);
            unset($fields['shipping']);
            break;
    }
    
    return $fields;
}

add_action('wp_ajax_save_checkout_step', 'save_checkout_step_data');
add_action('wp_ajax_nopriv_save_checkout_step', 'save_checkout_step_data');
function save_checkout_step_data() {
    if (!WC()->session) {
        wp_die('Session not available');
    }
    
    $step = sanitize_text_field($_POST['step']);
    $data = array();
    
    switch ($step) {
        case 'information':
            $data = array(
                'billing_first_name' => sanitize_text_field($_POST['billing_first_name']),
                'billing_last_name' => sanitize_text_field($_POST['billing_last_name']),
                'billing_email' => sanitize_email($_POST['billing_email']),
                'billing_phone' => sanitize_text_field($_POST['billing_phone'])
            );
            break;
            
        case 'shipping':
            $data = array(
                'billing_country' => sanitize_text_field($_POST['billing_country']),
                'billing_city' => sanitize_text_field($_POST['billing_city']),
                'billing_address_1' => sanitize_text_field($_POST['billing_address_1']),
                'billing_postcode' => sanitize_text_field($_POST['billing_postcode'])
            );
            break;
    }
    
    WC()->session->set('checkout_step_' . $step, $data);
    wp_send_json_success();
}

add_filter('woocommerce_checkout_get_value', 'restore_checkout_field_value', 10, 2);
function restore_checkout_field_value($value, $input) {
    if (!WC()->session) {
        return $value;
    }
    
    $information_data = WC()->session->get('checkout_step_information', array());
    $shipping_data = WC()->session->get('checkout_step_shipping', array());
    
    $all_data = array_merge($information_data, $shipping_data);
    
    if (isset($all_data[$input])) {
        return $all_data[$input];
    }
    
    return $value;
}

add_action('init', 'modify_checkout_hooks');
function modify_checkout_hooks() {
    $current_step = get_checkout_step();
    
    if (in_array($current_step, ['information', 'shipping'])) {
        remove_action('woocommerce_checkout_order_review', 'woocommerce_order_review', 10);
        remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);
    }
    
    remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
    add_filter('woocommerce_enable_order_notes_field', '__return_false');
}

add_action('wp_ajax_validate_checkout_step', 'validate_checkout_step_data');
add_action('wp_ajax_nopriv_validate_checkout_step', 'validate_checkout_step_data');
function validate_checkout_step_data() {
    $step = sanitize_text_field($_POST['step']);
    $errors = array();
    
    switch ($step) {
        case 'information':
            if (empty($_POST['billing_first_name'])) {
                $errors[] = 'First name is required';
            }
            if (empty($_POST['billing_last_name'])) {
                $errors[] = 'Last name is required';
            }
            if (empty($_POST['billing_email']) || !is_email($_POST['billing_email'])) {
                $errors[] = 'Please enter a valid email address';
            }
            break;
            
        case 'shipping':
            if (empty($_POST['billing_country'])) {
                $errors[] = 'Please select a country';
            }
            if (empty($_POST['billing_city'])) {
                $errors[] = 'City is required';
            }
            if (empty($_POST['billing_address_1'])) {
                $errors[] = 'Address is required';
            }
            if (empty($_POST['billing_postcode'])) {
                $errors[] = 'Postal code is required';
            }
            break;
    }
    
    if (!empty($errors)) {
        wp_send_json_error($errors);
    } else {
        wp_send_json_success();
    }
}

add_action('woocommerce_thankyou', 'clear_checkout_session_data');
function clear_checkout_session_data() {
    if (WC()->session) {
        WC()->session->set('checkout_step_information', null);
        WC()->session->set('checkout_step_shipping', null);
    }
}

add_action('wp_head', 'multistep_checkout_styles');
function multistep_checkout_styles() {
    if (!is_checkout()) {
        return;
    }
    // Ваши существующие стили checkout остаются без изменений
}

add_action('wp_footer', 'multistep_checkout_scripts');
function multistep_checkout_scripts() {
    if (!is_checkout()) {
        return;
    }
    // Ваши существующие скрипты checkout остаются без изменений
}

/**
 * SHOP FILTERS FUNCTIONALITY - CONSOLIDATED
 */

// Единая функция обработки всех фильтров
add_action('pre_get_posts', 'handle_all_shop_filters', 20);
function handle_all_shop_filters($query) {
    if (!is_admin() && $query->is_main_query() && (is_shop() || is_product_category() || is_product_tag())) {
        
        $meta_query = $query->get('meta_query') ?: array();
        $tax_query = $query->get('tax_query') ?: array();
        
        // Фильтр по цене
        if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
            $meta_query[] = array(
                'key' => '_price',
                'value' => floatval($_GET['min_price']),
                'compare' => '>=',
                'type' => 'NUMERIC'
            );
        }
        
        if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
            $meta_query[] = array(
                'key' => '_price',
                'value' => floatval($_GET['max_price']),
                'compare' => '<=',
                'type' => 'NUMERIC'
            );
        }
        
        // Фильтр распродажи
        if (isset($_GET['on_sale']) && $_GET['on_sale'] == '1') {
            $meta_query[] = array(
                'key' => '_sale_price',
                'value' => '',
                'compare' => '!='
            );
        }
        
        // Фильтр по наличию
        if (isset($_GET['stock_status']) && !empty($_GET['stock_status'])) {
            $stock_statuses = explode(',', sanitize_text_field($_GET['stock_status']));
            $meta_query[] = array(
                'key' => '_stock_status',
                'value' => $stock_statuses,
                'compare' => 'IN'
            );
        }
        
        // Фильтры по атрибутам
        foreach ($_GET as $key => $value) {
            if (strpos($key, 'filter_') === 0 && !empty($value)) {
                $attribute = str_replace('filter_', '', $key);
                $taxonomy = 'pa_' . $attribute;
                $terms = explode(',', sanitize_text_field($value));
                
                $tax_query[] = array(
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $terms,
                    'operator' => 'IN'
                );
            }
        }
        
        // Применяем мета-запросы
        if (!empty($meta_query)) {
            $meta_query['relation'] = 'AND';
            $query->set('meta_query', $meta_query);
        }
        
        // Применяем таксономические запросы
        if (!empty($tax_query)) {
            $tax_query['relation'] = 'AND';
            $query->set('tax_query', $tax_query);
        }
    }
}

// JavaScript для работы фильтров

// JavaScript для работы фильтров - ИСПРАВЛЕННАЯ ВЕРСИЯ
add_action('wp_footer', 'shop_filters_scripts');
function shop_filters_scripts() {
    if (is_shop() || is_product_category() || is_product_tag()) {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Проверяем доступность jQuery UI
            console.log('jQuery version:', $.fn.jquery);
            console.log('jQuery UI available:', typeof $.ui !== 'undefined');
            console.log('Slider available:', typeof $.fn.slider !== 'undefined');
            
            // Обработка всех фильтров (кроме цены)
            $('.attribute-filter, .stock-filter, .sale-filter').change(function() {
                applyFilters();
            });
            
            function applyFilters() {
                var url = new URL(window.location.href);
                
                // Фильтры атрибутов
                var attributeFilters = {};
                $('.attribute-filter:checked').each(function() {
                    var attribute = $(this).data('attribute');
                    if (!attributeFilters[attribute]) {
                        attributeFilters[attribute] = [];
                    }
                    attributeFilters[attribute].push($(this).val());
                });
                
                // Очищаем старые фильтры атрибутов
                for (var [key, value] of url.searchParams.entries()) {
                    if (key.startsWith('filter_')) {
                        url.searchParams.delete(key);
                    }
                }
                
                // Добавляем новые фильтры атрибутов
                for (var attribute in attributeFilters) {
                    if (attributeFilters[attribute].length > 0) {
                        url.searchParams.set('filter_' + attribute, attributeFilters[attribute].join(','));
                    }
                }
                
                // Фильтр наличия
                var stockStatuses = [];
                $('.stock-filter:checked').each(function() {
                    stockStatuses.push($(this).val());
                });
                
                if (stockStatuses.length > 0) {
                    url.searchParams.set('stock_status', stockStatuses.join(','));
                } else {
                    url.searchParams.delete('stock_status');
                }
                
                // Фильтр распродажи
                if ($('.sale-filter:checked').length > 0) {
                    url.searchParams.set('on_sale', '1');
                } else {
                    url.searchParams.delete('on_sale');
                }
                
                window.location.href = url.toString();
            }
            
            // Инициализация ползунка цены - УЛУЧШЕННАЯ ВЕРСИЯ
            function initPriceSlider() {
                // Пробуем WooCommerce встроенный ползунок
                if (typeof woocommerce_price_slider_params !== 'undefined') {
                    console.log('WooCommerce price slider params found');
                    $('body').trigger('init_price_filter');
                    return;
                }
                
                // Если есть jQuery UI, используем его
                if (typeof $.fn.slider !== 'undefined') {
                    $('.price_slider').each(function() {
                        var $slider = $(this);
                        var $amount = $slider.siblings('.price_slider_amount');
                        
                        if ($slider.length && !$slider.hasClass('ui-slider')) {
                            var min_price = parseInt($amount.find('.from').data('min')) || 0;
                            var max_price = parseInt($amount.find('.to').data('max')) || 1000;
                            var current_min = parseInt($amount.find('.from').val()) || min_price;
                            var current_max = parseInt($amount.find('.to').val()) || max_price;
                            
                            try {
                                $slider.slider({
                                    range: true,
                                    animate: true,
                                    min: min_price,
                                    max: max_price,
                                    values: [current_min, current_max],
                                    slide: function(event, ui) {
                                        $amount.find('.from').val(ui.values[0]);
                                        $amount.find('.to').val(ui.values[1]);
                                        updatePriceDisplay(ui.values[0], ui.values[1]);
                                    }
                                });
                                console.log('jQuery UI slider initialized successfully');
                            } catch(e) {
                                console.log('jQuery UI slider failed:', e);
                                fallbackToSimpleSlider();
                            }
                        }
                    });
                } else {
                    console.log('jQuery UI not available, using fallback');
                    fallbackToSimpleSlider();
                }
            }
            
            // Простой HTML5 ползунок как fallback
            function fallbackToSimpleSlider() {
                if ($('.simple-price-slider').length === 0 && $('.price-filter-form').length > 0) {
                    
                    // Получаем текущие значения
                    var current_min = parseInt($('input[name="min_price"]').val()) || 0;
                    var current_max = parseInt($('input[name="max_price"]').val()) || 1000;
                    
                    // Получаем диапазон цен через AJAX
                    $.ajax({
                        url: my_e_shop_params.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'get_price_range'
                        },
                        success: function(response) {
                            var min_price = 0;
                            var max_price = 1000;
                            
                            if (response.success) {
                                min_price = response.data.min;
                                max_price = response.data.max;
                            }
                            
                            createSimpleSlider(min_price, max_price, current_min, current_max);
                        },
                        error: function() {
                            createSimpleSlider(0, 1000, current_min, current_max);
                        }
                    });
                }
            }
            
            function createSimpleSlider(min, max, current_min, current_max) {
                // Создаем простой HTML5 range slider
                var sliderHtml = '<div class="simple-price-slider">' +
                    '<div class="price-range-display">Price: $<span id="min-price-display">' + current_min + '</span> - $<span id="max-price-display">' + current_max + '</span></div>' +
                    '<div class="dual-range-slider">' +
                        '<input type="range" id="min-range" min="' + min + '" max="' + max + '" value="' + current_min + '" step="1">' +
                        '<input type="range" id="max-range" min="' + min + '" max="' + max + '" value="' + current_max + '" step="1">' +
                    '</div>' +
                '</div>';
                
                $('.price-filter-inputs').before(sliderHtml);
                
                // Обработчики для ползунков
                $('#min-range, #max-range').on('input', function() {
                    var min_val = parseInt($('#min-range').val());
                    var max_val = parseInt($('#max-range').val());
                    
                    if (min_val > max_val) {
                        if (this.id === 'min-range') {
                            $('#max-range').val(min_val);
                            max_val = min_val;
                        } else {
                            $('#min-range').val(max_val);
                            min_val = max_val;
                        }
                    }
                    
                    updatePriceDisplay(min_val, max_val);
                    $('input[name="min_price"]').val(min_val);
                    $('input[name="max_price"]').val(max_val);
                });
                
                console.log('Simple HTML5 slider created');
            }
            
            function updatePriceDisplay(min, max) {
                $('#min-price-display').text(min);
                $('#max-price-display').text(max);
            }
            
            // Инициализируем ползунок с задержкой для загрузки всех скриптов
            setTimeout(function() {
                initPriceSlider();
            }, 500);
            
            // Обработка формы фильтра цены
            $(document).on('click', '.price_slider_amount button, .filter-btn', function(e) {
                e.preventDefault();
                
                var min_price = $('.price_slider_amount .from').val() || $('input[name="min_price"]').val();
                var max_price = $('.price_slider_amount .to').val() || $('input[name="max_price"]').val();
                var current_url = window.location.href.split('?')[0];
                var params = new URLSearchParams(window.location.search);
                
                if (min_price && min_price > 0) {
                    params.set('min_price', min_price);
                } else {
                    params.delete('min_price');
                }
                
                if (max_price && max_price > 0) {
                    params.set('max_price', max_price);
                } else {
                    params.delete('max_price');
                }
                
                var new_url = current_url + (params.toString() ? '?' + params.toString() : '');
                window.location.href = new_url;
            });
        });
        </script>
    
        <?php
    }
}

// AJAX функция для получения диапазона цен
add_action('wp_ajax_get_price_range', 'get_product_price_range');
add_action('wp_ajax_nopriv_get_price_range', 'get_product_price_range');
function get_product_price_range() {
    global $wpdb;
    
    $min_price = $wpdb->get_var("
        SELECT MIN(CAST(meta_value AS UNSIGNED)) 
        FROM {$wpdb->postmeta} 
        WHERE meta_key = '_price' 
        AND meta_value != '' 
        AND meta_value > 0
    ");
    
    $max_price = $wpdb->get_var("
        SELECT MAX(CAST(meta_value AS UNSIGNED)) 
        FROM {$wpdb->postmeta} 
        WHERE meta_key = '_price' 
        AND meta_value != ''
    ");
    
    wp_send_json_success(array(
        'min' => intval($min_price) ?: 0,
        'max' => intval($max_price) ?: 1000
    ));
}


// Добавляем поля на страницу редактирования категории
add_action( 'product_cat_add_form_fields', 'add_category_custom_fields', 10, 2 );
add_action( 'product_cat_edit_form_fields', 'edit_category_custom_fields', 10, 2 );

function add_category_custom_fields() {
    ?>
    <div class="form-field term-color-wrap">
        <label for="category_button_color"><?php esc_html_e( 'Цвет кнопки категории', 'my-shop' ); ?></label>
        <input type="text" name="category_button_color" id="category_button_color" value="" class="color-field" data-default-color="#4a4a4a" />
        <p class="description"><?php esc_html_e( 'Введите HEX-код цвета для фона кнопки.', 'my-shop' ); ?></p>
    </div>
    <div class="form-field term-icon-wrap">
        <label for="category_button_icon"><?php esc_html_e( 'Иконка кнопки категории', 'my-shop' ); ?></label>
        <input type="text" name="category_button_icon" id="category_button_icon" value="" />
        <p class="description"><?php esc_html_e( 'Введите символ иконки (например: ☆, ♥, ⚡) или класс FontAwesome (например: fa-star).', 'my-shop' ); ?></p>
    </div>
    <?php
}

function edit_category_custom_fields( $term ) {
    $color = get_term_meta( $term->term_id, 'category_button_color', true );
    $icon = get_term_meta( $term->term_id, 'category_button_icon', true );
    ?>
    <tr class="form-field term-color-wrap">
        <th scope="row"><label for="category_button_color"><?php esc_html_e( 'Цвет кнопки категории', 'my-shop' ); ?></label></th>
        <td>
            <input type="text" name="category_button_color" id="category_button_color" value="<?php echo esc_attr( $color ); ?>" class="color-field" data-default-color="#4a4a4a" />
            <p class="description"><?php esc_html_e( 'Введите HEX-код цвета для фона кнопки.', 'my-shop' ); ?></p>
        </td>
    </tr>
    <tr class="form-field term-icon-wrap">
        <th scope="row"><label for="category_button_icon"><?php esc_html_e( 'Иконка кнопки категории', 'my-shop' ); ?></label></th>
        <td>
            <input type="text" name="category_button_icon" id="category_button_icon" value="<?php echo esc_attr( $icon ); ?>" />
            <p class="description"><?php esc_html_e( 'Введите символ иконки (например: ☆, ♥, ⚡) или класс FontAwesome (например: fa-star).', 'my-shop' ); ?></p>
        </td>
    </tr>
    <?php
}

// Сохраняем значения полей
add_action( 'create_product_cat', 'save_category_custom_fields', 10, 2 );
add_action( 'edited_product_cat', 'save_category_custom_fields', 10, 2 );

function save_category_custom_fields( $term_id ) {
    if ( isset( $_POST['category_button_color'] ) ) {
        update_term_meta( $term_id, 'category_button_color', sanitize_hex_color( $_POST['category_button_color'] ) );
    }
    if ( isset( $_POST['category_button_icon'] ) ) {
        update_term_meta( $term_id, 'category_button_icon', sanitize_text_field( $_POST['category_button_icon'] ) );
    }
}

// Добавляем выбор цвета (опционально, требуется enqueue скрипта)
add_action( 'admin_enqueue_scripts', 'enqueue_category_color_picker' );
function enqueue_category_color_picker( $hook_suffix ) {
    if ( 'edit-tags.php' == $hook_suffix || 'term.php' == $hook_suffix ) {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'category-color-picker', get_template_directory_uri() . '/js/category-color-picker.js', array( 'jquery', 'wp-color-picker' ), '', true );
    }
}

// Создайте файл js/category-color-picker.js в вашей теме со следующим содержимым:
/*
jQuery(document).ready(function($){
    $('.color-field').wpColorPicker();
});
*/

/**
 * Настройка размеров изображений WooCommerce
 */
function my_e_shop_custom_image_sizes() {
    // Переопределяем размер thumbnail для WooCommerce
    update_option('woocommerce_thumbnail_image_width', 202);
    update_option('woocommerce_thumbnail_image_height', 290);
    update_option('woocommerce_thumbnail_cropping', 'custom');
    update_option('woocommerce_thumbnail_cropping_custom_width', 202);
    update_option('woocommerce_thumbnail_cropping_custom_height', 290);
    
    // Добавляем свой кастомный размер изображения
    add_image_size('product_thumbnail_202x290', 202, 290, true);
}
add_action('after_setup_theme', 'my_e_shop_custom_image_sizes');

/**
 * Временный код для принудительного обновления размеров (удалить через неделю)
 */
function force_update_woocommerce_image_sizes() {
    // Удаляем старые опции
    delete_option('woocommerce_thumbnail_image_width');
    delete_option('woocommerce_thumbnail_image_height');
    delete_option('woocommerce_thumbnail_cropping');
    delete_option('woocommerce_thumbnail_cropping_custom_width');
    delete_option('woocommerce_thumbnail_cropping_custom_height');
    
    // Принудительно устанавливаем новые
    update_option('woocommerce_thumbnail_image_width', 202);
    update_option('woocommerce_thumbnail_image_height', 290);
    update_option('woocommerce_thumbnail_cropping', 'custom');
    update_option('woocommerce_thumbnail_cropping_custom_width', 202);
    update_option('woocommerce_thumbnail_cropping_custom_height', 290);
}
// РАСКОММЕНТИРОВАТЬ СТРОКУ НИЖЕ НА 1 ЗАГРУЗКУ СТРАНИЦЫ, ЗАТЕМ ЗАКОММЕНТИРОВАТЬ ОБРАТНО
// add_action('init', 'force_update_woocommerce_image_sizes');

/**
 * Фильтр для использования кастомного размера изображений в WooCommerce
 */
function my_e_shop_woocommerce_get_image_size_thumbnail($size) {
    return array(
        'width'  => 202,
        'height' => 290,
        'crop'   => true,
    );
}
add_filter('woocommerce_get_image_size_thumbnail', 'my_e_shop_woocommerce_get_image_size_thumbnail');