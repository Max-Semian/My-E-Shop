<?php
/**
 * Plugin Name: Manual Payment with 5% Fee
 * Description: Adds a manual payment method with 5% fee (with blocks support)
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: manual-payment-fee
 * WC requires at least: 6.0
 * WC tested up to: 8.5
 */

defined('ABSPATH') || exit;

/**
 * Основной класс плагина
 */
class Manual_Payment_Fee_Plugin {
    /**
     * Конструктор
     */
    public function __construct() {
        // Проверка WooCommerce и регистрация хуков
        add_action('plugins_loaded', array($this, 'init'), 0);
    }

    /**
     * Инициализация плагина
     */
    public function init() {
        // Проверяем наличие WooCommerce
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }

        // Объявляем совместимость с функциями WooCommerce
        add_action('before_woocommerce_init', array($this, 'declare_compatibility'));
        
        // Ждём полной инициализации WooCommerce
        add_action('woocommerce_init', array($this, 'payment_gateway_init'));
        
        // Добавляем комиссию при выборе метода оплаты
        add_action('woocommerce_cart_calculate_fees', array($this, 'add_fee'));
        
        // JavaScript для обновления суммы
        add_action('wp_footer', array($this, 'payment_method_scripts'));
        
        // Регистрация интеграции с блоками (после загрузки блоков WooCommerce)
        add_action('woocommerce_blocks_loaded', array($this, 'register_blocks_integration'));
    }

    /**
     * Уведомление об отсутствии WooCommerce
     */
    public function woocommerce_missing_notice() {
        echo '<div class="error"><p>' . 
             'Для работы плагина "Manual Payment with 5% Fee" необходимо установить и активировать WooCommerce.' . 
             '</p></div>';
    }

    /**
     * Объявляем совместимость с функциями WooCommerce
     */
    public function declare_compatibility() {
        if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
        }
    }

    /**
     * Инициализация платежного шлюза
     */
    public function payment_gateway_init() {
        // Регистрируем класс платежного метода
        require_once dirname(__FILE__) . '/includes/class-payment-gateway.php';
        
        // Добавляем платежный метод в WooCommerce
        add_filter('woocommerce_payment_gateways', array($this, 'add_gateway'));
    }

    /**
     * Добавление платежного шлюза в список
     */
    public function add_gateway($gateways) {
        $gateways[] = 'WC_Manual_Payment_Fee_Gateway';
        return $gateways;
    }

    /**
     * Добавление комиссии
     */
    public function add_fee() {
        if (is_admin() && !defined('DOING_AJAX')) {
            return;
        }
        
        if (WC()->session && WC()->cart) {
            $chosen_payment_method = WC()->session->get('chosen_payment_method');
            
            if ($chosen_payment_method === 'manual_payment_fee') {
                $cart_total = WC()->cart->get_cart_contents_total() + WC()->cart->get_shipping_total();
                $fee = $cart_total * 0.05;
                WC()->cart->add_fee('Комиссия за ручную оплату (5%)', $fee, true);
            }
        }
    }

    /**
     * JavaScript для обновления суммы при изменении метода оплаты
     */
    public function payment_method_scripts() {
        if (is_checkout()) {
            ?>
            <script type="text/javascript">
                jQuery(function($) {
                    // Для обычного оформления заказа
                    $('body').on('change', 'input[name="payment_method"]', function() {
                        $('body').trigger('update_checkout');
                    });
                    
                    // Для блочного оформления заказа
                    $(document.body).on('payment_method_selected', function() {
                        $('body').trigger('update_checkout');
                    });
                });
            </script>
            <?php
        }
    }

    /**
     * Регистрация интеграции с блоками WooCommerce
     */
    public function register_blocks_integration() {
        // Проверяем, доступен ли класс интеграции с блоками
        if (!class_exists('Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
            return;
        }
        
        // Подключаем класс интеграции
        require_once dirname(__FILE__) . '/includes/class-blocks-integration.php';
        
        // Регистрируем интеграцию
        add_action(
            'woocommerce_blocks_payment_method_type_registration',
            function($registry) {
                $registry->register(new WC_Manual_Payment_Fee_Blocks_Integration());
            }
        );
    }

    /**
     * Активация плагина
     */
    public static function activate() {
        // Создаем необходимые директории и файлы
        $includes_dir = plugin_dir_path(__FILE__) . 'includes';
        
        if (!file_exists($includes_dir)) {
            mkdir($includes_dir, 0755, true);
        }
        
        // Создаем файл класса платежного шлюза
        self::create_gateway_class_file();
        
        // Создаем файл интеграции с блоками
        self::create_blocks_integration_file();
    }

    /**
     * Создание файла класса платежного шлюза
     */
    private static function create_gateway_class_file() {
        $file_path = plugin_dir_path(__FILE__) . 'includes/class-payment-gateway.php';
        
        if (!file_exists($file_path)) {
            $content = '<?php
// Выход, если файл открыт напрямую
defined("ABSPATH") || exit;

/**
 * Класс метода оплаты
 */
class WC_Manual_Payment_Fee_Gateway extends WC_Payment_Gateway {
    /**
     * Конструктор
     */
    public function __construct() {
        $this->id = "manual_payment_fee";
        $this->icon = "";
        $this->has_fields = false;
        $this->method_title = "Ручная оплата с комиссией";
        $this->method_description = "Ручная оплата с комиссией 5% от суммы заказа";
        
        // Поддержка блоков
        $this->supports = array(
            "products",
            "refunds",
            "blocks"
        );
        
        // Инициализация настроек
        $this->init_form_fields();
        $this->init_settings();
        
        // Получаем настройки
        $this->title = $this->get_option("title");
        $this->description = $this->get_option("description");
        $this->enabled = $this->get_option("enabled");
        
        // Сохранение настроек
        add_action("woocommerce_update_options_payment_gateways_" . $this->id, array($this, "process_admin_options"));
    }
    
    /**
     * Поля настроек
     */
    public function init_form_fields() {
        $this->form_fields = array(
            "enabled" => array(
                "title"   => "Включить/Выключить",
                "type"    => "checkbox",
                "label"   => "Включить метод оплаты",
                "default" => "yes"
            ),
            "title" => array(
                "title"       => "Название",
                "type"        => "text",
                "description" => "Название метода оплаты, отображаемое при оформлении заказа",
                "default"     => "Ручная оплата (комиссия 5%)",
                "desc_tip"    => true,
            ),
            "description" => array(
                "title"       => "Описание",
                "type"        => "textarea",
                "description" => "Описание метода оплаты, отображаемое при оформлении заказа",
                "default"     => "При выборе данного метода оплаты к сумме заказа добавляется комиссия 5%",
                "desc_tip"    => true,
            )
        );
    }
    
    /**
     * Обработка платежа
     */
    public function process_payment($order_id) {
        $order = wc_get_order($order_id);
        
        // Помечаем заказ как "в ожидании оплаты"
        $order->update_status("on-hold", "Ожидание ручной оплаты с комиссией 5%");
        
        // Уменьшаем количество товаров на складе
        wc_reduce_stock_levels($order_id);
        
        // Очищаем корзину
        WC()->cart->empty_cart();
        
        // Перенаправляем на страницу "спасибо"
        return array(
            "result"   => "success",
            "redirect" => $this->get_return_url($order)
        );
    }
}';
            
            file_put_contents($file_path, $content);
        }
    }

    /**
     * Создание файла интеграции с блоками
     */
    private static function create_blocks_integration_file() {
        $file_path = plugin_dir_path(__FILE__) . 'includes/class-blocks-integration.php';
        
        if (!file_exists($file_path)) {
            $content = '<?php
// Выход, если файл открыт напрямую
defined("ABSPATH") || exit;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

/**
 * Класс интеграции с блоками WooCommerce
 */
class WC_Manual_Payment_Fee_Blocks_Integration extends AbstractPaymentMethodType {
    /**
     * Имя платежного метода
     *
     * @var string
     */
    protected $name = "manual_payment_fee";

    /**
     * Инициализация настроек
     */
    public function initialize() {
        $this->settings = get_option("woocommerce_manual_payment_fee_settings", []);
        $this->title = !empty($this->settings["title"]) ? $this->settings["title"] : "Ручная оплата (комиссия 5%)";
        $this->description = !empty($this->settings["description"]) ? $this->settings["description"] : "При выборе данного метода оплаты к сумме заказа добавляется комиссия 5%";
        $this->enabled = !empty($this->settings["enabled"]) && $this->settings["enabled"] === "yes";
    }

    /**
     * Данные для фронтенда
     *
     * @return array
     */
    public function get_payment_method_data() {
        return [
            "title" => $this->title,
            "description" => $this->description,
            "supports" => ["products"],
            "enableFeeCalculation" => true
        ];
    }
    
    /**
     * Обработка платежа в блочном интерфейсе
     *
     * @param WC_Order $order
     * @param array $payment_data
     * @return array
     */
    public function process_payment($order, $payment_data) {
        // Добавляем комиссию к заказу
        $order_total = $order->get_subtotal() + $order->get_shipping_total();
        $fee_amount = $order_total * 0.05;
        
        $fee = new WC_Order_Item_Fee();
        $fee->set_name("Комиссия за ручную оплату (5%)");
        $fee->set_amount($fee_amount);
        $fee->set_tax_status("taxable");
        $fee->set_total($fee_amount);
        
        $order->add_item($fee);
        $order->calculate_totals(true);
        $order->save();
        
        // Помечаем заказ как "в ожидании оплаты"
        $order->update_status("on-hold", "Ожидание ручной оплаты с комиссией 5%");
        
        // Очищаем корзину
        WC()->cart->empty_cart();
        
        return [
            "result" => "success",
            "redirect" => $order->get_checkout_order_received_url(),
        ];
    }
}';
            
            file_put_contents($file_path, $content);
        }
    }
}

// Инициализация плагина
$manual_payment_fee_plugin = new Manual_Payment_Fee_Plugin();

// Регистрация действия при активации плагина
register_activation_hook(__FILE__, array('Manual_Payment_Fee_Plugin', 'activate'));