<div class="post-list">
    <h3 class="section-title"><?php esc_html_e('最新文章', 'techpress'); ?></h3>
    <?php
    $latest = new WP_Query([
        'post_type'              => 'post',
        'posts_per_page'         => 8,
        'ignore_sticky_posts'    => true,
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_term_meta_cache' => false,
    ]);
    if ($latest->have_posts()) :
        while ($latest->have_posts()) : $latest->the_post(); ?>
            <article class="list-item">
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <div class="excerpt"><?php echo wp_trim_words(get_the_excerpt(), 30); ?></div>
                <div class="meta-row">
                    <span><?php echo esc_html(get_the_date('Y-m-d')); ?></span>
                    <span>· <?php comments_number('0 评论', '1 评论', '% 评论'); ?></span>
                </div>
            </article>
        <?php endwhile;
    endif;
    wp_reset_postdata(); ?>
</div>
