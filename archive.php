<?php get_header(); ?>

<div class="content-with-sidebar">
    <main class="content-main">
        <header class="entry-header">
            <?php
            the_archive_title('<h1>', '</h1>');
            the_archive_description('<div class="archive-desc">', '</div>');
            ?>
        </header>

        <?php if (have_posts()) : ?>
            <div class="blog-posts">
                <?php $list_count = 0;
                while (have_posts()) : the_post(); $list_count++; ?>
                    <?php if ($list_count % 3 === 0 && techpress_has_ad('list_infeed')) : ?>
                        <div class="blog-post-card infeed-ad-placeholder">
                            <?php techpress_ad_slot('list_infeed', 'ad-slot-list-infeed'); ?>
                        </div>
                    <?php endif; ?>
                    <article class="blog-post-card">
                        <div class="thumb">
                            <a href="<?php the_permalink(); ?>">
                                <?php techpress_the_thumbnail(get_the_ID(), 'techpress-list'); ?>
                            </a>
                        </div>
                        <div class="post-body">
                            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <div class="meta">
                                <span><?php echo esc_html(get_the_date('Y-m-d')); ?></span>
                                <span>· <?php the_category(', '); ?></span>
                            </div>
                            <div class="excerpt"><?php echo wp_trim_words(get_the_excerpt(), 40); ?></div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="pagination">
                <?php
                the_posts_pagination([
                    'mid_size'  => 2,
                    'prev_text' => '‹',
                    'next_text' => '›',
                ]);
                ?>
            </div>
        <?php else : ?>
            <p><?php _e('暂无文章', 'techpress'); ?></p>
        <?php endif; ?>
    </main>

    <?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
