<?php
/**
 * Пользовательская функция для обработки WooCommerce шорткодов
 */
function force_woocommerce_shortcodes_init() {
    if (!class_exists('WooCommerce')) {
        return;
    }
    
    // Инициализируем WooCommerce если еще не инициализирован
    if (function_exists('WC') && WC()) {
        // Подключаем все необходимые файлы WooCommerce
        WC()->frontend_includes();
        
        // Подключаем дополнительные файлы если они не подключены
        $wc_path = WP_PLUGIN_DIR . '/woocommerce/';
        
        if (!function_exists('wc_get_default_products_per_row')) {
            $template_functions = $wc_path . 'includes/wc-template-functions.php';
            if (file_exists($template_functions)) {
                require_once $template_functions;
            }
        }
        
        if (!function_exists('woocommerce_output_related_products')) {
            $template_hooks = $wc_path . 'includes/wc-template-hooks.php';
            if (file_exists($template_hooks)) {
                require_once $template_hooks;
            }
        }
        
        if (!function_exists('wc_get_products')) {
            $product_functions = $wc_path . 'includes/wc-product-functions.php';
            if (file_exists($product_functions)) {
                require_once $product_functions;
            }
        }
        
        // Инициализируем необходимые компоненты
        if (!WC()->query) {
            WC()->query = new WC_Query();
        }
        if (!WC()->customer) {
            WC()->customer = new WC_Customer();
        }
        
        // Убеждаемся что основные хуки WooCommerce активированы
        if (!did_action('woocommerce_init')) {
            do_action('woocommerce_init');
        }
    }
    
    // Принудительно подключаем файл шорткодов
    if (!class_exists('WC_Shortcodes')) {
        $shortcodes_file = WP_PLUGIN_DIR . '/woocommerce/includes/class-wc-shortcodes.php';
        if (file_exists($shortcodes_file)) {
            include_once $shortcodes_file;
        }
    }
    
    // Инициализируем шорткоды
    if (class_exists('WC_Shortcodes')) {
        WC_Shortcodes::init();
    }
    
    // Принудительно регистрируем все основные шорткоды WooCommerce
    if (class_exists('WC_Shortcodes')) {
        $shortcodes_to_register = array(
            'woocommerce_products' => 'products',
            'products' => 'products',
            'woocommerce_product' => 'product',
            'product' => 'product',
            'woocommerce_product_page' => 'product_page',
            'product_page' => 'product_page',
            'woocommerce_product_category' => 'product_category',
            'product_category' => 'product_category',
            'woocommerce_product_categories' => 'product_categories',
            'product_categories' => 'product_categories',
            'woocommerce_add_to_cart' => 'product_add_to_cart',
            'add_to_cart' => 'product_add_to_cart',
            'woocommerce_add_to_cart_url' => 'product_add_to_cart_url',
            'add_to_cart_url' => 'product_add_to_cart_url',
            'recent_products' => 'recent_products',
            'sale_products' => 'sale_products',
            'best_selling_products' => 'best_selling_products',
            'top_rated_products' => 'top_rated_products',
            'featured_products' => 'featured_products'
        );
        
        foreach ($shortcodes_to_register as $shortcode => $method) {
            if (!shortcode_exists($shortcode) && method_exists('WC_Shortcodes', $method)) {
                add_shortcode($shortcode, array('WC_Shortcodes', $method));
            }
        }
    }
    
    // Если все равно не работает, регистрируем собственную функцию
    if (!shortcode_exists('woocommerce_products')) {
        add_shortcode('woocommerce_products', 'custom_woocommerce_products_shortcode');
    }
    if (!shortcode_exists('products')) {
        add_shortcode('products', 'custom_woocommerce_products_shortcode');
    }
}

/**
 * Пользовательская функция-обработчик для шорткода товаров
 */
