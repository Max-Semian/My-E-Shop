<?php if ( is_singular('post') ) :
    // ===== Article CTA footer (configured in the post sidebar) =====
    $cta_theme = get_post_meta(get_the_ID(), '_footer_cta_theme', true);
    if ($cta_theme === '') { $cta_theme = 'dark'; }
    $cta_title = get_post_meta(get_the_ID(), '_footer_cta_title', true);
    $cta_sub   = get_post_meta(get_the_ID(), '_footer_cta_subtitle', true);
    $cta_btn   = get_post_meta(get_the_ID(), '_footer_cta_button_text', true);
    if ($cta_btn === '') { $cta_btn = 'EXPLORE THE COLLECTION'; }
    $cta_page  = (int) get_post_meta(get_the_ID(), '_footer_cta_button_page', true);
    $cta_url   = $cta_page ? get_permalink($cta_page) : '';
    $shop_url  = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : home_url('/');
?>
    <footer class="footer article-footer article-footer--<?php echo esc_attr($cta_theme); ?>">
        <?php if ($cta_title || $cta_sub || ($cta_btn && $cta_url)) : ?>
            <div class="article-cta">
                <?php if ($cta_title) : ?>
                    <h2 class="article-cta-title"><?php echo esc_html($cta_title); ?></h2>
                <?php endif; ?>
                <?php if ($cta_sub) : ?>
                    <p class="article-cta-subtitle"><?php echo esc_html($cta_sub); ?></p>
                <?php endif; ?>
                <?php if ($cta_btn && $cta_url) : ?>
                    <a class="article-cta-btn" href="<?php echo esc_url($cta_url); ?>"><?php echo esc_html($cta_btn); ?></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="article-footer-bottom">
            <p class="article-footer-copy">&copy; <?php echo esc_html(date('Y')); ?> CRETHO. All rights reserved.</p>
            <nav class="article-footer-links">
                <a href="<?php echo esc_url(home_url('/about-us/')); ?>">About Us</a>
                <span class="sep">|</span>
                <a href="<?php echo esc_url($shop_url); ?>">Shop</a>
                <span class="sep">|</span>
                <a href="<?php echo esc_url(home_url('/blog/')); ?>">Blog</a>
            </nav>
            <nav class="article-footer-social">
                <a href="#">Instagram</a>
                <a href="#">Pinterest</a>
                <a href="#">TikTok</a>
            </nav>
        </div>
    </footer>
<?php else : ?>
<footer class="footer">
    <div class="footer-container">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-12">
                <div class="footer-logo">
                    <a href="/">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/footer-logo.png" alt="Crethos">
                    </a>
                </div>
                <p>
                    Your individuality, trending now
                </p>
            </div>
            <div class="col-lg-2 col-md-6 col-6">
                <div class="footer-menu">About Us</div>
                <ul class="list-unstyled">
                    <li><a href="index.html">Our Story</a></li>
                    <li><a href="#">Trends</a></li>
                    <li><a href="#">HOME</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 col-6">
                <div class="footer-menu">Support</div>
                <ul class="list-unstyled">
                    <li>FAQ</li>
                    <li>Shipping & Payment</li>
                    <li>Returns & Exchanges</li>
                    <li>Contact Us</li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 col-6">
                <div class="footer-menu">Legal</div>
                <ul class="list-unstyled">
                    <li>Privacy Policy</li>
                    <li>Terms of Service</li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 col-6">
                <div class="footer-menu">Follow Us</div>
                <ul class="list-unstyled">
                    <li>Instagram</li>
                    <li>Pinterest</li>
                    <li>TikTok</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom" style="border-top: 1px solid #ddd; padding-top: 40px;">
            <img class="footer-social-icon" src="<?php echo get_template_directory_uri(); ?>/assets/img/Social-media.png" alt="Crethos" style="height: 24px; margin-top: 10px;">
            <img class="footer-social-icon" src="<?php echo get_template_directory_uri(); ?>/assets/img/Social-media1.png" alt="Crethos" style="height: 24px; margin-top: 10px;">
            <img class="footer-social-icon" src="<?php echo get_template_directory_uri(); ?>/assets/img/Social-media2.png" alt="Crethos" style="height: 24px; margin-top: 10px;">    
            <img class="footer-social-icon" src="<?php echo get_template_directory_uri(); ?>/assets/img/Social-media3.png" alt="Crethos" style="height: 24px; margin-top: 10px;">
            <p class="footer-copy" style="padding-top: 20px;">&copy; 2024 My E-Shop. All rights reserved.</p>
    </div>
