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
                        <div class="nav-item dropdown">
                            <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="nav-link dropdown-toggle">
                                SHOP
                            </a>
                            <ul class="dropdown-menu shop-dropdown">
                                <li class="dropdown-item-wrapper">
                                    <a class="dropdown-item" href="<?php echo home_url('/product-category/witch-teaser/'); ?>">
                                    <div class="dropdown-icon">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Star.png" alt="Star" />
                                    </div>
                                        <span class="dropdown-text">Witch Core</span>
                                    </a>
                                </li>
                                <li class="dropdown-item-wrapper">
                                    <a class="dropdown-item" href="<?php echo home_url('/product-category/tender-thoughts'); ?>">
                                        <div class="dropdown-icon">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Heart.png" alt="Heart" />
                                        </div>
                                        <span class="dropdown-text">Tender Thoughts</span>
                                    </a>
                                </li>
                                <li class="dropdown-item-wrapper">
                                    <a class="dropdown-item" href="<?php echo home_url('/product-category/data-muse'); ?>">
                                        <div class="dropdown-icon">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Internet.png" alt="Internet" />
                                        </div>
                                        <span class="dropdown-text">Data Muse</span>
                                    </a>
                                </li>
                                <li class="dropdown-item-wrapper">
                                    <a class="dropdown-item" href="<?php echo home_url('/product-category/identity-in-action'); ?>">
                                        <div class="dropdown-icon">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Punch.png" alt="Punch" />
                                        </div>
                                        <span class="dropdown-text">Identity In Action</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <a href="<?php echo home_url('/blog'); ?>" class="nav-link">BLOG</a>
                        <a href="<?php echo home_url('/about'); ?>" class="nav-link">ABOUT US</a>
                    </nav>
                    
                    <!-- Иконки справа -->
                    <div class="header-icons">
                        <a href="<?php echo home_url('/search'); ?>" class="header-icon">
                            <i class="fas fa-search"></i>
                        </a>
                        <a href="<?php echo home_url('/account'); ?>" class="header-icon">
                            <i class="fas fa-user"></i>
                        </a>
                        <a href="<?php echo home_url('/wishlist'); ?>" class="header-icon">
                            <i class="fas fa-heart"></i>
                        </a>
                        <a href="<?php echo get_permalink(wc_get_page_id('cart')); ?>" class="header-icon">
                            <i class="fas fa-shopping-bag"></i>
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
                <a class="navbar-brand" href="<?php echo home_url('/')?>">
                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/Logo-Cretho.png" alt="logo">
                </a>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">×</button>
            </div>
            <div class="offcanvas-body">
                <ul class="mobile-nav-menu">
                    <li class="mobile-nav-item">
                        <a href="<?php echo home_url('/') ?>">Home</a>
                    </li>
                    <li class="mobile-nav-item">
                        <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>">Shop</a>
                    </li>
                    <li class="mobile-nav-item">
                        <a href="<?php echo home_url('/blog') ?>">Blog</a>
                    </li>
                    <li class="mobile-nav-item">
                        <a href="<?php echo home_url('/about') ?>">About Us</a>
                    </li>
                    <li class="mobile-nav-item">
                        <a href="<?php echo get_permalink(wc_get_page_id('cart')); ?>">Cart</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>