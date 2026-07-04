<?php
/**
 * Template Name: About Page
 * Template Post Type: page
 * Description: About Us page template with a hero block and breadcrumbs
 */

get_header();
?>

<main id="primary" class="site-main about-page">
    
    <?php
    while (have_posts()) :
        the_post();
        
        // Get the page content
        $content = get_the_content();

        // Split the content into blocks WITHOUT applying filters
        $blocks = parse_blocks($content);

        $hero_blocks = [];
        $content_blocks = [];

        foreach ($blocks as $block) {
            // If this is an about-hero block, store it separately
            if ($block['blockName'] === 'my-e-shop/about-hero') {
                $hero_blocks[] = $block;
            } else {
                // The remaining blocks go into the container
                $content_blocks[] = $block;
            }
        }

        // Output the hero blocks
        foreach ($hero_blocks as $hero_block) {
            echo render_block($hero_block);
        }
        ?>

        <!-- Breadcrumbs after the hero block -->
        <div class="about-page-breadcrumbs-wrapper">
            <div class="container">
                <?php if (function_exists('yoast_breadcrumb')) : ?>
                    <?php yoast_breadcrumb('<nav class="breadcrumbs">', '</nav>'); ?>
                <?php elseif (function_exists('bcn_display')) : ?>
                    <nav class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
                        <?php bcn_display(); ?>
                    </nav>
                <?php else : ?>
                    <?php 
                    $current_object = get_queried_object();
                    $page_title = '';
                    
                    if ($current_object && isset($current_object->post_title)) {
                        $page_title = $current_object->post_title;
                    } elseif (is_page()) {
                        global $post;
                        $page_title = $post->post_title;
                    }
                    ?>
                    <nav class="breadcrumbs">
                        <a href="<?php echo esc_url(home_url('/')); ?>">HOME</a>
                        <span class="current"><?php echo $page_title ? strtoupper(esc_html($page_title)) : 'ABOUT US'; ?></span>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
        
        <?php
        
        // Output the remaining content in the container
        if (!empty($content_blocks)) :
        ?>
        <div class="about-page-content-wrapper">
            <div class="container">
                <?php 
                foreach ($content_blocks as $content_block) {
                    echo render_block($content_block);
                }
                ?>
            </div>
        </div>
        <?php
        endif;
        
    endwhile;
    ?>
    
</main>

<?php
get_footer();
