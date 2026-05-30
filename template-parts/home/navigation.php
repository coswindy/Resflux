<?php
$limit = absint(techpress_setting('techpress_home_navigation_count', 8));
$links = techpress_get_home_navigation_links($limit ?: 8);

if (!$links->have_posts()) {
    wp_reset_postdata();
    return;
}
?>

<section class="home-module home-module-navigation">
    <div class="section-header">
        <h2 class="section-title"><?php esc_html_e('网址导航', 'techpress'); ?></h2>
    </div>

    <div class="navigation-grid">
        <?php while ($links->have_posts()) : $links->the_post();
            $url = get_post_meta(get_the_ID(), '_nav_url', true);
            $desc = get_post_meta(get_the_ID(), '_nav_desc', true);
            $logo = get_post_meta(get_the_ID(), '_nav_logo', true);
            if (!$url) {
                continue;
            }
            ?>
            <article class="navigation-card">
                <a href="<?php echo esc_url($url); ?>" target="_blank" rel="nofollow noopener">
                    <span class="navigation-logo">
                        <?php if ($logo) : ?>
                            <img src="<?php echo esc_url($logo); ?>" alt="" loading="lazy">
                        <?php elseif (has_post_thumbnail()) : ?>
                            <?php techpress_the_thumbnail(get_the_ID(), 'techpress-thumb-sm', '', true); ?>
                        <?php else : ?>
                            <span><?php echo esc_html(mb_substr(get_the_title(), 0, 1)); ?></span>
                        <?php endif; ?>
                    </span>
                    <span class="navigation-info">
                        <strong><?php the_title(); ?></strong>
                        <?php if ($desc) : ?>
                            <em><?php echo esc_html($desc); ?></em>
                        <?php endif; ?>
                    </span>
                </a>
            </article>
        <?php endwhile; ?>
    </div>
</section>

<?php wp_reset_postdata(); ?>
