// Scrambled Text Block - Frontend JavaScript
// Adaptation of the React component for vanilla JavaScript with GSAP

(function() {
    'use strict';
    
    function initScrambledBlocks() {
        // Check for GSAP
        if (typeof gsap === 'undefined') {
            setTimeout(initScrambledBlocks, 500);
            return;
        }
        
        // Register ScrambleTextPlugin if it is available
        if (typeof ScrambleTextPlugin !== 'undefined') {
            gsap.registerPlugin(ScrambleTextPlugin);
        }

        // Find all scrambled text blocks
        const scrambledBlocks = document.querySelectorAll('.scrambled-text-wrapper');
        
        if (!scrambledBlocks.length) {
            return;
        }

        scrambledBlocks.forEach(function(wrapper, index) {
            initScrambledText(wrapper, index);
        });
    }
    
    // Start initialization when the DOM is ready
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

    // Get parameters from data attributes
    const radius = parseFloat(wrapper.getAttribute('data-radius')) || 100;
    const duration = parseFloat(wrapper.getAttribute('data-duration')) || 1.2;
    const speed = parseFloat(wrapper.getAttribute('data-speed')) || 0.5;
    const scrambleChars = wrapper.getAttribute('data-scramble-chars') || '.:';

    // Get the text
    const originalText = textContent.textContent || textContent.innerText;
    
    if (!originalText || !originalText.trim()) {
        return;
    }

    // Clear and split the text into characters
    textContent.innerHTML = '';
    const chars = [];
    
    // Split the text into words, preserving spaces
    const words = originalText.split(/(\s+)/);
    
    words.forEach(function(word) {
        // If these are spaces - add them as is
        if (/^\s+$/.test(word)) {
            textContent.appendChild(document.createTextNode(word));
            return;
        }
        
        // Wrap the word in a span to prevent word breaking
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
            
            // Fix the character width to avoid jumps
            span.style.display = 'inline-block';
            span.style.textAlign = 'center';
            
            wordSpan.appendChild(span);
            chars.push(span);
        }
        
        textContent.appendChild(wordSpan);
    });
    
    // Fix the width of each character after rendering
    requestAnimationFrame(function() {
        chars.forEach(function(charEl) {
            const width = charEl.offsetWidth;
            charEl.style.width = width + 'px';
        });
    });

    // Mouse movement handler function
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
                
                // Check for ScrambleTextPlugin
                if (typeof ScrambleTextPlugin !== 'undefined' && gsap.plugins && gsap.plugins.scrambleText) {
                    // Use ScrambleTextPlugin
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
                    // Fallback: simple animation without ScrambleTextPlugin
                    // Random character from the set
                    const randomChars = scrambleChars.split('');
                    const randomChar = randomChars[Math.floor(Math.random() * randomChars.length)];
                    
                    charEl.textContent = randomChar;
                    
                    // Return animation (without scale to avoid jumps)
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

    // Add a mouse movement handler
    wrapper.addEventListener('pointermove', handleMove);
    
    // For touch devices
    wrapper.addEventListener('touchmove', function(e) {
        if (e.touches.length > 0) {
            const touch = e.touches[0];
            handleMove(touch);
        }
    });
}

// Function for re-initialization (for dynamic content)
window.reinitScrambledText = function() {
    const event = new Event('DOMContentLoaded');
    document.dispatchEvent(event);
};
