/**
 * Gallery Slider Block Frontend Script
 */
jQuery(document).ready(function($) {
    
    // Инициализация галереи слайдера
    function initGallerySlider() {
        $('.gallery-slider-block').each(function() {
            const $block = $(this);
            const $track = $block.find('.collections-smooth-track');
            const $images = $block.find('.collections-square-image');
            
            // Функция для плавной загрузки изображений
            function loadImages() {
                $images.each(function(index) {
                    const $img = $(this);
                    const img = new Image();
                    
                    img.onload = function() {
                        setTimeout(function() {
                            $img.addClass('loaded');
                        }, index * 100); // Задержка для каскадного эффекта
                    };
                    
                    img.src = $img.attr('src');
                });
            }
            
            // Функция для корректировки анимации при изменении размера окна
            function adjustAnimation() {
                const cardWidth = parseInt($block.css('--gallery-card-width')) || 280;
                const gap = 15;
                const totalCards = $images.length;
                const trackWidth = totalCards * (cardWidth + gap);
                
                $track.css('--track-width', trackWidth + 'px');
            }
            
            // Функция для паузы/возобновления анимации
            function setupHoverControls() {
                $block.find('.collections-horizontal-carousel').on('mouseenter', function() {
                    $track.css('animation-play-state', 'paused');
                }).on('mouseleave', function() {
                    $track.css('animation-play-state', 'running');
                });
            }
            
            // Функция для обработки touch событий на мобильных устройствах
            function setupTouchControls() {
                let startX = 0;
                let isDragging = false;
                
                $track.on('touchstart', function(e) {
                    startX = e.originalEvent.touches[0].pageX;
                    isDragging = true;
                    $(this).css('animation-play-state', 'paused');
                });
                
                $track.on('touchmove', function(e) {
                    if (!isDragging) return;
                    e.preventDefault();
                });
                
                $track.on('touchend', function(e) {
                    if (!isDragging) return;
                    isDragging = false;
                    $(this).css('animation-play-state', 'running');
                });
            }
            
            // Функция для оптимизации производительности
            function setupPerformanceOptimization() {
                // Intersection Observer для видимости блока
                if ('IntersectionObserver' in window) {
                    const observer = new IntersectionObserver(function(entries) {
                        entries.forEach(function(entry) {
                            if (entry.isIntersecting) {
                                $track.css('animation-play-state', 'running');
                            } else {
                                $track.css('animation-play-state', 'paused');
                            }
                        });
                    }, {
                        threshold: 0.1
                    });
                    
                    observer.observe($block[0]);
                }
                
                // Предзагрузка изображений
                $images.each(function() {
                    const $img = $(this);
                    const src = $img.attr('src');
                    if (src) {
                        const img = new Image();
                        img.src = src;
                    }
                });
            }
            
            // Инициализация всех функций
            loadImages();
            adjustAnimation();
            setupHoverControls();
            setupTouchControls();
            setupPerformanceOptimization();
            
            // Переинициализация при изменении размера окна
            $(window).on('resize.gallerySlider', function() {
                adjustAnimation();
            });
        });
    }
    
    // Инициализация при загрузке DOM
    initGallerySlider();
    
    // Переинициализация для динамически загруженного контента
    $(document).on('DOMNodeInserted', '.gallery-slider-block', function() {
        setTimeout(initGallerySlider, 100);
    });
    
    // Очистка при выгрузке страницы
    $(window).on('beforeunload', function() {
        $(window).off('resize.gallerySlider');
    });
    
    // CSS переменные для отзывчивого дизайна
    function updateResponsiveCSS() {
        const viewportWidth = $(window).width();
        const $blocks = $('.gallery-slider-block');
        
        $blocks.each(function() {
            const $block = $(this);
            const originalCardWidth = parseInt($block.css('--gallery-card-width')) || 280;
            const originalCardHeight = parseInt($block.css('--gallery-card-height')) || 492;
            
            let scaleFactor = 1;
            
            if (viewportWidth <= 480) {
                scaleFactor = 0.6;
            } else if (viewportWidth <= 768) {
                scaleFactor = 0.8;
            }
            
            const newCardWidth = originalCardWidth * scaleFactor;
            const newCardHeight = originalCardHeight * scaleFactor;
            
            $block.find('.collections-square-card').css({
                'width': newCardWidth + 'px',
                'height': newCardHeight + 'px'
            });
        });
    }
    
    // Обновление при изменении размера окна
    $(window).on('resize', function() {
        clearTimeout(window.gallerySliderResizeTimeout);
        window.gallerySliderResizeTimeout = setTimeout(updateResponsiveCSS, 250);
    });
    
    // Первоначальное обновление
    updateResponsiveCSS();
});
