<?php
/**
 * Template Name: Collection Page
 * Description: Template for the Collection page without theme selection
 */

get_header(); ?>

<main id="primary" class="site-main page-collection" style="background-color: #F4F0EB;">
    
    <?php
    while (have_posts()) :
        the_post();
        
        // Get the page content
        $content = get_the_content();

        // Split the content into blocks
        $blocks = parse_blocks($content);

        $hero_blocks = [];
        $content_blocks = [];

        foreach ($blocks as $block) {
            // Check for hero blocks (e.g. fashion-hero or other hero blocks)
            if (strpos($block['blockName'], 'hero') !== false) {
                $hero_blocks[] = $block;
            } else {
                // The remaining blocks go into the container
                $content_blocks[] = $block;
            }
        }

        // Output the hero blocks full width
        foreach ($hero_blocks as $hero_block) {
            echo render_block($hero_block);
        }
        ?>
        <?php
        // Output the remaining content in the container if there are non-hero blocks
        if (!empty($content_blocks)) :
        ?>
        <div class="page-content-wrapper">
            <div class="container">
                <?php
                // If this is a WooCommerce page or contains other shortcodes, use the_content()
                if (has_shortcode($content, 'woocommerce_my_account') || 
                    has_shortcode($content, 'woocommerce_checkout') || 
                    has_shortcode($content, 'woocommerce_cart') ||
                    has_shortcode($content, 'blog_grid')) {
                    the_content();
                } else {
                    foreach ($content_blocks as $content_block) {
                        echo render_block($content_block);
                    }
                }
                ?>
                
                <?php
                wp_link_pages(array(
                    'before' => '<div class="page-links">' . __('Pages:', 'my-e-shop'),
                    'after'  => '</div>',
                ));
                ?>
                
                <?php
                // If comments are open or there is at least one comment
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;
                ?>
            </div>
        </div>
        <?php endif; ?>

    <?php endwhile; ?>
    
</main>

<?php get_footer(); ?>
