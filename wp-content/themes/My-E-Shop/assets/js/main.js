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
        // Disable thumbnails
        Thumbs: false,
        // Ignore clicks on carousel controls
        on: {
            shouldClose: (fancybox, slide) => {
                return true;
            }
        }
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
        Fancybox.bind("[data-fancybox]", {
            Thumbs: false,
            on: {
                shouldClose: (fancybox, slide) => {
                    return true;
                }
            }
        });
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
        // If there's an onclick attribute, don't execute this handler
        if ($(this).attr('onclick')) {
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
            video.play().catch(e => {});
        }
    }
    
    if (video) {
        video.play().catch(e => {
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
    // Disable Owl Carousel for collection sliders
    $('.collections-horizontal-carousel').removeClass('owl-carousel owl-theme');
    
    // Check whether the slider exists on the page
    if ($('.collections-slider-section').length > 0) {
        
        // Remove all possible duplicated sliders, keep only the first
        $('.collections-slider-section').not(':first').remove();
        
        // Create the modal window (add to the DOM only once)
        if (!$('.collections-modal-overlay').length) {
            $('body').append(`
                <div class="collections-modal-overlay">
                    <div class="collections-modal-container">
                        <button class="collections-modal-close">
                            <i class="fas fa-times"></i>
                        </button>
                        <img class="collections-modal-image" src="" alt="">
                    </div>
                </div>
            `);
        }
        
        // Variables for the modal window
        const $modalOverlay = $('.collections-modal-overlay');
        const $modalImage = $('.collections-modal-image');
        const $modalTitle = $('.collections-modal-title');
        const $sliderTrack = $('.collections-smooth-track');
        const $carousel = $('.collections-horizontal-carousel');
        
        // Data for the images
        const imageData = {
            'Living Room': {
                title: 'Living Room Collection',
                description: 'Create a cozy atmosphere with our living room furniture collection'
            },
            'Bedroom': {
                title: 'Bedroom Collection',
                description: 'Turn your bedroom into a place of rest and relaxation'
            },
            'Kitchen': {
                title: 'Kitchen Collection',
                description: 'Modern solutions for a functional kitchen'
            },
            'Office': {
                title: 'Office Collection',
                description: 'Productive work in a stylish office space'
            },
            'Storage': {
                title: 'Storage Solutions',
                description: 'Smart solutions for storage and space organization'
            },
            'Decor': {
                title: 'Decor Collection',
                description: 'Add personality with decorative elements'
            }
        };
        
        // FIXED: Handle hover to pause the animation
        $('.collections-slider-section:first .collections-square-card').on('mouseenter', function() {
            if (!$modalOverlay.hasClass('active')) {
                // Stop the animation
                $sliderTrack.css('animation-play-state', 'paused');
                // Dim the other cards
                $(this).siblings('.collections-square-card').css('opacity', '0.7');
            }
        });

        $('.collections-slider-section:first .collections-square-card').on('mouseleave', function() {
            if (!$modalOverlay.hasClass('active')) {
                // Resume the animation
                $sliderTrack.css('animation-play-state', 'running');
                // Restore opacity
                $(this).siblings('.collections-square-card').css('opacity', '1');
            }
        });
        
        // Additional hover handler for the container
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
        
        // Click handler for the card
        $('.collections-slider-section:first .collections-square-card').on('click', function(e) {
            e.stopPropagation();
            
            const $card = $(this);
            const $img = $card.find('img');
            const imgSrc = $img.attr('src');
            const imgAlt = $img.attr('alt');
            
            // Stop the slider animation
            $sliderTrack.css('animation-play-state', 'paused');
            
            // Add class for the card animation
            $card.addClass('modal-opening');
            
            // Fill the modal window with data
            $modalImage.attr('src', imgSrc).attr('alt', imgAlt);
            
            // Set the image information
            const data = imageData[imgAlt] || {
                title: imgAlt,
                description: 'Discover our exclusive collection'
            };
            
            $modalTitle.text(data.title);
            
            // Show the modal window
            setTimeout(() => {
                $modalOverlay.addClass('active');
                $('body').addClass('modal-open').css('overflow', 'hidden');
            }, 100);
            
            // Remove the animation class after a while
            setTimeout(() => {
                $card.removeClass('modal-opening');
            }, 400);
        });
        
        // Function to close the modal window
        function closeModal() {
            $modalOverlay.removeClass('active');
            $('body').removeClass('modal-open').css('overflow', '');
            
            // Resume the slider animation
            setTimeout(() => {
                $sliderTrack.css('animation-play-state', 'running');
                $('.collections-square-card').css('opacity', '1');
            }, 400);
        }
        
        // Handlers to close the modal window
        $('.collections-modal-close').on('click', closeModal);
        
        // Clicking the image in the modal closes it
        $modalImage.on('click', closeModal);
        
        // Clicking the overlay closes the modal window
        $modalOverlay.on('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
        
        // Close with the Escape key
        $(document).on('keydown', function(e) {
            if (e.keyCode === 27 && $modalOverlay.hasClass('active')) {
                closeModal();
            }
        });
        
        // Prevent hover effects when the modal window is open
        $modalOverlay.on('transitionend', function() {
            if ($modalOverlay.hasClass('active')) {
                $('.collections-square-card').css('opacity', '1');
                $sliderTrack.css('animation-play-state', 'paused');
            }
        });
    }
});

// ==================== MOBILE MENU ====================
// Mobile menu - Bootstrap support and custom implementation
$(document).ready(function() {
    // ========== MOBILE MENU ==========
    // Get the elements
    const $toggleButton = $('.navbar-toggler');
    const $offcanvas = $('#offcanvasNavbar');
    const $closeButton = $('.btn-close');
    
    // Check whether Bootstrap 5 is present
    if (typeof bootstrap === 'undefined') {
        // Custom implementation for the mobile menu
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
        
        // Close on link click
        $('.mobile-nav-item a').on('click', function() {
            setTimeout(closeCustomMenu, 150);
        });
        
        function openCustomMenu() {
            $offcanvas.addClass('show');
            $offcanvas.css('visibility', 'visible');
            
            // Create the backdrop
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
        
        // Close on Escape
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $offcanvas.hasClass('show')) {
                closeCustomMenu();
            }
        });
    }
    
    // ========== SHOP DROPDOWN MENU ==========
    // For mobile - click
    $('.custom-dropdown .nav-link').on('click', function(e) {
        if ($(window).width() >= 992) {
            return true; // On desktop allow navigation
        }
        
        e.preventDefault();
        const $dropdown = $(this).siblings('.shop-dropdown');
        const isOpen = $dropdown.hasClass('show');
        
        $('.shop-dropdown').removeClass('show');
        
        if (!isOpen) {
            $dropdown.addClass('show');
        }
    });
    
    // Close on click outside the menu
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.custom-dropdown').length) {
            $('.shop-dropdown').removeClass('show');
        }
    });
});

