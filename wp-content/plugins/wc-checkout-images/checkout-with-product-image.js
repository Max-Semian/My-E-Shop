jQuery(document).ready(function($) {
    console.log('Checkout Images Plugin: Script loaded');
    
    /**
     * Основные настройки
     */
    const defaultPlaceholder = 'data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%2260%22%20height%3D%2260%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2060%2060%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_1%20text%20%7B%20fill%3A%23AAAAAA%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A10pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_1%22%3E%3Crect%20width%3D%2260%22%20height%3D%2260%22%20fill%3D%22%23EEEEEE%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2214%22%20y%3D%2235%22%3EImg%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E';
    
    // Получаем данные о товарах из PHP
    const productImages = typeof checkoutImages !== 'undefined' ? checkoutImages.products : {};
    const placeholderUrl = typeof checkoutImages !== 'undefined' && checkoutImages.placeholder_url ? checkoutImages.placeholder_url : defaultPlaceholder;
    
    console.log('Product data from PHP:', productImages);

    /**
     * Определяем текущий шаг (совместимо с functions.php)
     */
    function getCurrentStep() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('step') || 'information';
    }

    /**
     * ОСНОВНАЯ ФУНКЦИЯ: Улучшение отображения товаров ТОЛЬКО на шаге payment
     */
    function enhanceProductImagesOnPayment() {
        const currentStep = getCurrentStep();
        
        // Работаем только на шаге payment
        if (currentStep !== 'payment') {
            console.log('Not on payment step, skipping product images enhancement');
            return;
        }
        
        console.log('Enhancing product images on payment step');
        
        // Улучшаем отображение товаров в существующей таблице order review
        enhanceOrderReviewTable();
    }
    
    /**
     * Улучшение существующей таблицы order review с изображениями
     */
    function enhanceOrderReviewTable() {
        const $orderTable = $('.woocommerce-checkout-review-order-table, .shop_table');
        
        if ($orderTable.length === 0) {
            console.log('Order review table not found');
            return;
        }
        
        console.log('Found order review table, enhancing with images');
        
        // Обрабатываем каждую строку товара
        $orderTable.find('tr.cart_item').each(function() {
            const $row = $(this);
            const $nameCell = $row.find('.product-name');
            
            // Пропускаем если уже обработано
            if ($nameCell.find('.enhanced-product-display').length > 0) {
                return;
            }
            
            const productText = $nameCell.text().trim();
            const productNameClean = productText.replace(/\s*×\s*\d+.*/, '').trim();
            
            // Ищем соответствующие данные товара
            let productData = null;
            let cartKey = null;
            
            // Сначала ищем в данных PHP
            for (const key in productImages) {
                if (productImages[key] && productImages[key].name.includes(productNameClean)) {
                    productData = productImages[key];
                    cartKey = key;
                    break;
                }
            }
            
            // Если не найдено в PHP данных, ищем в data-атрибутах
            if (!productData) {
                const $itemData = $('.item-data').filter(function() {
                    return $(this).text().indexOf(productNameClean) !== -1;
                });
                
                if ($itemData.length > 0) {
                    cartKey = $itemData.data('cart_key');
                    const imageUrl = $itemData.data('image_url');
                    productData = {
                        name: productNameClean,
                        image_url: imageUrl || placeholderUrl,
                        id: $itemData.data('product_id') || ''
                    };
                }
            }
            
            // Если данные найдены, создаем улучшенное отображение
            if (productData && productData.image_url) {
                createEnhancedProductDisplay($nameCell, productData, cartKey, productText);
            }
        });
    }
    
    /**
     * Создание улучшенного отображения товара с изображением
     */
    function createEnhancedProductDisplay($nameCell, productData, cartKey, originalText) {
        const imageUrl = productData.image_url || placeholderUrl;
        const productName = productData.name;
        
        // Извлекаем количество из оригинального текста
        const quantityMatch = originalText.match(/×\s*(\d+)/);
        const quantity = quantityMatch ? quantityMatch[1] : '1';
        
        // Создаем новую структуру с изображением
        const enhancedHtml = `
            <div class="enhanced-product-display" data-cart-key="${cartKey || ''}">
                <div class="enhanced-product-image">
                    <img src="${imageUrl}" alt="${productName}" loading="lazy">
                </div>
                <div class="enhanced-product-info">
                    <div class="enhanced-product-name">${productName}</div>
                    <div class="enhanced-product-meta">Qty: ${quantity}</div>
                </div>
            </div>
        `;
        
        $nameCell.html(enhancedHtml);
        
        console.log('Enhanced product display created for:', productName);
    }
    
    /**
     * Обновление количества товара через AJAX
     */
    function updateCartQuantity(cartKey, newQuantity) {
        if (!cartKey || cartKey.startsWith('temp_')) {
            console.error('Invalid cart key');
            return;
        }
        
        console.log('Updating cart quantity:', {cartKey, newQuantity});
        
        // Показываем индикатор загрузки
        $('body').addClass('updating-cart');
        
        // Определяем AJAX URL
        const ajaxUrl = (typeof checkoutImages !== 'undefined' && checkoutImages.ajax_url) 
            ? checkoutImages.ajax_url 
            : '/wp-admin/admin-ajax.php';
        
        // Получаем nonce
        const nonce = $('input[name="woocommerce-process-checkout-nonce"]').val() || 
                     $('input[name="_wpnonce"]').val() || '';
        
        const requestData = {
            action: 'wc_checkout_update_quantity',
            cart_key: cartKey,
            quantity: newQuantity,
            security: nonce
        };
        
        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: requestData,
            success: function(response) {
                console.log('AJAX response:', response);
                
                if (response.success) {
                    // Запускаем обновление чекаута
                    $(document.body).trigger('update_checkout');
                    
                    if (newQuantity <= 0) {
                        // Товар удален
                        $(`.enhanced-product-display[data-cart-key="${cartKey}"]`).closest('tr').fadeOut();
                    }
                } else {
                    console.error('Failed to update cart:', response.data);
                    alert('Failed to update cart. Please refresh the page and try again.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                // Fallback: reload page
                window.location.reload();
            },
            complete: function() {
                $('body').removeClass('updating-cart');
            }
        });
    }
    
    /**
     * Добавляем стили для улучшенного отображения
     */
    function addEnhancedStyles() {
        if ($('#enhanced-checkout-styles').length > 0) {
            return; // Стили уже добавлены
        }
        
        const styles = `
            <style id="enhanced-checkout-styles">
            /* Enhanced product display styles */
            .enhanced-product-display {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 8px 0;
            }
            
            .enhanced-product-image {
                flex-shrink: 0;
                width: 50px;
                height: 50px;
                border-radius: 6px;
                overflow: hidden;
                background: #f5f5f5;
                border: 1px solid #e0e0e0;
            }
            
            .enhanced-product-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.2s ease;
            }
            
            .enhanced-product-image:hover img {
                transform: scale(1.05);
            }
            
            .enhanced-product-info {
                flex: 1;
                min-width: 0;
            }
            
            .enhanced-product-name {
                font-weight: 500;
                color: var(--black-color, #333);
                line-height: 1.4;
                margin-bottom: 4px;
                word-wrap: break-word;
            }
            
            .enhanced-product-meta {
                font-size: 13px;
                color: var(--grey-color, #666);
                opacity: 0.8;
            }
            
            /* Loading state */
            body.updating-cart {
                pointer-events: none;
            }
            
            body.updating-cart::after {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.8);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            body.updating-cart::before {
                content: 'Updating...';
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                z-index: 10000;
                background: var(--main-color, #007cba);
                color: white;
                padding: 12px 24px;
                border-radius: 6px;
                font-weight: 500;
            }
            
            /* Mobile responsiveness */
            @media (max-width: 768px) {
                .enhanced-product-image {
                    width: 40px;
                    height: 40px;
                }
                
                .enhanced-product-display {
                    gap: 10px;
                }
                
                .enhanced-product-name {
                    font-size: 14px;
                }
                
                .enhanced-product-meta {
                    font-size: 12px;
                }
            }
            
            /* Integration with existing checkout styles */
            .woocommerce-checkout-review-order-table .cart_item td {
                vertical-align: middle;
                padding: 15px 10px;
            }
            
            .woocommerce-checkout-review-order-table .product-total {
                font-weight: 600;
                color: var(--main-color, #007cba);
                text-align: right;
            }
            </style>
        `;
        
        $('head').append(styles);
    }
    
    /**
     * Инициализация плагина
     */
    function initializePlugin() {
        console.log('Initializing checkout images plugin');
        
        // Добавляем стили
        addEnhancedStyles();
        
        // Применяем улучшения
        enhanceProductImagesOnPayment();
        
        // Переприменяем при обновлении чекаута
        $(document.body).on('updated_checkout', function() {
            console.log('Checkout updated, re-applying enhancements');
            setTimeout(enhanceProductImagesOnPayment, 100);
        });
        
        // Наблюдатель за изменениями DOM
        if (window.MutationObserver) {
            const observer = new MutationObserver(function(mutations) {
                let needsUpdate = false;
                
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList') {
                        const hasOrderChanges = Array.from(mutation.addedNodes).some(node => 
                            node.nodeType === 1 && (
                                node.matches && node.matches('.cart_item, #order_review, .shop_table') ||
                                node.querySelector && node.querySelector('.cart_item, #order_review, .shop_table')
                            )
                        );
                        
                        if (hasOrderChanges) {
                            needsUpdate = true;
                        }
                    }
                });
                
                if (needsUpdate) {
                    console.log('DOM changes detected, updating product images');
                    setTimeout(enhanceProductImagesOnPayment, 50);
                }
            });
            
            // Наблюдаем за изменениями в области чекаута
            const targetNode = document.querySelector('.woocommerce-checkout, form.checkout');
            if (targetNode) {
                observer.observe(targetNode, { 
                    childList: true, 
                    subtree: true 
                });
                console.log('DOM observer started');
            }
        }
    }
    
    // Запускаем инициализацию
    initializePlugin();
    
    // Дополнительная инициализация при смене шага
    $(window).on('popstate', function() {
        setTimeout(initializePlugin, 100);
    });
    
    /**
     * ОБРАБОТКА КНОПОК CONTINUE ДЛЯ MULTI-STEP CHECKOUT
     */
    function handleContinueButtons() {
        $('.btn-next').off('click').on('click', function(e) {
            e.preventDefault();
            
            const currentStep = $(this).data('step');
            const $form = $(this).closest('form');
            const $button = $(this);
            let isValid = true;
            
            // Валидация обязательных полей
            $form.find('input[required], select[required]').each(function() {
                if (!$(this).val() || !$(this).val().trim()) {
                    isValid = false;
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                }
            });
            
            if (!isValid) {
                alert('Please fill in all required fields');
                $form.find('.error').first().focus();
                return;
            }
            
            // Блокируем кнопку
            $button.prop('disabled', true).css('opacity', '0.6');
            
            // Определяем следующий шаг
            let nextStep = '';
            if (currentStep === 'information') {
                nextStep = 'shipping';
            } else if (currentStep === 'shipping') {
                nextStep = 'payment';
            }
            
            // Собираем данные формы для отправки в WooCommerce
            const formData = {};
            $form.find('input, select, textarea').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    formData[name] = $(this).val();
                    
                    // Заполняем скрытые поля в основной форме WooCommerce (если она есть)
                    const $mainFormField = $('form.checkout').find('[name="' + name + '"]');
                    if ($mainFormField.length) {
                        $mainFormField.val($(this).val());
                    }
                }
            });
            
            // Сохраняем в sessionStorage для восстановления
            sessionStorage.setItem('checkout_' + currentStep, JSON.stringify(formData));
            
            // Сохраняем все собранные данные из всех шагов
            const allStepsData = {};
            ['information', 'shipping'].forEach(function(step) {
                const stepData = sessionStorage.getItem('checkout_' + step);
                if (stepData) {
                    Object.assign(allStepsData, JSON.parse(stepData));
                }
            });
            sessionStorage.setItem('checkout_all_data', JSON.stringify(allStepsData));
            
            // Переход на следующий шаг
            window.location.href = checkoutImages.checkout_url + '?step=' + nextStep;
        });
        
        // Восстановление данных формы при загрузке страницы
        const urlParams = new URLSearchParams(window.location.search);
        const currentStep = urlParams.get('step') || 'information';
        
        // Восстанавливаем данные текущего шага
        const savedData = sessionStorage.getItem('checkout_' + currentStep);
        if (savedData) {
            try {
                const formData = JSON.parse(savedData);
                $.each(formData, function(name, value) {
                    $('[name="' + name + '"]').val(value);
                });
            } catch(e) {
                console.error('Error restoring form data:', e);
            }
        }
        
        // НА ШАГЕ PAYMENT: Восстанавливаем ВСЕ данные из предыдущих шагов
        if (currentStep === 'payment') {
            // Проверяем флаг - восстанавливаем только 1 раз
            if (!window.checkoutDataRestored) {
                const allData = sessionStorage.getItem('checkout_all_data');
                
                if (allData) {
                    try {
                        const formData = JSON.parse(allData);
                        
                        $.each(formData, function(name, value) {
                            const $field = $('form.checkout').find('[name="' + name + '"]');
                            if ($field.length) {
                                $field.val(value);
                            } else {
                                // Создаем скрытое поле если его нет
                                $('form.checkout').append(
                                    $('<input>').attr({
                                        type: 'hidden',
                                        name: name,
                                        value: value
                                    })
                                );
                            }
                        });
                        
                        // Устанавливаем флаг что данные восстановлены
                        window.checkoutDataRestored = true;
                        
                        // Триггерим update_checkout ОДИН РАЗ для пересчета
                        setTimeout(function() {
                            $(document.body).trigger('update_checkout');
                        }, 100);
                    } catch(e) {
                        console.error('Error restoring all data:', e);
                    }
                }
            }
        }
        
        // Убираем класс error при вводе
        $('input, select, textarea').on('input change', function() {
            $(this).removeClass('error');
        });
    }
    
    // Инициализируем обработчики кнопок
    handleContinueButtons();
    
    // Переинициализация после обновления DOM
    $(document.body).on('updated_checkout', function() {
        handleContinueButtons();
    });
    
    console.log('Checkout Images Plugin: Initialization complete');
});