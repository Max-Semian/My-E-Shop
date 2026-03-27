<?php get_header() ?>
<main class="main">
<!-- Updated Slider Section with Background Video -->
        <section class="slider video-background">
            <!-- Background Video Container -->
            <div class="video-container">
                <video autoplay muted loop id="background-video">
                <source src="<?php echo get_template_directory_uri(); ?>/assets/video/background.mp4" type="video/mp4">
                    <!-- You can add more source formats for better browser compatibility -->
                    <!-- <source src="assets/video/background.webm" type="video/webm"> -->
                    Your browser does not support the video tag.
                </video>
                <div class="video-overlay"></div>
            </div>
            
            <div class="container">
                <div id="carousel-indicators" class="carousel slide" data-bs-theme="dark" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button class="carousel-control-prev" type="button" data-bs-target="#carousel-indicators" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button type="button" data-bs-target="#carousel-indicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#carousel-indicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#carousel-indicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carousel-indicators" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                    
            </div>
        </section> 

        <!-- Fashion Hero Section -->
        <section class="fashion-hero-section">
            <div class="fashion-hero-container">
                <!-- Background Images Grid -->
                <div class="fashion-images-grid">
                    <div class="fashion-image-card">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/fashion-1.jpg" alt="Fashion Model 1">
                    </div>
                    <div class="fashion-image-card">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/fashion-2.jpg" alt="Fashion Model 2">
                    </div>
                    <div class="fashion-image-card">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/fashion-3.jpg" alt="Fashion Model 3">
                    </div>
                    <div class="fashion-image-card">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/fashion-4.jpg" alt="Fashion Model 4">
                    </div>
                    <div class="fashion-image-card">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/fashion-5.jpg" alt="Fashion Model 5">
                    </div>
                    <div class="fashion-image-card">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/fashion-6.jpg" alt="Fashion Model 6">
                    </div>
                    <div class="fashion-image-card">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/fashion-7.jpg" alt="Fashion Model 7">
                    </div>
                </div>

                <!-- Dark Overlay -->
                <div class="fashion-hero-overlay"></div>

                <!-- Content -->
                <div class="fashion-hero-content">
                    <h1 class="fashion-hero-title"><?php _e('Fashioned for Your Energy', 'My-E-Shop'); ?></h1>
                    <p class="fashion-hero-subtitle"><?php _e('Designer T-shirts inspired by the catwalk and made for self-expression', 'My-E-Shop'); ?></p>
                    <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="fashion-hero-btn">
                        <?php _e('Shop Now', 'My-E-Shop'); ?>
                    </a>
                </div>
            </div>
        </section>

        <!-- New WooCommerce Category Products Section -->
        <section class="category-products">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-12">
                        <h2 class="section-title">
                            <span><?php _e('SHOP BY CATEGORY', 'My-E-Shop' )?></span>
                        </h2>
                    </div>
                </div>
                
                <div class="row mb-4 category-grid">
                    <?php
                    // Get product categories
                    $categories = get_terms([
                        'taxonomy' => 'product_cat',
                        'hide_empty' => false, // Show all categories including empty ones
                        'exclude' => array(get_option('default_product_cat')), // Exclude uncategorized
                        'number' => 8, // Show more categories
                    ]);
                    
                    if (!empty($categories) && !is_wp_error($categories)) {
                        foreach ($categories as $category) {
                            // Get category thumbnail
                            $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                            $image = wp_get_attachment_url($thumbnail_id);
                            
                            if (!$image) {
                                // Use placeholder if no image is available
                                $image = wc_placeholder_img_src('medium');
                            }
                            ?>
                            
                            <div class="col-lg-3 col-md-4 col-6 mb-4">
                                <div class="category-card">
                                    <a href="<?php echo get_term_link($category); ?>" class="category-link">
                                        <div class="category-thumb">
                                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($category->name); ?>" class="img-fluid">
                                        </div>
                                        <div class="category-info text-center">
                                            <h3 class="category-title"><?php echo esc_html($category->name); ?></h3>
                                            <p class="category-count">
                                                <?php echo sprintf(_n('%s product', '%s products', $category->count, 'My-E-Shop'), $category->count); ?>
                                            </p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </section><!-- Category Products -->
        <section class="collections">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-12">
                        <h2 class="section-title">
                            <span>EXPLORE COLLECTIONS</span>
                        </h2>
                    </div>
                </div>
                
                <!-- First Collection Row -->
                <div class="collections-row">
                    <!-- Main Hero Image -->
                    <div class="collections-main-image">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/collections-1.jpg" alt="Furniture Collection" class="collections-hero-img">
                        <div class="collections-image-overlay">
                            <div class="collections-image-content">
                                <h3 class="collections-image-title">FURNITURE COLLECTION</h3>
                                <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="collections-image-btn">
                                    <?php _e('DISCOVER ALL', 'My-E-Shop'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Horizontal Slider -->
                    <!-- <div class="collections-slider-section">
                        <h4 class="collections-slider-title">INTERIOR SOLUTIONS</h4>
                        <div class="collections-horizontal-carousel owl-carousel owl-theme" id="collections-slider-1">
                            <!-- Card 1 -->
                            <!-- <div class="collections-square-card">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-1.jpg" alt="Living Room" class="collections-square-image">
                            </div>                            -->
                            <!-- Card 2 -->
                            <!-- <div class="collections-square-card">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-2.jpg" alt="Bedroom" class="collections-square-image">
                            </div>                             -->
                            <!-- Card 3 -->
                            <!-- <div class="collections-square-card">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-3.jpg" alt="Kitchen" class="collections-square-image">
                            </div>                            -->
                            <!-- Card 4 -->
                            <!-- <div class="collections-square-card">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-4.jpg" alt="Office" class="collections-square-image">
                            </div>                            -->
                            <!-- Card 5 -->
                            <!-- <div class="collections-square-card">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-5.jpg" alt="Storage" class="collections-square-image">
                            </div>                          -->
                            <!-- Card 6 -->
                            <!-- <div class="collections-square-card">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-6.jpg" alt="Decor" class="collections-square-image">
                            </div> -->
                            <!-- Card 7 -->
                            <!-- <div class="collections-square-card">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-7.jpg" alt="Decor" class="collections-square-image">
                            </div> -->
                            <!-- Card 8 -->
                            <!-- <div class="collections-square-card">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-8.jpg" alt="Decor" class="collections-square-image">
                        </div>
                    </div>
                    </div>
                     -->

                    <!-- Horizontal Slider -->
                    <div class="collections-slider-section">
                        <h4 class="collections-slider-title">INTERIOR SOLUTIONS</h4>
                        <div class="collections-horizontal-carousel">
                            <div class="collections-smooth-track">
                                <!-- Оригинальные карточки -->
                                <div class="collections-square-card">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-1.jpg" alt="Living Room" class="collections-square-image">
                                </div>
                                <div class="collections-square-card">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-2.jpg" alt="Bedroom" class="collections-square-image">
                                </div>
                                <div class="collections-square-card">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-3.jpg" alt="Kitchen" class="collections-square-image">
                                </div>
                                <div class="collections-square-card">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-4.jpg" alt="Office" class="collections-square-image">
                                </div>
                                <div class="collections-square-card">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-5.jpg" alt="Storage" class="collections-square-image">
                                </div>
                                <div class="collections-square-card">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-6.jpg" alt="Decor" class="collections-square-image">
                                </div>
                                <div class="collections-square-card">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-7.jpg" alt="Decor" class="collections-square-image">
                                </div>
                                <div class="collections-square-card">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-8.jpg" alt="Decor" class="collections-square-image">
                                </div>
                                
                                <!-- Дублированные карточки для бесшовной петли -->
                                <div class="collections-square-card">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-1.jpg" alt="Living Room" class="collections-square-image">
                                </div>
                                <div class="collections-square-card">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-2.jpg" alt="Bedroom" class="collections-square-image">
                                </div>
                                <div class="collections-square-card">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-3.jpg" alt="Kitchen" class="collections-square-image">
                                </div>
                                <div class="collections-square-card">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-4.jpg" alt="Office" class="collections-square-image">
                                </div>
                                <div class="collections-square-card">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-5.jpg" alt="Storage" class="collections-square-image">
                                </div>
                                <div class="collections-square-card">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-6.jpg" alt="Decor" class="collections-square-image">
                                </div>
                                <div class="collections-square-card">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-7.jpg" alt="Decor" class="collections-square-image">
                                </div>
                                <div class="collections-square-card">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-8.jpg" alt="Decor" class="collections-square-image">
                                </div>
                            </div>
                        </div>
                    </div>
                
                <!-- Second Collection Row -->
                <div class="collections-row">
                    <!-- Main Hero Image -->
                    <div class="collections-main-image">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/collections-2.jpg" alt="Lighting Collection" class="collections-hero-img">
                        <div class="collections-image-overlay">
                            <div class="collections-image-content">
                                <h3 class="collections-image-title">EXPLORE NEW LIGHTING</h3>
                                <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="collections-image-btn">
                                    <?php _e('DISCOVER ALL', 'My-E-Shop'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Horizontal Slider -->
                    <div class="collections-slider-section">
                        <h4 class="collections-slider-title">LIGHTING SOLUTIONS</h4>
                        <div class="collections-horizontal-carousel owl-carousel owl-theme" id="collections-slider-2">
                            <!-- Card 1 -->
                            <div class="collections-square-card">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-2-1.jpg" alt="Table Lamp" class="collections-square-image">
                            </div>                           
                            <!-- Card 2 -->
                            <div class="collections-square-card">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-2-2.jpg" alt="Wall Light" class="collections-square-image">
                            </div>  
                            <!-- Card 3 -->
                            <div class="collections-square-card">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-2-3.jpg" alt="Ceiling Light" class="collections-square-image">
                            </div>
                            <!-- Card 4 -->
                            <div class="collections-square-card">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-2-4.jpg" alt="Floor Lamp" class="collections-square-image">
                            </div>
                            
                            <!-- Card 5 -->
                            <div class="collections-square-card">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-2-5.jpg" alt="LED Strips" class="collections-square-image">
                            </div>
                            
                            <!-- Card 6 -->
                            <div class="collections-square-card">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-2-6.jpg" alt="Smart Lights" class="collections-square-image">
                            </div>
                            <!-- Card 7 -->
                            <div class="collections-square-card">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-2-7.jpg" alt="Smart Lights" class="collections-square-image">
                            </div>
                            <!-- Card 8 -->
                            <div class="collections-square-card">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-2-8.jpg" alt="Smart Lights" class="collections-square-image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="featured-products">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-12">
                    <h2 class="section-title">
                        <span><?php _e('Featured products', 'My-E-Shop' )?></span>
                    </h2>
                    </div>
                </div>

                <?php echo do_shortcode('[featured_products]')?> 
                   <!-- <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                        <div class="product-card">
                            <div class="product-card-offer">
                                <div class="offer-hit">Hit</div>
                                <div class="offer-new">New</div>
                            </div>
                            <div class="product-thumb">
                                <a href="product.html"><img src="<?php echo get_template_directory_uri() ?>/assets/img/Mens-Winter-Boots-Tactical-Military-Shoes-ORTOREX-khaki-jpg.webp" alt=""></a>
                            </div>
                            <div class="products-details">
                                <h4>
                                    <a href="products.html">Product 1 some text some title</a>
                                </h4>
                                <p class="product-exerpt">
                                    some text some text description
                                </p>
                                <div class="product-bottom-details d-flex justify-content-between">
                                    <div class="product-price">
                                        <small>$70</small>
                                        $65
                                    </div>
                                    <div class="product-links">
                                        <a href="#" class="btn btn-outline-secondary add-to-cart"><i class="fas fa-shopping-cart"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>-->
        </section><!-- Featured products-->
        <section class="new-products">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-12">
                    <h2 class="section-title">
                        <span><?php _e('Our Best Selling Products', 'My-E-Shop')?></span>
                    </h2>
                    </div>
                </div>

                <?php echo do_shortcode( '[my_e_shop_recent_products limit="8"]')?>
                <!--<div class="owl-carousel owl-theme owl-carousel-full">
                    <div class="product-card">
                        <div class="product-card-offer">
                            <div class="offer-hit">Hit</div>
                            <div class="offer-new">New</div>
                        </div>
                        <div class="product-thumb">
                            <a href="product.html"><img src="<?php echo get_template_directory_uri() ?>/assets/img/Womens-Winter-Boots-Warm-Snow-Shoes-ORTOREX-5-jpg.webp" alt=""></a>
                        </div>
                        <div class="products-details">
                            <h4>
                                <a href="products.html">Product 8 some text some title</a>
                            </h4>
                            <p class="product-exerpt">
                                some text some text description
                            </p>
                            <div class="product-bottom-details d-flex justify-content-between">
                                <div class="product-price">
                                    <small>$90</small>
                                    $45
                                </div>
                                <div class="product-links">
                                    <a href="#" class="btn btn-outline-secondary add-to-cart"><i class="fas fa-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <-!--Owl Carousel-->
            </div>
        </section><!-- Best-sellers-->
        <section class="about-us">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-12">
                    <h2 class="section-title">
                        <span>About Us</span>
                    </h2>
                    <div class="row">
                        <div class="col-12">
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatibus sed sit repellat a. Vel pariatur recusandae nobis necessitatibus laudantium quasi, ipsa sapiente impedit nulla, omnis architecto alias fugit porro eligendi.</p>
                            <p>Quo fugiat exercitationem repellendus expedita quam consectetur a temporibus voluptatibus voluptatum odit reiciendis nesciunt, autem quas perferendis. Sunt quia maxime asperiores sapiente enim, impedit consequatur recusandae nemo saepe debitis. Voluptatum!</p>
                            <p>Reprehenderit amet corporis ut hic accusantium dolorem fugiat repellat nisi, omnis error sunt, nam dolores animi magni est fuga facere dicta debitis quibusdam distinctio veritatis reiciendis maiores dolorum at. Blanditiis?</p>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </section><!-- About Us-->
    </main>
<?php get_footer() ?>