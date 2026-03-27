(function() {
    'use strict';

    function initAutoSlider() {
        var sliders = document.querySelectorAll('.auto-slider-wrapper');
        
        sliders.forEach(function(slider) {
            var originalTrack = slider.querySelector('.auto-slider-track');
            var items = slider.querySelectorAll('.auto-slider-item');
            
            if (!originalTrack || items.length === 0) return;
            
            // Создаем контейнер для бесшовной петли
            var container = document.createElement('div');
            container.className = 'auto-slider-container';
            container.style.display = 'flex';
            container.style.height = '100%';
            
            // Дублируем track 3 раза для плавной бесшовной петли
            var clone1 = originalTrack.cloneNode(true);
            var clone2 = originalTrack.cloneNode(true);
            
            container.appendChild(originalTrack);
            container.appendChild(clone1);
            container.appendChild(clone2);
            
            slider.appendChild(container);
            
            var allTracks = [originalTrack, clone1, clone2];
            
            // Пауза при наведении для всех треков
            slider.addEventListener('mouseenter', function() {
                allTracks.forEach(function(track) {
                    track.style.animationPlayState = 'paused';
                });
            });
            
            slider.addEventListener('mouseleave', function() {
                allTracks.forEach(function(track) {
                    track.style.animationPlayState = 'running';
                });
            });
        });
    }

    // Запуск при загрузке
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAutoSlider);
    } else {
        initAutoSlider();
    }
})();
