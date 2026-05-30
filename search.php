<?php get_header(); ?>
<?php global $wp_query; ?>

<div class="content-with-sidebar">
    <main class="content-main">
        <header class="entry-header">
            <h1><?php printf(__('搜索结果：%s', 'techpress'), '<span>' . esc_html(get_search_query()) . '</span>'); ?></h1>
            <div class="entry-meta">
                <span><?php printf(_n('找到 %s 条结果', '找到 %s 条结果', (int) $wp_query->found_posts, 'techpress'), number_format_i18n((int) $wp_query->found_posts)); ?></span>
            </div>
        </header>

        <?php if (have_posts()) : ?>
            <div class="blog-posts">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="blog-post-card">
                        <div class="thumb">
                            <a href="<?php the_permalink(); ?>">
                                <?php techpress_the_thumbnail(get_the_ID(), 'techpress-list'); ?>
                            </a>
                        </div>
                        <div class="post-body">
                            <h2><a href="<?php the_permalink(); ?>"><?php echo techpress_highlight_search_terms(get_the_title()); ?></a></h2>
                            <div class="meta">
                                <span><?php echo esc_html(get_the_date('Y-m-d')); ?></span>
                                <span>· <?php echo esc_html(get_post_type_object(get_post_type())->labels->singular_name); ?></span>
                                <?php if ('post' === get_post_type()) : ?>
                                    <span>· <?php the_category(', '); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="excerpt"><?php echo techpress_highlight_search_terms(wp_trim_words(get_the_excerpt(), 40)); ?></div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="pagination">
                <?php the_posts_pagination(['mid_size' => 2, 'prev_text' => '‹', 'next_text' => '›']); ?>
            </div>
        <?php else : ?>
            <div class="search-empty">
                <p><?php _e('未找到相关文章，请尝试其他关键词。', 'techpress'); ?></p>
                <?php get_search_form(); ?>
            </div>
        <?php endif; ?>
    </main>

    <?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
