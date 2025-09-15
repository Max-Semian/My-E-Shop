(function($) {
    'use strict';
    
    // Инициализация функций для блока карточек категорий
    function initCategoryCards() {
        $('.category-cards-block').each(function() {
            var $block = $(this);
            
            // Добавляем эффекты hover
            $block.find('.category-card-item').on('mouseenter', function() {
                $(this).addClass('hovered');
            }).on('mouseleave', function() {
                $(this).removeClass('hovered');
            });
            
            // Lazy loading для изображений
            $block.find('.category-card-image').each(function() {
                var $img = $(this);
                
                if ($img.attr('data-src')) {
                    $img.attr('src', $img.attr('data-src'));
                    $img.removeAttr('data-src');
                }
            });
            
            // Трекинг кликов (если нужна аналитика)
            $block.find('.category-card-link').on('click', function() {
                var categoryTitle = $(this).find('.category-card-title').text();
                var categorySubtitle = $(this).find('.category-card-subtitle').text();
                
                // Можно добавить Google Analytics или другую аналитику
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'category_card_click', {
                        'category_title': categoryTitle,
                        'category_subtitle': categorySubtitle
                    });
                }
            });
        });
    }
    
    // Инициализация при загрузке страницы
    $(document).ready(function() {
        initCategoryCards();
    });
    
    // Реинициализация после AJAX загрузки (если используется)
    $(document).on('category-cards-loaded', function() {
        initCategoryCards();
    });
    
})(jQuery);
