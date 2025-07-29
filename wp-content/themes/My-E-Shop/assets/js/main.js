$(function () {
    // Search form button handler
    $('#search-form-btn').on('click', function (e) {
        e.preventDefault();
        let form = $(this).parent();
        let inputSearch = form.find('.form-control');
        inputSearch.toggleClass('show').focus();
        if (inputSearch.val()) {
            form.submit();
        }
    });
    
    Fancybox.bind("[data-fancybox]", {
        // Your custom options
    });
    
    // OwlCarousel initialization
    $(".owl-carousel-full").owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        responsive: {
            0: { items: 1 },
            500: { items: 2 },
            700: { items: 3 },
            1000: { items: 4 },
            1400: { items: 4 }
        }
    });

    // Quantity button handlers
    $('.quantity button').on('click', function () {
        let btn = $(this);
        let inputQty = btn.parent().find('input.qty');
        let prevValue = parseInt(inputQty.val(), 10);
        let newValue = 1;
        
        if (btn.hasClass('btn-plus')) {
            newValue = prevValue + 1;
        } else {
            if (prevValue > 1) {
                newValue = prevValue - 1;
            }
        }
        
        inputQty.val(newValue);
    });
    
    // Handle the load comments button click
    $('#load-comments').on('click', function() {
        $('#tab-reviews').css('display', 'block');
        $('#comments-container').toggle();
        
        if ($('#commentlist li').length === 0) {
            $('#review_form_wrapper').show();
        }
    });
    
    // Fancybox initialization
    if (typeof Fancybox !== 'undefined') {
        Fancybox.bind("[data-fancybox]", {});
    }
    
    // Make the loadMoreReviewsInline function globally available
    window.loadMoreReviewsInline = function(button, productId, page) {
        // Disable button and show loading state
        $(button).text('Loading...').prop('disabled', true);
        
        // AJAX URL
        let ajaxUrl = '/wp-admin/admin-ajax.php';
        if (typeof my_e_shop_params !== 'undefined' && my_e_shop_params.ajax_url) {
            ajaxUrl = my_e_shop_params.ajax_url;
        }
        
        // Make AJAX request
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                'action': 'load_product_reviews',
                'product_id': productId,
                'page': page,
                'per_page': 5
            },
            success: function(response) {
                // Parse response
                const tempDiv = $('<div>').html(response);
                
                // Get new comments and append them
                const newComments = tempDiv.find('.commentlist li');
                const commentList = $('.commentlist');
                
                if (commentList.length) {
                    commentList.append(newComments);
                }
                
                // Remove current button
                $(button).remove();
                
                // Add new button if present in response
                const newButton = tempDiv.find('#load-more-reviews');
                if (newButton.length) {
                    $('#reviews-container').append(newButton);
                }
            },
            error: function() {
                $(button).text('Error loading reviews. Try again').prop('disabled', false);
            }
        });
    };
    
    // Variables to track loading state for reviews
    let isLoading = false;
    let currentPage = 1;
    let hasMoreReviews = true;
    const reviewsPerPage = 5;
    
    // Function to load reviews with pagination
    function loadReviews(page = 1, append = false) {
        if (isLoading) return;
        
        const reviewsContainer = $('#reviews-container');
        const productId = $('input[name="product_id"]').val();
        
        if (!reviewsContainer.length || !productId) return;
        
        isLoading = true;
        
        // Show loading indicator
        if (page === 1) {
            reviewsContainer.html('<p>Loading reviews...</p>');
        } else {
            $('#load-more-reviews').text('Loading...').prop('disabled', true);
        }
        
        // AJAX URL
        let ajaxUrl = '/wp-admin/admin-ajax.php';
        if (typeof my_e_shop_params !== 'undefined' && my_e_shop_params.ajax_url) {
            ajaxUrl = my_e_shop_params.ajax_url;
        }
        
        // Make AJAX request
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                'action': 'load_product_reviews',
                'product_id': productId,
                'page': page,
                'per_page': reviewsPerPage
            },
            success: function(response) {
                console.log("AJAX success, got response");
                
                // Remove loading button if it exists
                $('#load-more-reviews').remove();
                
                if (append && page > 1) {
                    // Extract comments from response
                    const tempDiv = $('<div>').html(response);
                    const newComments = tempDiv.find('.commentlist li');
                    const pagination = tempDiv.find('.reviews-pagination');
                    
                    // Append new comments to existing list
                    reviewsContainer.find('.commentlist').append(newComments);
                    
                    // Update pagination data
                    reviewsContainer.find('.reviews-pagination').remove();
                    reviewsContainer.append(pagination);
                } else {
                    // First load - replace all content
                    reviewsContainer.html(response);
                }
                
                reviewsContainer.attr('data-loaded', 'true');
                isLoading = false;
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", error);
                
                if (page > 1) {
                    $('#load-more-reviews').text('Error loading reviews. Try again').prop('disabled', false);
                } else {
                    reviewsContainer.html('<p>Error loading reviews: ' + error + '</p>');
                }
                isLoading = false;
            }
        });
    }
    
    // Load initial reviews
    setTimeout(function() {
        loadReviews(1, false);
    }, 1000);
    
    // Global click event handler for the button
    $(document).on('click', '#load-more-reviews', function(e) {
        console.log("Load More Reviews button clicked via jQuery handler");
        
        // If there's an onclick attribute, don't execute this handler
        if ($(this).attr('onclick')) {
            console.log("Button has onclick, letting that handle it");
            return;
        }
        
        e.preventDefault();
        
        const reviewsContainer = $('#reviews-container');
        const pagination = reviewsContainer.find('.reviews-pagination');
        const nextPage = parseInt(pagination.data('page') || 1) + 1;
        
        loadReviews(nextPage, true);
    });
    
    // Test AJAX Loading button
    $('#test-ajax-btn').on('click', function() {
        currentPage = 1;
        hasMoreReviews = true;
        loadReviews(1, false);
    });
    
    // Direct Load Content button
    $('#direct-load-btn').on('click', function() {
        $('#reviews-container').html('<div style="background: #e9f7e9; padding: 15px; border: 1px solid #ccc;"><h3>Direct Content</h3><p>This content was loaded directly without AJAX.</p></div>');
    });
});

