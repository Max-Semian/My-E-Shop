// Product Cards Frontend JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Находим все блоки карточек товаров
    const productCardsBlocks = document.querySelectorAll('.product-cards-block');
    
    // Обработчик для кнопок "В корзину"
    function handleAddToCart(event) {
        event.preventDefault();
        event.stopPropagation();
        
        const button = event.target;
        const productId = button.getAttribute('data-product-id');
        
        if (!productId) {
            return;
        }
        
        // Блокируем кнопку во время запроса
        const originalText = button.textContent;
        button.disabled = true;
        button.textContent = 'Добавление...';
        button.style.opacity = '0.6';
        
        // Данные для AJAX запроса
        const formData = new FormData();
        formData.append('action', 'woocommerce_add_to_cart');
        formData.append('product_id', productId);
        formData.append('quantity', 1);
        
        // Отправляем AJAX запрос
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
            
            // Успешное добавление
            button.textContent = 'Добавлено!';
            button.style.background = '#27ae60';
            
            // Обновляем счетчик корзины если есть
            if (data.fragments) {
                Object.keys(data.fragments).forEach(function(key) {
                    const element = document.querySelector(key);
                    if (element) {
                        element.innerHTML = data.fragments[key];
                    }
                });
            }
            
            // Показываем уведомление
            showNotification('Товар добавлен в корзину!', 'success');
            
            // Возвращаем кнопку в исходное состояние через 2 секунды
            setTimeout(() => {
                button.disabled = false;
                button.textContent = originalText;
                button.style.opacity = '1';
                button.style.background = '';
            }, 2000);
        })
        .catch(error => {
            console.error('Ошибка при добавлении в корзину:', error);
            
            // Возвращаем кнопку в исходное состояние
            button.disabled = false;
            button.textContent = originalText;
            button.style.opacity = '1';
            
            showNotification('Ошибка при добавлении товара', 'error');
        });
    }
    
    // Функция для показа уведомлений
    function showNotification(message, type = 'info') {
        // Создаем элемент уведомления
        const notification = document.createElement('div');
        notification.className = `product-notification ${type}`;
        notification.textContent = message;
        
        // Стили уведомления
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
        
        // Анимация появления
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Автоматическое скрытие через 3 секунды
        setTimeout(() => {
            notification.style.transform = 'translateX(400px)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
    
    // Инициализация для каждого блока
    productCardsBlocks.forEach(function(block) {
        // Добавляем обработчики для кнопок "В корзину"
        const addToCartButtons = block.querySelectorAll('.add-to-cart-btn');
        addToCartButtons.forEach(function(button) {
            button.addEventListener('click', handleAddToCart);
        });
        
        // Lazy loading для изображений
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
        
        // Анимация появления карточек
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
                // Устанавливаем начальные стили для анимации
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
                
                cardObserver.observe(card);
            });
        } else {
            // Fallback для старых браузеров
            cards.forEach(function(card) {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            });
        }
    });
    
    // Обработка ошибок загрузки изображений
    document.addEventListener('error', function(event) {
        if (event.target.tagName === 'IMG' && event.target.closest('.product-image')) {
            event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2Y4ZjlmYSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5ObyBJbWFnZTwvdGV4dD48L3N2Zz4=';
            event.target.alt = 'No Image';
        }
    }, true);
});
