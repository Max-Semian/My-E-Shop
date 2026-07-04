// Fade In Words Block - Frontend JavaScript
(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Fade In Words: Script loaded');
        
        const blocks = document.querySelectorAll('.fade-in-words-wrapper');
        
        if (!blocks.length) {
            return;
        }
        
        console.log('Found blocks:', blocks.length);
        
        // Intersection Observer to trigger the animation
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const wrapper = entry.target;
                    const textContent = wrapper.querySelector('.text-content');
                    
                    if (!textContent || textContent.classList.contains('loaded')) {
                        return;
                    }
                    
                    const speed = parseInt(wrapper.getAttribute('data-speed')) || 100;
                    const text = textContent.getAttribute('data-text') || '';
                    
                    console.log('Animating text:', text);
                    console.log('Speed:', speed);
                    
                    if (!text.trim()) {
                        textContent.style.visibility = 'visible';
                        textContent.classList.add('loaded');
                        return;
                    }
                    
                    // Clear and split the text into words
                    textContent.innerHTML = '';
                    textContent.style.visibility = 'visible';
                    textContent.classList.add('loaded');
                    
                    const words = text.split(/\s+/).filter(word => word.length > 0);
                    const delayStep = speed / 1000;
                    
                    words.forEach(function(word, index) {
                        const span = document.createElement('span');
                        span.className = 'word';
                        span.textContent = word;
                        span.style.animationDelay = (index * delayStep) + 's';
                        textContent.appendChild(span);
                        
                        // Add a space after each word (except the last)
                        if (index < words.length - 1) {
                            textContent.appendChild(document.createTextNode(' '));
                        }
                    });
                    
                    observer.unobserve(wrapper);
                }
            });
        }, {
            threshold: 0.2,
            rootMargin: '0px'
        });
        
        blocks.forEach(function(block) {
            observer.observe(block);
        });
    });
})();
