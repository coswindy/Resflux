<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('techpress_setting')) {
    function techpress_setting($key, $default = '') {
        $sentinel = '__techpress_missing__';
        $value = get_option($key, $sentinel);

        if ($sentinel !== $value) {
            return $value;
        }

        return get_theme_mod($key, $default);
    }
}

if (!function_exists('techpress_home_module_definitions')) {
    function techpress_home_module_definitions() {
        return [
            'featured' => [
                'label'   => __('精选轮播', 'techpress'),
                'area'    => 'top',
                'default' => true,
            ],
            'carousel_ad' => [
                'label'   => __('轮播下广告', 'techpress'),
                'area'    => 'top',
                'default' => true,
            ],
            'category_feed' => [
                'label'   => __('分类文章流', 'techpress'),
                'area'    => 'main',
                'default' => true,
            ],
            'resources' => [
                'label'   => __('资源聚合', 'techpress'),
                'area'    => 'main',
                'default' => false,
            ],
            'navigation' => [
                'label'   => __('网址导航', 'techpress'),
                'area'    => 'main',
                'default' => false,
            ],
            'latest' => [
                'label'   => __('最新文章', 'techpress'),
                'area'    => 'aside',
                'default' => true,
            ],
            'sidebar' => [
                'label'   => __('首页侧栏', 'techpress'),
                'area'    => 'aside',
                'default' => true,
            ],
        ];
    }
}

if (!function_exists('techpress_default_home_modules')) {
    function techpress_default_home_modules() {
        $modules = [];
        foreach (techpress_home_module_definitions() as $key => $module) {
            if (!empty($module['default'])) {
                $modules[] = $key;
            }
        }
        return $modules;
    }
}

if (!function_exists('techpress_default_home_module_order')) {
    function techpress_default_home_module_order() {
        return array_keys(techpress_home_module_definitions());
    }
}

if (!function_exists('techpress_sanitize_home_modules')) {
    function techpress_sanitize_home_modules($value) {
        if (empty($value)) {
            return [];
        }

        $valid = array_keys(techpress_home_module_definitions());
        $items = is_array($value) ? $value : explode(',', (string) $value);
        $items = array_map('sanitize_key', $items);

        return array_values(array_unique(array_intersect($items, $valid)));
    }
}

if (!function_exists('techpress_sanitize_home_module_order')) {
    function techpress_sanitize_home_module_order($value) {
        $valid = array_keys(techpress_home_module_definitions());
        $items = array_map('sanitize_key', explode(',', (string) $value));
        $items = array_values(array_unique(array_intersect($items, $valid)));

        foreach ($valid as $module) {
            if (!in_array($module, $items, true)) {
                $items[] = $module;
            }
        }

        return implode(',', $items);
    }
}

if (!function_exists('techpress_get_home_modules')) {
    function techpress_get_home_modules($area = '') {
        $definitions = techpress_home_module_definitions();
        $enabled = get_option('techpress_home_modules_enabled', techpress_default_home_modules());
        $enabled = techpress_sanitize_home_modules($enabled);
        $order = get_option('techpress_home_modules_order', implode(',', techpress_default_home_module_order()));
        $order = explode(',', techpress_sanitize_home_module_order($order));
        $modules = [];

        foreach ($order as $module) {
            if (!isset($definitions[$module]) || !in_array($module, $enabled, true)) {
                continue;
            }
            if ($area && $definitions[$module]['area'] !== $area) {
                continue;
            }
            $modules[] = $module;
        }

        return $modules;
    }
}

if (!function_exists('techpress_home_module_enabled')) {
    function techpress_home_module_enabled($module) {
        return in_array($module, techpress_get_home_modules(), true);
    }
}

require_once get_template_directory() . '/inc/Autoloader.php';

TechPress\Autoloader::register();

$theme = TechPress\Theme::instance();

$theme->set('assets', new TechPress\Assets());
$theme->set('ajax', new TechPress\Ajax());
$theme->set('ad_manager', new TechPress\AdManager());
$theme->set('admin_settings', new TechPress\AdminSettings());
$theme->set('breadcrumbs', new TechPress\Breadcrumbs());
$theme->set('category_icons', new TechPress\CategoryIcons());
$theme->set('customizer', new TechPress\Customizer());
$theme->set('friend_links', new TechPress\FriendLinks());
$theme->set('home_modules', new TechPress\HomeModules());
$theme->set('lazy_load', new TechPress\LazyLoad());
$theme->set('excerpt', new TechPress\Excerpt());
$theme->set('query_hooks', new TechPress\QueryHooks());
$theme->set('post_views', new TechPress\PostViews());
$theme->set('reading_time', new TechPress\ReadingTime());
$theme->set('related_posts', new TechPress\RelatedPosts());
$theme->set('shortcodes', new TechPress\Shortcodes());
$theme->set('social_icons', new TechPress\SocialIcons());
$theme->set('thumbnail', new TechPress\Thumbnail());
$theme->set('seo', new TechPress\Seo());
$theme->set('security', new TechPress\Security());

require_once get_template_directory() . '/inc/template-tags.php';

add_filter('query_vars', function ($vars) {
    $vars[] = 'techpress_sitemap';
    return $vars;
});

/** 移除评论表单中的 Cookie 同意勾选框 (后台已有插件审核) */
add_filter('comment_form_default_fields', function ($fields) {
    if (isset($fields['cookies'])) {
        unset($fields['cookies']);
    }
    return $fields;
});
