<?php

defined( 'ABSPATH' ) || exit;

global $product;
?>

<div class="product-page-container">
	<?php do_action( 'woocommerce_before_single_product' ); ?>
</div>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'product-content-wrapper', $product ); ?>>
    <input type="hidden" id="product_id_field" name="product_id" value="<?php echo esc_attr(get_the_ID()); ?>">
    
    <div class="product-layout">
        <div class="product-images-section">
            <div class="bg-white h-100">
                <div id="carouselExampleFade" class="carousel carousel-dark slide carousel-fade">
                    <div class="carousel-inner">
						<?php
						$product_img_id = $product->get_image_id();
						if ( $product_img_id ) {
							$main_img = wp_get_attachment_url( $product_img_id );
						} else {
							$main_img = wc_placeholder_img_src( 'woocommerce_full' );
						}
						$product_img_ids = $product->get_gallery_image_ids();
						?>
                        <div class="carousel-item active">
                            <img data-fancybox="gallery" src="<?php echo $main_img; ?>" class="d-block w-100"
                                 alt="<?php echo $product->get_title(); ?>">
                        </div>
						<?php if ( $product_img_ids ): ?>
							<?php foreach ( $product_img_ids as $product_img_id ): ?>
                                <div class="carousel-item">
                                    <img data-fancybox="gallery" src="<?php echo wp_get_attachment_url( $product_img_id ); ?>"
                                         class="d-block w-100" alt="<?php echo $product->get_title(); ?>">
                                </div>
							<?php endforeach; ?>
						<?php endif; ?>
                    </div>
					<?php if ( $product_img_ids ): ?>
                        <button class="carousel-control-prev" type="button"
                                data-bs-target="#carouselExampleFade" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button"
                                data-bs-target="#carouselExampleFade" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
					<?php endif; ?>
                </div>
            </div>
        </div>

        <div class="product-info-section">
            <div class="bg-white product-content p-3 h-100">
                <?php woocommerce_show_product_sale_flash(); ?>
                
                <!-- РќР°Р·РІР°РЅРёРµ С‚РѕРІР°СЂР° -->
                <h1 class="product-title"><?php the_title(); ?></h1>
                
                <!-- Р РµР№С‚РёРЅРі Рё РѕС‚Р·С‹РІС‹ -->
                <?php if ( wc_review_ratings_enabled() ) : ?>
                    <?php echo wc_get_rating_html( $product->get_average_rating() ); ?>
                <?php endif; ?>
                
                <!-- РћРїРёСЃР°РЅРёРµ С‚РѕРІР°СЂР° -->
                <?php if ( $product->get_short_description() ) : ?>
                <div class="product-short-description">
                    <?php echo apply_filters( 'woocommerce_short_description', $product->get_short_description() ); ?>
                </div>
                <?php endif; ?>
                
                <!-- Р¦РµРЅР° С‚РѕРІР°СЂР° -->
                <div class="product-price">
                    <?php echo $product->get_price_html(); ?>
                </div>
                
                <!-- Р¤РѕСЂРјР° РґРѕР±Р°РІР»РµРЅРёСЏ РІ РєРѕСЂР·РёРЅСѓ -->
                <?php woocommerce_template_single_add_to_cart(); ?>
                
                <!-- РњРµС‚Р° РёРЅС„РѕСЂРјР°С†РёСЏ -->
                <div class="product-meta">
                    <?php echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>
                    <?php echo wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>
                    <?php do_action( 'woocommerce_product_meta_start' ); ?>
                    <?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
                        <span class="sku_wrapper">SKU: <span class="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span></span>
                    <?php endif; ?>
                    <?php do_action( 'woocommerce_product_meta_end' ); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="product-description-section">
        <div class="bg-white p-3">
            <?php
            // Get ACF fields
            $title = get_field('title');
            $description = get_field('description');
            $image = get_field('image');

            // Display ACF fields if they exist
            if (!empty($title) || !empty($description) || !empty($image)) : ?>
                <div class="product-custom-description">
                    <?php if (!empty($title)) : ?>
                        <h2 class="acf-title"><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>

                    <?php if (!empty($description)) : ?>
                        <div class="acf-desc"><?php echo wpautop($description); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($image)) : ?>
                        <div class="acf-image mt-3">
                            <?php echo wp_get_attachment_image($image, 'large', false, ['class' => 'acf-custom-img img-fluid']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Category Products Section -->
    <div class="category-products-section mt-4" id="category-products-section">
        <div class="bg-white p-3">
            <h2><?php echo esc_html__('YOU MAY ALSO LIKE', 'My-E-Shop'); ?></h2>
            <div class="category-products-container">
                <?php
                // Get the product categories
                $product_categories = wp_get_post_terms(get_the_ID(), 'product_cat');
                
                if (!empty($product_categories)) {
                    // Get the first category ID (you can modify this to use a specific category)
                    $category_id = $product_categories[0]->term_id;
                    $current_product_id = get_the_ID();
                    
                    // Query for products in the same category
                    $args = array(
                        'post_type' => 'product',
                        'posts_per_page' => 4, // Show only 4 products
                        'post__not_in' => array($current_product_id), // Exclude current product
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'product_cat',
                                'field' => 'term_id',
                                'terms' => $category_id,
                            ),
                        ),
                        'orderby' => 'rand', // Random order
                    );
                    
                    $category_products = new WP_Query($args);
                    
                    if ($category_products->have_posts()) {
                        echo '<div class="row">';
                        
                        while ($category_products->have_posts()) {
                            $category_products->the_post();
                            global $product;
                            ?>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="card h-100">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php 
                                        if (has_post_thumbnail()) {
                                            echo get_the_post_thumbnail(get_the_ID(), 'woocommerce_thumbnail', array('class' => 'card-img-top'));
                                        } else {
                                            echo wc_placeholder_img('woocommerce_thumbnail'); 
                                        }
                                        ?>
                                    </a>
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h5>
                                        <p class="card-text price"><?php echo $product->get_price_html(); ?></p>
                                        <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" class="btn btn-primary btn-sm add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo get_the_ID(); ?>">
                                            <?php echo esc_html__('Add to Cart', 'My-E-Shop'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        
                        echo '</div>';
                        wp_reset_postdata();
                    } else {
                        echo '<p>' . esc_html__('No related products found.', 'My-E-Shop') . '</p>';
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="mt-4" id="reviews-section">
        <div class="bg-white p-3">
            <h2><?php echo esc_html__('Reviews', 'My-E-Shop'); ?></h2>
            <div id="reviews-container" data-loaded="false">
                <div class="reviews-loading">
                    <p><?php echo esc_html__('Loading reviews...', 'My-E-Shop'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add the updated script here -->
    <script>
    jQuery(document).ready(function($) {
        // Load initial reviews
        const productId = <?php echo get_the_ID(); ?>;
        console.log("Loading reviews for product:", productId);
        
        // Simple check to make sure we're not loading twice
        if ($('#reviews-container').attr('data-loaded') === 'false') {
            $.ajax({
                url: '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    'action': 'load_product_reviews',
                    'product_id': productId
                },
                success: function(response) {
                    $('#reviews-container').html(response).attr('data-loaded', 'true');
                    console.log("Reviews loaded successfully");
                },
                error: function(xhr, status, error) {
                    console.error("Error loading reviews:", error);
                    $('#reviews-container').html('<p>Error loading reviews: ' + error + '</p>');
                }
            });
        }
        
        // Event delegation for load more button
        $(document).on('click', '#load-more-reviews', function(e) {
            e.preventDefault();
            
            console.log("Load more button clicked");
            
            const $button = $(this);
            $button.text('Loading...').prop('disabled', true);
            
            const productId = $button.data('product-id');
            const nextPage = $button.data('page');
            
            $.ajax({
                url: '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    'action': 'load_product_reviews',
                    'product_id': productId,
                    'page': nextPage
                },
                success: function(response) {
                    // Create temporary div to parse the HTML
                    const tempDiv = $('<div>').html(response);
                    
                    // Find new reviews and append them
                    const newReviews = tempDiv.find('.commentlist li');
                    $('#reviews-container .commentlist').append(newReviews);
                    
                    // Replace old button with new one
                    $button.remove();
                    
                    const newButton = tempDiv.find('#load-more-reviews');
                    if (newButton.length) {
                        $('#reviews-container').append(newButton);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error loading more reviews:", error);
                    $button.text('Error. Try again').prop('disabled', false);
                }
            });
        });
    });
    </script>
</div>
<?php do_action( 'woocommerce_after_single_product' ); ?>
