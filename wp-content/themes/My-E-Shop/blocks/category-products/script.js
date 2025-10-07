(function() {
    'use strict';

    // Простая реализация Swiper для слайдера
    function initCategoryProductsSlider() {
        var sliders = document.querySelectorAll('.category-products-slider');
        
        sliders.forEach(function(sliderElement) {
            var wrapper = sliderElement.querySelector('.swiper-wrapper');
            var slides = sliderElement.querySelectorAll('.swiper-slide');
            
            // Ищем кнопки навигации в родительском контейнере
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
            var maxIndex = Math.max(0, slides.length - slidesPerView);
            var autoplayInterval;

            // Добавляем плавную анимацию к wrapper
            wrapper.style.transition = 'transform 0.3s ease-in-out';

            // Функция обновления позиции слайдера
            function updateSliderPosition() {
                var translateX = -(currentIndex * (100 / slidesPerView));
                wrapper.style.transform = 'translateX(' + translateX + '%)';
                
                // Обновляем состояние кнопок навигации
                if (prevBtn) {
                    prevBtn.style.opacity = currentIndex === 0 ? '0.5' : '1';
                    prevBtn.style.pointerEvents = currentIndex === 0 ? 'none' : 'auto';
                }
                
                if (nextBtn) {
                    nextBtn.style.opacity = currentIndex >= maxIndex ? '0.5' : '1';
                    nextBtn.style.pointerEvents = currentIndex >= maxIndex ? 'none' : 'auto';
                }
                
                // Обновляем пагинацию
                updatePagination();
            }

            // Функция создания пагинации
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

            // Функция обновления активной пагинации
            function updatePagination() {
                if (!pagination) return;
                
                var bullets = pagination.querySelectorAll('.swiper-pagination-bullet');
                bullets.forEach(function(bullet, index) {
                    bullet.classList.toggle('swiper-pagination-bullet-active', index === currentIndex);
                });
            }

            // Функция следующего слайда
            function nextSlide() {
                if (currentIndex < maxIndex) {
                    currentIndex++;
                    updateSliderPosition();
                }
            }

            // Функция предыдущего слайда
            function prevSlide() {
                if (currentIndex > 0) {
                    currentIndex--;
                    updateSliderPosition();
                }
            }

            // Функция автопрокрутки
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

            // Функция сброса автопрокрутки
            function resetAutoplay() {
                if (autoplayInterval) {
                    clearInterval(autoplayInterval);
                }
                startAutoplay();
            }

            // Обработчики событий навигации
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

            // Touch события для мобильных устройств
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

            // Остановка автопрокрутки при наведении
            sliderElement.addEventListener('mouseenter', function() {
                if (autoplayInterval) {
                    clearInterval(autoplayInterval);
                }
            });

            sliderElement.addEventListener('mouseleave', function() {
                startAutoplay();
            });

            // Инициализация
            createPagination();
            updateSliderPosition();
            startAutoplay();

            // Обработка изменения размера окна
            var resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {
                    // Пересчитываем максимальный индекс для нового размера экрана
                    var newSlidesPerView = slidesPerView;
                    
                    if (window.innerWidth <= 480) {
                        newSlidesPerView = 1;
                    } else if (window.innerWidth <= 768) {
                        newSlidesPerView = 2;
                    } else if (window.innerWidth <= 1024) {
                        newSlidesPerView = 3;
                    }
                    
                    maxIndex = Math.max(0, slides.length - newSlidesPerView);
                    
                    if (currentIndex > maxIndex) {
                        currentIndex = maxIndex;
                    }
                    
                    createPagination();
                    updateSliderPosition();
                }, 150);
            });
        });
    }

    // Функция поиска товаров
    function initCategorySearch() {
        var searchForms = document.querySelectorAll('.category-search-form');
        
        searchForms.forEach(function(form) {
            var input = form.querySelector('.category-search-input');
            var categoryId = input.dataset.categoryId;
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                var searchTerm = input.value.trim();
                if (!searchTerm) return;
                
                // Здесь можно реализовать AJAX поиск
                console.log('Поиск товаров:', searchTerm, 'в категории:', categoryId);
                
                // Пример перенаправления на страницу поиска
                var searchUrl = window.location.origin + '/?s=' + encodeURIComponent(searchTerm) + '&product_cat=' + categoryId;
                window.location.href = searchUrl;
            });
        });
    }

    // Функция добавления в корзину
    function initAddToCart() {
        var addToCartBtns = document.querySelectorAll('.add-to-cart-btn');
        
        addToCartBtns.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                var productId = this.dataset.productId;
                var productType = this.dataset.productType;
                
                // Показываем состояние загрузки
                var originalText = this.textContent;
                this.textContent = 'Добавляем...';
                this.disabled = true;
                
                // AJAX запрос добавления в корзину
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
                        alert('Ошибка добавления в корзину');
                    } else {
                        // Успешно добавлено
                        btn.textContent = 'Добавлено!';
                        btn.style.background = '#27ae60';
                        
                        setTimeout(function() {
                            btn.textContent = originalText;
                            btn.style.background = '';
                            btn.disabled = false;
                        }, 2000);
                        
                        // Обновляем счетчик корзины
                        document.dispatchEvent(new CustomEvent('wc_fragment_refresh'));
                    }
                })
                .catch(function() {
                    alert('Ошибка добавления в корзину');
                    btn.textContent = originalText;
                    btn.disabled = false;
                });
            });
        });
    }

    // Функция добавления в избранное
    function initWishlist() {
        var wishlistBtns = document.querySelectorAll('.add-to-wishlist');
        
        wishlistBtns.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                var productId = this.dataset.productId;
                
                // Переключаем состояние кнопки
                this.classList.toggle('active');
                
                // Здесь можно реализовать сохранение в избранное
                console.log('Добавить/удалить из избранного:', productId);
                
                // Показываем уведомление
                var message = this.classList.contains('active') ? 'Добавлено в избранное' : 'Удалено из избранного';
                showNotification(message);
            });
        });
    }

    // Функция показа уведомлений
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

    // Инициализация при загрузке DOM
    document.addEventListener('DOMContentLoaded', function() {
        initCategoryProductsSlider();
        initCategorySearch();
        initAddToCart();
        initWishlist();
    });

    // Инициализация для динамически загруженного контента
    document.addEventListener('block-rendered', function() {
        initCategoryProductsSlider();
        initCategorySearch();
        initAddToCart();
        initWishlist();
    });

})();