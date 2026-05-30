<?php

namespace TechPress;

use WP_Widget_Factory;

class Theme {

    private static $instance = null;

    private $services = [];

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get($id) {
        return $this->services[$id] ?? null;
    }

    public function set($id, $service) {
        $this->services[$id] = $service;
        return $this;
    }

    private function __construct() {
        $this->init_hooks();
        $this->init_filters();
    }

    private function init_hooks() {
        add_action('after_setup_theme', [$this, 'setup']);
        add_action('widgets_init', [$this, 'register_widgets']);
        add_action('wp_head', [$this, 'output_color_scheme'], 4);
        add_action('wp_head', [$this, 'output_font_style'], 5);
        add_action('wp_headers', [$this, 'add_cache_headers']);
    }

    private function init_filters() {
        add_filter('pre_get_avatar_data', [$this, 'filter_gravatar_mirror'], 10, 2);
    }

    public function filter_gravatar_mirror($args, $id_or_email) {
        $mirror = techpress_setting('techpress_gravatar_mirror', 'www.gravatar.com');
        if ('www.gravatar.com' !== $mirror && !empty($args['url'])) {
            $host = preg_replace('#^https?://#', '', $mirror);
            $args['url'] = str_replace('www.gravatar.com', $host, $args['url']);
            $args['url'] = str_replace('secure.gravatar.com', $host, $args['url']);
            $args['url'] = str_replace('0.gravatar.com', $host, $args['url']);
        }
        return $args;
    }

    public function get_color_schemes() {
        return [
            'aurora-blue' => [
                'label' => __('极光蓝', 'techpress'),
                'light' => [
                    '--primary'          => '#2563eb',
                    '--primary-light'    => '#38bdf8',
                    '--primary-dark'     => '#1e40af',
                    '--primary-bg'       => '#eef6ff',
                    '--focus-ring'       => 'rgba(37, 99, 235, 0.14)',
                ],
                'dark'  => [
                    '--primary'          => '#60a5fa',
                    '--primary-light'    => '#67e8f9',
                    '--primary-dark'     => '#3b82f6',
                    '--primary-bg'       => 'rgba(96,165,250,0.14)',
                    '--focus-ring'       => 'rgba(96, 165, 250, 0.18)',
                ],
            ],
            'glacier-teal' => [
                'label' => __('冰川青', 'techpress'),
                'light' => [
                    '--primary'          => '#0f766e',
                    '--primary-light'    => '#22c55e',
                    '--primary-dark'     => '#115e59',
                    '--primary-bg'       => '#ecfdf5',
                    '--focus-ring'       => 'rgba(15, 118, 110, 0.14)',
                ],
                'dark'  => [
                    '--primary'          => '#2dd4bf',
                    '--primary-light'    => '#86efac',
                    '--primary-dark'     => '#14b8a6',
                    '--primary-bg'       => 'rgba(45, 212, 191, 0.14)',
                    '--focus-ring'       => 'rgba(45, 212, 191, 0.18)',
                ],
            ],
            'nebula-purple' => [
                'label' => __('星云紫', 'techpress'),
                'light' => [
                    '--primary'          => '#7c3aed',
                    '--primary-light'    => '#06b6d4',
                    '--primary-dark'     => '#6d28d9',
                    '--primary-bg'       => '#f5f3ff',
                    '--focus-ring'       => 'rgba(124, 58, 237, 0.14)',
                ],
                'dark'  => [
                    '--primary'          => '#a78bfa',
                    '--primary-light'    => '#22d3ee',
                    '--primary-dark'     => '#8b5cf6',
                    '--primary-bg'       => 'rgba(167, 139, 250, 0.14)',
                    '--focus-ring'       => 'rgba(167, 139, 250, 0.18)',
                ],
            ],
        ];
    }

    public function output_color_scheme() {
        $schemes = $this->get_color_schemes();
        $choice  = techpress_setting('techpress_color_scheme', 'aurora-blue');

        if (!isset($schemes[$choice])) {
            return;
        }

        $scheme = $schemes[$choice];
        $light  = $scheme['light'];
        $dark   = $scheme['dark'];

        $light_css = '';
        foreach ($light as $var => $val) {
            $light_css .= $var . ':' . $val . ';';
        }

        $dark_css = '';
        foreach ($dark as $var => $val) {
            $dark_css .= $var . ':' . $val . ';';
        }

        echo '<style id="techpress-color-scheme">:root:root{' . $light_css . '}:root[data-theme="dark"]{' . $dark_css . '}</style>' . "\n";
    }

