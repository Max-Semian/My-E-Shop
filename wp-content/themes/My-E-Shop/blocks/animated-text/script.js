// Animated Text Block Frontend JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Находим все блоки анимированного текста
    const animatedTextBlocks = document.querySelectorAll('.animated-text-section');
    console.log('Found animated text blocks:', animatedTextBlocks.length);
    
    // Функция для очистки HTML тегов и получения чистого текста
    function getCleanText(element) {
        let text = element.getAttribute('data-text');
        if (!text) {
            // Если нет data-text, получаем из innerHTML и очищаем
            text = element.innerHTML;
            // Сохраняем переносы строк, заменяя <br> на \n
            text = text.replace(/<br\s*\/?>/gi, '\n');
            // Убираем все HTML теги кроме содержимого
            text = text.replace(/<[^>]*>/g, '');
            // Декодируем HTML сущности
            text = text.replace(/&nbsp;/g, ' ')
                      .replace(/&amp;/g, '&')
                      .replace(/&lt;/g, '<')
                      .replace(/&gt;/g, '>')
                      .replace(/&quot;/g, '"')
                      .replace(/&#39;/g, "'");
            // Убираем лишние пробелы, но сохраняем переносы строк
            text = text.replace(/[ \t]+/g, ' ').replace(/\n\s+/g, '\n').trim();
            // Сохраняем очищенный текст в data-атрибут
            element.setAttribute('data-text', text);
        }
        return text;
    }
    
    // Функция для анимации печатной машинки
    function typewriterAnimation(element, speed = 50) {
        const cleanText = getCleanText(element);
        
        // Проверяем размер экрана для адаптивности
        const isMobile = window.innerWidth <= 768;
        
        if (isMobile) {
            // На мобильных устройствах показываем весь текст сразу
            element.style.visibility = 'visible';
            element.innerHTML = cleanText.replace(/\n/g, '<br>');
            element.style.opacity = '1';
            return;
        }
        
        // Вставляем весь текст невидимо для измерения
        element.innerHTML = cleanText.replace(/\n/g, '<br>');
        element.style.visibility = 'hidden';
        
        // Даем браузеру обновить Layout
        const fullHeight = element.offsetHeight;
        
        // Показываем элемент и фиксируем высоту
        element.style.visibility = 'visible';
        element.style.height = fullHeight + 'px';
        element.style.borderRight = '3px solid';
        
        // Очищаем содержимое
        element.innerHTML = '';
        
        // Постепенно добавляем символы
        const chars = cleanText.split('');
        let currentIndex = 0;
        let animationActive = true;
        
        // MutationObserver для защиты от внешних изменений
        const observer = new MutationObserver(function(mutations) {
            if (!animationActive) return;
            
            // Если кто-то добавил контент, восстанавливаем наше
            for (let mutation of mutations) {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    // Проверяем не наши ли это ноды
                    let isOurNode = false;
                    for (let i = 0; i < currentIndex; i++) {
                        if (element.childNodes.length > i) {
                            // Если есть наши ноды, значит это нормально
                            isOurNode = true;
                            break;
                        }
                    }
                    
                    // Если это чужие ноды, удаляем их
                    if (!isOurNode && mutation.addedNodes.length > 0) {
                        for (let node of mutation.addedNodes) {
                            if (node.parentNode === element) {
                                element.removeChild(node);
                            }
                        }
                    }
                }
            }
        });
        
        observer.observe(element, {
            childList: true,
            subtree: false
        });
        
        const animate = function() {
            if (currentIndex < chars.length) {
                const char = chars[currentIndex];
                
                if (char === '\n') {
                    element.appendChild(document.createElement('br'));
                } else if (char === ' ') {
                    const space = document.createTextNode('\u00A0');
                    element.appendChild(space);
                } else {
                    const text = document.createTextNode(char);
                    element.appendChild(text);
                }
                
                currentIndex++;
                setTimeout(animate, speed);
            } else {
                // Анимация завершена
                animationActive = false;
                observer.disconnect();
                setTimeout(function() {
                    element.style.borderRight = 'none';
                    element.style.height = 'auto';
                }, 2000);
            }
        };
        
        animate();
    }
    
    // Функция для анимации появления слов
    function fadeInWordsAnimation(element, speed = 100) {
        console.log('fadeInWordsAnimation called');
        const cleanText = getCleanText(element);
        const words = cleanText.split(' ').filter(word => word.trim() !== '');
        
        element.innerHTML = '';
        element.style.visibility = 'visible';
        element.style.opacity = '1';
        element.classList.add('fadeInWords');
        
        words.forEach((word, index) => {
            const span = document.createElement('span');
            span.className = 'word';
            span.textContent = word + ' ';
            span.style.animationDelay = (index * 0.1) + 's';
            span.style.opacity = '0';
            span.style.transform = 'translateY(20px)';
            span.style.display = 'inline-block';
            span.style.animation = 'fadeInWord 0.6s ease forwards';
            element.appendChild(span);
        });
    }
    
    // Функция для плавного появления
    function fadeInAnimation(element) {
        console.log('fadeInAnimation called');
        element.style.visibility = 'visible';
        element.classList.add('fadeIn');
    }
    
    // Функция для скольжения снизу
    function slideUpAnimation(element) {
        console.log('slideUpAnimation called');
        element.style.visibility = 'visible';
        element.classList.add('slideUp');
    }
    
    // Intersection Observer для запуска анимаций при появлении в поле зрения
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                const section = entry.target;
                const textContent = section.querySelector('.text-content');

                // Снимаем opacity:0 с обёртки (.text-block { opacity:0 } → .visible { opacity:1 }).
                // Без этого блок остаётся невидимым, даже когда текст «loaded».
                const textBlock = section.querySelector('.text-block');
                if (textBlock) {
                    textBlock.classList.add('visible');
                }

                if (textContent && !textContent.classList.contains('loaded')) {
                    const animationType = section.getAttribute('data-animation') || 'fadeIn';
                    const speed = parseInt(section.getAttribute('data-speed')) || 50;
                    
                    console.log('Starting animation:', animationType, 'for', textContent);
                    
                    // Запускаем анимацию в зависимости от типа
                    switch (animationType) {
                        case 'typewriter':
                            typewriterAnimation(textContent, speed);
                            break;
                        case 'fadeInWords':
                            fadeInWordsAnimation(textContent, speed);
                            break;
                        case 'slideUp':
                            slideUpAnimation(textContent);
                            break;
                        default:
                            fadeInAnimation(textContent);
                    }
                    
                    textContent.classList.add('loaded');
                    observer.unobserve(section);
                }
            }
        });
    }, { 
        threshold: 0.3,
        rootMargin: '0px 0px -50px 0px'
    });
    
    // Наблюдаем за всеми блоками анимированного текста
    animatedTextBlocks.forEach(function(block) {
        // Инициализируем очистку текста для каждого блока
        const textContent = block.querySelector('.text-content');
        if (textContent) {
            getCleanText(textContent);
        }
        observer.observe(block);
    });
    
    // Функция для повторного запуска анимации (для разработки)
    function restartAnimations() {
        animatedTextBlocks.forEach(function(block) {
            const textContent = block.querySelector('.text-content');
            if (textContent) {
                textContent.classList.remove('loaded', 'typewriter', 'fadeInWords', 'fadeIn', 'slideUp');
                textContent.style.opacity = '0';
                textContent.style.transform = 'translateY(30px)';
                textContent.style.borderRight = '';
                
                // Восстанавливаем оригинальный текст
                const originalText = textContent.getAttribute('data-text');
                if (originalText) {
                    textContent.innerHTML = originalText;
                } else {
                    // Очищаем spans от предыдущей анимации fadeInWords
                    const words = textContent.querySelectorAll('.word');
                    if (words.length > 0) {
                        const originalText = Array.from(words).map(word => word.textContent).join('');
                        textContent.innerHTML = originalText;
                        textContent.setAttribute('data-text', originalText);
                    }
                }
                
                observer.observe(block);
            }
        });
    }
    
    // Добавляем функцию в глобальную область видимости для отладки
    window.restartTextAnimations = restartAnimations;
    
    // Добавляем функцию для отладки текста
    window.debugTextContent = function() {
        animatedTextBlocks.forEach(function(block, index) {
            const textContent = block.querySelector('.text-content');
            if (textContent) {
                console.log(`Block ${index + 1}:`);
                console.log('innerHTML:', textContent.innerHTML);
                console.log('textContent:', textContent.textContent);
                console.log('data-text:', textContent.getAttribute('data-text'));
                console.log('animation:', block.getAttribute('data-animation'));
                console.log('---');
            }
        });
    };
    
    // Обработчик изменения размера окна для адаптивности
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            // Проверяем, нужно ли перезапустить анимации на мобильных устройствах
            if (window.innerWidth <= 768) {
                animatedTextBlocks.forEach(function(block) {
                    const textContent = block.querySelector('.text-content');
                    if (textContent && textContent.classList.contains('typewriter')) {
                        textContent.classList.remove('typewriter');
                        textContent.style.borderRight = 'none';
                        textContent.style.whiteSpace = 'normal';
                        fadeInAnimation(textContent);
                    }
                });
            }
        }, 250);
    });
});