function custom_woocommerce_products_shortcode($atts) {
    if (!class_exists('WooCommerce')) {
        return '<p>WooCommerce не активен</p>';
    }
    
    // Если стандартный метод существует, используем его
    if (class_exists('WC_Shortcodes') && method_exists('WC_Shortcodes', 'products')) {
        return WC_Shortcodes::products($atts);
    }
    
    // Иначе создаем простую замену
    $atts = shortcode_atts(array(
        'category' => '',
        'columns' => 4,
        'limit' => 12,
        'orderby' => 'menu_order title',
        'order' => 'ASC',
        'ids' => '',
        'skus' => '',
        'visibility' => 'visible'
    ), $atts);
    
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => intval($atts['limit']),
        'orderby' => $atts['orderby'],
        'order' => $atts['order'],
        'post_status' => 'publish',
        'meta_query' => WC()->query->get_meta_query()
    );
    
    // Добавляем фильтр по видимости товаров
    $args['tax_query'] = WC()->query->get_tax_query();
    
    if (!empty($atts['category'])) {
        $args['tax_query'][] = array(
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => explode(',', $atts['category'])
        );
    }
    
    if (!empty($atts['ids'])) {
        $args['post__in'] = array_map('trim', explode(',', $atts['ids']));
    }
    
    // Убираем товары не в наличии если нужно
    if ($atts['visibility'] === 'visible') {
        $args['meta_query'][] = array(
            'key' => '_visibility',
            'value' => array('catalog', 'visible'),
            'compare' => 'IN'
        );
    }
    
    $products = new WP_Query($args);
    
    if (!$products->have_posts()) {
        return '<div class="woocommerce"><p class="woocommerce-info">Товары не найдены.</p></div>';
    }
    
    ob_start();
    
    // Добавляем классы WooCommerce для правильного отображения
    echo '<div class="woocommerce">';
    echo '<div class="woocommerce-products-shortcode">';
    echo '<ul class="products columns-' . esc_attr($atts['columns']) . '">';
    
    // Устанавливаем глобальную переменную для колонок
    global $woocommerce_loop;
    $woocommerce_loop['columns'] = intval($atts['columns']);
    
    while ($products->have_posts()) {
        $products->the_post();
        wc_get_template_part('content', 'product');
    }
    
    echo '</ul>';
    echo '</div>';
    echo '</div>';
    
    wp_reset_postdata();
    
    return ob_get_clean();
}

// Запускаем инициализацию на хуке template_redirect для категорий товаров
add_action('template_redirect', function() {
    if (is_product_category()) {
        force_woocommerce_shortcodes_init();
    }
});

// Также запускаем инициализацию на хуке wp_loaded для всех страниц
add_action('wp_loaded', function() {
    if (class_exists('WooCommerce')) {
        force_woocommerce_shortcodes_init();
    }
});

// И еще один хук на случай если нужно для админки
add_action('init', function() {
    if (class_exists('WooCommerce')) {
        force_woocommerce_shortcodes_init();
    }
}, 20);

