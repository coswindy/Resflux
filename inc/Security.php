<?php

namespace TechPress;

/**
 * Security Hardening
 *
 * All features are independently toggleable via Theme Settings → 安全设置.
 * Each option defaults to enabled ('1') except RSS feed which defaults to off ('0').
 */
class Security {

    /**
     * Option key → default value map.
     * Keeps defaults in one place so both __construct and AdminSettings stay in sync.
     */
    private const DEFAULTS = [
        'techpress_sec_hide_generator'      => '1',
        'techpress_sec_remove_rsd'          => '1',
        'techpress_sec_remove_rest_link'    => '1',
        'techpress_sec_remove_oembed'       => '1',
        'techpress_sec_disable_emoji'       => '1',
        'techpress_sec_disable_feed'        => '0',
        'techpress_sec_disable_xmlrpc'      => '1',
        'techpress_sec_obscure_login_errors' => '1',
    ];

    public function __construct() {
        $this->hide_wordpress_info();
        $this->disable_emoji();
        $this->disable_feed();
        $this->disable_xmlrpc();
        $this->obscure_login_errors();
    }

    /**
     * Check whether a security toggle is enabled.
     */
    private function is_enabled(string $key): bool {
        $default = self::DEFAULTS[$key] ?? '0';
        return '1' === get_option($key, $default);
    }

    /* ─────────────────────────────────────
       WordPress Information Hiding
       ───────────────────────────────────── */

    private function hide_wordpress_info(): void {
        if ($this->is_enabled('techpress_sec_hide_generator')) {
            remove_action('wp_head', 'wp_generator');
            add_filter('the_generator', '__return_empty_string');
        }

        if ($this->is_enabled('techpress_sec_remove_rsd')) {
            remove_action('wp_head', 'rsd_link');
            remove_action('wp_head', 'wlwmanifest_link');
            remove_action('wp_head', 'wp_shortlink_wp_head', 10);
        }

        if ($this->is_enabled('techpress_sec_remove_rest_link')) {
            remove_action('wp_head', 'rest_output_link_widget', 10);
            remove_action('template_redirect', 'rest_output_link_header', 99);
            add_filter('rest_jsonp_enabled', '__return_false');
        }

        if ($this->is_enabled('techpress_sec_remove_oembed')) {
            remove_action('wp_head', 'wp_oembed_add_discovery_links');
            remove_action('wp_head', 'wp_oembed_add_host_js');
            remove_action('rest_api_init', 'wp_oembed_register_route');
            add_filter('embed_oembed_discover', '__return_false');
        }
    }

    /* ─────────────────────────────────────
       Feature Trimming
       ───────────────────────────────────── */

    private function disable_emoji(): void {
        if (!$this->is_enabled('techpress_sec_disable_emoji')) {
            return;
        }

        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_action('embed_head', 'print_emoji_detection_script');

        add_filter('emoji_svg_url', '__return_false');
        add_filter('wp_resource_hints', [$this, 'remove_emoji_dns_prefetch'], 10, 2);
        add_filter('tiny_mce_plugins', [$this, 'remove_emoji_tinymce_plugin']);
    }

    /**
     * Remove s.w.org from DNS prefetch hints.
     */
    public function remove_emoji_dns_prefetch(array $urls, string $relation_type): array {
        if ('dns-prefetch' === $relation_type) {
            $urls = array_filter($urls, function ($url) {
                return false === strpos($url, 's.w.org');
            });
        }
        return $urls;
    }

    /**
     * Remove wpemoji from TinyMCE plugins.
     */
    public function remove_emoji_tinymce_plugin(array $plugins): array {
        return array_diff($plugins, ['wpemoji']);
    }

    /**
     * Disable all RSS/Atom feeds and return 404.
     */
    private function disable_feed(): void {
        if (!$this->is_enabled('techpress_sec_disable_feed')) {
            return;
        }

        $feed_hooks = [
            'do_feed',
            'do_feed_rdf',
            'do_feed_rss',
            'do_feed_rss2',
            'do_feed_atom',
            'do_feed_rss2_comments',
            'do_feed_atom_comments',
        ];

        foreach ($feed_hooks as $hook) {
            add_action($hook, [$this, 'respond_feed_disabled'], 1);
        }

        add_action('wp_head', [$this, 'remove_feed_links_from_head'], 1);
    }

    public function respond_feed_disabled(): void {
        wp_die(
            __('本站暂不支持 RSS 订阅。', 'techpress'),
            __('RSS 已禁用', 'techpress'),
            ['response' => 404]
        );
    }

    /**
     * Remove <link rel="alternate" type="application/rss+xml"> from <head>.
     */
    public function remove_feed_links_from_head(): void {
        remove_action('wp_head', 'feed_links', 2);
        remove_action('wp_head', 'feed_links_extra', 3);
    }

    /* ─────────────────────────────────────
       API Security
       ───────────────────────────────────── */

    /**
     * Disable XML-RPC to prevent brute-force and DDoS amplification attacks.
     */
    private function disable_xmlrpc(): void {
        if (!$this->is_enabled('techpress_sec_disable_xmlrpc')) {
            return;
        }

        add_filter('xmlrpc_enabled', '__return_false');
        add_action('wp', [$this, 'block_xmlrpc_direct_access']);
    }

    /**
     * Block direct access to xmlrpc.php with a clean 403 response.
     */
    public function block_xmlrpc_direct_access(): void {
        if (isset($_SERVER['REQUEST_URI']) && false !== strpos($_SERVER['REQUEST_URI'], 'xmlrpc.php')) {
            status_header(403);
            exit;
        }
    }

    /**
     * Obscure login error messages to prevent username enumeration.
     */
    private function obscure_login_errors(): void {
        if (!$this->is_enabled('techpress_sec_obscure_login_errors')) {
            return;
        }

        add_filter('login_errors', [$this, 'generic_login_error']);
    }

    /**
     * Replace specific login errors with a generic message.
     */
    public function generic_login_error(): string {
        return __('用户名或密码不正确。', 'techpress');
    }
}
