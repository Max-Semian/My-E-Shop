<!DOCTYPE html>
<html <?php language_attributes();?>>
<head>
    <meta charset="<?php bloginfo( 'charset')?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php wp_head(); ?>
</head>
<body <?php body_class();?>>
    <?php wp_body_open();?>
    <header class="header">

        <div class="top-line">
            <div class="container">
                <div class="d-flex">
                    <div class="text-center flex-grow-1">
                        Sale 10% by promocode "Zamok" on All Orders!
                    </div>
                    <div class="callback">
                        <a href="#"> Call Back</a>
                    </div>
                </div>
            </div>
        </div> <!-- ./top-line -->
        <nav class="navbar navbar-expand-lg top-navbar">
            <div class="container">
              <a class="navbar-brand" href="<?php echo home_url('/')?>">
                <img src="<?php echo get_template_directory_uri() ?>/assets/img/Logo.svg" alt="Logo">
              </a>
              <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="offcanvas offcanvas-start" id="offcanvasNavbar">

                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                  </div>
                  <div class="offcanvas-body">
                    <?php
                    wp_nav_menu(array(
                            'theme_location' => 'header-menu',
                            'container' => false,
                            'menu_class' => 'navbar-nav',
                            'walker' => new My_E_Shop_Header_Menu(),
                    ));
                    ?>
                 <!--<ul class="navbar-nav me-lg-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown has-megamenu">
                          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                              Catalog
                          </a>
                          <div class="container dropdown-menu megamenu animate slideIn" role="menu">
                              <div class="row g-3">
                                  <div class="col-lg-3 col-6">
                                      <h6 class="title">Electonic Locks</h6>
                                      <ul class="list-unstyled">
                                          <li><a href="category.html">Locks</a></li>
                                          <li><a href="category.html">Big Locks</a></li>
                                          <li><a href="category.html">Very Big Locks</a></li>
                                          <li><a href="category.html">Locks for Office</a></li>
                                          <li><a href="category.html">Home Locks</a></li>
                                          <li><a href="category.html">Locks for Hotels</a></li>
                                          <li><a href="category.html">Locks for Bikes</a></li>
                                      </ul>
                                  </div> 
                                  <div class="col-lg-3 col-6">
                                      <h6 class="title">Decorative Locks</h6>
                                      <ul class="list-unstyled">
                                          <li><a href="category.html">Indoor Locks</a></li>
                                          <li><a href="category.html">Outdoor Locks</a></li>
                                          <li><a href="category.html">Apartment Locks</a></li>
                                          <li><a href="category.html">Indoor Locks</a></li>
                                          <li><a href="category.html">Indoor Locks</a></li>
                                      </ul>
                                  </div> 
                                  <div class="col-lg-3 col-6">
                                      <h6 class="title">Ultralight Locks</h6>
                                      <ul class="list-unstyled">
                                          <li><a href="category.html">Indoor Locks</a></li>
                                          <li><a href="category.html">Outdoor Locks</a></li>
                                          <li><a href="category.html">Apartment Locks</a></li>
                                          <li><a href="category.html">Indoor Locks</a></li>
                                          <li><a href="category.html">Indoor Locks</a></li>
                                      </ul>
                                  </div>
                              </div>
                          </div>
                    </ul>-->
                    <div class="mobile-phones d-block d-lg-none">
                        <ul>
                            <li class="mobile-phones-phone">
                                <i class="fa-solid fa-phone"></i>
                                <a href="tel:+12345678900">+1(123)678-900</a>
                            </li>
                            <li class="mobile-phones-callback">
                                <a href="#">Call Back</a>
                            </li>
                        </ul>
                    </div> <!-- ./mobile-phones-->
                  </div><!-- ./offcanvas -->
              </div> <!-- ./offcanvas navbar-collapse -->
              <div class="navbar-side">
                <ul>
                    <li class="navbar-phone">
                    <i class="fa-solid fa-phone"></i>
                        <a href="tel:+12345678900">+12345678900</a>
                    </li>
                    <li>
                        <a href="#"><i class="fa-regular fa-heart"></i></a>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-regular fa-user"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end animate slideIn">
                            <li><a href="#" class="dropdown-item">Registration</a></li>
                            <li><a href="#" class="dropdown-item">Log In</a></li>
                            <li><a href="#" class="dropdown-item">My Account</a></li>
                        </ul> 
                    </li>
                </ul>
                <form action="" class="search-form">
                    <input class="form-control" name="s" placeholder="Search Product..." required>
                    <button id="search-form-btn" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
              </div><!-- navbar-side -->
            </div>
          </nav> <!-- Bootstrap Navbar-->
    </header>