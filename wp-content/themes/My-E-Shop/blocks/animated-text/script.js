// Animated Text Block Frontend JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Find all animated text blocks
    const animatedTextBlocks = document.querySelectorAll('.animated-text-section');
    console.log('Found animated text blocks:', animatedTextBlocks.length);
    
    // Function to strip HTML tags and get plain text
    function getCleanText(element) {
        let text = element.getAttribute('data-text');
        if (!text) {
            // If there is no data-text, get it from innerHTML and clean it
            text = element.innerHTML;
            // Preserve line breaks by replacing <br> with \n
            text = text.replace(/<br\s*\/?>/gi, '\n');
            // Remove all HTML tags except the content
            text = text.replace(/<[^>]*>/g, '');
            // Decode HTML entities
            text = text.replace(/&nbsp;/g, ' ')
                      .replace(/&amp;/g, '&')
                      .replace(/&lt;/g, '<')
                      .replace(/&gt;/g, '>')
                      .replace(/&quot;/g, '"')
                      .replace(/&#39;/g, "'");
            // Remove extra spaces but preserve line breaks
            text = text.replace(/[ \t]+/g, ' ').replace(/\n\s+/g, '\n').trim();
            // Store the cleaned text in a data attribute
            element.setAttribute('data-text', text);
        }
        return text;
    }
    
    // Function for the typewriter animation
    function typewriterAnimation(element, speed = 50) {
        const cleanText = getCleanText(element);

        // Check the screen size for responsiveness
        const isMobile = window.innerWidth <= 768;

        if (isMobile) {
            // On mobile devices show the whole text at once
            element.style.visibility = 'visible';
            element.innerHTML = cleanText.replace(/\n/g, '<br>');
            element.style.opacity = '1';
            return;
        }
        
        // Insert the whole text invisibly for measurement
        element.innerHTML = cleanText.replace(/\n/g, '<br>');
        element.style.visibility = 'hidden';

        // Let the browser update the layout
        const fullHeight = element.offsetHeight;

        // Show the element and fix the height
        element.style.visibility = 'visible';
        element.style.height = fullHeight + 'px';
        element.style.borderRight = '3px solid';

        // Clear the content
        element.innerHTML = '';

        // Gradually add characters
        const chars = cleanText.split('');
        let currentIndex = 0;
        let animationActive = true;

        // MutationObserver to guard against external changes
        const observer = new MutationObserver(function(mutations) {
            if (!animationActive) return;

            // If someone added content, restore ours
            for (let mutation of mutations) {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    // Check whether these are our nodes
                    let isOurNode = false;
                    for (let i = 0; i < currentIndex; i++) {
                        if (element.childNodes.length > i) {
                            // If our nodes exist, this is fine
                            isOurNode = true;
                            break;
                        }
                    }

                    // If these are foreign nodes, remove them
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
                // Animation finished
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
    
    // Function for the word appearance animation
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
            span.style.opacity = '0';
            span.style.transform = 'translateY(20px)';
            span.style.display = 'inline-block';
            // Put the delay directly into the animation shorthand (4th value = delay),
            // otherwise a separate animationDelay gets overwritten by this shorthand and the words
            // appear all at once instead of one by one.
            span.style.animation = 'fadeInWord 0.6s ease ' + (index * 0.1) + 's forwards';
            element.appendChild(span);
        });
    }
    
    // Function for the fade in
    function fadeInAnimation(element) {
        console.log('fadeInAnimation called');
        element.style.visibility = 'visible';
        element.classList.add('fadeIn');
    }
    
    // Function for the slide up
    function slideUpAnimation(element) {
        console.log('slideUpAnimation called');
        element.style.visibility = 'visible';
        element.classList.add('slideUp');
    }
    
    // Intersection Observer to start animations when they come into view
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                const section = entry.target;
                const textContent = section.querySelector('.text-content');

                // Remove opacity:0 from the wrapper (.text-block { opacity:0 } → .visible { opacity:1 }).
                // Without this the block stays invisible even when the text is "loaded".
                const textBlock = section.querySelector('.text-block');
                if (textBlock) {
                    textBlock.classList.add('visible');
                }

                if (textContent && !textContent.classList.contains('loaded')) {
                    const animationType = section.getAttribute('data-animation') || 'fadeIn';
                    const speed = parseInt(section.getAttribute('data-speed')) || 50;
                    
                    console.log('Starting animation:', animationType, 'for', textContent);
                    
                    // Start the animation depending on the type
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
    
    // Observe all animated text blocks
    animatedTextBlocks.forEach(function(block) {
        // Initialize text cleanup for each block
        const textContent = block.querySelector('.text-content');
        if (textContent) {
            getCleanText(textContent);
        }
        observer.observe(block);
    });
    
    // Function to restart the animation (for development)
    function restartAnimations() {
        animatedTextBlocks.forEach(function(block) {
            const textContent = block.querySelector('.text-content');
            if (textContent) {
                textContent.classList.remove('loaded', 'typewriter', 'fadeInWords', 'fadeIn', 'slideUp');
                textContent.style.opacity = '0';
                textContent.style.transform = 'translateY(30px)';
                textContent.style.borderRight = '';
                
                // Restore the original text
                const originalText = textContent.getAttribute('data-text');
                if (originalText) {
                    textContent.innerHTML = originalText;
                } else {
                    // Clear spans from the previous fadeInWords animation
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
    
    // Add the function to the global scope for debugging
    window.restartTextAnimations = restartAnimations;
    
    // Add a function to debug the text
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
    
    // Window resize handler for responsiveness
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            // Check whether the animations need to be restarted on mobile devices
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
