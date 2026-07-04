<?php
/**
 * Author archive template — author bio + their articles as cards.
 */

get_header();

$author = get_queried_object(); // WP_User
$author_id   = $author instanceof WP_User ? (int) $author->ID : (int) get_query_var('author');
$author_name = get_the_author_meta('display_name', $author_id);
$author_bio  = get_the_author_meta('description', $author_id);
$post_count  = (int) count_user_posts($author_id, 'post', true);
?>

<div class="author-page">

    <!-- Author hero -->
    <div class="author-hero">
        <div class="author-hero-inner">
            <div class="author-avatar">
                <?php echo get_avatar($author_id, 140, '', $author_name); ?>
            </div>
            <h1 class="author-name"><?php echo esc_html($author_name); ?></h1>
            <?php if ($author_bio) : ?>
                <p class="author-bio"><?php echo esc_html($author_bio); ?></p>
            <?php endif; ?>
            <span class="author-post-count">
                <?php printf(_n('%s article', '%s articles', $post_count, 'my-e-shop'), number_format_i18n($post_count)); ?>
            </span>
        </div>
    </div>

    <!-- Author articles -->
    <div class="author-posts">
        <div class="container">
            <?php if (have_posts()) : ?>
                <div class="blog-trends-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <div class="blog-trend-card">
                            <div class="blog-trend-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('large', array('alt' => get_the_title())); ?>
                                    <?php else : ?>
                                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/default-blog.jpg'); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                                    <?php endif; ?>
                                </a>
                            </div>

                            <div class="blog-trend-content">
                                <?php
                                $cats = get_the_category();
                                $primary_cat = null;
                                foreach ($cats as $c) {
                                    if ($c->slug !== 'uncategorized') { $primary_cat = $c; break; }
                                }
                                if ($primary_cat) : ?>
                                    <a class="blog-trend-cat" href="<?php echo esc_url(get_category_link($primary_cat->term_id)); ?>">
                                        <?php echo esc_html($primary_cat->name); ?>
                                    </a>
                                <?php endif; ?>

                                <h3 class="blog-trend-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>

                                <div class="blog-trend-meta">
                                    <span class="blog-trend-date"><?php echo esc_html(get_the_date('F j, Y')); ?></span>
                                </div>

                                <div class="blog-trend-excerpt">
                                    <?php
                                    $excerpt = get_the_excerpt();
                                    if (empty($excerpt)) {
                                        $excerpt = wp_trim_words(get_the_content(), 20, '…');
                                    }
                                    echo esc_html(wp_strip_all_tags($excerpt));
                                    ?>
                                </div>

                                <a href="<?php the_permalink(); ?>" class="blog-trend-read-more"><?php esc_html_e('read more', 'my-e-shop'); ?></a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <?php
                the_posts_pagination(array(
                    'mid_size'  => 1,
                    'prev_text' => __('Previous', 'my-e-shop'),
                    'next_text' => __('Next', 'my-e-shop'),
                ));
                ?>
            <?php else : ?>
                <p class="author-no-posts"><?php esc_html_e('This author has no articles yet.', 'my-e-shop'); ?></p>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php get_footer(); ?>
