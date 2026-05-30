<?php
$limit = absint(techpress_setting('techpress_home_resources_count', 6));
$resources = techpress_get_home_resources($limit ?: 6);

if (!$resources->have_posts()) {
    wp_reset_postdata();
    return;
}
?>

<section class="home-module home-module-resources">
    <div class="section-header">
        <h2 class="section-title"><?php esc_html_e('资源聚合', 'techpress'); ?></h2>
    </div>

    <div class="resource-grid">
        <?php while ($resources->have_posts()) : $resources->the_post();
            $url = get_post_meta(get_the_ID(), '_resource_url', true);
            $desc = get_post_meta(get_the_ID(), '_resource_desc', true);
            $type = get_post_meta(get_the_ID(), '_resource_type', true);
            if (!$url) {
                continue;
            }
            ?>
            <article class="resource-card">
                <a class="resource-card-link" href="<?php echo esc_url($url); ?>" target="_blank" rel="nofollow noopener">
                    <span class="resource-type"><?php echo esc_html($type ?: __('资源', 'techpress')); ?></span>
                    <h3><?php the_title(); ?></h3>
                    <?php if ($desc) : ?>
                        <p><?php echo esc_html($desc); ?></p>
                    <?php endif; ?>
                    <span class="resource-action"><?php esc_html_e('查看资源', 'techpress'); ?></span>
                </a>
            </article>
        <?php endwhile; ?>
    </div>
</section>

<?php wp_reset_postdata(); ?>
