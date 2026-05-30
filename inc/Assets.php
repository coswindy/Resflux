<?php

namespace TechPress;

class Assets {

    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function enqueue_scripts() {
        $version = '2.2.0';
        $dir     = get_template_directory_uri();

        $this->register_css_modules($dir, $version);

        $this->register_js_module('dark-mode');
        $this->register_js_module('search-modal');
        $this->register_js_module('mobile-menu');
        $this->register_js_module('back-to-top');
        $this->register_js_module('footer-qrcode');

        if (is_front_page() && techpress_home_module_enabled('featured')) {
            wp_enqueue_script('techpress-carousel', $dir . '/assets/js/carousel.js', [], $version, true);
        }

        if (is_front_page() && techpress_home_module_enabled('category_feed')) {
            $this->register_js_module('category-tabs');
        }

        if (is_singular()) {
            $this->register_js_module('progress-bar');
            $this->register_js_module('view-counter');
            $this->register_js_module('share-buttons');

            $threshold = techpress_setting('techpress_readmore_threshold', 1800);
            if ($threshold > 0) {
                wp_enqueue_script('techpress-read-more', $dir . '/assets/js/modules/read-more.js', [], $version, true);
                wp_localize_script('techpress-read-more', 'techpress_readmore', [
                    'threshold' => $threshold,
                    'text'      => __('阅读余下全文 ↓', 'techpress'),
                ]);
            }
        }

        if ($this->is_login_page()) {
            $this->register_js_module('auth');
        }

        if ($this->is_submit_page()) {
            $this->register_js_module('submit-post');
        }

        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }

        wp_localize_script('techpress-dark-mode', 'techpress', [
            'ajax_url'  => admin_url('admin-ajax.php'),
            'nonce'     => wp_create_nonce('techpress_nonce'),
            'is_single' => is_singular(),
            'post_id'   => is_singular() ? get_the_ID() : 0,
        ]);
    }

    private function is_login_page() {
        return is_page_template('page-login.php')
            || is_page('login')
            || (is_singular() && has_shortcode(get_post_field('post_content', get_the_ID()), 'techpress_login_form'));
    }

    private function is_submit_page() {
        return is_page_template('page-submit.php')
            || is_page('submit')
            || (is_singular() && has_shortcode(get_post_field('post_content', get_the_ID()), 'techpress_submit_form'));
    }

    private function register_js_module($slug) {
        $version = '2.2.0';
        $handle  = 'techpress-' . $slug;
        $src     = get_template_directory_uri() . '/assets/js/modules/' . $slug . '.js';
        $deps    = [];

        if ('view-counter' === $slug || 'category-tabs' === $slug || 'auth' === $slug || 'submit-post' === $slug) {
            $deps = ['techpress-dark-mode'];
        }

        wp_enqueue_script($handle, $src, $deps, $version, true);
    }

    private function register_css_modules($dir, $version) {
        $css_dir = $dir . '/assets/css';
        $modules = [
            'variables',
            'base',
            'header',
            'search-modal',
            'carousel',
            'grid',
            'post',
            'comments',
            'sidebar',
            'footer',
            'components',
            'extras',
            'mobile',
        ];

        $prev_handle = null;
        foreach ($modules as $slug) {
            $handle = 'techpress-' . str_replace('_', '-', $slug);
            $src    = $css_dir . '/_' . $slug . '.css';
            $deps   = $prev_handle ? [$prev_handle] : [];
            wp_enqueue_style($handle, $src, $deps, $version);
            $prev_handle = $handle;
        }
    }
}
