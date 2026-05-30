<?php
$featured = new WP_Query([
    'post_type'              => 'post',
    'posts_per_page'         => 5,
    'meta_key'               => '_thumbnail_id',
    'ignore_sticky_posts'    => true,
    'no_found_rows'          => true,
    'update_post_meta_cache' => false,
    'update_term_meta_cache' => false,
]);
?>

<section class="hero-carousel">
    <div class="carousel-wrapper" id="carousel-wrapper">
        <div class="carousel-track" id="carousel-track">
            <?php if ($featured->have_posts()) :
                $featured_count = 0;
                while ($featured->have_posts()) : $featured->the_post();
                    $featured_count++;
                    $cats = get_the_category();
                    $cat_name = !empty($cats) ? $cats[0]->name : '';
                    $thumb_attr = 1 === $featured_count ? [
                        'loading'       => 'eager',
                        'fetchpriority' => 'high',
                    ] : [];
                    ?>
                    <div class="carousel-slide">
                        <a href="<?php the_permalink(); ?>">
                            <?php techpress_the_thumbnail(get_the_ID(), 'techpress-carousel', '', false, $thumb_attr); ?>
                            <div class="carousel-overlay">
                                <?php if ($cat_name) : ?>
                                    <span class="carousel-cat"><?php echo esc_html($cat_name); ?></span>
                                <?php endif; ?>
                                <h2><?php the_title(); ?></h2>
                                <div class="carousel-meta">
                                    <span><?php echo esc_html(get_the_date('Y-m-d')); ?></span>
                                    <span><?php comments_number('0 评论', '1 评论', '% 评论'); ?></span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endwhile;
            endif;
            wp_reset_postdata(); ?>
        </div>

        <button class="carousel-btn carousel-prev" id="carousel-prev" aria-label="<?php esc_attr_e('上一张', 'techpress'); ?>">‹</button>
        <button class="carousel-btn carousel-next" id="carousel-next" aria-label="<?php esc_attr_e('下一张', 'techpress'); ?>">›</button>
    </div>
    <div class="carousel-dots" id="carousel-dots"></div>
</section>
