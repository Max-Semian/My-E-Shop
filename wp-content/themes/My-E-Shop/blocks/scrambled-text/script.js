// Scrambled Text Block - Frontend JavaScript
// Адаптация React компонента для vanilla JavaScript с GSAP

(function() {
    'use strict';
    
    function initScrambledBlocks() {
        // Проверяем наличие GSAP
        if (typeof gsap === 'undefined') {
            setTimeout(initScrambledBlocks, 500);
            return;
        }
        
        // Регистрируем ScrambleTextPlugin если он доступен
        if (typeof ScrambleTextPlugin !== 'undefined') {
            gsap.registerPlugin(ScrambleTextPlugin);
        }

        // Находим все блоки scrambled text
        const scrambledBlocks = document.querySelectorAll('.scrambled-text-wrapper');
        
        if (!scrambledBlocks.length) {
            return;
        }

        scrambledBlocks.forEach(function(wrapper, index) {
            initScrambledText(wrapper, index);
        });
    }
    
    // Запускаем инициализацию когда DOM готов
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initScrambledBlocks);
    } else {
        initScrambledBlocks();
    }
})();

function initScrambledText(wrapper, blockIndex) {
    const textBlock = wrapper.querySelector('.text-block');
    const textContent = wrapper.querySelector('.scrambled-text-content, p');
    
    if (!textBlock || !textContent) {
        return;
    }

    // Получаем параметры из data-атрибутов
    const radius = parseFloat(wrapper.getAttribute('data-radius')) || 100;
    const duration = parseFloat(wrapper.getAttribute('data-duration')) || 1.2;
    const speed = parseFloat(wrapper.getAttribute('data-speed')) || 0.5;
    const scrambleChars = wrapper.getAttribute('data-scramble-chars') || '.:';

    // Получаем текст
    const originalText = textContent.textContent || textContent.innerText;
    
    if (!originalText || !originalText.trim()) {
        return;
    }

    // Очищаем и разбиваем текст на символы
    textContent.innerHTML = '';
    const chars = [];
    
    // Разбиваем текст на слова, сохраняя пробелы
    const words = originalText.split(/(\s+)/);
    
    words.forEach(function(word) {
        // Если это пробелы - добавляем как есть
        if (/^\s+$/.test(word)) {
            textContent.appendChild(document.createTextNode(word));
            return;
        }
        
        // Оборачиваем слово в span для предотвращения разрыва слова
        const wordSpan = document.createElement('span');
        wordSpan.className = 'word';
        wordSpan.style.whiteSpace = 'nowrap';
        wordSpan.style.display = 'inline';
        
        for (let i = 0; i < word.length; i++) {
            const char = word.charAt(i);
            
            const span = document.createElement('span');
            span.className = 'char';
            span.textContent = char;
            span.setAttribute('data-content', char);
            
            // Фиксируем ширину символа чтобы избежать скачков
            span.style.display = 'inline-block';
            span.style.textAlign = 'center';
            
            wordSpan.appendChild(span);
            chars.push(span);
        }
        
        textContent.appendChild(wordSpan);
    });
    
    // Фиксируем ширину каждого символа после рендеринга
    requestAnimationFrame(function() {
        chars.forEach(function(charEl) {
            const width = charEl.offsetWidth;
            charEl.style.width = width + 'px';
        });
    });

    // Функция обработки движения мыши
    const handleMove = function(e) {
        chars.forEach(function(charEl) {
            const rect = charEl.getBoundingClientRect();
            const charCenterX = rect.left + rect.width / 2;
            const charCenterY = rect.top + rect.height / 2;
            
            const dx = e.clientX - charCenterX;
            const dy = e.clientY - charCenterY;
            const dist = Math.sqrt(dx * dx + dy * dy);
            
            if (dist < radius) {
                const originalChar = charEl.getAttribute('data-content') || '';
                
                // Проверяем наличие ScrambleTextPlugin
                if (typeof ScrambleTextPlugin !== 'undefined' && gsap.plugins && gsap.plugins.scrambleText) {
                    // Используем ScrambleTextPlugin
                    gsap.to(charEl, {
                        duration: duration * (1 - dist / radius),
                        scrambleText: {
                            text: originalChar,
                            chars: scrambleChars,
                            speed: speed
                        },
                        ease: 'none',
                        overwrite: true
                    });
                } else {
                    // Fallback: простая анимация без ScrambleTextPlugin
                    // Случайный символ из набора
                    const randomChars = scrambleChars.split('');
                    const randomChar = randomChars[Math.floor(Math.random() * randomChars.length)];
                    
                    charEl.textContent = randomChar;
                    
                    // Анимация возврата (без scale чтобы избежать скачков)
                    gsap.to(charEl, {
                        duration: duration * (1 - dist / radius) * 0.5,
                        opacity: 0.5,
                        ease: 'power2.out',
                        overwrite: true,
                        onComplete: function() {
                            charEl.textContent = originalChar;
                            gsap.to(charEl, {
                                duration: 0.2,
                                opacity: 1
                            });
                        }
                    });
                }
            }
        });
    };

    // Добавляем обработчик движения мыши
    wrapper.addEventListener('pointermove', handleMove);
    
    // Для touch устройств
    wrapper.addEventListener('touchmove', function(e) {
        if (e.touches.length > 0) {
            const touch = e.touches[0];
            handleMove(touch);
        }
    });
}

// Функция для повторной инициализации (для динамического контента)
window.reinitScrambledText = function() {
    const event = new Event('DOMContentLoaded');
    document.dispatchEvent(event);
};
