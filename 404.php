<?php get_header(); ?>

<div class="content-area page-centered">
    <h1>404</h1>
    <p><?php _e('页面未找到', 'techpress'); ?></p>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="carousel-btn btn-home"><?php _e('返回首页', 'techpress'); ?></a>
</div>

<?php get_footer(); ?>
