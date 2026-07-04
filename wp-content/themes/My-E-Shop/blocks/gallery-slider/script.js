/**
 * Gallery Slider Block Frontend Script
 */
jQuery(document).ready(function($) {
    
    // Initialize the gallery slider
    function initGallerySlider() {
        $('.gallery-slider-block').each(function() {
            const $block = $(this);
            const $track = $block.find('.collections-smooth-track');
            const $images = $block.find('.collections-square-image');
            
            // Function for smooth image loading
            function loadImages() {
                $images.each(function(index) {
                    const $img = $(this);
                    const img = new Image();
                    
                    img.onload = function() {
                        setTimeout(function() {
                            $img.addClass('loaded');
                        }, index * 100); // Delay for a cascading effect
                    };
                    
                    img.src = $img.attr('src');
                });
            }
            
            // Function to adjust the animation on window resize
            function adjustAnimation() {
                const cardWidth = parseInt($block.css('--gallery-card-width')) || 280;
                const gap = 15;
                const totalCards = $images.length;
                const trackWidth = totalCards * (cardWidth + gap);
                
                $track.css('--track-width', trackWidth + 'px');
            }
            
            // Function to pause/resume the animation
            function setupHoverControls() {
                $block.find('.collections-horizontal-carousel').on('mouseenter', function() {
                    $track.css('animation-play-state', 'paused');
                }).on('mouseleave', function() {
                    $track.css('animation-play-state', 'running');
                });
            }
            
            // Function to handle touch events on mobile devices
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
            
            // Function for performance optimization
            function setupPerformanceOptimization() {
                // Intersection Observer for block visibility
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
                
                // Preload images
                $images.each(function() {
                    const $img = $(this);
                    const src = $img.attr('src');
                    if (src) {
                        const img = new Image();
                        img.src = src;
                    }
                });
            }
            
            // Initialize all functions
            loadImages();
            adjustAnimation();
            setupHoverControls();
            setupTouchControls();
            setupPerformanceOptimization();
            
            // Reinitialize on window resize
            $(window).on('resize.gallerySlider', function() {
                adjustAnimation();
            });
        });
    }
    
    // Initialize on DOM load
    initGallerySlider();

    // Reinitialize for dynamically loaded content
    $(document).on('DOMNodeInserted', '.gallery-slider-block', function() {
        setTimeout(initGallerySlider, 100);
    });
    
    // Cleanup on page unload
    $(window).on('beforeunload', function() {
        $(window).off('resize.gallerySlider');
    });
    
    // CSS variables for responsive design
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
    
    // Update on window resize
    $(window).on('resize', function() {
        clearTimeout(window.gallerySliderResizeTimeout);
        window.gallerySliderResizeTimeout = setTimeout(updateResponsiveCSS, 250);
    });
    
    // Initial update
    updateResponsiveCSS();
});
