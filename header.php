<!DOCTYPE html>
<html <?php language_attributes(); ?> data-theme="<?php echo esc_attr(techpress_setting('techpress_dark_mode', 'auto')); ?>">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="progress-bar" id="progress-bar"></div>

<?php
$primary_nav_align = techpress_setting('techpress_primary_nav_align', 'center');
if (!in_array($primary_nav_align, ['left', 'center', 'right'], true)) {
    $primary_nav_align = 'center';
}
?>

<header class="site-header">
    <div class="header-inner header-nav-<?php echo esc_attr($primary_nav_align); ?>">
        <div class="site-branding">
            <?php
            $logo_url = techpress_setting('techpress_logo_url', '');
            if ($logo_url) : ?>
                <a class="custom-logo-link" href="<?php echo esc_url(home_url('/')); ?>">
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php bloginfo('name'); ?>" class="custom-logo">
                </a>
            <?php elseif (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></h1>
                <?php $desc = get_bloginfo('description', 'display'); if ($desc) : ?>
                    <p class="site-description"><?php echo esc_html($desc); ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <button class="menu-toggle header-btn" aria-label="<?php esc_attr_e('菜单', 'techpress'); ?>" aria-expanded="false">
            <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>

        <nav class="main-nav" id="main-nav">
            <?php wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'fallback_cb'    => false,
                'depth'          => 2,
            ]); ?>
        </nav>

        <div class="header-actions">
            <button class="header-btn" id="search-toggle" aria-label="<?php esc_attr_e('搜索', 'techpress'); ?>">
                <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="m21 21-4.3-4.3M10.8 18a7.2 7.2 0 1 1 0-14.4 7.2 7.2 0 0 1 0 14.4Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
            <button class="header-btn" id="dark-toggle" aria-label="<?php esc_attr_e('切换深色模式', 'techpress'); ?>">
                <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M20.4 14.4A8.4 8.4 0 0 1 9.6 3.6 8.4 8.4 0 1 0 20.4 14.4Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
            </button>
            <?php if (is_user_logged_in()) : ?>
                <a href="<?php echo esc_url(home_url('/submit')); ?>" class="header-btn header-btn-link" title="<?php esc_attr_e('投稿', 'techpress'); ?>" aria-label="<?php esc_attr_e('投稿', 'techpress'); ?>">
                    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/login')); ?>" class="header-btn header-btn-link" title="<?php esc_attr_e('登录', 'techpress'); ?>" aria-label="<?php esc_attr_e('登录', 'techpress'); ?>">
                    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M20 21a8 8 0 0 0-16 0M12 13a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<div class="search-modal" id="search-modal">
    <button class="search-modal-close" id="search-close">✕</button>
    <div class="search-modal-inner">
        <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="search" placeholder="<?php esc_attr_e('搜索...', 'techpress'); ?>" value="<?php echo get_search_query(); ?>" name="s">
            <button type="submit" class="search-submit"><?php _e('搜索', 'techpress'); ?></button>
        </form>
    </div>
</div>

<main id="main" class="site-main">
