<?php

namespace TechPress;

class HomeModules {

    private $meta_boxes = [
        'tech_resource' => [
            'nonce_action' => 'techpress_resource_save',
            'nonce_name'   => 'techpress_resource_nonce',
            'fields'       => [
                '_resource_url'  => ['name' => 'resource_url', 'sanitize' => 'url'],
                '_resource_desc' => ['name' => 'resource_desc', 'sanitize' => 'text'],
                '_resource_type' => ['name' => 'resource_type', 'sanitize' => 'text'],
            ],
        ],
        'tech_navigation' => [
            'nonce_action' => 'techpress_navigation_save',
            'nonce_name'   => 'techpress_navigation_nonce',
            'fields'       => [
                '_nav_url'  => ['name' => 'nav_url', 'sanitize' => 'url'],
                '_nav_desc' => ['name' => 'nav_desc', 'sanitize' => 'text'],
                '_nav_logo' => ['name' => 'nav_logo', 'sanitize' => 'url'],
            ],
        ],
    ];

    public function __construct() {
        add_action('init', [$this, 'register_post_types']);
        add_action('init', [$this, 'register_taxonomies']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_tech_resource', [$this, 'save_meta']);
        add_action('save_post_tech_navigation', [$this, 'save_meta']);
    }

    public function register_post_types() {
        register_post_type('tech_resource', [
            'labels' => [
                'name'          => __('资源', 'techpress'),
                'singular_name' => __('资源', 'techpress'),
                'add_new_item'  => __('添加资源', 'techpress'),
                'edit_item'     => __('编辑资源', 'techpress'),
                'all_items'     => __('全部资源', 'techpress'),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => true,
            'menu_icon'    => 'dashicons-download',
            'menu_position' => 26,
            'supports'     => ['title', 'thumbnail', 'page-attributes'],
            'capability_type' => 'post',
            'map_meta_cap' => true,
        ]);

        register_post_type('tech_navigation', [
            'labels' => [
                'name'          => __('网址导航', 'techpress'),
                'singular_name' => __('导航站点', 'techpress'),
                'add_new_item'  => __('添加站点', 'techpress'),
                'edit_item'     => __('编辑站点', 'techpress'),
                'all_items'     => __('全部站点', 'techpress'),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => true,
            'menu_icon'    => 'dashicons-location-alt',
            'menu_position' => 27,
            'supports'     => ['title', 'thumbnail', 'page-attributes'],
            'capability_type' => 'post',
            'map_meta_cap' => true,
        ]);
    }

    public function register_taxonomies() {
        register_taxonomy('resource_category', ['tech_resource'], [
            'labels' => [
                'name'          => __('资源分类', 'techpress'),
                'singular_name' => __('资源分类', 'techpress'),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => true,
            'hierarchical' => true,
            'show_admin_column' => true,
        ]);

        register_taxonomy('navigation_category', ['tech_navigation'], [
            'labels' => [
                'name'          => __('导航分类', 'techpress'),
                'singular_name' => __('导航分类', 'techpress'),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => true,
            'hierarchical' => true,
            'show_admin_column' => true,
        ]);
    }

    public function add_meta_boxes() {
        add_meta_box('techpress_resource_fields', __('资源信息', 'techpress'), [$this, 'resource_meta_box'], 'tech_resource', 'normal', 'high');
        add_meta_box('techpress_navigation_fields', __('站点信息', 'techpress'), [$this, 'navigation_meta_box'], 'tech_navigation', 'normal', 'high');
    }

    public function resource_meta_box($post) {
        wp_nonce_field('techpress_resource_save', 'techpress_resource_nonce');
        $url = get_post_meta($post->ID, '_resource_url', true);
        $desc = get_post_meta($post->ID, '_resource_desc', true);
        $type = get_post_meta($post->ID, '_resource_type', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="resource_url"><?php esc_html_e('资源地址', 'techpress'); ?></label></th>
                <td><input type="url" name="resource_url" id="resource_url" value="<?php echo esc_attr($url); ?>" class="regular-text" placeholder="https://"></td>
            </tr>
            <tr>
                <th><label for="resource_type"><?php esc_html_e('资源类型', 'techpress'); ?></label></th>
                <td><input type="text" name="resource_type" id="resource_type" value="<?php echo esc_attr($type); ?>" class="regular-text" placeholder="<?php esc_attr_e('工具 / 下载 / 文档 / 合集', 'techpress'); ?>"></td>
            </tr>
            <tr>
                <th><label for="resource_desc"><?php esc_html_e('资源描述', 'techpress'); ?></label></th>
                <td><input type="text" name="resource_desc" id="resource_desc" value="<?php echo esc_attr($desc); ?>" class="large-text" placeholder="<?php esc_attr_e('一句话说明这个资源的用途', 'techpress'); ?>"></td>
            </tr>
        </table>
        <?php
    }

    public function navigation_meta_box($post) {
        wp_nonce_field('techpress_navigation_save', 'techpress_navigation_nonce');
        $url = get_post_meta($post->ID, '_nav_url', true);
        $desc = get_post_meta($post->ID, '_nav_desc', true);
        $logo = get_post_meta($post->ID, '_nav_logo', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="nav_url"><?php esc_html_e('站点地址', 'techpress'); ?></label></th>
                <td><input type="url" name="nav_url" id="nav_url" value="<?php echo esc_attr($url); ?>" class="regular-text" placeholder="https://"></td>
            </tr>
            <tr>
                <th><label for="nav_logo"><?php esc_html_e('Logo URL（可选）', 'techpress'); ?></label></th>
                <td><input type="url" name="nav_logo" id="nav_logo" value="<?php echo esc_attr($logo); ?>" class="regular-text" placeholder="https://.../logo.png"></td>
            </tr>
            <tr>
                <th><label for="nav_desc"><?php esc_html_e('站点描述', 'techpress'); ?></label></th>
                <td><input type="text" name="nav_desc" id="nav_desc" value="<?php echo esc_attr($desc); ?>" class="large-text" placeholder="<?php esc_attr_e('一句话说明这个站点', 'techpress'); ?>"></td>
            </tr>
        </table>
        <?php
    }

    public function save_meta($post_id) {
        $post_type = get_post_type($post_id);
        if (!isset($this->meta_boxes[$post_type])) {
            return;
        }

        $config = $this->meta_boxes[$post_type];
        if (!isset($_POST[$config['nonce_name']]) || !wp_verify_nonce(wp_unslash($_POST[$config['nonce_name']]), $config['nonce_action'])) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        foreach ($config['fields'] as $meta_key => $field) {
            if (!isset($_POST[$field['name']])) {
                continue;
            }

            $raw = wp_unslash($_POST[$field['name']]);
            $value = 'url' === $field['sanitize'] ? esc_url_raw($raw) : sanitize_text_field($raw);
            update_post_meta($post_id, $meta_key, $value);
        }
    }

    public function get_resources($limit = 6) {
        return new \WP_Query([
            'post_type'              => 'tech_resource',
            'posts_per_page'         => $limit,
            'post_status'            => 'publish',
            'orderby'                => 'date',
            'order'                  => 'DESC',
            'ignore_sticky_posts'    => true,
            'no_found_rows'          => true,
            'update_post_meta_cache' => true,
            'update_term_meta_cache' => false,
            'meta_query'             => [
                [
                    'key'     => '_resource_url',
                    'value'   => '',
                    'compare' => '!=',
                ],
            ],
        ]);
    }

    public function get_navigation_links($limit = 8) {
        return new \WP_Query([
            'post_type'              => 'tech_navigation',
            'posts_per_page'         => $limit,
            'post_status'            => 'publish',
            'orderby'                => 'menu_order title',
            'order'                  => 'ASC',
            'ignore_sticky_posts'    => true,
            'no_found_rows'          => true,
            'update_post_meta_cache' => true,
            'update_term_meta_cache' => false,
            'meta_query'             => [
                [
                    'key'     => '_nav_url',
                    'value'   => '',
                    'compare' => '!=',
                ],
            ],
        ]);
    }
}