// Фильтр для выбора правильного шаблона категории
add_filter('template_include', function($template) {
    if (is_product_category()) {
        $current_category = get_queried_object();
        if ($current_category) {
            $selected_template = get_term_meta($current_category->term_id, '_category_template', true);
            
            if ($selected_template === 'dark') {
                $dark_template = locate_template('woocommerce/taxonomy-product-cat-dark.php');
                if ($dark_template) {
                    return $dark_template;
                }
            }
        }
    }
    return $template;
});

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
    // Подключаем Google Fonts
    wp_enqueue_style('google-fonts-playfair', 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap', array(), null);
    
    wp_enqueue_style('My-E-Shop-bootstrap', get_template_directory_uri() . '/assets/bootstrap/css/bootstrap.min.css');
    wp_enqueue_style('My-E-Shop-fontawesome', get_template_directory_uri() . '/assets/fonts/fontawesome-free-6.6.0-web/css/all.min.css');
    wp_enqueue_style('My-E-Shop-owlcarousel', get_template_directory_uri() . '/assets/owlcarousel2/owl.carousel.min.css');
    wp_enqueue_style('My-E-Shop-owlcarousel-theme', get_template_directory_uri() . '/assets/owlcarousel2/owl.theme.default.min.css');
    wp_enqueue_style('My-E-Shop-fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css');
    wp_enqueue_style('My-E-Shop-main', get_template_directory_uri() .'/assets/css/main.css');
    wp_enqueue_style('My-E-Shop-media', get_template_directory_uri() .'/assets/css/media.css');
    
    // Подключаем стили для страниц категорий на соответствующих страницах
    if (is_product_category() || (is_page() && strpos(get_post()->post_name, 'category-') === 0)) {
        wp_enqueue_style('My-E-Shop-category-pages', get_template_directory_uri() . '/assets/css/category-pages.css');
    }

    // Подключаем jQuery первым
    wp_deregister_script('jquery');
    wp_enqueue_script('jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js', array(), '3.7.1', false);
    
    // Подключаем GSAP библиотеки для анимаций (загружаем в head для доступности)
    wp_enqueue_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js', array(), '3.12.5', false);
    wp_enqueue_script('gsap-scramble-text', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrambleTextPlugin.min.js', array('gsap'), '3.12.5', false);
    
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
    <div class="form-field term-template-wrap">
        <label for="category_template"><?php esc_html_e( 'Шаблон категории', 'my-shop' ); ?></label>
        <select name="category_template" id="category_template">
            <option value="default"><?php esc_html_e( 'Светлый шаблон (по умолчанию)', 'my-shop' ); ?></option>
            <option value="dark"><?php esc_html_e( 'Тёмный шаблон', 'my-shop' ); ?></option>
        </select>
        <p class="description"><?php esc_html_e( 'Выберите шаблон для отображения страницы категории.', 'my-shop' ); ?></p>
    </div>
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
    $template = get_term_meta( $term->term_id, '_category_template', true );
    $color = get_term_meta( $term->term_id, 'category_button_color', true );
    $icon = get_term_meta( $term->term_id, 'category_button_icon', true );
    ?>
    <tr class="form-field term-template-wrap">
        <th scope="row"><label for="category_template"><?php esc_html_e( 'Шаблон категории', 'my-shop' ); ?></label></th>
        <td>
            <select name="category_template" id="category_template">
                <option value="default" <?php selected($template, 'default'); ?>><?php esc_html_e( 'Светлый шаблон (по умолчанию)', 'my-shop' ); ?></option>
                <option value="dark" <?php selected($template, 'dark'); ?>><?php esc_html_e( 'Тёмный шаблон', 'my-shop' ); ?></option>
            </select>
            <p class="description"><?php esc_html_e( 'Выберите шаблон для отображения страницы категории.', 'my-shop' ); ?></p>
        </td>
    </tr>
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
    if ( isset( $_POST['category_template'] ) ) {
        update_term_meta( $term_id, '_category_template', sanitize_text_field( $_POST['category_template'] ) );
    }
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
 * Добавляем выбор шаблона для товаров (аналогично категориям)
 */

// Добавляем мета-бокс для выбора шаблона товара
add_action( 'add_meta_boxes', 'add_product_template_meta_box' );

function add_product_template_meta_box() {
    add_meta_box(
        'product_template_selection',
        __( 'Настройки шаблона товара', 'my-shop' ),
        'product_template_meta_box_callback',
        'product',
        'side',
        'default'
    );
}

function product_template_meta_box_callback( $post ) {
    // Добавляем nonce для безопасности
    wp_nonce_field( 'product_template_meta_box', 'product_template_meta_box_nonce' );
    
    // Получаем текущее значение
    $template = get_post_meta( $post->ID, '_product_template', true );
    
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="product_template"><?php _e( 'Шаблон товара', 'my-shop' ); ?></label>
            </th>
            <td>
                <select name="product_template" id="product_template" style="width: 100%;">
                    <option value="default" <?php selected( $template, 'default' ); ?>><?php _e( 'Светлый шаблон (по умолчанию)', 'my-shop' ); ?></option>
                    <option value="dark" <?php selected( $template, 'dark' ); ?>><?php _e( 'Тёмный шаблон', 'my-shop' ); ?></option>
                </select>
                <p class="description"><?php _e( 'Выберите шаблон для отображения страницы товара.', 'my-shop' ); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

// Сохраняем значение мета-поля
add_action( 'save_post', 'save_product_template_meta_box' );

function save_product_template_meta_box( $post_id ) {
    // Проверяем nonce
    if ( ! isset( $_POST['product_template_meta_box_nonce'] ) ) {
        return;
    }
    
    if ( ! wp_verify_nonce( $_POST['product_template_meta_box_nonce'], 'product_template_meta_box' ) ) {
        return;
    }
    
    // Проверяем автосохранение
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    
    // Проверяем права пользователя
    if ( isset( $_POST['post_type'] ) && 'product' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_product', $post_id ) ) {
            return;
        }
    }
    
    // Сохраняем данные
    if ( isset( $_POST['product_template'] ) ) {
        update_post_meta( $post_id, '_product_template', sanitize_text_field( $_POST['product_template'] ) );
    }
}

/**
 * Функция для получения hex цвета по названию
 */
function get_color_hex_by_name($color_name) {
    $color_map = array(
        'red' => '#FF0000',
        'blue' => '#0000FF',
        'green' => '#008000',
        'yellow' => '#FFFF00',
        'black' => '#000000',
        'white' => '#FFFFFF',
        'purple' => '#800080',
        'pink' => '#FFC0CB',
        'orange' => '#FFA500',
        'brown' => '#A52A2A',
        'gray' => '#808080',
        'grey' => '#808080',
        'navy' => '#000080',
        'lime' => '#00FF00',
        'cyan' => '#00FFFF',
        'magenta' => '#FF00FF',
        'maroon' => '#800000',
        'olive' => '#808000',
        'silver' => '#C0C0C0',
        'gold' => '#FFD700',
        'beige' => '#F5F5DC',
        'coral' => '#FF7F50',
        'crimson' => '#DC143C',
        'indigo' => '#4B0082',
        'khaki' => '#F0E68C',
        'lavender' => '#E6E6FA',
        'salmon' => '#FA8072',
        'tan' => '#D2B48C',
        'turquoise' => '#40E0D0',
        'violet' => '#EE82EE'
    );
    
    $color_name_lower = strtolower(trim($color_name));
    
    // Если цвет найден в карте
    if (isset($color_map[$color_name_lower])) {
        return $color_map[$color_name_lower];
    }
    
    // Если цвет уже в hex формате
    if (preg_match('/^#[a-f0-9]{6}$/i', $color_name)) {
        return $color_name;
    }
    
    // По умолчанию возвращаем серый
    return '#808080';
}

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

function advanced_blog_posts_shortcode($atts) {
    $atts = shortcode_atts(array(
        'posts' => 5,
        'category' => '',
        'show_image' => 'true',
        'image_size' => 'medium',
        'show_excerpt' => 'true',
        'excerpt_length' => 20,
        'columns' => 1
    ), $atts);

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => intval($atts['posts']),
        'post_status' => 'publish'
    );

    if (!empty($atts['category'])) {
        $args['category_name'] = $atts['category'];
    }

    $posts = get_posts($args);
    $columns_class = 'columns-' . intval($atts['columns']);
    $output = '<div class="blog-posts-grid ' . $columns_class . '">';

    foreach ($posts as $post) {
        setup_postdata($post);
        
        $output .= '<div class="blog-post-card">';
        
        if ($atts['show_image'] === 'true' && has_post_thumbnail($post->ID)) {
            $output .= '<div class="post-thumbnail">';
            $output .= '<a href="' . get_permalink($post->ID) . '">';
            $output .= get_the_post_thumbnail($post->ID, $atts['image_size']);
            $output .= '</a></div>';
        }
        
        $output .= '<div class="post-content">';
        $output .= '<h3><a href="' . get_permalink($post->ID) . '">' . get_the_title($post->ID) . '</a></h3>';
        $output .= '<div class="post-meta">' . get_the_date('', $post->ID) . '</div>';
        
        if ($atts['show_excerpt'] === 'true') {
            $excerpt = wp_trim_words(get_the_excerpt($post->ID), intval($atts['excerpt_length']));
            $output .= '<div class="post-excerpt">' . $excerpt . '</div>';
        }
        
        $output .= '<a href="' . get_permalink($post->ID) . '" class="read-more-btn">Подробнее →</a>';
        $output .= '</div></div>';
    }

    $output .= '</div>';
    wp_reset_postdata();
    
    return $output;
}
add_shortcode('blog_grid', 'advanced_blog_posts_shortcode');

// 1. Шорткод для блока подписки
function newsletter_subscription_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => 'Join our world and get 5% off your first order',
        'subtitle' => 'Stay updated with new drops, visual stories & rare finds before anyone else',
        'placeholder' => 'Enter your email here',
        'button_text' => 'JOIN NOW',
        'privacy_text' => 'We only send thoughtful emails - no spam, just style. By subscribing, you agree to receive inspiration and exclusive privileges. Your data is safe with us.',
        'background_color' => '#f5f5f5',
        'text_color' => '#333',
        'button_color' => '#000'
    ), $atts);

    ob_start();
    ?>
    <?php
    return ob_get_clean();
}
add_shortcode('newsletter_block', 'newsletter_subscription_shortcode');

