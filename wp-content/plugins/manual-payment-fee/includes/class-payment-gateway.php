<?php
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
}