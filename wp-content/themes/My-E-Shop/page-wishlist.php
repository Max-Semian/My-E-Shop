<?php
/**
 * Template Name: Wishlist Page
 */

get_header();
?>

<div class="wishlist-page-wrapper" style="background-color: #F6F6F4; min-height: 100vh; padding: 40px 0;">
    <div class="container">
        <div class="wishlist-header" style="text-align: center; margin-bottom: 50px;">
            <h1 style="font-family: 'Playfair Display', serif; font-size: 48px; margin-bottom: 15px; color: #2C2C2C;">
                My Wishlist
            </h1>
            <p style="font-family: 'Montserrat', sans-serif; font-size: 16px; color: #666;">
                Save your favorite items for later
            </p>
        </div>

        <?php
        // Get wishlist from cookie or session
        $wishlist = array();
        if (isset($_COOKIE['my_eshop_wishlist'])) {
            $wishlist = json_decode(stripslashes($_COOKIE['my_eshop_wishlist']), true);
        }

        if (!empty($wishlist) && is_array($wishlist)):
        ?>
            <div class="wishlist-items-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; margin-bottom: 40px;">
                <?php
                foreach ($wishlist as $product_id):
                    $product = wc_get_product($product_id);
                    if (!$product || !$product->is_visible()) continue;
                    
                    $image = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'large');
                    $permalink = get_permalink($product_id);
                ?>
                    <div class="wishlist-item" data-product-id="<?php echo esc_attr($product_id); ?>" style="background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.08); transition: all 0.3s ease;">
                        <div class="wishlist-item-image" style="position: relative; padding-top: 125%; overflow: hidden;">
                            <?php if ($image): ?>
                                <img src="<?php echo esc_url($image[0]); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <img src="<?php echo wc_placeholder_img_src(); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                            <?php endif; ?>
                            
                            <button class="remove-from-wishlist" data-product-id="<?php echo esc_attr($product_id); ?>" style="position: absolute; top: 15px; right: 15px; background: rgba(255,255,255,0.95); border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                <i class="fas fa-times" style="color: #dc3545; font-size: 18px;"></i>
                            </button>
                        </div>
                        
                        <div class="wishlist-item-content" style="padding: 20px;">
                            <h3 style="font-family: 'Montserrat', sans-serif; font-size: 16px; font-weight: 600; margin: 0 0 10px 0; color: #2C2C2C;">
                                <a href="<?php echo esc_url($permalink); ?>" style="color: inherit; text-decoration: none; transition: color 0.3s ease;">
                                    <?php echo $product->get_name(); ?>
                                </a>
                            </h3>
                            
                            <div class="wishlist-item-price" style="margin-bottom: 15px;">
                                <span style="font-family: 'Montserrat', sans-serif; font-size: 18px; font-weight: 700; color: #AA2DD0;">
                                    <?php echo $product->get_price_html(); ?>
                                </span>
                            </div>
                            
                            <?php if ($product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()): ?>
                                <a href="<?php echo esc_url('?add-to-cart=' . $product_id); ?>" 
                                   class="add-to-cart-btn" 
                                   data-product_id="<?php echo esc_attr($product_id); ?>"
                                   style="display: block; width: 100%; padding: 12px; background: #AA2DD0; color: #fff; text-align: center; text-decoration: none; border-radius: 8px; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 14px; transition: all 0.3s ease; border: none; cursor: pointer;">
                                    Add to Cart
                                </a>
                            <?php else: ?>
                                <a href="<?php echo esc_url($permalink); ?>" 
                                   style="display: block; width: 100%; padding: 12px; background: #6B7280; color: #fff; text-align: center; text-decoration: none; border-radius: 8px; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 14px; transition: all 0.3s ease;">
                                    View Product
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" 
                   style="display: inline-block; padding: 15px 40px; background: #2C2C2C; color: #fff; text-decoration: none; border-radius: 8px; font-family: 'Montserrat', sans-serif; font-weight: 600; transition: all 0.3s ease;">
                    Continue Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="empty-wishlist" style="text-align: center; padding: 0 20px;">
                <i class="far fa-heart" style="font-size: 80px; color: #E0E0E0; margin-bottom: 30px;"></i>
                <h2 style="font-family: 'Playfair Display', serif; font-size: 32px; margin-bottom: 15px; color: #2C2C2C;">
                    Your Wishlist is Empty
                </h2>
                <p style="font-family: 'Montserrat', sans-serif; font-size: 16px; color: #666; margin-bottom: 30px;">
                    Start adding products you love to your wishlist
                </p>
                <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" 
                   style="display: inline-block; padding: 15px 40px; background: #AA2DD0; color: #fff; text-decoration: none; border-radius: 8px; font-family: 'Montserrat', sans-serif; font-weight: 600; transition: all 0.3s ease;">
                    Browse Products
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.wishlist-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.wishlist-item-content h3 a:hover {
    color: #AA2DD0;
}

.add-to-cart-btn:hover {
    background: #A21CAF;
    transform: translateY(-2px);
}

.remove-from-wishlist:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

@media (max-width: 768px) {
    .wishlist-items-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)) !important;
        gap: 20px !important;
    }
    
    .wishlist-header h1 {
        font-size: 36px !important;
    }
}

@media (max-width: 480px) {
    .wishlist-items-grid {
        grid-template-columns: 1fr !important;
    }
    
    .wishlist-header h1 {
        font-size: 28px !important;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Remove from wishlist
    $('.remove-from-wishlist').on('click', function(e) {
        e.preventDefault();
        var button = $(this);
        var productId = button.data('product-id');
        var item = button.closest('.wishlist-item');
        
        // Remove from cookie
        var wishlist = getCookie('my_eshop_wishlist');
        if (wishlist) {
            wishlist = JSON.parse(wishlist);
            var index = wishlist.indexOf(productId);
            if (index > -1) {
                wishlist.splice(index, 1);
                setCookie('my_eshop_wishlist', JSON.stringify(wishlist), 30);
            }
        }
        
        // Animate removal
        item.fadeOut(300, function() {
            $(this).remove();
            
            // Check if wishlist is empty
            if ($('.wishlist-item').length === 0) {
                location.reload();
            }
        });
    });
    
    // Helper functions for cookies
    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }
    
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
});
</script>

<?php
get_footer();
?>
