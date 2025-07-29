<?php
/**
 * Plugin Name: WooCommerce Checkout with Product Images
 * Description: Улучшенный интерфейс оформления заказа с изображениями товаров и пошаговой структурой
 * Version: 1.3
 * Author: Your Name
 * Text Domain: wc-checkout-images
 */

// Если файл вызван напрямую, выходим
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Подключение CSS и JS файлов для страницы оформления заказа
 */
function checkout_with_product_image() {
    // Проверяем, что мы на странице чекаута и WooCommerce активен
    if (!function_exists('is_checkout') || !is_checkout()) {
        return;
    }
    
    // Подключаем CSS файл
    wp_enqueue_style(
        'checkout-product-image-css', 
        plugin_dir_url(__FILE__) . 'checkout-with-product-image.css',
        array(),
        '1.3.0' 
    );
    
    // Подключаем JS файл в футере
    wp_enqueue_script(
        'checkout-product-image-js',
        plugin_dir_url(__FILE__) . 'checkout-with-product-image.js',
        array('jquery', 'wc-checkout'),
        '1.3.0',
        true
    );
    
    // Передаем данные в JavaScript
    $product_images = array();
    
    // Если корзина доступна, получаем данные о товарах и их изображениях
    if (function_exists('WC') && isset(WC()->cart) && WC()->cart) {
        foreach (WC()->cart->get_cart() as $cart_key => $cart_item) {
            if (isset($cart_item['data']) && is_object($cart_item['data'])) {
                $product = $cart_item['data'];
                $product_id = $product->get_id();
                $product_name = $product->get_name();
                $image_id = $product->get_image_id();
                $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : wc_placeholder_img_src('thumbnail');
                
                $product_images[$cart_key] = array(
                    'id' => $product_id,
                    'name' => $product_name,
                    'image_url' => $image_url,
                    'quantity' => $cart_item['quantity']
                );
            }
        }
    }
    
    // Локализуем скрипт с данными
    wp_localize_script('checkout-product-image-js', 'checkoutImages', array(
        'products' => $product_images,
        'placeholder_url' => function_exists('wc_placeholder_img_src') ? wc_placeholder_img_src('thumbnail') : '',
        'ajax_url' => admin_url('admin-ajax.php'),
        'cart_url' => function_exists('wc_get_cart_url') ? wc_get_cart_url() : '',
        'checkout_url' => function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : ''
    ));
}
add_action('wp_enqueue_scripts', 'checkout_with_product_image');

/**
 * УБИРАЕМ СТАРЫЕ ФУНКЦИИ ПОШАГОВОГО ЧЕКАУТА
 * Заменяем их на совместимые с нашей системой из functions.php
 */

/**
 * Получение текущего шага (совместимо с functions.php)
 */
function get_current_checkout_step() {
    // Используем функцию из functions.php если она существует
    if (function_exists('get_checkout_step')) {
        return get_checkout_step();
    }
    
    // Фолбэк
    if (isset($_GET['step'])) {
        return sanitize_text_field($_GET['step']);
    }
    return 'information';
}

/**
 * Добавляем хлебные крошки (адаптированные под нашу систему)
 */
function add_checkout_breadcrumbs() {
    if (!is_checkout()) {
        return;
    }
    
    $current_step = get_current_checkout_step();
    
    // HTML для хлебных крошек - совместимо с нашими шагами
    ?>
    <div class="checkout-breadcrumb">
        <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="checkout-step">
            <span class="checkout-step-text"><?php _e('Basket', 'wc-checkout-images'); ?></span>
        </a>
        <span class="checkout-step-arrow">›</span>
        
        <a href="<?php echo esc_url(add_query_arg('step', 'information', wc_get_checkout_url())); ?>" class="checkout-step <?php echo ($current_step == 'information' ? 'active' : ''); ?>">
            <span class="checkout-step-text"><?php _e('Information', 'wc-checkout-images'); ?></span>
        </a>
        <span class="checkout-step-arrow">›</span>
        
        <a href="<?php echo esc_url(add_query_arg('step', 'shipping', wc_get_checkout_url())); ?>" class="checkout-step <?php echo ($current_step == 'shipping' ? 'active' : ''); ?>">
            <span class="checkout-step-text"><?php _e('Shipping', 'wc-checkout-images'); ?></span>
        </a>
        <span class="checkout-step-arrow">›</span>
        
        <a href="<?php echo esc_url(add_query_arg('step', 'payment', wc_get_checkout_url())); ?>" class="checkout-step <?php echo ($current_step == 'payment' ? 'active' : ''); ?>">
            <span class="checkout-step-text"><?php _e('Payment', 'wc-checkout-images'); ?></span>
        </a>
    </div>
    <?php
}
add_action('woocommerce_before_checkout_form', 'add_checkout_breadcrumbs', 5);