(function() {
            'use strict';
            
            function initTextAnimation() {
                const textBlock = document.querySelector('.animated-text-section #textBlock');
                const textContent = document.querySelector('.animated-text-section #textContent');
                
                if (!textBlock || !textContent) {
                    return;
                }
                
                // Split the text into words
                function splitTextIntoWords() {
                    const text = textContent.textContent;
                    const words = text.split(' ');
                    textContent.innerHTML = '';
                    
                    words.forEach((word, index) => {
                        const span = document.createElement('span');
                        span.className = 'word';
                        span.textContent = word;
                        span.style.transitionDelay = `${index * 0.1}s`;
                        textContent.appendChild(span);
                        
                        // Add a space after each word (except the last)
                        if (index < words.length - 1) {
                            textContent.appendChild(document.createTextNode(' '));
                        }
                    });
                }
                
                // Check element visibility
                function isElementVisible(element) {
                    const rect = element.getBoundingClientRect();
                    const windowHeight = window.innerHeight;
                    return rect.top < windowHeight * 0.75 && rect.bottom > 0;
                }
                
                // Main animation function
                function handleScroll() {
                    if (isElementVisible(textBlock)) {
                        // Show the block
                        textBlock.classList.add('visible');
                        
                        // After a short delay animate the words
                        setTimeout(() => {
                            const words = textContent.querySelectorAll('.word');
                            words.forEach(word => {
                                word.classList.add('animate');
                            });
                        }, 400);
                        
                        // Remove the handler after the first animation
                        window.removeEventListener('scroll', handleScroll);
                    }
                }
                
                // Initialization
                splitTextIntoWords();
                
                // Check immediately on load
                setTimeout(handleScroll, 100);
                
                // Add scroll handler
                window.addEventListener('scroll', handleScroll);
            }
            
            // Initialize when the DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initTextAnimation);
            } else {
                initTextAnimation();
            }
            
            // Additional check via jQuery if available
            if (typeof jQuery !== 'undefined') {
                jQuery(document).ready(function($) {
                    setTimeout(initTextAnimation, 500);
                });
            }
        })();