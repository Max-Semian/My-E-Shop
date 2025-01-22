<?php get_header() ?>
    <main class="main">
        <section class="slider">
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
                    <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row">
                            <div class="col-md-6">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/slider-1.webp" class="d-block w-100" alt="">
                            </div>
                            <div class="col-md-6">
                                <div class="carousel-text">
                                    <h5>First slide label</h5>
                                    <div class="carousel-desc">
                                        <p>Some representative placeholder content for the first slide.</p>
                                    </div>
                                    <div class="carousel-price">
                                        <strong>$330</strong>
                                        <del>$380</del>
                                        <p class="carousel-btn"><a href="#" class="btn btn-primary">Buy Now</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="row">
                            <div class="col-md-6">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/slider-2.png" class="d-block w-100" alt="">
                            </div>
                            <div class="col-md-6">
                                <div class="carousel-text">
                                    <h5>First slide label</h5>
                                    <div class="carousel-desc">
                                        <p>Some representative placeholder content for the first slide.</p>
                                    </div>
                                    <div class="carousel-price">
                                        <strong>$330</strong>
                                        <del>$380</del>
                                        <p class="carousel-btn"><a href="#" class="btn btn-primary">Buy Now</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="row">
                            <div class="col-md-6">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/slider-3.webp" class="d-block w-100" alt="">
                            </div>
                            <div class="col-md-6">
                                <div class="carousel-text">
                                    <h5>First slide label</h5>
                                    <div class="carousel-desc">
                                        <p>Some representative placeholder content for the first slide.</p>
                                    </div>
                                    <div class="carousel-price">
                                        <strong>$330</strong>
                                        <del>$380</del>
                                        <p class="carousel-btn"><a href="#" class="btn btn-primary">Buy Now</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </section> <!-- Slider-->
        <section class="advantages">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2 class="section-title">
                            <span>Our Advantages</span>   
                        </h2>
                    </div>
                </div>
                    <div class="row gy-3 items">
                        <div class="col-lg-3 col-sm-6">
                            <div class="item">
                                <p><i class="fas fa-shipping-fast"></i></p>
                                <p>Some text some text some text</p>
                            </div>
                        </div> 
                        <div class="col-lg-3 col-sm-6">
                            <div class="item">
                                <p><i class="fas fa-cubes"></i></p>
                                <p>Some text some text some text</p>
                            </div>
                        </div> 
                        <div class="col-lg-3 col-sm-6">
                            <div class="item">
                                <p><i class="fas fa-hand-holding-usd"></i></p>
                                <p>Some text some text some text</p>
                            </div>
                        </div> 
                        <div class="col-lg-3 col-sm-6">
                            <div class="item">
                                <p><i class="fas fa-user-graduate"></i></p>
                                <p>Some text some text some text</p>
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
            </div>
        </section>-->
        <section class="new-products">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-12">
                    <h2 class="section-title">
                        <span><?php _e('Best Sellers', 'My-E-Shop')?></span>
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
                </div> <!--Owl Carousel-->
            </div>
        </section>
        <section class="about-us">
            <div class="container-fluid">
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
        </section>
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d30624.394926310808!2d108.21351700240915!3d16.068161842891225!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x314218358f50a1bb%3A0x4b451504a6ccd4df!2sCon%20Market!5e0!3m2!1sen!2s!4v1734585369473!5m2!1sen!2s" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" display: block;></iframe>
    </main>
<?php get_footer() ?>