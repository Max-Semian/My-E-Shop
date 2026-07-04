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
    
    // Completely disable the standard WooCommerce handlers for variable products
    if ($('.custom-variation-add-to-cart').length > 0) {
        // Disable all submit handlers on the variations form
        $('form.variations_form').off('submit');
        $('form.variations_form').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });
        
        // Disable click handlers on the standard WooCommerce button inside the form
        $('form.variations_form .single_add_to_cart_button').off('click');
        $('form.variations_form .single_add_to_cart_button').on('click', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });
    }
    
    // Form validation before submit
    $('form.cart').on('submit', function(e) {
        // Skip processing for variable products with a custom button
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
    // Initialize default values for variable products
    function initializeDefaultVariations() {
        // For color circles - find the checked element and synchronize
        var checkedColorInput = $('.variation-color-input:checked');
        if (checkedColorInput.length) {
            var selectedColor = checkedColorInput.val();
            var hiddenSelect = $('form.variations_form select[name="' + checkedColorInput.attr('name') + '"]');
            if (hiddenSelect.length) {
                hiddenSelect.val(selectedColor).trigger('change');
            }
        }
        
        // For sizes - find the selected element and synchronize
        var selectedSizeOption = $('.variation-size-select option:selected');
        if (selectedSizeOption.length && selectedSizeOption.val() !== '') {
            var selectedSize = selectedSizeOption.val();
            var sizeSelect = $('.variation-size-select');
            var hiddenSelect = $('form.variations_form select[name="' + sizeSelect.attr('name') + '"]');
            if (hiddenSelect.length) {
                hiddenSelect.val(selectedSize).trigger('change');
            }
            // Add class for styling
            sizeSelect.addClass('has-value');
        }
    }
    
    // Function to update the visual state of the selects
    function updateSelectState() {
        $('.variation-size-select').each(function() {
            if ($(this).val() !== '') {
                $(this).addClass('has-value');
            } else {
                $(this).removeClass('has-value');
            }
        });
    }
    
    // Run initialization after the page loads
    setTimeout(function() {
        initializeDefaultVariations();
        updateSelectState();
    }, 500);
    
    // If this is a variable product, listen for variation form changes
    $('form.variations_form').on('found_variation', function(event, variation) {
        
        // Get the variation image
        if (variation.image && variation.image.src) {
            // Find the active carousel slide
            var activeSlide = $('#carouselExampleFade .carousel-item.active img');
            
            // Smoothly change the image
            activeSlide.fadeOut(200, function() {
                $(this).attr('src', variation.image.src)
                       .attr('alt', variation.image.alt || '')
                       .fadeIn(200);
            });
            
            // Also update images in fancybox if used
            activeSlide.attr('data-fancybox', 'gallery');
        }
    });
    
    // Reset to the original image when the variation is reset
    $('form.variations_form').on('reset_data', function() {
        // Restore the main product image
        var activeSlide = $('#carouselExampleFade .carousel-item.active img');
        var originalImage = $('#carouselExampleFade .carousel-item:first-child img').attr('src');
        
        if (originalImage) {
            activeSlide.fadeOut(200, function() {
                $(this).attr('src', originalImage)
                       .fadeIn(200);
            });
        }
    });
    
    // Synchronize custom selects with the WooCommerce form
    function syncAttributesToWooForm() {
        var $form = $('form.variations_form');
        if (!$form.length) {
            return;
        }
        
        // Synchronize color
        var $colorInput = $('.variation-color-input:checked');
        if ($colorInput.length) {
            var colorAttrName = $colorInput.attr('name');
            var $hiddenSelect = $form.find('select[name="' + colorAttrName + '"]');
            if ($hiddenSelect.length && $hiddenSelect.val() !== $colorInput.val()) {
                $hiddenSelect.val($colorInput.val()).trigger('change');
            }
        }
        
        // Synchronize size
        var $sizeSelect = $('.variation-size-select');
        if ($sizeSelect.length && $sizeSelect.val()) {
            var sizeAttrName = $sizeSelect.attr('name');
            var $hiddenSelect = $form.find('select[name="' + sizeAttrName + '"]');
            if ($hiddenSelect.length && $hiddenSelect.val() !== $sizeSelect.val()) {
                $hiddenSelect.val($sizeSelect.val()).trigger('change');
            }
        }
        
        // Synchronize fit (style)
        var $fitSelect = $('.variation-fit-select');
        if ($fitSelect.length && $fitSelect.val()) {
            var fitAttrName = $fitSelect.attr('name');
            var $hiddenSelect = $form.find('select[name="' + fitAttrName + '"]');
            if ($hiddenSelect.length && $hiddenSelect.val() !== $fitSelect.val()) {
                $hiddenSelect.val($fitSelect.val()).trigger('change');
            }
        }
    }
    
    // Function to find variation_id by the selected attributes
    function findVariationId() {
        var $form = $('form.variations_form');
        if (!$form.length) return null;
        
        var variationsJson = $form.data('product_variations');
        if (!variationsJson) {
            return null;
        }
        
        // Collect the selected attributes from the HIDDEN WooCommerce form (it is synchronized)
        var selectedAttrs = {};
        
        $form.find('select[name^="attribute_"]').each(function() {
            var $select = $(this);
            var val = $select.val();
            if (val) {
                selectedAttrs[$select.attr('name')] = val;
            }
        });
        
        // Look for a matching variation
        for (var i = 0; i < variationsJson.length; i++) {
            var variation = variationsJson[i];
            var match = true;
            
            for (var attrName in selectedAttrs) {
                var selectedVal = selectedAttrs[attrName].toLowerCase();
                var variationVal = variation.attributes[attrName];
                
                // WooCommerce may store an empty value for "any"
                if (variationVal === undefined || variationVal === '') {
                    continue; // "any" value - matches
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
    
    // Listen for custom select changes and synchronize
    $('.variation-color-input').on('change', function() {
        syncAttributesToWooForm();
    });
    
    $('.variation-size-select, .variation-fit-select').on('change', function() {
        syncAttributesToWooForm();
    });
    
    // Listen for when WooCommerce finds a variation
    $('form.variations_form').on('found_variation', function(event, variation) {
        // Variation found
    });
    
    // Initialization on page load
    $(document).ready(function() {
        setTimeout(function() {
            syncAttributesToWooForm();
        }, 500);
    });
    
    // Custom Add to Cart button
    $('.custom-variation-add-to-cart').on('click', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        
        // Check the global flag (shared with the sticky plugin)
        if (window.wpcsb_adding_to_cart) {
            return false;
        }
        
        var $button = $(this);
        var $form = $('form.variations_form');
        
        if (!$form.length) {
            return;
        }
        
        // Check that all custom options are selected
        var $sizeSelect = $('.variation-size-select');
        if ($sizeSelect.length && !$sizeSelect.val()) {
            alert('Please select a size.');
            return;
        }
        
        // Synchronize all attributes
        syncAttributesToWooForm();
        
        // Set the global flag
        window.wpcsb_adding_to_cart = true;
        
        // Disable the button and the form
        $button.prop('disabled', true).addClass('disabled loading');
        $form.addClass('processing');
        
        // Find variation_id ourselves
        var variationId = findVariationId();
        var productId = $form.find('input[name="product_id"]').val() || $button.data('product-id');
        
        if (!variationId) {
            alert('Could not find the selected variation. Please make sure all options are selected.');
            $button.prop('disabled', false);
            window.wpcsb_adding_to_cart = false;
            return;
        }
        
        // Collect data for the AJAX request
        var formData = new FormData();
        formData.append('add-to-cart', variationId);
        formData.append('product_id', productId);
        formData.append('variation_id', variationId);
        formData.append('quantity', 1);
        
        // Add attributes
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
        
        // Send via AJAX
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
                
                // Trigger the add-to-cart event
                $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
                
                // Replace POST with GET in the history
                if (window.history.replaceState) {
                    window.history.replaceState(null, null, window.location.href);
                }
                
                // Reset the global flag
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
    
    // Update the UI after adding to cart
    $(document.body).on('added_to_cart', function() {
        var $button = $('.custom-variation-add-to-cart');
        $button.html('<span class="success">✓ Added!</span>');
        
        // Replace the POST request in the history with GET to avoid resubmission on reload
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
        
        setTimeout(function() {
            var iconSrc = $button.data('icon-src');
            var text = $button.data('original-text') || 'Add to cart';
            $button.prop('disabled', false).html('<img src="' + iconSrc + '" alt="Add to cart"> ' + text);
        }, 2000);
    });
    
    // Handling for simple products
    $('.color-picker-item input[type="radio"]:not(.variation-color-input)').on('change', function() {
        var selectedColor = $(this).val();
        
        // Trigger a change in the standard WooCommerce form if present
        var wooSelect = $('select[name="attribute_pa_color"], select[name="attribute_color"]');
        if (wooSelect.length) {
            wooSelect.val(selectedColor.toLowerCase()).trigger('change');
        }
    });
    
    // Handling for the custom size select (simple products)
    $('.custom-select:not(.variation-size-select)').on('change', function() {
        var selectedSize = $(this).val();
        
        // Trigger a change in the standard WooCommerce form if present
        var wooSelect = $('select[name="attribute_pa_size"], select[name="attribute_size"]');
        if (wooSelect.length) {
            wooSelect.val(selectedSize.toLowerCase()).trigger('change');
        }
    });
});