// 2. Обработка формы подписки
function handle_newsletter_subscription() {
    if (isset($_POST['newsletter_submit']) && wp_verify_nonce($_POST['newsletter_nonce'], 'newsletter_subscription')) {
        $email = sanitize_email($_POST['newsletter_email']);
        
        if (is_email($email)) {
            // Сохраняем email в базу данных
            global $wpdb;
            $table_name = $wpdb->prefix . 'newsletter_subscribers';
            
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE email = %s",
                $email
            ));
            
            if ($existing == 0) {
                $wpdb->insert(
                    $table_name,
                    array(
                        'email' => $email,
                        'subscribe_date' => current_time('mysql'),
                        'status' => 'active'
                    )
                );
                
                // Отправляем welcome email (опционально)
                wp_mail(
                    $email,
                    'Welcome to our newsletter!',
                    'Thank you for subscribing! Here\'s your 5% discount code: WELCOME5'
                );
                
                echo '<script>alert("Thank you for subscribing!");</script>';
            } else {
                echo '<script>alert("You are already subscribed!");</script>';
            }
        }
    }
}
add_action('wp_loaded', 'handle_newsletter_subscription');

// 3. Создание таблицы для подписчиков
function create_newsletter_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'newsletter_subscribers';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        email varchar(100) NOT NULL,
        subscribe_date datetime DEFAULT CURRENT_TIMESTAMP,
        status varchar(20) DEFAULT 'active',
        PRIMARY KEY (id),
        UNIQUE KEY email (email)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'create_newsletter_table');


