<?php get_header(); ?>

<div class="single-post-container">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        
        <!-- Хлебные крошки -->
        <div class="post-breadcrumbs">
            <div class="container">
                <nav class="breadcrumb-nav">
                    <a href="<?php echo esc_url(home_url('/')); ?>">Главная</a>
                    <span class="breadcrumb-separator">/</span>
                    
                    <?php
                    // Получаем категории поста
                    $categories = get_the_category();
                    if (!empty($categories)) {
                        $category = $categories[0];
                        
                        // Если есть родительская категория
                        if ($category->parent) {
                            $parent_category = get_category($category->parent);
                            if ($parent_category && !is_wp_error($parent_category)) {
                                echo '<a href="' . esc_url(get_category_link($parent_category->term_id)) . '">' . esc_html($parent_category->name) . '</a>';
                                echo '<span class="breadcrumb-separator">/</span>';
                            }
                        }
                        
                        // Текущая категория
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                        echo '<span class="breadcrumb-separator">/</span>';
                    }
                    ?>
                    
                    <span class="breadcrumb-current"><?php the_title(); ?></span>
                </nav>
            </div>
        </div>

        <!-- Контент поста -->
        <div class="container">
            <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>
                <header class="post-header">
                    <h1 class="post-title"><?php the_title(); ?></h1>
                    
                    <div class="post-meta">
                        <span class="post-date"><?php echo get_the_date(); ?></span>
                        <?php if (!empty($categories)) : ?>
                            <span class="post-categories">
                                <?php foreach ($categories as $cat) : ?>
                                    <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>"><?php echo esc_html($cat->name); ?></a>
                                <?php endforeach; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </header>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-thumbnail">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>

                <div class="post-content">
                    <?php the_content(); ?>
                </div>

                <?php
                wp_link_pages(array(
                    'before' => '<div class="page-links">' . __('Страницы:', 'my-e-shop'),
                    'after'  => '</div>',
                ));
                ?>

                <?php if (get_the_tags()) : ?>
                    <div class="post-tags">
                        <?php the_tags('', ', ', ''); ?>
                    </div>
                <?php endif; ?>
            </article>

            <?php
            // Навигация между постами
            the_post_navigation(array(
                'prev_text' => '<span class="nav-subtitle">' . __('Предыдущий:', 'my-e-shop') . '</span> <span class="nav-title">%title</span>',
                'next_text' => '<span class="nav-subtitle">' . __('Следующий:', 'my-e-shop') . '</span> <span class="nav-title">%title</span>',
            ));
            ?>

            <?php
            // Если комментарии открыты или есть хотя бы один комментарий
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            ?>
        </div>

    <?php endwhile; else : ?>
        <div class="container">
            <p><?php _e('Запись не найдена.', 'my-e-shop'); ?></p>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
