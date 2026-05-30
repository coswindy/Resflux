<?php
$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$current_cat = isset($_GET['cat']) ? absint(wp_unslash($_GET['cat'])) : 0;

$grid_args = [
    'post_type'           => 'post',
    'posts_per_page'      => 8,
    'paged'               => $paged,
    'ignore_sticky_posts' => false,
];
if ($current_cat > 0) {
    $grid_args['cat'] = $current_cat;
}

$grid = new WP_Query($grid_args);
$categories = get_categories(['exclude' => 1, 'hide_empty' => false]);
$layout = techpress_home_layout();
$container_class = 'grid' === $layout ? 'post-grid' : 'blog-posts';
$ad_slot = 'grid' === $layout ? 'grid_infeed' : 'list_infeed';
$ad_freq = 'grid' === $layout ? 4 : 3;
$ad_class = 'grid' === $layout ? 'ad-slot-grid-infeed' : 'ad-slot-list-infeed';
$ad_wrap_class = 'grid' === $layout ? 'grid-item ad-grid-placeholder' : 'blog-post-card infeed-ad-placeholder';
?>

<?php if (!empty($categories)) : ?>
    <div class="cat-tabs" id="cat-tabs">
        <button class="cat-tab <?php echo $current_cat === 0 ? 'active' : ''; ?>" data-cat="0"><?php esc_html_e('全部', 'techpress'); ?></button>
        <?php foreach ($categories as $cat) : ?>
            <button class="cat-tab <?php echo $current_cat === $cat->term_id ? 'active' : ''; ?>" data-cat="<?php echo esc_attr($cat->term_id); ?>"><?php echo esc_html($cat->name); ?></button>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="<?php echo esc_attr($container_class); ?>" id="post-grid">
    <?php if ($grid->have_posts()) :
        $count = 0;
        while ($grid->have_posts()) : $grid->the_post();
            $count++; ?>
            <?php if ($count % $ad_freq === 0 && techpress_has_ad($ad_slot)) : ?>
                <div class="<?php echo esc_attr($ad_wrap_class); ?>">
                    <?php techpress_ad_slot($ad_slot, $ad_class); ?>
                </div>
            <?php endif; ?>
            <?php techpress_render_post_card($layout); ?>
        <?php endwhile;
    endif;
    wp_reset_postdata(); ?>
</div>

<?php if ($grid->max_num_pages > 1) : ?>
    <div class="load-more-wrap">
        <button class="load-more-btn" id="load-more-btn" data-page="1" data-cat="<?php echo esc_attr($current_cat); ?>">
            <?php esc_html_e('加载更多 ↓', 'techpress'); ?>
        </button>
    </div>
<?php endif; ?>
