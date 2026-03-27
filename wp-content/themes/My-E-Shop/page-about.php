<?php
/**
 * Template Name: About Page
 * Template Post Type: page
 * Description: Шаблон страницы "О нас" с hero блоком и хлебными крошками
 */

get_header();
?>

<main id="primary" class="site-main about-page">
    
    <?php
    while (have_posts()) :
        the_post();
        
        // Получаем контент страницы
        $content = get_the_content();
        
        // Разделяем контент на блоки БЕЗ применения фильтров
        $blocks = parse_blocks($content);
        
        $hero_blocks = [];
        $content_blocks = [];
        
        foreach ($blocks as $block) {
            // Если это about-hero блок, сохраняем отдельно
            if ($block['blockName'] === 'my-e-shop/about-hero') {
                $hero_blocks[] = $block;
            } else {
                // Остальные блоки идут в контейнер
                $content_blocks[] = $block;
            }
        }
        
        // Выводим hero блоки
        foreach ($hero_blocks as $hero_block) {
            echo render_block($hero_block);
        }
        ?>
        
        <!-- Хлебные крошки после hero блока -->
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
        
        // Выводим остальной контент в контейнере
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
