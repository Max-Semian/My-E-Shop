<!DOCTYPE html>
<html <?php language_attributes();?>>
<head>
    <meta charset="<?php bloginfo( 'charset')?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
</head>
<body <?php body_class();?>>
    <?php wp_body_open();?>
    
    <header class="header header-transparent">
        <!-- Single Header Bar -->
        <div class="main-header-bar">
            <div class="container">
                <div class="header-content">
                    <!-- Логотип слева -->
                    <a class="navbar-brand" href="<?php echo home_url('/')?>">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/Logo.png" alt="logo">
                    </a>
                    
                    <!-- Навигация по центру -->
                    <nav class="navbar-nav d-none d-lg-flex">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'header-menu',
                            'container' => false,
                            'items_wrap' => '%3$s',
                            'walker' => new My_E_Shop_Header_Walker(),
                            'fallback_cb' => false,
                        ));
                        ?>
                    </nav>
                    
                    <!-- Иконки справа -->
                    <div class="header-icons">
                        <a href="#" class="header-icon" onclick="openSearchModal(); return false;">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Search.svg" alt="Search" />
                        </a>
                        <a href="<?php echo is_user_logged_in() ? esc_url(wc_get_page_permalink('myaccount')) : '#'; ?>" class="header-icon" <?php echo !is_user_logged_in() ? 'onclick="openLoginModal(); return false;"' : ''; ?>>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Account.svg" alt="Account" />
                        </a>
                        <a href="<?php echo home_url('/wishlist'); ?>" class="header-icon">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/favourite.svg" alt="Wishlist" />
                        </a>
                        <a href="<?php echo get_permalink(wc_get_page_id('cart')); ?>" class="header-icon">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Cart.svg" alt="Cart" />
                        </a>
                        
                        <!-- Mobile toggle button -->
                        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header">
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">×</button>
            </div>
            <div class="offcanvas-body">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'header-menu',
                    'container' => false,
                    'menu_class' => 'mobile-nav-menu',
                    'walker' => new My_E_Shop_Mobile_Walker(),
                    'fallback_cb' => false,
                ));
                ?>
            </div>
        </div>
    </header>