/**
 * НЕ ВМЕШИВАЕМСЯ В СТРУКТУРУ ЧЕКАУТА
 * Оставляем управление шагами functions.php
 */

/**
 * Кастомный заголовок для блока обзора заказа (оставляем без изменений)
 */
function custom_order_review_heading() {
    echo '<h3 id="order_review_heading">' . __('Your order', 'wc-checkout-images') . '</h3>';
    return false;
}

/**
 * УБИРАЕМ custom_checkout_sections - пусть functions.php управляет полями
 */

/**
 * Добавляем AJAX endpoint для обновления количества товаров (оставляем)
 */
function add_cart_quantity_endpoint() {
    add_action('wp_ajax_wc_checkout_update_quantity', 'custom_update_cart_quantity');
    add_action('wp_ajax_nopriv_wc_checkout_update_quantity', 'custom_update_cart_quantity');
}
add_action('init', 'add_cart_quantity_endpoint');

/**
 * Обработчик для обновления количества товаров (оставляем без изменений)
 */
function custom_update_cart_quantity() {
    // Получаем параметры
    $cart_key = '';
    $quantity = 0;
    
    if (isset($_POST['cart_key'])) {
        $cart_key = sanitize_text_field($_POST['cart_key']);
    }
    
    if (isset($_POST['quantity'])) {
        $quantity = intval($_POST['quantity']);
    }
    
    // Проверяем наличие ключа
    if (empty($cart_key)) {
        wp_send_json_error(array('message' => 'Cart key is empty'));
        return;
    }
    
    // Проверяем доступность WooCommerce
    if (!function_exists('WC') || !isset(WC()->cart)) {
        wp_send_json_error(array('message' => 'WooCommerce cart not available'));
        return;
    }
    
    // Обновляем корзину
    $cart = WC()->cart;
    $success = false;
    $message = '';
    
    if ($quantity <= 0) {
        // Удаляем товар
        $success = $cart->remove_cart_item($cart_key);
        $message = 'Item removed';
    } else {
        // Устанавливаем новое количество
        $success = $cart->set_quantity($cart_key, $quantity);
        $message = 'Quantity updated';
    }
    
    // Пересчитываем корзину
    $cart->calculate_totals();
    
    // Возвращаем результат
    if ($success) {
        wp_send_json_success(array(
            'message' => $message,
            'cart_total' => $cart->get_cart_total(),
            'cart_count' => $cart->get_cart_contents_count()
        ));
    } else {
        wp_send_json_error(array('message' => 'Failed to update cart'));
    }
}

/**
 * Добавляем URL изображений товаров и ключи корзины в HTML (оставляем)
 */
function add_product_images_data_to_checkout($html, $cart_item, $cart_item_key) {
    if (is_checkout()) {
        // Получаем продукт
        $product = $cart_item['data'];
        $product_id = $product->get_id();
        
        // Получаем URL изображения
        $image_id = $product->get_image_id();
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : wc_placeholder_img_src('thumbnail');
        
        // Оборачиваем HTML в контейнер с data-атрибутами
        $new_html = '<span data-cart_key="' . esc_attr($cart_item_key) . '" data-product_id="' . esc_attr($product_id) . '" data-image_url="' . esc_url($image_url) . '" class="item-data">' . $html . '</span>';
        
        return $new_html;
    }
    
    return $html;
}
add_filter('woocommerce_cart_item_name', 'add_product_images_data_to_checkout', 10, 3);

/**
 * УБИРАЕМ custom_checkout_template - не нужен, используем наш шаблон
 */

/**
 * Улучшенное отображение товаров в заказе с изображениями
 */
