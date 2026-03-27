(function() {
    'use strict';

    function initFAQ() {
        var faqBlock = document.querySelector('.faq-block');
        if (!faqBlock) return;

        var faqItems = Array.prototype.slice.call(faqBlock.querySelectorAll('.faq-item'));
        var itemStates = {};
        
        // Переменные для замедления скролла
        var scrollMultiplier = 0.3; // Коэффициент замедления (чем меньше, тем медленнее)

        // Инициализируем состояния для каждого элемента
        faqItems.forEach(function(item) {
            var itemId = item.getAttribute('data-faq-id');
            if (!itemId) {
                itemId = 'faq-' + Math.random().toString(36).substr(2, 9);
                item.setAttribute('data-faq-id', itemId);
            }
            itemStates[itemId] = {
                progress: 0, // От 0 до 1
                isInView: false,
                height: 0
            };
        });

        // Функция получения высоты контента
        function getContentHeight(item) {
            var answer = item.querySelector('.faq-answer');
            if (!answer) return 0;
            
            var clone = answer.cloneNode(true);
            clone.style.cssText = 'max-height: none !important; height: auto !important; position: absolute !important; visibility: hidden !important; display: block !important; padding: 25px 30px !important; opacity: 1 !important; transform: none !important; overflow: visible !important;';
            clone.style.width = answer.offsetWidth + 'px';
            document.body.appendChild(clone);
            var height = clone.offsetHeight;
            document.body.removeChild(clone);
            
            return height;
        }

        // Функция плавного обновления прогресса
        function updateItemProgress(item, targetProgress) {
            var itemId = item.getAttribute('data-faq-id');
            var state = itemStates[itemId];
            var answer = item.querySelector('.faq-answer');
            
            if (!answer) return;
            
            // Получаем высоту контента если еще не получили
            if (state.height === 0) {
                state.height = getContentHeight(item);
            }
            
            var currentProgress = state.progress;
            var diff = targetProgress - currentProgress;
            
            // Если разница очень маленькая, устанавливаем целевое значение
            if (Math.abs(diff) < 0.001) {
                state.progress = targetProgress;
            } else {
                // Плавное изменение прогресса (lerp)
                state.progress += diff * 0.1;
            }
            
            var progress = state.progress;
            
            // Применяем стили только если прогресс больше 0
            if (progress > 0.001) {
                answer.style.maxHeight = (state.height * progress) + 'px';
                answer.style.opacity = progress.toString();
                answer.style.paddingTop = (25 * progress) + 'px';
                answer.style.paddingBottom = (25 * progress) + 'px';
            } else {
                // Полностью сбрасываем стили для закрытого состояния
                answer.style.maxHeight = '0px';
                answer.style.opacity = '0';
                answer.style.paddingTop = '0px';
                answer.style.paddingBottom = '0px';
            }
            
            // Управляем классом active
            if (progress > 0.5) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        }

        // Функция проверки позиции относительно середины экрана
        function getItemViewProgress(item) {
            var question = item.querySelector('.faq-question');
            if (!question) return 0;
            
            var rect = question.getBoundingClientRect();
            var viewportHeight = window.innerHeight;
            var questionTop = rect.top;
            var questionMiddle = questionTop + (rect.height / 2);
            
            // Зона активации: от 20% до 80% высоты экрана
            var startZone = viewportHeight * 0.2;  // Начало раскрытия
            var peakStart = viewportHeight * 0.4;  // Начало полного раскрытия
            var peakEnd = viewportHeight * 0.6;    // Конец полного раскрытия
            var endZone = viewportHeight * 0.8;    // Начало закрытия
            
            // Если вопрос выше зоны активации - закрыт
            if (questionMiddle < startZone) {
                return 0;
            }
            
            // Если вопрос ниже зоны активации - закрыт
            if (questionMiddle > endZone) {
                return 0;
            }
            
            // Фаза раскрытия (от 20% до 40% экрана)
            if (questionMiddle >= startZone && questionMiddle <= peakStart) {
                var distance = questionMiddle - startZone;
                var totalZone = peakStart - startZone;
                var progress = distance / totalZone;
                // Плавное раскрытие
                return 0.5 - Math.cos(progress * Math.PI) / 2;
            }
            
            // Фаза полного раскрытия (от 40% до 60% экрана) - остается открытым
            if (questionMiddle >= peakStart && questionMiddle <= peakEnd) {
                return 1;
            }
            
            // Фаза закрытия (от 60% до 80% экрана)
            if (questionMiddle >= peakEnd && questionMiddle <= endZone) {
                var distance = questionMiddle - peakEnd;
                var totalZone = endZone - peakEnd;
                var progress = distance / totalZone;
                // Плавное закрытие
                return 1 - (0.5 - Math.cos(progress * Math.PI) / 2);
            }
            
            return 0;
        }

        // Обработчик колеса мыши для замедления скролла в пределах блока
        function handleWheel(e) {
            var rect = faqBlock.getBoundingClientRect();
            var viewportHeight = window.innerHeight;
            
            // Проверяем, находится ли блок в видимой области
            var isBlockVisible = rect.top < viewportHeight && rect.bottom > 0;
            
            if (isBlockVisible) {
                e.preventDefault();
                
                // Замедляем скролл
                var scrollAmount = e.deltaY * scrollMultiplier;
                window.scrollBy({
                    top: scrollAmount,
                    behavior: 'auto'
                });
            }
        }
        
        // Добавляем обработчик замедленного скролла
        window.addEventListener('wheel', handleWheel, { passive: false });

        // Функция анимации
        var lastUpdateTime = 0;
        var updateInterval = 16; // Обновление каждые 16мс (~60fps)
        
        function animate(timestamp) {
            if (timestamp - lastUpdateTime >= updateInterval) {
                faqItems.forEach(function(item) {
                    var targetProgress = getItemViewProgress(item);
                    var itemId = item.getAttribute('data-faq-id');
                    var state = itemStates[itemId];
                    
                    // Обновляем только если есть изменение больше порога
                    if (Math.abs(targetProgress - state.progress) > 0.01) {
                        updateItemProgress(item, targetProgress);
                    }
                });
                lastUpdateTime = timestamp;
            }
            
            requestAnimationFrame(animate);
        }

        // Запускаем анимацию
        requestAnimationFrame(animate);

        // Обработчик клика для ручного управления
        faqItems.forEach(function(item) {
            var question = item.querySelector('.faq-question');
            var itemId = item.getAttribute('data-faq-id');
            
            question.addEventListener('click', function(e) {
                e.preventDefault();
                
                var state = itemStates[itemId];
                // Переключаем состояние
                if (state.progress > 0.5) {
                    // Закрываем
                    var closeInterval = setInterval(function() {
                        state.progress -= 0.05;
                        if (state.progress <= 0) {
                            state.progress = 0;
                            clearInterval(closeInterval);
                        }
                        updateItemProgress(item, state.progress);
                    }, 16);
                } else {
                    // Открываем
                    var openInterval = setInterval(function() {
                        state.progress += 0.05;
                        if (state.progress >= 1) {
                            state.progress = 1;
                            clearInterval(openInterval);
                        }
                        updateItemProgress(item, state.progress);
                    }, 16);
                }
            });
        });
    }

    // Запуск при загрузке DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFAQ);
    } else {
        initFAQ();
    }
})();
