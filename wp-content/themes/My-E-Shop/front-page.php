<?php get_header() ?>
<main class="main">
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

        
<!-- Вставьте этот код вместо вашего блока animated-text-section -->
        <style>
        /* Анимированный текстовый блок - встроенные стили */
        .animated-text-section {
            padding: 50px 0;
        }

        .animated-text-section .text-block {
            max-width: 1200px;
            margin: 0 auto;
            padding: 50px 40px;
            /* background: linear-gradient(135deg, #F4F0EB 0%, #FFFFFF 100%); */
            /* border: 3px solid #D85AFF; */
            /* box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); */
            position: relative;
            overflow: hidden;
            
            /* Начальное состояние - скрыто */
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        /* Анимированная граница */
        .animated-text-section .text-block::before {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            /* background: linear-gradient(45deg, #D85AFF, #a855f7, #D85AFF, #a855f7); */
            background-size: 400% 400%;
            z-index: -1;
            animation: borderPulse 4s ease infinite;
        }

        @keyframes borderPulse {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Состояние когда блок становится видимым */
        .animated-text-section .text-block.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Текст */
        .animated-text-section .text-content {
            font-size: 24px;
            font-weight: 600;
            color: #2C2C2C;
            line-height: 1.5;
            text-align: center;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            position: relative;
            z-index: 1;
        }

        /* Слова изначально скрыты */
        .animated-text-section .word {
            display: inline-block;
            opacity: 0;
            transform: translateY(30px);
            margin-right: 0.3em;
            transition: all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        /* Анимация появления слов */
        .animated-text-section .word.animate {
            opacity: 1;
            transform: translateY(0);
        }

        /* Hover эффект */
        .animated-text-section .text-block:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .animated-text-section .text-block.visible:hover {
            transform: translateY(-5px);
        }

        /* Мобильная адаптация */
        @media (max-width: 768px) {
            .animated-text-section {
                padding: 30px 0;
            }
            
            .animated-text-section .text-block {
                margin: 0 20px;
                padding: 30px 25px;
            }
            
            .animated-text-section .text-content {
                font-size: 20px;
                line-height: 1.4;
            }
        }

        @media (max-width: 480px) {
            .animated-text-section {
                padding: 20px 0;
            }
            
            .animated-text-section .text-block {
                margin: 0 15px;
                padding: 25px 20px;
            }
            
            .animated-text-section .text-content {
                font-size: 18px;
                line-height: 1.3;
            }
        }
        </style>

        <section class="animated-text-section">
            <div class="text-block" id="textBlock">
                <div class="text-content" id="textContent">
                    T-shirts that combine runway aesthetics with individuality. These are clothes for those who want to be on trend, express themselves and belong to a niche community - without overpaying for a brand.
                </div>
            </div>
        </section>

        <script>
        // Простая и надежная анимация текста при скролле
        (function() {
            'use strict';
            
            function initTextAnimation() {
                const textBlock = document.querySelector('.animated-text-section #textBlock');
                const textContent = document.querySelector('.animated-text-section #textContent');
                
                if (!textBlock || !textContent) {
                    console.log('Элементы анимации не найдены');
                    return;
                }
                
                console.log('Анимация текста инициализирована');
                
                // Разбиваем текст на слова
                function splitTextIntoWords() {
                    const text = textContent.textContent;
                    const words = text.split(' ');
                    textContent.innerHTML = '';
                    
                    words.forEach((word, index) => {
                        const span = document.createElement('span');
                        span.className = 'word';
                        span.textContent = word;
                        span.style.transitionDelay = `${index * 0.1}s`;
                        textContent.appendChild(span);
                        
                        // Добавляем пробел после каждого слова (кроме последнего)
                        if (index < words.length - 1) {
                            textContent.appendChild(document.createTextNode(' '));
                        }
                    });
                }
                
                // Проверяем видимость элемента
                function isElementVisible(element) {
                    const rect = element.getBoundingClientRect();
                    const windowHeight = window.innerHeight;
                    return rect.top < windowHeight * 0.75 && rect.bottom > 0;
                }
                
                // Главная функция анимации
                function handleScroll() {
                    if (isElementVisible(textBlock)) {
                        console.log('Блок стал видимым, запуск анимации');
                        
                        // Показываем блок
                        textBlock.classList.add('visible');
                        
                        // Через небольшую задержку анимируем слова
                        setTimeout(() => {
                            const words = textContent.querySelectorAll('.word');
                            words.forEach(word => {
                                word.classList.add('animate');
                            });
                        }, 400);
                        
                        // Убираем обработчик после первой анимации
                        window.removeEventListener('scroll', handleScroll);
                    }
                }
                
                // Инициализация
                splitTextIntoWords();
                
                // Проверяем сразу при загрузке
                setTimeout(handleScroll, 100);
                
                // Добавляем обработчик прокрутки
                window.addEventListener('scroll', handleScroll);
            }
            
            // Инициализация когда DOM готов
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initTextAnimation);
            } else {
                initTextAnimation();
            }
            
            // Дополнительная проверка через jQuery если доступен
            if (typeof jQuery !== 'undefined') {
                jQuery(document).ready(function($) {
                    setTimeout(initTextAnimation, 500);
                });
            }
        })();
        </script>


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
                    <div class="all-products-btn">
                        <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="btn btn-primary">
                            <?php _e('View All Products', 'My-E-Shop'); ?>
                        </a>
                    </div>
                </div>
        </section><!-- Best-sellers-->
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