function enhance_order_review_with_images() {
    if (!is_checkout()) {
        return;
    }
    
    $current_step = get_current_checkout_step();
    
    // Показываем только на шаге payment
    if ($current_step !== 'payment') {
        return;
    }
    
    // Добавляем стили для улучшенного отображения
    ?>
    <style>
    /* Улучшенные стили для товаров в order review */
    .woocommerce-checkout-review-order-table .cart_item td {
        vertical-align: middle;
        padding: 15px 10px;
    }
    
    .woocommerce-checkout-review-order-table .product-name {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .checkout-product-image {
        width: 50px;
        height: 50px;
        flex-shrink: 0;
        border-radius: 6px;
        overflow: hidden;
        background: #f0f0f0;
    }
    
    .checkout-product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .checkout-product-details {
        flex: 1;
    }
    
    .checkout-product-title {
        font-weight: 500;
        color: var(--black-color);
        margin: 0;
        line-height: 1.4;
    }
    
    .checkout-product-meta {
        font-size: 13px;
        color: var(--grey-color);
        margin-top: 2px;
    }
    
    .product-total {
        font-weight: 600;
        color: var(--main-color);
        text-align: right;
    }
    
    @media (max-width: 768px) {
        .checkout-product-image {
            width: 40px;
            height: 40px;
        }
        
        .woocommerce-checkout-review-order-table .cart_item td {
            padding: 12px 8px;
        }
        
        .checkout-product-title {
            font-size: 14px;
        }
        
        .checkout-product-meta {
            font-size: 12px;
        }
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // Улучшаем отображение товаров в order review
        function enhanceProductDisplay() {
            $('.woocommerce-checkout-review-order-table .cart_item').each(function() {
                var $row = $(this);
                var $nameCell = $row.find('.product-name');
                
                if ($nameCell.find('.checkout-product-image').length > 0) {
                    return; // Уже обработано
                }
                
                var productName = $nameCell.text().trim();
                var $itemData = $('.item-data').filter(function() {
                    return $(this).text().indexOf(productName.split(' ×')[0]) !== -1;
                });
                
                if ($itemData.length > 0) {
                    var imageUrl = $itemData.data('image_url');
                    var productId = $itemData.data('product_id');
                    
                    if (imageUrl) {
                        // Извлекаем количество
                        var quantityMatch = productName.match(/×\s*(\d+)/);
                        var quantity = quantityMatch ? quantityMatch[1] : '1';
                        
                        // Очищаем название от количества
                        var cleanName = productName.replace(/\s*×\s*\d+/, '');
                        
                        // Создаем новую структуру
                        var newContent = '<div class="checkout-product-image">' +
                                        '<img src="' + imageUrl + '" alt="' + cleanName + '">' +
                                        '</div>' +
                                        '<div class="checkout-product-details">' +
                                        '<div class="checkout-product-title">' + cleanName + '</div>' +
                                        '<div class="checkout-product-meta">Qty: ' + quantity + '</div>' +
                                        '</div>';
                        
                        $nameCell.html(newContent);
                    }
                }
            });
        }
        
        // Применяем при загрузке и обновлении чекаута
        enhanceProductDisplay();
        $(document.body).on('updated_checkout', enhanceProductDisplay);
    });
    </script>
    <?php
}
add_action('wp_head', 'enhance_order_review_with_images');

/**
 * Создание директории и файлов для шаблонов при активации плагина (оставляем)
 */
function checkout_images_activate() {
    // Создаем директорию для шаблонов
    $templates_dir = plugin_dir_path(__FILE__) . 'templates';
    if (!file_exists($templates_dir)) {
        mkdir($templates_dir, 0755);
    }
    
    // Создаем CSS файл если его нет
    $css_file = plugin_dir_path(__FILE__) . 'checkout-with-product-image.css';
    if (!file_exists($css_file)) {
        // Содержимое CSS файла
        $css_content = "/* Plugin CSS will be here */";
        file_put_contents($css_file, $css_content);
    }
}
register_activation_hook(__FILE__, 'checkout_images_activate');

/**
 * Добавляем информацию о настройке плагина в список плагинов (оставляем)
 */
function checkout_images_action_links($links) {
    $plugin_links = array(
        '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout') . '">' . __('WooCommerce Settings', 'wc-checkout-images') . '</a>',
    );
    
    return array_merge($plugin_links, $links);
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'checkout_images_action_links');