// 4. Админ страница для просмотра подписчиков
function newsletter_admin_menu() {
    add_menu_page(
        'Newsletter Subscribers',
        'Newsletter',
        'manage_options',
        'newsletter-subscribers',
        'newsletter_admin_page',
        'dashicons-email-alt',
        30
    );
}
add_action('admin_menu', 'newsletter_admin_menu');

function newsletter_admin_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'newsletter_subscribers';
    $subscribers = $wpdb->get_results("SELECT * FROM $table_name ORDER BY subscribe_date DESC");

    echo '<div class="wrap">';
    echo '<h1>Newsletter Subscribers</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Email</th><th>Subscribe Date</th><th>Status</th></tr></thead>';
    echo '<tbody>';

    foreach ($subscribers as $subscriber) {
        echo '<tr>';
        echo '<td>' . esc_html($subscriber->email) . '</td>';
        echo '<td>' . esc_html($subscriber->subscribe_date) . '</td>';
        echo '<td>' . esc_html($subscriber->status) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</div>';
}
add_action('admin_page', 'newsletter_admin_page');

/**
 * ===================================================================
 * GUTENBERG BLOCKS СИСТЕМА
 * ===================================================================
 */

/**
 * Register category-cards block
 */
function my_e_shop_register_blocks() {
    // Create design category for blocks
    add_filter('block_categories_all', function($categories) {
        array_unshift($categories, array(
            'slug' => 'my-e-shop',
            'title' => __('My E-Shop', 'my-e-shop')
        ));
        return $categories;
    });

    // Enqueue block editor scripts and styles
    add_action('enqueue_block_editor_assets', function() {
        // Category Cards Block
        wp_enqueue_script(
            'my-e-shop-category-cards-editor',
            get_template_directory_uri() . '/blocks/category-cards/index.js',
            array('wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor', 'wp-components'),
            '1.0.0',
            true
        );
        
        wp_enqueue_style(
            'my-e-shop-category-cards-editor-style',
            get_template_directory_uri() . '/blocks/category-cards/editor.css',
            array(),
            '1.0.0'
        );

        // Fashion Hero Block
        wp_enqueue_script(
            'my-e-shop-fashion-hero-editor',
            get_template_directory_uri() . '/blocks/fashion-hero/index.js',
            array('wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor', 'wp-components'),
            '1.0.0',
            true
        );
        
        wp_enqueue_style(
            'my-e-shop-fashion-hero-editor-style',
            get_template_directory_uri() . '/blocks/fashion-hero/editor.css',
            array(),
            '1.0.0'
        );

        // Animated Text Block
        wp_enqueue_script(
            'my-e-shop-animated-text-editor',
            get_template_directory_uri() . '/blocks/animated-text/block.js',
            array('wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor', 'wp-components'),
            '1.0.0',
            true
        );
        
        wp_enqueue_style(
            'my-e-shop-animated-text-editor-style',
            get_template_directory_uri() . '/blocks/animated-text/editor.css',
            array(),
            '1.0.0'
        );

        // Scrambled Text Block (GSAP)
        wp_enqueue_script(
            'my-e-shop-scrambled-text-editor',
            get_template_directory_uri() . '/blocks/scrambled-text/index.js',
            array('wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor', 'wp-components'),
            '1.0.0',
            true
        );
        
        wp_enqueue_style(
            'my-e-shop-scrambled-text-editor-style',
            get_template_directory_uri() . '/blocks/scrambled-text/editor.css',
            array(),
            '1.0.0'
        );

        // Product Cards Block
        wp_enqueue_script(
            'my-e-shop-product-cards-editor',
            get_template_directory_uri() . '/blocks/product-cards/index.js',
            array('wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor', 'wp-components', 'wp-api-fetch', 'wp-data', 'wp-editor'),
            '2.0.2',
            true
        );
        
        wp_enqueue_style(
            'my-e-shop-product-cards-editor-style',
            get_template_directory_uri() . '/blocks/product-cards/editor.css',
            array(),
            '2.0.0'
        );

        // About Section Block
        wp_enqueue_script(
            'my-e-shop-about-section-editor',
            get_template_directory_uri() . '/blocks/about-section/index.js',
            array('wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor', 'wp-components'),
            '1.0.0',
            true
        );
        
        wp_enqueue_style(
            'my-e-shop-about-section-editor-style',
            get_template_directory_uri() . '/blocks/about-section/editor.css',
            array(),
            '1.0.0'
        );

        // Gallery Slider Block
        wp_enqueue_script(
            'my-e-shop-gallery-slider-editor',
            get_template_directory_uri() . '/blocks/gallery-slider/index.js',
            array('wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor', 'wp-components'),
            '1.0.0',
            true
        );
        
        wp_enqueue_style(
            'my-e-shop-gallery-slider-editor-style',
            get_template_directory_uri() . '/blocks/gallery-slider/editor.css',
            array(),
            '1.0.0'
        );

        // Why Choose Us Block
        wp_enqueue_script(
            'my-e-shop-why-choose-us-editor',
            get_template_directory_uri() . '/blocks/why-choose-us/index.js',
            array('wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor', 'wp-components'),
            '1.0.0',
            true
        );
        
        wp_enqueue_style(
            'my-e-shop-why-choose-us-editor-style',
            get_template_directory_uri() . '/blocks/why-choose-us/editor.css',
            array(),
            '1.0.0'
        );

        // Newsletter Section Block
        wp_enqueue_script(
            'my-e-shop-newsletter-section-editor',
            get_template_directory_uri() . '/blocks/newsletter-section/index.js',
            array('wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor', 'wp-components'),
            '1.0.0',
            true
        );
        
        wp_enqueue_style(
            'my-e-shop-newsletter-section-editor-style',
            get_template_directory_uri() . '/blocks/newsletter-section/editor.css',
            array(),
            '1.0.0'
        );

        // Category Hero Block
        wp_enqueue_script(
            'my-e-shop-category-hero-editor',
            get_template_directory_uri() . '/blocks/category-hero/index.js',
            array('wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor', 'wp-components'),
            '1.0.0',
            true
        );
        
        wp_enqueue_style(
            'my-e-shop-category-hero-editor-style',
            get_template_directory_uri() . '/blocks/category-hero/editor.css',
            array(),
            '1.0.0'
        );

        // Category Products Block
        wp_enqueue_script(
            'my-e-shop-category-products-editor',
            get_template_directory_uri() . '/blocks/category-products/index.js',
            array('wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor', 'wp-components', 'wp-api-fetch', 'wp-data'),
            '1.0.0',
            true
        );
        
        wp_enqueue_style(
            'my-e-shop-category-products-editor-style',
            get_template_directory_uri() . '/blocks/category-products/editor.css',
            array(),
            '1.0.0'
        );

        // Newsletter Subscription Block
        wp_enqueue_script(
            'my-e-shop-newsletter-subscription-editor',
            get_template_directory_uri() . '/blocks/newsletter-subscription/index.js',
            array('wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor', 'wp-components'),
            '1.0.0',
            true
        );
        
        wp_enqueue_style(
            'my-e-shop-newsletter-subscription-editor-style',
            get_template_directory_uri() . '/blocks/newsletter-subscription/editor.css',
            array(),
            '1.0.0'
        );
    });

    // Enqueue frontend assets
    add_action('wp_enqueue_scripts', function() {
        // Category Cards Block
        wp_enqueue_style(
            'my-e-shop-category-cards-style',
            get_template_directory_uri() . '/blocks/category-cards/style.css',
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'my-e-shop-category-cards-script',
            get_template_directory_uri() . '/blocks/category-cards/script.js',
            array('jquery'),
            '1.0.0',
            true
        );

        // Fashion Hero Block
        wp_enqueue_style(
            'my-e-shop-fashion-hero-style',
            get_template_directory_uri() . '/blocks/fashion-hero/style.css',
            array(),
            '1.0.0'
        );

        // Animated Text Block
        wp_enqueue_style(
            'my-e-shop-animated-text-style',
            get_template_directory_uri() . '/blocks/animated-text/style.css',
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'my-e-shop-animated-text-script',
            get_template_directory_uri() . '/blocks/animated-text/script.js',
            array('jquery'),
            '1.0.0',
            true
        );

        // Scrambled Text Block (GSAP)
        wp_enqueue_style(
            'my-e-shop-scrambled-text-style',
            get_template_directory_uri() . '/blocks/scrambled-text/style.css',
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'my-e-shop-scrambled-text-script',
            get_template_directory_uri() . '/blocks/scrambled-text/script.js',
            array('gsap', 'gsap-scramble-text'),
            '1.0.0',
            true
        );

        // Product Cards Block
        wp_enqueue_style(
            'my-e-shop-product-cards-style',
            get_template_directory_uri() . '/blocks/product-cards/style.css',
            array(),
            '2.0.0'
        );
        
        wp_enqueue_script(
            'my-e-shop-product-cards-script',
            get_template_directory_uri() . '/blocks/product-cards/script.js',
            array('jquery'),
            '2.0.0',
            true
        );

        // About Section Block
        wp_enqueue_style(
            'my-e-shop-about-section-style',
            get_template_directory_uri() . '/blocks/about-section/style.css',
            array(),
            '1.0.0'
        );

        // Gallery Slider Block
        wp_enqueue_style(
            'my-e-shop-gallery-slider-style',
            get_template_directory_uri() . '/blocks/gallery-slider/style.css',
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'my-e-shop-gallery-slider-script',
            get_template_directory_uri() . '/blocks/gallery-slider/script.js',
            array('jquery'),
            '1.0.0',
            true
        );

        // Why Choose Us Block
        wp_enqueue_style(
            'my-e-shop-why-choose-us-style',
            get_template_directory_uri() . '/blocks/why-choose-us/style.css',
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'my-e-shop-why-choose-us-script',
            get_template_directory_uri() . '/blocks/why-choose-us/script.js',
            array('jquery'),
            '1.0.0',
            true
        );

        // Newsletter Section Block
        wp_enqueue_style(
            'my-e-shop-newsletter-section-style',
            get_template_directory_uri() . '/blocks/newsletter-section/style.css',
            array(),
            '1.0.0'
        );

        // Category Hero Block
        wp_enqueue_style(
            'my-e-shop-category-hero-style',
            get_template_directory_uri() . '/blocks/category-hero/style.css',
            array(),
            '1.0.0'
        );

        // Category Products Block
        wp_enqueue_style(
            'my-e-shop-category-products-style',
            get_template_directory_uri() . '/blocks/category-products/style.css',
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'my-e-shop-category-products-script',
            get_template_directory_uri() . '/blocks/category-products/script.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        // Передать AJAX URL для блока category-products
        wp_localize_script(
            'my-e-shop-category-products-script',
            'categoryProductsAjax',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('category_products_nonce')
            )
        );

        // Newsletter Subscription Block
        wp_enqueue_style(
            'my-e-shop-newsletter-subscription-style',
            get_template_directory_uri() . '/blocks/newsletter-subscription/style.css',
            array(),
            '1.0.0'
        );
    });

    // Register blocks using block.json
    register_block_type(get_template_directory() . '/blocks/category-cards/block.json');
    register_block_type(get_template_directory() . '/blocks/fashion-hero/block.json');
    register_block_type(get_template_directory() . '/blocks/animated-text/block.json');
    register_block_type(get_template_directory() . '/blocks/scrambled-text/block.json');
    register_block_type(get_template_directory() . '/blocks/product-cards/block.json');
    register_block_type(get_template_directory() . '/blocks/about-section/block.json');
    register_block_type(get_template_directory() . '/blocks/gallery-slider/block.json');
    register_block_type(get_template_directory() . '/blocks/why-choose-us/block.json');
    register_block_type(get_template_directory() . '/blocks/newsletter-section/block.json');
    register_block_type(get_template_directory() . '/blocks/category-hero/block.json');
    register_block_type(get_template_directory() . '/blocks/category-products/block.json');
    register_block_type(get_template_directory() . '/blocks/newsletter-subscription/block.json');
}
add_action('init', 'my_e_shop_register_blocks', 5);