// Video Background JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('background-video');
    
    function checkVideo() {
        if (video && (video.paused || video.ended)) {
            video.play().catch(e => {
                console.log("Video autoplay prevented by browser:", e);
            });
        }
    }
    
    if (video) {
        video.play().catch(e => {
            console.log("Initial video autoplay prevented by browser:", e);
            
            const videoContainer = document.querySelector('.video-container');
            if (videoContainer) {
                const playButton = document.createElement('button');
                playButton.className = 'video-play-button';
                playButton.innerHTML = '<i class="fas fa-play"></i>';
                playButton.setAttribute('aria-label', 'Play Background Video');
                
                playButton.addEventListener('click', function() {
                    video.play();
                    this.remove();
                });
                
                videoContainer.appendChild(playButton);
            }
        });
        
        setInterval(checkVideo, 1000);
    }
    
    document.body.classList.add('has-video-background');
});

document.addEventListener('DOMContentLoaded', function() {
    // Handle submenu toggles
    const submenuToggles = document.querySelectorAll('.submenu-toggle');
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            // Toggle aria-expanded attribute
            const expanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !expanded);
            
            // Toggle the submenu visibility
            const submenu = this.nextElementSibling;
            submenu.classList.toggle('active');
        });
    });
    
    // Handle child menu toggles
    const childToggles = document.querySelectorAll('.child-toggle');
    childToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            // Toggle aria-expanded attribute
            const expanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !expanded);
            
            // Toggle the child menu visibility
            const childMenu = this.nextElementSibling;
            childMenu.classList.toggle('active');
        });
    });
    
    // Close offcanvas when a link without children is clicked
    const finalLinks = document.querySelectorAll('.mobile-nav-item:not(.has-submenu) > a, .submenu-item:not(.has-children) > a, .child-item > a');
    finalLinks.forEach(link => {
        link.addEventListener('click', function() {
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasNavbar'));
            if (bsOffcanvas) {
                bsOffcanvas.hide();
            }
        });
    });
});

// $(document).ready(function() {
//     // Initialize collections carousels
//     $('.collections-horizontal-carousel').owlCarousel({
//         loop: true,
//         margin: 5,
//         nav: true,
//         dots: true,
//         autoplay: true,
//         autoplayTimeout: 3000,
//         autoplayHoverPause: true,
//         navText: [
//             '<i class="fas fa-chevron-left"></i>',
//             '<i class="fas fa-chevron-right"></i>'
//         ],
//         responsive: {
//             0: {
//                 items: 2,
//                 margin: 5
//             },
//             600: {
//                 items: 3,
//                 margin: 5
//             },
//             1000: {
//                 items: 5,
//                 margin: 5
//             },
//             1200: {
//                 items: 6,
//                 margin: 5
//             }
//         }
//     });
// });



