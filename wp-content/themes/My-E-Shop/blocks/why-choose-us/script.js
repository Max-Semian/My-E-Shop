/**
 * Why Choose Us Block Frontend Script
 */
jQuery(document).ready(function($) {
    
    // Инициализация аккордеона
    function initWhyChooseUsAccordion() {
        $('.why-choose-us-block').each(function() {
            const $block = $(this);
            const $headers = $block.find('.accordion-header');
            
            // Функция для закрытия всех элементов аккордеона
            function closeAllItems() {
                $headers.removeClass('active').attr('aria-expanded', 'false');
                $block.find('.accordion-content').removeClass('active').attr('aria-hidden', 'true');
            }
            
            // Функция для открытия элемента аккордеона
            function openItem($header) {
                const targetId = $header.data('toggle');
                const $content = $('#' + targetId);
                
                $header.addClass('active').attr('aria-expanded', 'true');
                $content.addClass('active').attr('aria-hidden', 'false');
                
                // Плавная прокрутка к активному элементу
                setTimeout(function() {
                    $('html, body').animate({
                        scrollTop: $header.offset().top - 100
                    }, 300);
                }, 100);
            }
            
            // Функция для переключения элемента аккордеона
            function toggleItem($header) {
                const isActive = $header.hasClass('active');
                
                // Закрываем все элементы
                closeAllItems();
                
                // Если элемент не был активным, открываем его
                if (!isActive) {
                    openItem($header);
                }
            }
            
            // Обработчик клика по заголовку
            $headers.on('click', function(e) {
                e.preventDefault();
                toggleItem($(this));
            });
            
            // Обработчик нажатия клавиш для доступности
            $headers.on('keydown', function(e) {
                const $current = $(this);
                const $allHeaders = $block.find('.accordion-header');
                const currentIndex = $allHeaders.index($current);
                
                switch(e.key) {
                    case 'Enter':
                    case ' ':
                        e.preventDefault();
                        toggleItem($current);
                        break;
                    case 'ArrowDown':
                        e.preventDefault();
                        const nextIndex = (currentIndex + 1) % $allHeaders.length;
                        $allHeaders.eq(nextIndex).focus();
                        break;
                    case 'ArrowUp':
                        e.preventDefault();
                        const prevIndex = (currentIndex - 1 + $allHeaders.length) % $allHeaders.length;
                        $allHeaders.eq(prevIndex).focus();
                        break;
                    case 'Home':
                        e.preventDefault();
                        $allHeaders.first().focus();
                        break;
                    case 'End':
                        e.preventDefault();
                        $allHeaders.last().focus();
                        break;
                }
            });
            
            // Функция для автоматической настройки высоты контента
            function adjustContentHeight() {
                $block.find('.accordion-content.active').each(function() {
                    const $content = $(this);
                    const $answer = $content.find('.accordion-answer');
                    const actualHeight = $answer.outerHeight();
                    
                    $content.css('max-height', (actualHeight + 50) + 'px');
                });
            }
            
            // Настройка высоты при изменении размера окна
            $(window).on('resize.whyChooseUs', function() {
                adjustContentHeight();
            });
            
            // Первоначальная настройка высоты
            adjustContentHeight();
            
            // Наблюдатель за изменениями в DOM для динамического контента
            if ('MutationObserver' in window) {
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'childList' || mutation.type === 'characterData') {
                            adjustContentHeight();
                        }
                    });
                });
                
                observer.observe($block[0], {
                    childList: true,
                    subtree: true,
                    characterData: true
                });
            }
            
            // Функция для обработки touch-событий на мобильных устройствах
            function setupTouchSupport() {
                let touchStartY = 0;
                
                $headers.on('touchstart', function(e) {
                    touchStartY = e.originalEvent.touches[0].clientY;
                });
                
                $headers.on('touchend', function(e) {
                    const touchEndY = e.originalEvent.changedTouches[0].clientY;
                    const touchDiff = Math.abs(touchStartY - touchEndY);
                    
                    // Если это не свайп, обрабатываем как клик
                    if (touchDiff < 10) {
                        toggleItem($(this));
                    }
                });
            }
            
            setupTouchSupport();
        });
    }
    
    // Инициализация при загрузке DOM
    initWhyChooseUsAccordion();
    
    // Переинициализация для динамически загруженного контента
    $(document).on('DOMNodeInserted', '.why-choose-us-block', function() {
        setTimeout(initWhyChooseUsAccordion, 100);
    });
    
    // Очистка при выгрузке страницы
    $(window).on('beforeunload', function() {
        $(window).off('resize.whyChooseUs');
    });
    
    // Функция для анимации появления блока при скролле
    function setupScrollAnimation() {
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const $block = $(entry.target);
                        $block.addClass('animate-in');
                        
                        // Анимация элементов аккордеона с задержкой
                        $block.find('.accordion-item').each(function(index) {
                            const $item = $(this);
                            setTimeout(function() {
                                $item.addClass('animate-item');
                            }, index * 100);
                        });
                        
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            $('.why-choose-us-block').each(function() {
                observer.observe(this);
            });
        }
    }
    
    setupScrollAnimation();
    
    // CSS для анимации (добавляется динамически)
    const animationCSS = `
        <style>
        .why-choose-us-block:not(.animate-in) {
            opacity: 0.3;
            transform: translateY(30px);
        }
        
        .why-choose-us-block.animate-in {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        
        .why-choose-us-block .accordion-item:not(.animate-item) {
            opacity: 0.5;
            transform: translateX(-20px);
        }
        
        .why-choose-us-block .accordion-item.animate-item {
            opacity: 1;
            transform: translateX(0);
            transition: opacity 0.4s ease, transform 0.4s ease;
        }
        </style>
    `;
    
    $('head').append(animationCSS);
});
