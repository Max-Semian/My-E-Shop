/**
 * Single Product Dark Theme JavaScript
 * Handles carousel, wishlist, variations and reviews
 */

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
        var button = e.target.closest('.product-dark-carousel-control');
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
    // Get product ID from localized script data
    var productId = typeof singleProductData !== 'undefined' ? singleProductData.productId : 0;
    
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
        
        // Add visual feedback
        $('.color-picker-item').removeClass('selected');
        $(this).closest('.color-picker-item').addClass('selected');
        
        // Here you can add logic to change product images based on color
    });
    
    // Size picker functionality
    $('#size-select').on('change', function() {
        var selectedSize = $(this).val();
        
        if (selectedSize) {
            
            // Add visual feedback
            $(this).addClass('selected');
            
            // Enable add to cart button if it was disabled
            $('.single_add_to_cart_button, .custom-variation-add-to-cart').prop('disabled', false);
        } else {
            $(this).removeClass('selected');
        }
    });
    
    // Helper functions for wishlist cookies
    function getWishlist() {
        var wishlist = getCookie('my_eshop_wishlist');
        if (wishlist) {
            try {
                var parsed = JSON.parse(wishlist);
                // Ensure all IDs are numbers
                return parsed.map(function(id) { return parseInt(id, 10); });
            } catch(e) {
                console.error('Error parsing wishlist:', e);
                return [];
            }
        }
        return [];
    }
    
    function saveWishlist(wishlist) {
        // Ensure all IDs are numbers before saving
        var cleanedWishlist = wishlist.map(function(id) { return parseInt(id, 10); });
        setCookie('my_eshop_wishlist', JSON.stringify(cleanedWishlist), 30);
    }
    
    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }
    
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
    
    // Initialize wishlist button state
    var wishlist = getWishlist();
    if (wishlist.indexOf(productId) > -1) {
        $('.add-to-favorite-btn-dark').addClass('favorited');
        $('.add-to-favorite-btn-dark svg').attr('fill', '#ff6b9d');
        $('.add-to-favorite-btn-dark path').attr('fill', '#ff6b9d');
    }
    
    // Add to favorite functionality
    $('.add-to-favorite-btn-dark').on('click', function(e) {
        var button = $(this);
        var wishlist = getWishlist();
        var index = wishlist.indexOf(productId);
        
        // Toggle visual state
        button.toggleClass('favorited');
        
        if (button.hasClass('favorited')) {
            // Add to wishlist
            if (index === -1) {
                wishlist.push(productId);
                wishlist = wishlist.filter(function(id) { return id != null && id > 0; });
                saveWishlist(wishlist);
            }
            
            button.find('svg').attr('fill', '#ff6b9d');
            button.find('path').attr('fill', '#ff6b9d');
            showMessage('Added to favorites!', 'success');
        } else {
            // Remove from wishlist using filter
            wishlist = wishlist.filter(function(id) { return id !== productId && id != null && id > 0; });
            saveWishlist(wishlist);
            
            button.find('svg').attr('fill', 'none');
            button.find('path').attr('fill', 'none');
            showMessage('Removed from favorites!', 'info');
        }
    });
    
    // Полностью отключаем стандартные обработчики WooCommerce для вариативных товаров
    if ($('.custom-variation-add-to-cart').length > 0) {
        // Отключаем все обработчики submit на форме вариаций
        $('form.variations_form').off('submit');
        $('form.variations_form').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });
        
        // Отключаем обработчики клика на стандартной кнопке WooCommerce внутри формы
        $('form.variations_form .single_add_to_cart_button').off('click');
        $('form.variations_form .single_add_to_cart_button').on('click', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });
    }
    
    // Form validation before submit
    $('form.cart').on('submit', function(e) {
        // Пропускаем обработку для вариативных товаров с кастомной кнопкой
        if ($('.custom-variation-add-to-cart').length > 0) {
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        }
        
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
        $('.single_add_to_cart_button, .custom-variation-add-to-cart').addClass('loading').text('Adding...');
    });
    
    // Reset button state after adding to cart
    $(document.body).on('added_to_cart', function() {
        // Only process our custom buttons with specific class
        $('.custom-atc-btn').each(function() {
            var $btn = $(this);
            
            // Skip if already processed
            if ($btn.data('restored')) return;
            
            $btn.removeClass('loading');
            
            var iconSrc = $btn.attr('data-icon-src');
            var originalText = $btn.attr('data-original-text');
            
            if (iconSrc && originalText) {
                $btn.html('<img src="' + iconSrc + '" alt="Add to cart"> ' + originalText);
                $btn.data('restored', true);
                
                // Reset flag after short delay
                setTimeout(function() {
                    $btn.data('restored', false);
                }, 100);
            }
        });
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
        var $section = $(this).closest('.detail-section');
        var $content = $section.find('.detail-content');
        
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
        var $btn = $(this);
        var target = $btn.data('target');
        var $contentArea = $('#accordionContent');
        var $sourceContent = $('#content-' + target);
        
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

    // Auto-open Description tab on page load
    setTimeout(function() {
        var $descriptionBtn = $('.product-info-accordion .accordion-nav-btn[data-target="description"]');
        if ($descriptionBtn.length && !$descriptionBtn.hasClass('active')) {
            $descriptionBtn.trigger('click');
        }
    }, 100);
    
    // Load initial reviews
    
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
        
        var $button = $(this);
        $button.text('Loading...').prop('disabled', true);
        
        var btnProductId = $button.data('product-id');
        var nextPage = $button.data('page');
        
        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                'action': 'load_product_reviews',
                'product_id': btnProductId,
                'page': nextPage
            },
            success: function(response) {
                // Create temporary div to parse the HTML
                var tempDiv = $('<div>').html(response);
                
                // Find new reviews and append them
                var newReviews = tempDiv.find('.commentlist li');
                $('#reviews-container .commentlist').append(newReviews);
                
                // Replace old button with new one
                $button.remove();
                
                var newButton = tempDiv.find('#load-more-reviews');
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
    // Инициализация дефолтных значений для вариативных продуктов
    function initializeDefaultVariations() {
        // Для цветных кружочков - находим checked элемент и синхронизируем
        var checkedColorInput = $('.variation-color-input:checked');
        if (checkedColorInput.length) {
            var selectedColor = checkedColorInput.val();
            var hiddenSelect = $('form.variations_form select[name="' + checkedColorInput.attr('name') + '"]');
            if (hiddenSelect.length) {
                hiddenSelect.val(selectedColor).trigger('change');
            }
        }
        
        // Для размеров - находим selected элемент и синхронизируем
        var selectedSizeOption = $('.variation-size-select option:selected');
        if (selectedSizeOption.length && selectedSizeOption.val() !== '') {
            var selectedSize = selectedSizeOption.val();
            var sizeSelect = $('.variation-size-select');
            var hiddenSelect = $('form.variations_form select[name="' + sizeSelect.attr('name') + '"]');
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
        
        // Получаем изображение вариации
        if (variation.image && variation.image.src) {
            // Находим активный слайд карусели
            var activeSlide = $('#carouselExampleFade .carousel-item.active img');
            
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
        var activeSlide = $('#carouselExampleFade .carousel-item.active img');
        var originalImage = $('#carouselExampleFade .carousel-item:first-child img').attr('src');
        
        if (originalImage) {
            activeSlide.fadeOut(200, function() {
                $(this).attr('src', originalImage)
                       .fadeIn(200);
            });
        }
    });
    
    // Синхронизация кастомных селектов с WooCommerce формой
    function syncAttributesToWooForm() {
        var $form = $('form.variations_form');
        if (!$form.length) {
            return;
        }
        
        // Синхронизируем цвет
        var $colorInput = $('.variation-color-input:checked');
        if ($colorInput.length) {
            var colorAttrName = $colorInput.attr('name');
            var $hiddenSelect = $form.find('select[name="' + colorAttrName + '"]');
            if ($hiddenSelect.length && $hiddenSelect.val() !== $colorInput.val()) {
                $hiddenSelect.val($colorInput.val()).trigger('change');
            }
        }
        
        // Синхронизируем размер
        var $sizeSelect = $('.variation-size-select');
        if ($sizeSelect.length && $sizeSelect.val()) {
            var sizeAttrName = $sizeSelect.attr('name');
            var $hiddenSelect = $form.find('select[name="' + sizeAttrName + '"]');
            if ($hiddenSelect.length && $hiddenSelect.val() !== $sizeSelect.val()) {
                $hiddenSelect.val($sizeSelect.val()).trigger('change');
            }
        }
        
        // Синхронизируем fit (style)
        var $fitSelect = $('.variation-fit-select');
        if ($fitSelect.length && $fitSelect.val()) {
            var fitAttrName = $fitSelect.attr('name');
            var $hiddenSelect = $form.find('select[name="' + fitAttrName + '"]');
            if ($hiddenSelect.length && $hiddenSelect.val() !== $fitSelect.val()) {
                $hiddenSelect.val($fitSelect.val()).trigger('change');
            }
        }
    }
    
    // Функция поиска variation_id по выбранным атрибутам
    function findVariationId() {
        var $form = $('form.variations_form');
        if (!$form.length) return null;
        
        var variationsJson = $form.data('product_variations');
        if (!variationsJson) {
            return null;
        }
        
        // Собираем выбранные атрибуты из СКРЫТОЙ WooCommerce формы (она синхронизирована)
        var selectedAttrs = {};
        
        $form.find('select[name^="attribute_"]').each(function() {
            var $select = $(this);
            var val = $select.val();
            if (val) {
                selectedAttrs[$select.attr('name')] = val;
            }
        });
        
        // Ищем подходящую вариацию
        for (var i = 0; i < variationsJson.length; i++) {
            var variation = variationsJson[i];
            var match = true;
            
            for (var attrName in selectedAttrs) {
                var selectedVal = selectedAttrs[attrName].toLowerCase();
                var variationVal = variation.attributes[attrName];
                
                // WooCommerce может хранить пустое значение для "any"
                if (variationVal === undefined || variationVal === '') {
                    continue; // "any" значение - подходит
                }
                
                variationVal = variationVal.toLowerCase();
                
                if (variationVal !== selectedVal) {
                    match = false;
                    break;
                }
            }
            
            if (match) {
                return variation.variation_id;
            }
        }
        
        return null;
    }
    
    // Слушаем изменения кастомных селектов и синхронизируем
    $('.variation-color-input').on('change', function() {
        syncAttributesToWooForm();
    });
    
    $('.variation-size-select, .variation-fit-select').on('change', function() {
        syncAttributesToWooForm();
    });
    
    // Слушаем когда WooCommerce находит вариацию
    $('form.variations_form').on('found_variation', function(event, variation) {
        // Variation found
    });
    
    // Инициализация при загрузке страницы
    $(document).ready(function() {
        setTimeout(function() {
            syncAttributesToWooForm();
        }, 500);
    });
    
    // Кастомная кнопка Add to Cart
    $('.custom-variation-add-to-cart').on('click', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        
        // Проверяем глобальный флаг (общий с sticky плагином)
        if (window.wpcsb_adding_to_cart) {
            return false;
        }
        
        var $button = $(this);
        var $form = $('form.variations_form');
        
        if (!$form.length) {
            return;
        }
        
        // Проверяем что все кастомные опции выбраны
        var $sizeSelect = $('.variation-size-select');
        if ($sizeSelect.length && !$sizeSelect.val()) {
            alert('Please select a size.');
            return;
        }
        
        // Синхронизируем все атрибуты
        syncAttributesToWooForm();
        
        // Устанавливаем глобальный флаг
        window.wpcsb_adding_to_cart = true;
        
        // Отключаем кнопку и форму
        $button.prop('disabled', true).addClass('disabled loading');
        $form.addClass('processing');
        
        // Находим variation_id самостоятельно
        var variationId = findVariationId();
        var productId = $form.find('input[name="product_id"]').val() || $button.data('product-id');
        
        if (!variationId) {
            alert('Could not find the selected variation. Please make sure all options are selected.');
            $button.prop('disabled', false);
            window.wpcsb_adding_to_cart = false;
            return;
        }
        
        // Собираем данные для AJAX запроса
        var formData = new FormData();
        formData.append('add-to-cart', variationId);
        formData.append('product_id', productId);
        formData.append('variation_id', variationId);
        formData.append('quantity', 1);
        
        // Добавляем атрибуты
        var $colorInput = $('.variation-color-input:checked');
        if ($colorInput.length) {
            formData.append($colorInput.attr('name'), $colorInput.val());
        }
        var $sizeSelectEl = $('.variation-size-select');
        if ($sizeSelectEl.length && $sizeSelectEl.val()) {
            formData.append($sizeSelectEl.attr('name'), $sizeSelectEl.val());
        }
        var $fitSelect = $('.variation-fit-select');
        if ($fitSelect.length && $fitSelect.val()) {
            formData.append($fitSelect.attr('name'), $fitSelect.val());
        }
        
        // Отправляем через AJAX
        $.ajax({
            type: 'POST',
            url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'),
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.error && response.product_url) {
                    window.location = response.product_url;
                    return;
                }
                
                // Триггерим событие добавления в корзину
                $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
                
                // Заменяем POST в истории на GET
                if (window.history.replaceState) {
                    window.history.replaceState(null, null, window.location.href);
                }
                
                // Сбрасываем глобальный флаг
                setTimeout(function() {
                    window.wpcsb_adding_to_cart = false;
                }, 2000);
            },
            error: function() {
                $button.prop('disabled', false).removeClass('disabled loading');
                $form.removeClass('processing');
                window.wpcsb_adding_to_cart = false;
                alert('Error adding to cart. Please try again.');
            }
        });
        
        return false;
    });
    
    // Обновляем UI после добавления в корзину
    $(document.body).on('added_to_cart', function() {
        var $button = $('.custom-variation-add-to-cart');
        $button.html('<span class="success">✓ Added!</span>');
        
        // Заменяем POST-запрос в истории на GET, чтобы избежать повторной отправки при обновлении
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
        
        setTimeout(function() {
            var iconSrc = $button.data('icon-src');
            var text = $button.data('original-text') || 'Add to cart';
            $button.prop('disabled', false).html('<img src="' + iconSrc + '" alt="Add to cart"> ' + text);
        }, 2000);
    });
    
    // Обработка для простых продуктов
    $('.color-picker-item input[type="radio"]:not(.variation-color-input)').on('change', function() {
        var selectedColor = $(this).val();
        
        // Триггерим изменение в стандартной форме WooCommerce если она есть
        var wooSelect = $('select[name="attribute_pa_color"], select[name="attribute_color"]');
        if (wooSelect.length) {
            wooSelect.val(selectedColor.toLowerCase()).trigger('change');
        }
    });
    
    // Обработка для кастомного селекта размеров (простые продукты)
    $('.custom-select:not(.variation-size-select)').on('change', function() {
        var selectedSize = $(this).val();
        
        // Триггерим изменение в стандартной форме WooCommerce если она есть
        var wooSelect = $('select[name="attribute_pa_size"], select[name="attribute_size"]');
        if (wooSelect.length) {
            wooSelect.val(selectedSize.toLowerCase()).trigger('change');
        }
    });
});