// Collections Slider with Image Modal - FIXED HOVER
$(document).ready(function() {
    // Отключаем Owl Carousel для слайдеров коллекций
    $('.collections-horizontal-carousel').removeClass('owl-carousel owl-theme');
    
    // Проверяем, существует ли слайдер на странице
    if ($('.collections-slider-section').length > 0) {
        
        // Убираем все возможные дублированные слайдеры, оставляем только первый
        $('.collections-slider-section').not(':first').remove();
        
        // Создаем модальное окно (добавляем в DOM только один раз)
        if (!$('.collections-modal-overlay').length) {
            $('body').append(`
                <div class="collections-modal-overlay">
                    <div class="collections-modal-container">
                        <button class="collections-modal-close">
                            <i class="fas fa-times"></i>
                        </button>
                        <img class="collections-modal-image" src="" alt="">
                        <div class="collections-modal-info">
                            <h3 class="collections-modal-title"></h3>
                            <p class="collections-modal-description"></p>
                        </div>
                    </div>
                </div>
            `);
        }
        
        // Переменные для модального окна
        const $modalOverlay = $('.collections-modal-overlay');
        const $modalImage = $('.collections-modal-image');
        const $modalTitle = $('.collections-modal-title');
        const $modalDescription = $('.collections-modal-description');
        const $sliderTrack = $('.collections-smooth-track');
        const $carousel = $('.collections-horizontal-carousel');
        
        // Данные для изображений
        const imageData = {
            'Living Room': {
                title: 'Living Room Collection',
                description: 'Создайте уютную атмосферу с нашей коллекцией мебели для гостиной'
            },
            'Bedroom': {
                title: 'Bedroom Collection',
                description: 'Превратите вашу спальню в место отдыха и релаксации'
            },
            'Kitchen': {
                title: 'Kitchen Collection',
                description: 'Современные решения для функциональной кухни'
            },
            'Office': {
                title: 'Office Collection',
                description: 'Продуктивная работа в стильном офисном пространстве'
            },
            'Storage': {
                title: 'Storage Solutions',
                description: 'Умные решения для хранения и организации пространства'
            },
            'Decor': {
                title: 'Decor Collection',
                description: 'Добавьте индивидуальности с помощью декоративных элементов'
            }
        };
        
        // ИСПРАВЛЕНО: Обработка hover для паузы анимации
        $('.collections-slider-section:first .collections-square-card').on('mouseenter', function() {
            if (!$modalOverlay.hasClass('active')) {
                // Останавливаем анимацию
                $sliderTrack.css('animation-play-state', 'paused');
                // Затемняем другие карточки
                $(this).siblings('.collections-square-card').css('opacity', '0.7');
            }
        });

        $('.collections-slider-section:first .collections-square-card').on('mouseleave', function() {
            if (!$modalOverlay.hasClass('active')) {
                // Возобновляем анимацию
                $sliderTrack.css('animation-play-state', 'running');
                // Восстанавливаем прозрачность
                $(this).siblings('.collections-square-card').css('opacity', '1');
            }
        });
        
        // Дополнительный обработчик hover для контейнера
        $carousel.on('mouseenter', function() {
            if (!$modalOverlay.hasClass('active')) {
                $sliderTrack.css('animation-play-state', 'paused');
            }
        });
        
        $carousel.on('mouseleave', function() {
            if (!$modalOverlay.hasClass('active')) {
                $sliderTrack.css('animation-play-state', 'running');
                $('.collections-square-card').css('opacity', '1');
            }
        });
        
        // Обработчик клика на карточку
        $('.collections-slider-section:first .collections-square-card').on('click', function(e) {
            e.stopPropagation();
            
            const $card = $(this);
            const $img = $card.find('img');
            const imgSrc = $img.attr('src');
            const imgAlt = $img.attr('alt');
            
            // Останавливаем анимацию слайдера
            $sliderTrack.css('animation-play-state', 'paused');
            
            // Добавляем класс для анимации карточки
            $card.addClass('modal-opening');
            
            // Заполняем модальное окно данными
            $modalImage.attr('src', imgSrc).attr('alt', imgAlt);
            
            // Устанавливаем информацию об изображении
            const data = imageData[imgAlt] || {
                title: imgAlt,
                description: 'Откройте для себя нашу эксклюзивную коллекцию'
            };
            
            $modalTitle.text(data.title);
            $modalDescription.text(data.description);
            
            // Показываем модальное окно
            setTimeout(() => {
                $modalOverlay.addClass('active');
                $('body').addClass('modal-open').css('overflow', 'hidden');
            }, 100);
            
            // Убираем класс анимации через некоторое время
            setTimeout(() => {
                $card.removeClass('modal-opening');
            }, 400);
        });
        
        // Функция закрытия модального окна
        function closeModal() {
            $modalOverlay.removeClass('active');
            $('body').removeClass('modal-open').css('overflow', '');
            
            // Возобновляем анимацию слайдера
            setTimeout(() => {
                $sliderTrack.css('animation-play-state', 'running');
                $('.collections-square-card').css('opacity', '1');
            }, 400);
        }
        
        // Обработчики закрытия модального окна
        $('.collections-modal-close').on('click', closeModal);
        
        // Клик по изображению в модальном окне закрывает его
        $modalImage.on('click', closeModal);
        
        // Клик по оверлею закрывает модальное окно
        $modalOverlay.on('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
        
        // Закрытие по клавише Escape
        $(document).on('keydown', function(e) {
            if (e.keyCode === 27 && $modalOverlay.hasClass('active')) {
                closeModal();
            }
        });
        
        // Предотвращаем hover эффекты когда модальное окно открыто
        $modalOverlay.on('transitionend', function() {
            if ($modalOverlay.hasClass('active')) {
                $('.collections-square-card').css('opacity', '1');
                $sliderTrack.css('animation-play-state', 'paused');
            }
        });
    }
});

// ==================== МОБИЛЬНОЕ МЕНЮ ====================
// Мобильное меню - поддержка Bootstrap и кастомная реализация
$(document).ready(function() {
    // ========== МОБИЛЬНОЕ МЕНЮ ==========
    // Получаем элементы
    const $toggleButton = $('.navbar-toggler');
    const $offcanvas = $('#offcanvasNavbar');
    const $closeButton = $('.btn-close');
    
    // Проверяем есть ли Bootstrap 5
    if (typeof bootstrap === 'undefined') {
        console.log('Bootstrap не найден, используем кастомную реализацию');
        
        // Кастомная реализация для мобильного меню
        $toggleButton.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if ($offcanvas.hasClass('show')) {
                closeCustomMenu();
            } else {
                openCustomMenu();
            }
        });
        
        $closeButton.on('click', function(e) {
            e.preventDefault();
            closeCustomMenu();
        });
        
        // Закрытие при клике на ссылку
        $('.mobile-nav-item a').on('click', function() {
            setTimeout(closeCustomMenu, 150);
        });
        
        function openCustomMenu() {
            $offcanvas.addClass('show');
            $offcanvas.css('visibility', 'visible');
            
            // Создаем backdrop
            $('<div class="offcanvas-backdrop fade show"></div>')
                .appendTo('body')
                .on('click', closeCustomMenu);
            
            $('body').css('overflow', 'hidden');
        }
        
        function closeCustomMenu() {
            $offcanvas.removeClass('show');
            $('.offcanvas-backdrop').remove();
            $('body').css('overflow', '');
            
            setTimeout(() => {
                if (!$offcanvas.hasClass('show')) {
                    $offcanvas.css('visibility', 'hidden');
                }
            }, 300);
        }
        
        // Закрытие по Escape
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $offcanvas.hasClass('show')) {
                closeCustomMenu();
            }
        });
    } else {
        console.log('Bootstrap найден, используем встроенный offcanvas');
    }
    
    // ========== SHOP DROPDOWN МЕНЮ ==========
    // Shop Dropdown функциональность - ТОЛЬКО ПО КЛИКУ
    $('.main-dark-bar .dropdown-toggle').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $dropdown = $(this).siblings('.dropdown-menu');
        const isOpen = $dropdown.hasClass('show');
        
        // Закрываем все другие dropdown
        $('.main-dark-bar .dropdown-menu').removeClass('show');
        $('.main-dark-bar .dropdown-toggle').attr('aria-expanded', 'false');
        
        // Переключаем текущий dropdown
        if (!isOpen) {
            $dropdown.addClass('show');
            $(this).attr('aria-expanded', 'true');
        }
    });
    
    // Закрытие dropdown при клике вне его
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.nav-item.dropdown').length) {
            $('.main-dark-bar .dropdown-menu').removeClass('show');
            $('.main-dark-bar .dropdown-toggle').attr('aria-expanded', 'false');
        }
    });
    
    // Закрытие dropdown по клавише Escape
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            $('.main-dark-bar .dropdown-menu').removeClass('show');
            $('.main-dark-bar .dropdown-toggle').attr('aria-expanded', 'false');
        }
    });
    
    // Дебаг информация
    console.log('Toggle button найден:', $toggleButton.length > 0);
    console.log('Offcanvas найден:', $offcanvas.length > 0);
});

