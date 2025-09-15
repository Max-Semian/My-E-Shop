<?php get_header() ?>
<main class="main">
        <!-- Fashion Hero Section -->
        <!-- <section class="fashion-hero-section">
            <div class="fashion-hero-container">
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
                <div class="fashion-hero-overlay"></div>

        
                <div class="fashion-hero-content">
                    <h1 class="fashion-hero-title"><?php _e('Fashioned for Your Energy', 'My-E-Shop'); ?></h1>
                    <p class="fashion-hero-subtitle"><?php _e('Designer T-shirts inspired by the catwalk and made for self-expression', 'My-E-Shop'); ?></p>
                    <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="fashion-hero-btn">
                        <?php _e('Shop Now', 'My-E-Shop'); ?>
                    </a>
                </div>
            </div>
        </section> -->

        <!-- Эта секция заменена на Gutenberg блок "Animated Text Block" -->
        <!-- Теперь вы можете добавить блок через редактор WordPress -->
        <!-- <section class="animated-text-section">
            <div class="text-block" id="textBlock">
                <div class="text-content" id="textContent">
                    T-shirts that combine runway aesthetics with individuality. These are clothes for those who want to be on trend, express themselves and belong to a niche community - without overpaying for a brand.
                </div>
            </div>
        </section> -->


        <!-- New WooCommerce Category Products Section -->
        <section class="category-products">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-12">
                        <h2 class="section-title">
                            <span><?php _e('Сollection', 'My-E-Shop' )?></span>
                        </h2>
                        <p class="section-description">
                            Wear your mark. Feel your power
                        </p>
                    </div>
                </div>
                
            <div class="category-grid">
                <?php
                // Определяем массив ID категорий, которые нужно вывести
                $specific_category_ids = array(21, 39, 40, 36); // Замени на реальные ID твоих категорий

                // Получаем объекты категорий по их ID
                $categories_to_display = get_terms([
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => false, // Всегда показываем, даже если нет товаров
                    'include'    => $specific_category_ids, // Включаем только указанные ID
                    'orderby'    => 'include', // Важно: сохраняет порядок, как в массиве $specific_category_ids
                ]);

                // Проверяем, что категории найдены и нет ошибок
                if (!empty($categories_to_display) && !is_wp_error($categories_to_display)) {

                    // Добавляем проверку и сортировку, чтобы категории отображались в заданном порядке
                    $ordered_categories = [];
                    foreach ($specific_category_ids as $cat_id) {
                        foreach ($categories_to_display as $category) {
                            if ($category->term_id == $cat_id) {
                                $ordered_categories[] = $category;
                                break;
                            }
                        }
                    }

                    foreach ($ordered_categories as $category) {
                        // Get category thumbnail
                        $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                        $image = wp_get_attachment_url($thumbnail_id);

                        if (!$image) {
                            // Use placeholder if no image is available
                            $image = wc_placeholder_img_src('medium');
                        }

                        // Если вы добавили поля через functions.php:
                         $button_color = get_term_meta($category->term_id, 'category_button_color', true);
                         $button_icon = get_term_meta($category->term_id, 'category_button_icon', true);

                        // Устанавливаем значения по умолчанию, если поля не заполнены
                        if (empty($button_color)) {
                            $button_color = '#4a4a4a'; // Дефолтный цвет фона
                        }
                        $button_shadow_color = '#c58aff'; // Цвет тени (можно сделать отдельным полем или фиксированным)

                        // --- Конец блока для получения кастомных полей ---
                        ?>

                        <div class="category-card">
                            <a href="<?php echo esc_url(get_term_link($category)); ?>" class="category-link">
                                <div class="category-thumb">
                                    <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($category->name); ?>" class="img-fluid">
                                </div>
                                <div class="category-info text-center">
                                    <span
                                        class="category-title"
                                        style="
                                            color: <?php echo esc_attr($button_color); ?>; /* Цвет текста берется из мета-поля */
                                            text-shadow: 0 0 5px <?php echo esc_attr($button_color); ?>; /* Свечение берется из того же мета-поля */
                                        "
                                    >
                                        <i class="<?php echo esc_attr($button_icon); ?>"></i> <?php echo esc_html($category->name); ?>
                                    </span>
                                </div>
                            </a>
                        </div>

                        <?php
                    }
                }
                ?>
            </div>
        </section><!-- Category Products -->
        <section class="new-products">
                <div class="container">
                    <div class="row mb-5">
                        <div class="col-12">
                        <h2 class="section-title">
                            <span><?php _e('Bestsellers', 'My-E-Shop')?></span>
                        </h2>
                        <p class="section-description">
                            Your aura in cotton
                        </p>
                        </div>
                    </div>
                    <?php echo do_shortcode( '[my_e_shop_recent_products limit="5"]')?>
                        <div class="trends-btn-container">
                            <a href="/shop/" class="trends-btn-link">All Products<span class="arrow">→</span></a>
                        </div>
                </div>
        </section><!-- Best-sellers-->
        <section class="about-brand" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/img/about-brand-bg.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; height: 670px; display: flex; align-items: center;">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="about-content">
                            <h2 class="about-section-title">
                                <span>About the brand</span>
                            </h2>
                            <p class="brand-description">
                                We combine love for fashion and individuality. Our prints are manifestos about inner worlds, protest and dreams. Find out how it is born "Geetho"
                            </p>
                            <a href="#" class="btn-link">
                                OUR STORY <span class="arrow">→</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section><!-- About the brand-->

        <section class="collections">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-12">
                        <h2 class="section-title">
                            <span>Gallery of Inspiration</span>
                        </h2>
                        <p class="section-description">
                            <span>Create your own archetype lookbook</span>
                        </p>
                    </div>
                </div>
                
                <!-- First Collection Row -->
                <div class="collections-row">
                    <!-- Main Hero Image -->
                    <!-- <div class="collections-main-image">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/collections-1.jpg" alt="Furniture Collection" class="collections-hero-img">
                        <div class="collections-image-overlay">
                            <div class="collections-image-content">
                            <h2 class="section-title">
                                <span>EXPLORE COLLECTIONS</span>
                            </h2>
                                <h3 class="collections-image-title">FURNITURE COLLECTION</h3>
                                <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="collections-image-btn">
                                    <?php _e('DISCOVER ALL', 'My-E-Shop'); ?>
                                </a>
                            </div>
                        </div>
                    </div> -->
                    
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
                        <!-- <h4 class="collections-slider-title">INTERIOR SOLUTIONS</h4> -->
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
            </div>
        </section><!-- Gallery of Inspiration-->

        <section class="why-choose-us">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2 class="about-section-title text-center">Why choose us</h2>
                    </div>
                </div>
                <div class="row features-row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="feature-item text-center">
                            <div class="feature-icon">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/Why-Us-icon-1.png" alt="Runway inspiration, wearable style">
                            </div>
                            <h3 class="feature-title">Runway inspiration, wearable style</h3>
                            <p class="feature-description">
                                Each T-shirt is an art manifesto with
                                designer method by the street fashion
                                trends and niche aesthetics. Wear fashion
                                that speaks for you.
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="feature-item text-center">
                            <div class="feature-icon">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/Why-Us-icon-2.png" alt="Runway inspiration, wearable style">
                            </div>
                            <h3 class="feature-title">Meaningful quality in every detail</h3>
                            <p class="feature-description">
                                Soft and breathable cotton, eco-friendly
                                dyes that carefully crafted prints - because
                                the quality of your clothes reflects your
                                values.
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="feature-item text-center">
                            <div class="feature-icon">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/Why-Us-icon-3.png" alt="Runway inspiration, wearable style">
                            </div>
                            <h3 class="feature-title">Uniqueness without overpaying</h3>
                            <p class="feature-description">
                                Our collections are produced in small
                                editions. Create your unique fashion story
                                without the markup of luxury brands.
                                Express yourself, be on trend and save your
                                budget.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section><!-- Why choose Us-->
        
        <section class="trends">
                <div class="container">
                    <div class="row mb-5">
                        <div class="col-12">
                            <h2 class="section-title">
                                <span><?php _e('Trends', 'My-E-Shop')?></span>
                            </h2>
                            <p class="section-description">
                                Ride the wave of the future
                            </p>
                        </div>
                    </div>
                    <?php echo do_shortcode('[blog_grid posts="2" show_excerpt="true" columns="2"]'); ?>
                    <div class="trends-btn-container">
                        <a href="/blog/" class="trends-btn-link">SEE MORE<span class="arrow">→</span></a>
                    </div>
                </div>
        </section>
        <section class="newsletter-subscription" style="background-color: #F4F0EB;">
            <div class="container">
                <div class="newsletter-content">
                    <h2 class="newsletter-title">
                        Join our world and get 5% off your first order
                    </h2>
                    <p class="newsletter-subtitle">
                        Stay updated with new drops, visual stories & rare finds before anyone else
                    </p>
                    
                    <div class="success-message" id="success-message">
                        Thank you for subscribing! Check your email for the discount code.
                    </div>
                    
                    <div class="error-message" id="error-message">
                        Please enter a valid email address.
                    </div>
                    
                    <form class="newsletter-form" onsubmit="handleSubmit(event)">
                        <div class="form-group">
                            <input 
                                type="email" 
                                id="newsletter-email"
                                class="newsletter-input" 
                                placeholder="Enter your email here" 
                                required
                            >
                            <button 
                                type="submit" 
                                class="newsletter-button"
                            >
                                JOIN NOW
                            </button>
                        </div>
                    </form>
                    
                    <p>
                        We only send thoughtful emails - no spam, just style. By subscribing, you agree to receive inspiration and exclusive privileges. Your data is safe with us.
                    </p>
                </div>
            </div>
        </section>
    </main>
<?php get_footer() ?>