</footer>
<?php endif; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Look for all possible variants
    const shopButton1 = document.querySelector('.nav-link.dropdown-toggle');
    const shopButton2 = document.querySelector('a[href*="shop"]');
    const shopButton3 = document.querySelector('.dropdown-toggle');
    
    const dropdown1 = document.querySelector('.shop-dropdown');
    const dropdown2 = document.querySelector('.dropdown-menu');
    const dropdown3 = document.querySelector('ul.shop-dropdown');
    
    const dropdownParent = document.querySelector('.nav-item.dropdown');
    
    const shopButton = shopButton1 || shopButton2 || shopButton3;
    const dropdown = dropdown1 || dropdown2 || dropdown3;
    
         if (shopButton && dropdown && dropdownParent) {
         let hideTimeout;
         
         dropdownParent.addEventListener('mouseenter', function() {
             // Cancel hiding if it was scheduled
             if (hideTimeout) {
                 clearTimeout(hideTimeout);
                 hideTimeout = null;
             }
             const rect = shopButton.getBoundingClientRect();

             // Restore the required classes for styling inner elements
             dropdown.className = 'dropdown-menu shop-dropdown';
             dropdown.removeAttribute('style');

             // Apply only our styles
             dropdown.style.cssText = `
                 position: fixed !important;
                 top: ${rect.bottom + 5}px !important;
                 left: ${rect.left}px !important;
                 display: block !important;
                 opacity: 1 !important;
                 visibility: visible !important;
                 transform: translateY(0) !important;
                 z-index: 2147483647 !important;
                 background: white !important;
                 border: 1px solid #ccc !important;
                 border-radius: 12px !important;
                 box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
                 padding: 15px !important;
                 min-width: 250px !important;
                 max-height: none !important;
                 overflow: visible !important;
                 transition: none !important;
                 animation: none !important;
                 pointer-events: auto !important;
             `;
             
             // Move the dropdown to the end of body to guarantee the correct z-index
             document.body.appendChild(dropdown);

             // Add styles for the inner elements
             const dropdownItems = dropdown.querySelectorAll('.dropdown-item');
             dropdownItems.forEach((item, index) => {
                 item.style.cssText = `
                     display: flex !important;
                     align-items: center !important;
                     padding: 12px 20px !important;
                     color: #333333 !important;
                     text-decoration: none !important;
                     font-size: 14px !important;
                     font-weight: 500 !important;
                     font-family: 'Montserrat', sans-serif !important;
                     transition: all 0.3s ease !important;
                     border: none !important;
                     background: transparent !important;
                     opacity: 1 !important;
                     transform: translateX(0) !important;
                     animation: none !important;
                     position: relative !important;
                     overflow: hidden !important;
                 `;
                 
                 // Add a hover effect
                 item.addEventListener('mouseenter', function() {
                     this.style.background = 'linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important';
                     this.style.color = '#D85AFF !important';
                     this.style.transform = 'translateX(5px) !important';
                 });
                 
                 item.addEventListener('mouseleave', function() {
                     this.style.background = 'transparent !important';
                     this.style.color = '#333333 !important';
                     this.style.transform = 'translateX(0) !important';
                 });
             });
             
             // Style the icons
             const dropdownIcons = dropdown.querySelectorAll('.dropdown-icon');
             dropdownIcons.forEach(icon => {
                 icon.style.cssText = `
                     width: 35px !important;
                     height: 35px !important;
                     background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%) !important;
                     border-radius: 8px !important;
                     display: flex !important;
                     align-items: center !important;
                     justify-content: center !important;
                     margin-right: 12px !important;
                     transition: all 0.3s ease !important;
                     font-size: 16px !important;
                     color: #666 !important;
                 `;
             });
             
             // Style the text
             const dropdownTexts = dropdown.querySelectorAll('.dropdown-text');
             dropdownTexts.forEach(text => {
                 text.style.cssText = `
                     flex: 1 !important;
                     font-weight: 500 !important;
                     letter-spacing: 0.3px !important;
                     color: #333333 !important;
                 `;
             });
         });
        
                 dropdownParent.addEventListener('mouseleave', function(e) {
             // Set a delay before hiding
             hideTimeout = setTimeout(function() {
                 dropdown.style.display = 'none';
             }, 100);
         });

         // Add events for the dropdown itself
         dropdown.addEventListener('mouseenter', function() {
             // Cancel hiding if the cursor is on the dropdown
             if (hideTimeout) {
                 clearTimeout(hideTimeout);
                 hideTimeout = null;
             }
         });
         
         dropdown.addEventListener('mouseleave', function() {
             // A small delay for smoothness
             hideTimeout = setTimeout(function() {
                 dropdown.style.display = 'none';
             }, 100);
         });

        // Also add events for the dropdown itself
        dropdown.addEventListener('mouseenter', function() {
            dropdown.style.display = 'block';
            dropdown.style.opacity = '1';
            dropdown.style.visibility = 'visible';
            dropdown.style.transform = 'translateY(0)';
        });
    }
});
</script>