// Шорткод для вывода двух постов блога
function blog_trends_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => 'Trends',
        'subtitle' => 'Ride the wave of the future',
        'posts_count' => 2
    ), $atts);

    // Получаем последние посты
    $posts = get_posts(array(
        'post_type' => 'post',
        'posts_per_page' => intval($atts['posts_count']),
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    ));

    if (empty($posts)) {
        return '<p>No blog posts found.</p>';
    }

    ob_start();
    ?>
    <section class="blog-trends-section">
        <div class="container">
            <div class="blog-trends-header">
                <h2 class="blog-trends-title"><?php echo esc_html($atts['title']); ?></h2>
                <p class="blog-trends-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
            </div>
            
            <div class="blog-trends-grid">
                <?php foreach ($posts as $post) : ?>
                    <div class="blog-trend-card">
                        <div class="blog-trend-image">
                            <?php if (has_post_thumbnail($post->ID)) : ?>
                                <a href="<?php echo get_permalink($post->ID); ?>">
                                    <?php echo get_the_post_thumbnail($post->ID, 'large', array('alt' => get_the_title($post->ID))); ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php echo get_permalink($post->ID); ?>">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/default-blog.jpg" alt="<?php echo esc_attr(get_the_title($post->ID)); ?>">
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="blog-trend-content">
                            <h3 class="blog-trend-title">
                                <a href="<?php echo get_permalink($post->ID); ?>">
                                    <?php echo get_the_title($post->ID); ?>
                                </a>
                            </h3>
                            
                            <div class="blog-trend-meta">
                                <span class="blog-trend-date">
                                    <?php echo get_the_date('F j, Y', $post->ID); ?>
                                </span>
                            </div>
                            
                            <div class="blog-trend-excerpt">
                                <?php 
                                $excerpt = get_the_excerpt($post->ID);
                                if (empty($excerpt)) {
                                    $excerpt = wp_trim_words(get_the_content(null, false, $post->ID), 20, '...');
                                }
                                echo wp_strip_all_tags($excerpt); 
                                ?>
                            </div>
                            
                            <a href="<?php echo get_permalink($post->ID); ?>" class="blog-trend-read-more">
                                read more
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('blog_trends', 'blog_trends_shortcode');

