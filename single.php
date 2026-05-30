<?php get_header(); ?>

<?php techpress_breadcrumbs(); ?>

<?php
$layout = techpress_setting('techpress_single_layout', 'sidebar');
$threshold = techpress_setting('techpress_readmore_threshold', 1800);
$wrap_class = 'sidebar' === $layout ? 'content-with-sidebar' : 'content-area';
$main_class = 'sidebar' === $layout ? 'content-main' : '';
?>

<div class="<?php echo esc_attr($wrap_class); ?>">
    <main class="<?php echo esc_attr($main_class); ?>">
        <?php while (have_posts()) : the_post(); ?>

            <article class="entry">
                <header class="entry-header">
                    <?php $cats = get_the_category(); if (!empty($cats)) : ?>
                        <a class="cat-link" href="<?php echo esc_url(get_category_link($cats[0]->term_id)); ?>"><?php echo esc_html($cats[0]->name); ?></a>
                    <?php endif; ?>
                    <h1><?php the_title(); ?></h1>
                    <div class="entry-meta">
                        <span>📅 <?php echo esc_html(get_the_date('Y-m-d')); ?></span>
                        <span>📁 <?php the_category(', '); ?></span>
                        <span>💬 <?php comments_number('0 评论', '1 评论', '% 评论'); ?></span>
                        <span>⏱ <?php techpress_reading_time(); ?></span>
                        <span>👁 <?php echo esc_html(techpress_get_post_views()); ?> <?php _e('次阅读', 'techpress'); ?></span>
                    </div>
                </header>

                <?php if (has_post_thumbnail() || get_post_meta(get_the_ID(), '_techpress_thumb_url', true)) : ?>
                <div class="entry-thumb">
                    <?php techpress_the_thumbnail(get_the_ID(), 'large', '', true); ?>
                </div>
                <?php endif; ?>

                <?php techpress_ad_slot('single_top'); ?>

                <div class="entry-content-wrapper" data-threshold="<?php echo esc_attr($threshold); ?>">
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </div>

                <?php techpress_ad_slot('single_bottom'); ?>

                <?php
                wp_link_pages([
                    'before'     => '<div class="pagination">',
                    'after'      => '</div>',
                    'link_before' => '<span>',
                    'link_after'  => '</span>',
                ]);
                ?>

                <?php if (has_tag()) : ?>
                    <div class="entry-tags">
                        <?php the_tags('', ''); ?>
                    </div>
                <?php endif; ?>
            </article>

            <?php get_template_part('template-parts/share-buttons'); ?>
            <?php get_template_part('template-parts/author-box'); ?>
            <?php get_template_part('template-parts/related-posts'); ?>

            <nav class="post-navigation">
                <div class="post-navi-item prev">
                    <?php previous_post_link(
                        '<span class="post-navi-label">← ' . __('上一篇', 'techpress') . '</span><div class="post-navi-title">%link</div>'
                    ); ?>
                </div>
                <div class="post-navi-item next">
                    <?php next_post_link(
                        '<span class="post-navi-label">' . __('下一篇', 'techpress') . ' →</span><div class="post-navi-title">%link</div>'
                    ); ?>
                </div>
            </nav>

            <?php if (comments_open() || get_comments_number()) : ?>
                <div class="entry-comments">
                    <?php comments_template(); ?>
                </div>
            <?php endif; ?>

        <?php endwhile; ?>
    </main>

    <?php if ('sidebar' === $layout) : ?>
        <?php get_sidebar(); ?>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
