// Product Cards Frontend JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Find all product cards blocks
    const productCardsBlocks = document.querySelectorAll('.product-cards-block');

    // Handler for "Add to cart" buttons
    function handleAddToCart(event) {
        event.preventDefault();
        event.stopPropagation();
        
        const button = event.target;
        const productId = button.getAttribute('data-product-id');
        
        if (!productId) {
            return;
        }
        
        // Disable the button during the request
        const originalText = button.textContent;
        button.disabled = true;
        button.textContent = 'Adding...';
        button.style.opacity = '0.6';

        // Data for the AJAX request
        const formData = new FormData();
        formData.append('action', 'woocommerce_add_to_cart');
        formData.append('product_id', productId);
        formData.append('quantity', 1);
        
        // Send the AJAX request
        fetch(wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'), {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error && data.product_url) {
                window.location = data.product_url;
                return;
            }
            
            // Successfully added
            button.textContent = 'Added!';
            button.style.background = '#27ae60';

            // Update the cart counter if present
            if (data.fragments) {
                Object.keys(data.fragments).forEach(function(key) {
                    const element = document.querySelector(key);
                    if (element) {
                        element.innerHTML = data.fragments[key];
                    }
                });
            }
            
            // Show a notification
            showNotification('Product added to cart!', 'success');

            // Restore the button to its original state after 2 seconds
            setTimeout(() => {
                button.disabled = false;
                button.textContent = originalText;
                button.style.opacity = '1';
                button.style.background = '';
            }, 2000);
        })
        .catch(error => {
            console.error('Error adding to cart:', error);

            // Restore the button to its original state
            button.disabled = false;
            button.textContent = originalText;
            button.style.opacity = '1';

            showNotification('Error adding product', 'error');
        });
    }
    
    // Function to show notifications
    function showNotification(message, type = 'info') {
        // Create the notification element
        const notification = document.createElement('div');
        notification.className = `product-notification ${type}`;
        notification.textContent = message;

        // Notification styles
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#27ae60' : type === 'error' ? '#e74c3c' : '#3498db'};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            z-index: 9999;
            font-weight: 600;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        // Appearance animation
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);

        // Automatically hide after 3 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(400px)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
    
    // Initialize each block
    productCardsBlocks.forEach(function(block) {
        // Add handlers for "Add to cart" buttons
        const addToCartButtons = block.querySelectorAll('.add-to-cart-btn');
        addToCartButtons.forEach(function(button) {
            button.addEventListener('click', handleAddToCart);
        });

        // Lazy loading for images
        const images = block.querySelectorAll('.product-image img');
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            images.forEach(function(img) {
                imageObserver.observe(img);
            });
        }
        
        // Card appearance animation
        const cards = block.querySelectorAll('.product-card');
        if ('IntersectionObserver' in window) {
            const cardObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                        cardObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            
            cards.forEach(function(card, index) {
                // Set initial styles for the animation
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
                
                cardObserver.observe(card);
            });
        } else {
            // Fallback for older browsers
            cards.forEach(function(card) {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            });
        }
    });
    
    // Handle image loading errors
    document.addEventListener('error', function(event) {
        if (event.target.tagName === 'IMG' && event.target.closest('.product-image')) {
            event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2Y4ZjlmYSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5ObyBJbWFnZTwvdGV4dD48L3N2Zz4=';
            event.target.alt = 'No Image';
        }
    }, true);
});