// Скрыть заголовки на всех страницах
add_filter('the_title', function($title, $id) {
    if (is_page() && in_the_loop()) {
        return '';
    }
    return $title;
}, 10, 2);

// Подключаем функционал кастомных страниц категорий
require_once get_template_directory() . '/includes/category-pages.php';

// AJAX обработчик для добавления товара в корзину
function handle_ajax_add_to_cart() {
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'wc_add_to_cart_nonce') && !current_user_can('edit_posts')) {
        // Разрешаем без nonce для совместимости
    }

    if (!class_exists('WooCommerce')) {
        wp_die('WooCommerce not active');
    }

    $product_id = absint($_POST['product_id'] ?? 0);
    $quantity = absint($_POST['quantity'] ?? 1);
    $variation_id = absint($_POST['variation_id'] ?? 0);
    $variation = array();

    if (empty($product_id)) {
        wp_send_json_error('Invalid product ID');
        return;
    }

    // Получаем продукт
    $product = wc_get_product($product_id);
    if (!$product) {
        wp_send_json_error('Product not found');
        return;
    }

    // Добавляем товар в корзину
    $result = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation);
    
    if ($result) {
        wp_send_json_success(array(
            'message' => 'Product added to cart',
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'cart_total' => WC()->cart->get_cart_total()
        ));
    } else {
        wp_send_json_error('Failed to add product to cart');
    }
}
add_action('wp_ajax_woocommerce_add_to_cart', 'handle_ajax_add_to_cart');
add_action('wp_ajax_nopriv_woocommerce_add_to_cart', 'handle_ajax_add_to_cart');