<!-- Search Modal -->
<div id="searchModal" class="modal-overlay">
    <div class="modal-content search-modal">
        <button class="modal-close" onclick="closeSearchModal()">&times;</button>
        <div class="search-modal-body">
            <h2>Search Products</h2>
            <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                <div class="search-input-wrapper">
                    <input type="search" 
                           class="search-field" 
                           placeholder="Search for products..." 
                           value="<?php echo get_search_query(); ?>" 
                           name="s" 
                           autocomplete="off" />
                    <input type="hidden" name="post_type" value="product" />
                    <button type="submit" class="search-submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            <div class="search-suggestions" id="searchSuggestions"></div>
        </div>
    </div>
</div>

<!-- Login Modal -->
<div id="loginModal" class="modal-overlay">
    <div class="modal-content login-modal">
        <button class="modal-close" onclick="closeLoginModal()">&times;</button>
        <div class="login-modal-body">
            <?php if (is_user_logged_in()): ?>
                <div class="logged-in-content">
                    <h2>Welcome, <?php echo wp_get_current_user()->display_name; ?>!</h2>
                    <div class="account-links">
                        <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="account-link">
                            <i class="fas fa-user"></i> My Account
                        </a>
                        <a href="<?php echo esc_url(wc_get_endpoint_url('orders', '', wc_get_page_permalink('myaccount'))); ?>" class="account-link">
                            <i class="fas fa-shopping-bag"></i> Orders
                        </a>
                        <a href="<?php echo esc_url(home_url('/wishlist')); ?>" class="account-link">
                            <i class="fas fa-heart"></i> Wishlist
                        </a>
                        <a href="<?php echo esc_url(wc_logout_url()); ?>" class="account-link logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <h2>Login to Your Account</h2>
                <?php woocommerce_login_form(array('redirect' => wc_get_page_permalink('myaccount'))); ?>
                <div class="register-link">
                    <p>Don't have an account? <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>">Register here</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Search Modal Functions
function openSearchModal() {
    document.getElementById('searchModal').classList.add('active');
    document.body.style.overflow = 'hidden';
    document.querySelector('#searchModal .search-field').focus();
}

function closeSearchModal() {
    document.getElementById('searchModal').classList.remove('active');
    document.body.style.overflow = '';
}

// Login Modal Functions
function openLoginModal() {
    document.getElementById('loginModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLoginModal() {
    document.getElementById('loginModal').classList.remove('active');
    document.body.style.overflow = '';
}

// Close on outside click
document.querySelectorAll('.modal-overlay').forEach(function(modal) {
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
});

// Close on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSearchModal();
        closeLoginModal();
    }
});

// Live search functionality
jQuery(document).ready(function($) {
    var searchTimeout;
    $('#searchModal .search-field').on('keyup', function() {
        var query = $(this).val();
        
        clearTimeout(searchTimeout);
        
        if (query.length >= 3) {
            searchTimeout = setTimeout(function() {
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'live_product_search',
                        query: query
                    },
                    success: function(response) {
                        $('#searchSuggestions').html(response);
                    }
                });
            }, 300);
        } else {
            $('#searchSuggestions').html('');
        }
    });
});
</script>

<?php wp_footer();?>
</body>
</html>