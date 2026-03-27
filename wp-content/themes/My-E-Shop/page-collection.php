<?php
/**
 * Template Name: Collection Page
 * Description: Шаблон для страницы Collection без выбора темы
 */

get_header(); ?>

<main id="primary" class="site-main page-collection" style="background-color: #F4F0EB;">
    
    <?php
    while (have_posts()) :
        the_post();
        
        // Получаем контент страницы
        $content = get_the_content();
        
        // Разделяем контент на блоки
        $blocks = parse_blocks($content);
        
        $hero_blocks = [];
        $content_blocks = [];
        
        foreach ($blocks as $block) {
            // Проверяем на hero блоки (например, fashion-hero или другие hero блоки)
            if (strpos($block['blockName'], 'hero') !== false) {
                $hero_blocks[] = $block;
            } else {
                // Остальные блоки идут в контейнер
                $content_blocks[] = $block;
            }
        }
        
        // Выводим hero блоки на всю ширину
        foreach ($hero_blocks as $hero_block) {
            echo render_block($hero_block);
        }
        ?>
        <?php
        // Выводим остальной контент в контейнере, если есть не-hero блоки
        if (!empty($content_blocks)) :
        ?>
        <div class="page-content-wrapper">
            <div class="container">
                <?php 
                // Если это страница WooCommerce или содержит другие шорткоды, используем the_content()
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
                    'before' => '<div class="page-links">' . __('Страницы:', 'my-e-shop'),
                    'after'  => '</div>',
                ));
                ?>
                
                <?php
                // Если комментарии открыты или есть хотя бы один комментарий
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
