<?php get_header(); ?>

<!-- Hero block -->
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <?php
    // Get the hero block data
    $hero_image_id = get_post_meta(get_the_ID(), '_hero_image', true);
    $hero_title = get_post_meta(get_the_ID(), '_hero_title', true);
    $hero_description = get_post_meta(get_the_ID(), '_hero_description', true);

    // If the hero title is not set, use the post title
    if (empty($hero_title)) {
        $hero_title = get_the_title();
    }

    // If the subtitle is not set manually, use a short excerpt of the article
    if (empty($hero_description)) {
        $hero_description = wp_trim_words(get_the_excerpt(), 14, '…');
    }

    // Article category for the tag above the title (except "Uncategorized")
    $hero_category = null;
    $post_cats = get_the_category();
    if (!empty($post_cats)) {
        foreach ($post_cats as $c) {
            if ($c->slug !== 'uncategorized') {
                $hero_category = $c;
                break;
            }
        }
    }
    
    // Determine the image for the hero block
    $hero_image_url = '';
    if ($hero_image_id) {
        $hero_image_url = wp_get_attachment_image_url($hero_image_id, 'full');
    } elseif (has_post_thumbnail()) {
        $hero_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
    }
    
    ?>

    <div class="post-hero<?php echo $hero_image_url ? ' has-image' : ''; ?>">
        <?php if ($hero_image_url) : ?>
            <img class="post-hero-img" src="<?php echo esc_url($hero_image_url); ?>" alt="<?php echo esc_attr($hero_title); ?>">
        <?php endif; ?>
        <div class="post-hero-content">
            <div class="post-container">
                <?php if ($hero_category) : ?>
                    <a class="post-hero-category" href="<?php echo esc_url(get_category_link($hero_category->term_id)); ?>">
                        <?php echo esc_html($hero_category->name); ?>
                    </a>
                <?php endif; ?>

                <h1 class="hero-title"><?php echo esc_html($hero_title); ?></h1>

                <div class="hero-info-line">
                    <?php if ($hero_description) : ?>
                        <span class="post-hero-description"><?php echo esc_html($hero_description); ?></span>
                    <?php endif; ?>

                    <span class="post-hero-meta">
                        <span class="meta-author">
                            <?php $post_author_id = (int) get_the_author_meta('ID'); ?>
                            <a class="meta-author-link" href="<?php echo esc_url(get_author_posts_url($post_author_id)); ?>">
                                <?php echo get_avatar($post_author_id, 48, '', get_the_author(), array('class' => 'meta-author-avatar')); ?>
                                <span class="meta-author-name"><?php printf(__('By %s', 'my-e-shop'), esc_html(get_the_author())); ?></span>
                            </a>
                        </span>
                        <span class="meta-date"><?php echo get_the_date(); ?></span>
                        <span class="meta-reading-time"><?php echo get_reading_time(); ?> min read</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumbs -->
    <div class="post-breadcrumbs">
        <div class="post-breadcrumbs-container">
            <nav class="breadcrumb-nav">
                <a href="<?php echo esc_url(home_url('/')); ?>">HOME</a>
                <span class="breadcrumb-separator">/</span>
                <a href="<?php echo esc_url(home_url('/blog/')); ?>">BLOG</a>
                <span class="breadcrumb-separator">/</span>
                <?php if ($hero_category) : ?>
                    <a href="<?php echo esc_url(get_category_link($hero_category->term_id)); ?>"><?php echo esc_html($hero_category->name); ?></a>
                    <span class="breadcrumb-separator">/</span>
                <?php endif; ?>
                <span class="breadcrumb-current"><?php the_title(); ?></span>
            </nav>
        </div>
    </div>

<div class="single-post-container">

        <!-- Post content -->
        <div class="post-container">
            <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>
                <div class="post-content">
                    <?php the_content(); ?>
                </div>

                <?php
                wp_link_pages(array(
                    'before' => '<div class="page-links">' . __('Pages:', 'my-e-shop'),
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
            // Navigation between posts
            the_post_navigation(array(
                'prev_text' => '<span class="nav-subtitle">' . __('Previous:', 'my-e-shop') . '</span> <span class="nav-title">%title</span>',
                'next_text' => '<span class="nav-subtitle">' . __('Next:', 'my-e-shop') . '</span> <span class="nav-title">%title</span>',
            ));
            ?>

            <?php
            // If comments are open or there is at least one comment
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            ?>
        </div>

    <?php endwhile; else : ?>
        <div class="container">
            <p><?php _e('No posts found.', 'my-e-shop'); ?></p>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
