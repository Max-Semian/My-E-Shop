(function($) {
    'use strict';
    
    // Initialize functions for the category cards block
    function initCategoryCards() {
        $('.category-cards-block').each(function() {
            var $block = $(this);
            
            // Add hover effects
            $block.find('.category-card-item').on('mouseenter', function() {
                $(this).addClass('hovered');
            }).on('mouseleave', function() {
                $(this).removeClass('hovered');
            });
            
            // Lazy loading for images
            $block.find('.category-card-image').each(function() {
                var $img = $(this);
                
                if ($img.attr('data-src')) {
                    $img.attr('src', $img.attr('data-src'));
                    $img.removeAttr('data-src');
                }
            });
            
            // Click tracking (if analytics is needed)
            $block.find('.category-card-link').on('click', function() {
                var categoryTitle = $(this).find('.category-card-title').text();
                var categorySubtitle = $(this).find('.category-card-subtitle').text();
                
                // You can add Google Analytics or other analytics here
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'category_card_click', {
                        'category_title': categoryTitle,
                        'category_subtitle': categorySubtitle
                    });
                }
            });
        });
    }
    
    // Initialize on page load
    $(document).ready(function() {
        initCategoryCards();
    });
    
    // Reinitialize after AJAX load (if used)
    $(document).on('category-cards-loaded', function() {
        initCategoryCards();
    });
    
})(jQuery);