// AJAX обработчик для загрузки товаров категории
function load_category_products_ajax() {
    // Проверка безопасности
    if (!wp_verify_nonce($_POST['nonce'], 'category_products_nonce')) {
        wp_send_json_error('Invalid nonce');
    }
    
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 4;
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    if (!$category_id) {
        wp_send_json_error('Category ID required');
    }
    
    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => $per_page,
        'paged' => $page,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $category_id,
            ),
        ),
    );
    
    // Добавляем поиск если указан
    if (!empty($search)) {
        $args['s'] = $search;
    }
    
    $products = new WP_Query($args);
    
    ob_start();
    if ($products->have_posts()) {
        while ($products->have_posts()) {
            $products->the_post();
            global $product;
            
            echo '<div class="product-slide">';
            echo '<div class="product-card">';
            echo '<div class="product-image">';
            echo '<a href="' . get_permalink() . '">';
            if (has_post_thumbnail()) {
                the_post_thumbnail('woocommerce_thumbnail');
            } else {
                echo '<img src="' . wc_placeholder_img_src() . '" alt="No Image">';
            }
            echo '</a>';
            echo '</div>';
            
            echo '<div class="product-info">';
            echo '<h3 class="product-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
            echo '<div class="product-price">' . $product->get_price_html() . '</div>';
            
            // Кнопка добавления в корзину
            if ($product->is_purchasable() && $product->is_in_stock()) {
                echo '<button class="add-to-cart-btn" data-product-id="' . get_the_ID() . '">Add to Cart</button>';
            }
            
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<div class="no-products">No products found</div>';
    }
    
    $content = ob_get_clean();
    wp_reset_postdata();
    
    wp_send_json_success(array(
        'content' => $content,
        'total_pages' => $products->max_num_pages,
        'current_page' => $page,
        'total_products' => $products->found_posts
    ));
}
add_action('wp_ajax_load_category_products', 'load_category_products_ajax');
add_action('wp_ajax_nopriv_load_category_products', 'load_category_products_ajax');

