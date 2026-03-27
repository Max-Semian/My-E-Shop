'use strict';

(function($) {
  // Глобальный флаг для предотвращения двойного добавления
  window.wpcsb_adding_to_cart = false;
  
  $(document).on('woovr_selected', function(e, selected, variations) {
    var id = selected.attr('data-id');
    var pid = selected.attr('data-pid');
    var price_html = selected.attr('data-pricehtml');
    var image_src = selected.attr('data-imagesrc');
    var purchasable = selected.attr('data-purchasable');

    // change price
    if (price_html !== undefined && price_html !== '') {
      $('.wpcsb-price-ori').hide();
      $('.wpcsb-price-new').html(price_html).show();
    } else {
      $('.wpcsb-price-new').html('').hide();
      $('.wpcsb-price-ori').show();
    }

    // change image
    if (image_src !== undefined && image_src !== '') {
      $('.wpcsb-image-ori').hide();
      $('.wpcsb-image-new').html('<img src="' + image_src + '"/>').show();
    } else {
      $('.wpcsb-image-ori').show();
      $('.wpcsb-image-new').html('').hide();
    }

    // change buttons
    if (id > 0) {
      $('.wpcsb-wrapper .woosq-btn, .wpcsb-wrapper .woosc-btn, .wpcsb-wrapper .woosw-btn').
          attr('data-id', id);
    } else if (pid > 0) {
      $('.wpcsb-wrapper .woosq-btn, .wpcsb-wrapper .woosc-btn, .wpcsb-wrapper .woosw-btn').
          attr('data-id', pid);
    }

    if (purchasable === 'yes' && id >= 0) {
      $('.wpcsb-add-to-cart[data-product_id="' + pid + '"] .wpcsb-btn').
          removeClass('wpcsb-disabled');
    } else {
      $('.wpcsb-add-to-cart[data-product_id="' + pid + '"] .wpcsb-btn').
          addClass('wpcsb-disabled');
    }

    $(document).trigger('wpcsb_woovr_selected', [selected, variations]);
  });

  $(document).on('found_variation', function(e, t) {
    var pid = $(e['target']).
        closest('.variations_form').
        attr('data-product_id');

    // change price
    if (t['price_html'] !== undefined && t['price_html'] !== '') {
      $('.wpcsb-price-ori').hide();
      $('.wpcsb-price-new').html(t['price_html']).show();
    } else {
      $('.wpcsb-price-new').html('').hide();
      $('.wpcsb-price-ori').show();
    }

    // change image
    if (t['image']['url'] && t['image']['url'] !== '') {
      var image_src = t['image']['url'];

      if (t['image']['thumb_src'] && t['image']['thumb_src'] !== '') {
        image_src = t['image']['thumb_src'];
      }

      $('.wpcsb-image-ori').hide();

      if (t['image']['srcset'] && t['image']['srcset'] !== '') {
        $('.wpcsb-image-new').
            html('<img src="' + image_src + '" srcset="' +
                t['image']['srcset'] + '"/>').
            show();
      } else {
        $('.wpcsb-image-new').
            html('<img src="' + image_src + '"/>').
            show();
      }
    } else {
      $('.wpcsb-image-ori').show();
      $('.wpcsb-image-new').html('').hide();
    }

    // change buttons
    $('.wpcsb-wrapper .woosq-btn, .wpcsb-wrapper .woosc-btn, .wpcsb-wrapper .woosw-btn').
        attr('data-id', t['variation_id']);

    if (t['is_in_stock'] && t['is_purchasable']) {
      $('.wpcsb-add-to-cart[data-product_id="' + pid + '"] .wpcsb-btn').
          removeClass('wpcsb-disabled');
    } else {
      $('.wpcsb-add-to-cart[data-product_id="' + pid + '"] .wpcsb-btn').
          addClass('wpcsb-disabled');
    }

    $(document).trigger('wpcsb_found_variation', [t]);
  });

  $(document).on('reset_data', function(e) {
    var pid = $(e['target']).
        closest('.variations_form').
        attr('data-product_id');

    // reset price
    $('.wpcsb-price-new').html('').hide();
    $('.wpcsb-price-ori').show();

    // reset image
    $('.wpcsb-image-ori').show();
    $('.wpcsb-image-new').html('').hide();

    // reset buttons
    $('.wpcsb-wrapper .woosq-btn, .wpcsb-wrapper .woosc-btn, .wpcsb-wrapper .woosw-btn').
        attr('data-id', pid);

    // disable button
    $('.wpcsb-add-to-cart[data-product_id="' + pid + '"] .wpcsb-btn').
        addClass('wpcsb-disabled');

    $(document).trigger('wpcsb_reset_data');
  });

  $(document).on('click touch', '.wpcsb-btn', function(e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    
    // Проверяем глобальный флаг
    if (window.wpcsb_adding_to_cart) {
      return false;
    }

    var $button = $(this);
    var $stickyWrapper = $button.closest('.wpcsb-wrapper');
    var product_id = $button.closest('.wpcsb-add-to-cart').data('product_id');
    var quantity = $('.wpcsb-qty-' + product_id).val() || 1;
    
    // Проверяем, есть ли форма вариаций в sticky панели
    var $variationsForm = $stickyWrapper.find('.variations_form');
    var isVariableProduct = $variationsForm.length > 0;

    if ($button.hasClass('wpcsb-disabled')) {
      wpcsb_scroll();
    } else if (isVariableProduct) {
      // Для вариативных товаров - проверяем variation_id
      var variation_id = $variationsForm.find('input[name="variation_id"]').val();
      
      if (!variation_id || variation_id == '0') {
        // Прокручиваем к основной форме если вариация не выбрана
        wpcsb_scroll();
        return false;
      }
      
      // Устанавливаем глобальный флаг
      window.wpcsb_adding_to_cart = true;
      
      // Собираем данные для вариативного товара
      var formData = new FormData();
      formData.append('add-to-cart', variation_id);
      formData.append('product_id', product_id);
      formData.append('variation_id', variation_id);
      formData.append('quantity', quantity);
      
      // Добавляем атрибуты вариации
      $variationsForm.find('select[name^="attribute_"]').each(function() {
        var attrName = $(this).attr('name');
        var attrValue = $(this).val();
        if (attrValue) {
          formData.append(attrName, attrValue);
        }
      });
      
      // Отправляем AJAX запрос для вариативного товара
      $.ajax({
        type: 'POST',
        url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'),
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
          $button.addClass('loading').prop('disabled', true);
        },
        success: function(response) {
          if (response.error && response.product_url) {
            window.location = response.product_url;
            return;
          }
          
          $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
        },
        complete: function() {
          $button.removeClass('loading').prop('disabled', false);
          // Сбрасываем флаг через 2 секунды
          setTimeout(function() {
            window.wpcsb_adding_to_cart = false;
          }, 2000);
        }
      });
    } else {
      // Устанавливаем глобальный флаг
      window.wpcsb_adding_to_cart = true;
      
      // Для простых товаров - обычная логика
      $.ajax({
        type: 'POST',
        url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'),
        data: {
          product_id: product_id,
          quantity: quantity
        },
        beforeSend: function() {
          $button.addClass('loading').prop('disabled', true);
        },
        success: function(response) {
          if (response.error && response.product_url) {
            window.location = response.product_url;
            return;
          }
          
          $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
        },
        complete: function() {
          $button.removeClass('loading').prop('disabled', false);
          // Сбрасываем флаг через 2 секунды
          setTimeout(function() {
            window.wpcsb_adding_to_cart = false;
          }, 2000);
        }
      });
    }

    $(document).trigger('wpcsb_btn_clicked');
  });

  // Обработчик для кнопки вариативного товара в sticky панели
  $(document).on('click', '.wpcsb-wrapper .variations_form .add2cart-btn-var', function(e) {
    e.preventDefault();
    
    var $button = $(this);
    var $stickyForm = $button.closest('.variations_form');
    var variation_id = $stickyForm.find('input[name="variation_id"]').val();
    var product_id = $stickyForm.find('input[name="product_id"]').val();
    var quantity = $stickyForm.find('input[name="quantity"]').val() || 1;
    
    // Проверяем, что вариация выбрана
    if (!variation_id || variation_id == '0') {
      // Прокручиваем к основной форме
      wpcsb_scroll();
      return false;
    }
    
    // Собираем данные для WooCommerce
    var formData = new FormData();
    formData.append('add-to-cart', product_id);
    formData.append('product_id', product_id);
    formData.append('variation_id', variation_id);
    formData.append('quantity', quantity);
    
    // Добавляем атрибуты вариации
    $stickyForm.find('select[name^="attribute_"]').each(function() {
      var attrName = $(this).attr('name');
      var attrValue = $(this).val();
      if (attrValue) {
        formData.append(attrName, attrValue);
      }
    });
    
    // Отправляем AJAX запрос
    $.ajax({
      type: 'POST',
      url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'),
      data: formData,
      processData: false,
      contentType: false,
      beforeSend: function() {
        $button.addClass('loading').prop('disabled', true);
      },
      success: function(response) {
        if (response.error && response.product_url) {
          window.location = response.product_url;
          return;
        }
        
        // Обновляем фрагменты корзины
        $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
      },
      complete: function() {
        $button.removeClass('loading').prop('disabled', false);
      },
      error: function() {
        $button.removeClass('loading').prop('disabled', false);
      }
    });
    
    return false;
  });

  $(document).on('click touch', '.wpcsb-wrapper .wpcbn-btn', function(e) {
    wpcsb_scroll();
    $('.wpcbn-btn').not(this).trigger('click');
  });

  $(document).on('click keyup keydown paste', '.wpcsb-qty', function() {
    // wpcsb-qty
    var qty = $(this).val();
    var product_id = $(this).closest('.wpcsb-add-to-cart').data('product_id');

    $('.wpcsb-id-' + product_id).
        closest('form.cart').
        find('.qty').
        val(qty).
        trigger('change');

    $(document).trigger('wpcsb_change_qty', [qty, product_id]);
  });

  $(document).on('click keyup keydown paste', 'form.cart .qty', function() {
    // main qty
    var qty = $(this).val();
    var product_id = $(this).
        closest('form.cart').
        find('.wpcsb-id').
        data('product_id');

    $('.wpcsb-qty-' + product_id).val(qty).trigger('change');

    $(document).trigger('wpcsb_change_main_qty', [qty, product_id]);
  });

  $(window).on('scroll', function() {
    if ((($(window).scrollTop() + $(window).height() +
            parseInt(wpcsb_vars.offset_bottom)) >= $(document).height()) ||
        (($(window).scrollTop() - parseInt(wpcsb_vars.offset_top)) <= 0)) {
      wpcsb_hide();
    } else {
      wpcsb_show();
    }
  });

  function wpcsb_show() {
    $('.wpcsb-wrapper').addClass('wpcsb-active');
  }

  function wpcsb_hide() {
    $('.wpcsb-wrapper').removeClass('wpcsb-active');
  }

  function wpcsb_scroll() {
    $('html, body').animate({
      scrollTop: $('form.cart').offset().top - 100,
    }, 300);

    $(document).trigger('wpcsb_scroll');
  }
})(jQuery);