    public function output_font_style() {
        $choice = techpress_setting('techpress_font_family', 'default');
        $font_stack = '';

        switch ($choice) {
            case 'yahei':
                $font_stack = "'Microsoft YaHei', '微软雅黑', 'PingFang SC', 'Noto Sans SC', sans-serif";
                break;
            case 'noto':
                $font_stack = "'Noto Sans SC', 'PingFang SC', 'Microsoft YaHei', sans-serif";
                break;
            case 'custom':
                $custom = techpress_setting('techpress_custom_font', '');
                if ($custom) {
                    $font_stack = $custom;
                }
                break;
            default:
                return;
        }

        if ($font_stack) {
            echo '<style>:root{--font:' . esc_attr($font_stack) . '}</style>' . "\n";
        }
    }

    public function add_cache_headers($headers) {
        if (!is_user_logged_in()) {
            $headers['Cache-Control'] = 'public, max-age=3600, s-maxage=3600';
        }
        return $headers;
    }

    public static function purge_cache() {
        do_action('techpress_purge_cache');

        if (function_exists('wp_cache_clean_cache')) {
            wp_cache_clean_cache(get_current_blog_id());
        }
        if (function_exists('w3tc_pgcache_flush')) {
            w3tc_pgcache_flush();
        }
        if (function_exists('rocket_clean_domain')) {
            rocket_clean_domain();
        }
        if (function_exists('litespeed_purge_all')) {
            litespeed_purge_all('techpress_purge');
        }
    }

    public function setup() {
        add_theme_support('automatic-feed-links');
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
        add_theme_support('custom-logo', [
            'height'      => 60,
            'width'       => 200,
            'flex-height' => true,
            'flex-width'  => true,
        ]);
        add_theme_support('responsive-embeds');
        add_theme_support('align-wide');
        add_theme_support('wp-block-styles');
        add_theme_support('editor-styles');
        add_theme_support('custom-line-height');
        add_theme_support('custom-spacing');
        add_theme_support('custom-units', ['px', 'em', 'rem', '%', 'vh', 'vw']);
        add_theme_support('appearance-tools');
        add_theme_support('editor-color-palette', [
            [
                'name'  => __('主色', 'techpress'),
                'slug'  => 'primary',
                'color' => '#2563eb',
            ],
            [
                'name'  => __('强调色', 'techpress'),
                'slug'  => 'accent',
                'color' => '#f59e0b',
            ],
            [
                'name'  => __('正文', 'techpress'),
                'slug'  => 'text',
                'color' => '#0f172a',
            ],
            [
                'name'  => __('浅背景', 'techpress'),
                'slug'  => 'background-alt',
                'color' => '#f8fafc',
            ],
        ]);
        add_theme_support('editor-font-sizes', [
            [
                'name' => __('小', 'techpress'),
                'slug' => 'small',
                'size' => 14,
            ],
            [
                'name' => __('正文', 'techpress'),
                'slug' => 'normal',
                'size' => 16,
            ],
            [
                'name' => __('大', 'techpress'),
                'slug' => 'large',
                'size' => 22,
            ],
            [
                'name' => __('标题', 'techpress'),
                'slug' => 'heading',
                'size' => 32,
            ],
        ]);
        add_editor_style('assets/css/editor-style.css');

        register_nav_menus([
            'primary' => __('主导航', 'techpress'),
            'footer'  => __('页脚导航', 'techpress'),
        ]);

        set_post_thumbnail_size(1200, 630, true);
        add_image_size('techpress-carousel', 1200, 550, true);
        add_image_size('techpress-grid', 600, 375, true);
        add_image_size('techpress-list', 320, 220, true);
        add_image_size('techpress-related', 400, 250, true);
        add_image_size('techpress-thumb-sm', 120, 120, true);
    }

    public function register_widgets() {
        $this->register_sidebars();
        register_widget('TechPress\\Widgets\\PopularPosts');
        register_widget('TechPress\\Widgets\\RecentPosts');
        register_widget('TechPress\\Widgets\\RecentComments');
        register_widget('TechPress\\Widgets\\CategoriesWidget');
        register_widget('TechPress\\Widgets\\AdWidget');
    }

    private function register_sidebars() {
        register_sidebar([
            'id'            => 'sidebar-1',
            'name'          => __('侧边栏', 'techpress'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ]);
    }
}
