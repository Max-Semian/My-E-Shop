<?php
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
}