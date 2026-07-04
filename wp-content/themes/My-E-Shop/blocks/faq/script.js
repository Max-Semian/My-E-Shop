(function() {
    'use strict';

    function initFAQ() {
        var faqBlock = document.querySelector('.faq-block');
        if (!faqBlock) return;

        var faqItems = Array.prototype.slice.call(faqBlock.querySelectorAll('.faq-item'));
        var itemStates = {};
        
        // Variables for slowing down the scroll
        var scrollMultiplier = 0.3; // Slowdown factor (the smaller, the slower)

        // Initialize state for each item
        faqItems.forEach(function(item) {
            var itemId = item.getAttribute('data-faq-id');
            if (!itemId) {
                itemId = 'faq-' + Math.random().toString(36).substr(2, 9);
                item.setAttribute('data-faq-id', itemId);
            }
            itemStates[itemId] = {
                progress: 0, // From 0 to 1
                isInView: false,
                height: 0
            };
        });

        // Function to get the content height
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

        // Function for smooth progress update
        function updateItemProgress(item, targetProgress) {
            var itemId = item.getAttribute('data-faq-id');
            var state = itemStates[itemId];
            var answer = item.querySelector('.faq-answer');
            
            if (!answer) return;
            
            // Get the content height if we haven't yet
            if (state.height === 0) {
                state.height = getContentHeight(item);
            }
            
            var currentProgress = state.progress;
            var diff = targetProgress - currentProgress;
            
            // If the difference is very small, set the target value
            if (Math.abs(diff) < 0.001) {
                state.progress = targetProgress;
            } else {
                // Smooth progress change (lerp)
                state.progress += diff * 0.1;
            }
            
            var progress = state.progress;
            
            // Apply styles only if progress is greater than 0
            if (progress > 0.001) {
                answer.style.maxHeight = (state.height * progress) + 'px';
                answer.style.opacity = progress.toString();
                answer.style.paddingTop = (25 * progress) + 'px';
                answer.style.paddingBottom = (25 * progress) + 'px';
            } else {
                // Fully reset styles for the closed state
                answer.style.maxHeight = '0px';
                answer.style.opacity = '0';
                answer.style.paddingTop = '0px';
                answer.style.paddingBottom = '0px';
            }
            
            // Manage the active class
            if (progress > 0.5) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        }

        // Function to check position relative to the middle of the screen
        function getItemViewProgress(item) {
            var question = item.querySelector('.faq-question');
            if (!question) return 0;
            
            var rect = question.getBoundingClientRect();
            var viewportHeight = window.innerHeight;
            var questionTop = rect.top;
            var questionMiddle = questionTop + (rect.height / 2);
            
            // Activation zone: from 20% to 80% of the screen height
            var startZone = viewportHeight * 0.2;  // Start of expansion
            var peakStart = viewportHeight * 0.4;  // Start of full expansion
            var peakEnd = viewportHeight * 0.6;    // End of full expansion
            var endZone = viewportHeight * 0.8;    // Start of closing

            // If the question is above the activation zone - closed
            if (questionMiddle < startZone) {
                return 0;
            }

            // If the question is below the activation zone - closed
            if (questionMiddle > endZone) {
                return 0;
            }

            // Expansion phase (from 20% to 40% of the screen)
            if (questionMiddle >= startZone && questionMiddle <= peakStart) {
                var distance = questionMiddle - startZone;
                var totalZone = peakStart - startZone;
                var progress = distance / totalZone;
                // Smooth expansion
                return 0.5 - Math.cos(progress * Math.PI) / 2;
            }

            // Full expansion phase (from 40% to 60% of the screen) - stays open
            if (questionMiddle >= peakStart && questionMiddle <= peakEnd) {
                return 1;
            }

            // Closing phase (from 60% to 80% of the screen)
            if (questionMiddle >= peakEnd && questionMiddle <= endZone) {
                var distance = questionMiddle - peakEnd;
                var totalZone = endZone - peakEnd;
                var progress = distance / totalZone;
                // Smooth closing
                return 1 - (0.5 - Math.cos(progress * Math.PI) / 2);
            }
            
            return 0;
        }

        // Mouse wheel handler for slowing down scroll within the block
        function handleWheel(e) {
            var rect = faqBlock.getBoundingClientRect();
            var viewportHeight = window.innerHeight;
            
            // Check whether the block is in the visible area
            var isBlockVisible = rect.top < viewportHeight && rect.bottom > 0;
            
            if (isBlockVisible) {
                e.preventDefault();
                
                // Slow down the scroll
                var scrollAmount = e.deltaY * scrollMultiplier;
                window.scrollBy({
                    top: scrollAmount,
                    behavior: 'auto'
                });
            }
        }
        
        // Add the slowed scroll handler
        window.addEventListener('wheel', handleWheel, { passive: false });

        // Animation function
        var lastUpdateTime = 0;
        var updateInterval = 16; // Update every 16ms (~60fps)
        
        function animate(timestamp) {
            if (timestamp - lastUpdateTime >= updateInterval) {
                faqItems.forEach(function(item) {
                    var targetProgress = getItemViewProgress(item);
                    var itemId = item.getAttribute('data-faq-id');
                    var state = itemStates[itemId];
                    
                    // Update only if the change is larger than the threshold
                    if (Math.abs(targetProgress - state.progress) > 0.01) {
                        updateItemProgress(item, targetProgress);
                    }
                });
                lastUpdateTime = timestamp;
            }
            
            requestAnimationFrame(animate);
        }

        // Start the animation
        requestAnimationFrame(animate);

        // Click handler for manual control
        faqItems.forEach(function(item) {
            var question = item.querySelector('.faq-question');
            var itemId = item.getAttribute('data-faq-id');
            
            question.addEventListener('click', function(e) {
                e.preventDefault();
                
                var state = itemStates[itemId];
                // Toggle the state
                if (state.progress > 0.5) {
                    // Close
                    var closeInterval = setInterval(function() {
                        state.progress -= 0.05;
                        if (state.progress <= 0) {
                            state.progress = 0;
                            clearInterval(closeInterval);
                        }
                        updateItemProgress(item, state.progress);
                    }, 16);
                } else {
                    // Open
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

    // Run on DOM load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFAQ);
    } else {
        initFAQ();
    }
})();
