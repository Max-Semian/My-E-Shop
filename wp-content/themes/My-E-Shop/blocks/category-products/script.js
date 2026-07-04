// VERSION: 2.0.0 - UPDATED: 2026-02-10 - MOBILE FIX APPLIED
(function() {
    'use strict';

    // Simple Swiper implementation for the slider
    function initCategoryProductsSlider() {
        var sliders = document.querySelectorAll('.category-products-slider');
        
        sliders.forEach(function(sliderElement) {
            var wrapper = sliderElement.querySelector('.swiper-wrapper');
            var slides = sliderElement.querySelectorAll('.swiper-slide');
            
            // Find navigation buttons in the parent container
            var sliderWrapper = sliderElement.closest('.category-products-slider-wrapper');
            var prevBtn = sliderWrapper ? sliderWrapper.querySelector('.swiper-button-prev') : null;
            var nextBtn = sliderWrapper ? sliderWrapper.querySelector('.swiper-button-next') : null;
            var pagination = sliderElement.querySelector('.swiper-pagination');
            
            var slidesPerView = parseInt(sliderElement.dataset.slidesPerView) || 4;
            var autoplay = sliderElement.dataset.autoplay === 'true';
            var autoplaySpeed = parseInt(sliderElement.dataset.autoplaySpeed) || 3000;
            var showNavigation = sliderElement.dataset.showNavigation === 'true';
            var showPagination = sliderElement.dataset.showPagination === 'true';
            
            var currentIndex = 0;
            var autoplayInterval;

            // Add a smooth animation to the wrapper
            wrapper.style.transition = 'transform 0.3s ease-in-out';

            // Function that determines the number of visible slides at the current resolution
            function getCurrentSlidesPerView() {
                if (window.innerWidth <= 576) {
                    return 2;
                } else if (window.innerWidth <= 768) {
                    return 2;
                } else if (window.innerWidth <= 1024) {
                    return 3;
                } else {
                    return slidesPerView;
                }
            }
            
            // Calculate maxIndex taking the current resolution into account
            var maxIndex = Math.max(0, slides.length - getCurrentSlidesPerView());

            // Function to update the slider position
            function updateSliderPosition() {
                var currentSlidesPerView = getCurrentSlidesPerView();
                
                // Dynamically get the slide width from the DOM
                var slideWidth = slides.length > 0 ? slides[0].offsetWidth : 250;
                
                // Determine gap depending on the resolution
                var gap = window.innerWidth <= 576 ? 20 : 36;
                
                var translateX = -(currentIndex * (slideWidth + gap));
                
                wrapper.style.transform = 'translateX(' + translateX + 'px)';
                
                // Update the state of the navigation buttons
                if (prevBtn) {
                    prevBtn.style.opacity = currentIndex === 0 ? '0.5' : '1';
                    prevBtn.style.pointerEvents = currentIndex === 0 ? 'none' : 'auto';
                }
                
                if (nextBtn) {
                    nextBtn.style.opacity = currentIndex >= maxIndex ? '0.5' : '1';
                    nextBtn.style.pointerEvents = currentIndex >= maxIndex ? 'none' : 'auto';
                }
                
                // Update the pagination
                updatePagination();
            }

            // Function to create the pagination
            function createPagination() {
                if (!pagination || !showPagination) return;
                
                pagination.innerHTML = '';
                var paginationCount = maxIndex + 1;
                
                for (var i = 0; i < paginationCount; i++) {
                    var bullet = document.createElement('span');
                    bullet.className = 'swiper-pagination-bullet';
                    bullet.dataset.index = i;
                    
                    bullet.addEventListener('click', function() {
                        currentIndex = parseInt(this.dataset.index);
                        updateSliderPosition();
                        resetAutoplay();
                    });
                    
                    pagination.appendChild(bullet);
                }
            }

            // Function to update the active pagination
            function updatePagination() {
                if (!pagination) return;
                
                var bullets = pagination.querySelectorAll('.swiper-pagination-bullet');
                bullets.forEach(function(bullet, index) {
                    bullet.classList.toggle('swiper-pagination-bullet-active', index === currentIndex);
                });
            }

            // Next slide function
            function nextSlide() {
                if (currentIndex < maxIndex) {
                    currentIndex++;
                    updateSliderPosition();
                }
            }

            // Previous slide function
            function prevSlide() {
                if (currentIndex > 0) {
                    currentIndex--;
                    updateSliderPosition();
                }
            }

            // Autoplay function
            function startAutoplay() {
                if (!autoplay) return;
                
                autoplayInterval = setInterval(function() {
                    if (currentIndex < maxIndex) {
                        nextSlide();
                    } else {
                        currentIndex = 0;
                        updateSliderPosition();
                    }
                }, autoplaySpeed);
            }

            // Function to reset autoplay
            function resetAutoplay() {
                if (autoplayInterval) {
                    clearInterval(autoplayInterval);
                }
                startAutoplay();
            }

            // Navigation event handlers
            if (prevBtn && showNavigation) {
                prevBtn.addEventListener('click', function() {
                    prevSlide();
                    resetAutoplay();
                });
            }

            if (nextBtn && showNavigation) {
                nextBtn.addEventListener('click', function() {
                    nextSlide();
                    resetAutoplay();
                });
            }

            // Touch events for mobile devices
            var startX = 0;
            var endX = 0;

            sliderElement.addEventListener('touchstart', function(e) {
                startX = e.touches[0].clientX;
            });

            sliderElement.addEventListener('touchend', function(e) {
                endX = e.changedTouches[0].clientX;
                var diff = startX - endX;
                
                if (Math.abs(diff) > 50) {
                    if (diff > 0) {
                        nextSlide();
                    } else {
                        prevSlide();
                    }
                    resetAutoplay();
                }
            });

            // Stop autoplay on hover
            sliderElement.addEventListener('mouseenter', function() {
                if (autoplayInterval) {
                    clearInterval(autoplayInterval);
                }
            });

            sliderElement.addEventListener('mouseleave', function() {
                startAutoplay();
            });

            // Initialization
            createPagination();
            updateSliderPosition();
            startAutoplay();

            // Handle window resize
            var resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {
                    // Recalculate the maximum index for the new screen size
                    var currentSlidesPerView = getCurrentSlidesPerView();
                    maxIndex = Math.max(0, slides.length - currentSlidesPerView);
                    
                    if (currentIndex > maxIndex) {
                        currentIndex = maxIndex;
                    }
                    
                    createPagination();
                    updateSliderPosition();
                }, 150);
            });
        });
    }

    // Product search function
    function initCategorySearch() {
        var searchForms = document.querySelectorAll('.category-search-form');
        
        searchForms.forEach(function(form) {
            var input = form.querySelector('.category-search-input');
            var categoryId = input.dataset.categoryId;
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                var searchTerm = input.value.trim();
                if (!searchTerm) return;
                
                // AJAX search can be implemented here
                console.log('Product search:', searchTerm, 'in category:', categoryId);

                // Example of redirecting to the search page
                var searchUrl = window.location.origin + '/?s=' + encodeURIComponent(searchTerm) + '&product_cat=' + categoryId;
                window.location.href = searchUrl;
            });
        });
    }

    // Add to cart function
    function initAddToCart() {
        var addToCartBtns = document.querySelectorAll('.add-to-cart-btn');
        
        addToCartBtns.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                var productId = this.dataset.productId;
                var productType = this.dataset.productType;
                
                // Show the loading state
                var originalText = this.textContent;
                this.textContent = 'Adding...';
                this.disabled = true;
                
                // AJAX request to add to cart
                var formData = new FormData();
                formData.append('action', 'woocommerce_add_to_cart');
                formData.append('product_id', productId);
                formData.append('quantity', '1');
                
                fetch(window.wc_add_to_cart_params ? window.wc_add_to_cart_params.ajax_url : '/wp-admin/admin-ajax.php', {
                    method: 'POST',
                    body: formData
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    if (data.error) {
                        alert('Error adding to cart');
                    } else {
                        // Successfully added
                        btn.textContent = 'Added!';
                        btn.style.background = '#27ae60';
                        
                        setTimeout(function() {
                            btn.textContent = originalText;
                            btn.style.background = '';
                            btn.disabled = false;
                        }, 2000);
                        
                        // Update the cart counter
                        document.dispatchEvent(new CustomEvent('wc_fragment_refresh'));
                    }
                })
                .catch(function() {
                    alert('Error adding to cart');
                    btn.textContent = originalText;
                    btn.disabled = false;
                });
            });
        });
    }

    // Add to wishlist function
    function initWishlist() {
        var wishlistBtns = document.querySelectorAll('.add-to-wishlist');
        
        wishlistBtns.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                var productId = this.dataset.productId;
                
                // Toggle the button state
                this.classList.toggle('active');
                
                // Saving to the wishlist can be implemented here
                console.log('Add/remove from wishlist:', productId);

                // Show a notification
                var message = this.classList.contains('active') ? 'Added to wishlist' : 'Removed from wishlist';
                showNotification(message);
            });
        });
    }

    // Notification display function
    function showNotification(message) {
        var notification = document.createElement('div');
        notification.className = 'category-products-notification';
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #333;
            color: white;
            padding: 12px 20px;
            border-radius: 4px;
            z-index: 10000;
            font-size: 14px;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(function() {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        setTimeout(function() {
            notification.style.transform = 'translateX(100%)';
            setTimeout(function() {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Initialization on DOM load
    document.addEventListener('DOMContentLoaded', function() {
        initCategoryProductsSlider();
        initCategorySearch();
        initAddToCart();
        initWishlist();
    });

    // Initialization for dynamically loaded content
    document.addEventListener('block-rendered', function() {
        initCategoryProductsSlider();
        initCategorySearch();
        initAddToCart();
        initWishlist();
    });

})();