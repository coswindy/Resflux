<?php

namespace TechPress;

class FriendLinks {

    public function __construct() {
        add_action('init', [$this, 'register_post_type']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_friend_link', [$this, 'save_meta']);
    }

    public function register_post_type() {
        register_post_type('friend_link', [
            'labels' => [
                'name'          => __('友情链接', 'techpress'),
                'singular_name' => __('友链', 'techpress'),
                'add_new_item'  => __('添加友链', 'techpress'),
                'edit_item'     => __('编辑友链', 'techpress'),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => true,
            'menu_icon'    => 'dashicons-admin-links',
            'menu_position' => 25,
            'supports'     => ['title'],
            'capability_type' => 'post',
            'map_meta_cap' => true,
        ]);
    }

    public function add_meta_boxes() {
        add_meta_box('techpress_fl_fields', __('友链信息', 'techpress'), [$this, 'meta_box'], 'friend_link', 'normal', 'high');
    }

    public function meta_box($post) {
        wp_nonce_field('techpress_fl_save', 'techpress_fl_nonce');
        $url  = get_post_meta($post->ID, '_fl_url', true);
        $desc = get_post_meta($post->ID, '_fl_desc', true);
        $logo = get_post_meta($post->ID, '_fl_logo', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="fl_url"><?php _e('网站地址', 'techpress'); ?></label></th>
                <td><input type="url" name="fl_url" id="fl_url" value="<?php echo esc_attr($url); ?>" class="regular-text" placeholder="https://"></td>
            </tr>
            <tr>
                <th><label for="fl_desc"><?php _e('网站描述', 'techpress'); ?></label></th>
                <td><input type="text" name="fl_desc" id="fl_desc" value="<?php echo esc_attr($desc); ?>" class="regular-text" placeholder="<?php esc_attr_e('简短描述', 'techpress'); ?>"></td>
            </tr>
            <tr>
                <th><label for="fl_logo"><?php _e('Logo URL（可选）', 'techpress'); ?></label></th>
                <td><input type="url" name="fl_logo" id="fl_logo" value="<?php echo esc_attr($logo); ?>" class="regular-text" placeholder="https://.../logo.png"></td>
            </tr>
        </table>
        <?php
    }

    public function save_meta($post_id) {
        if (!isset($_POST['techpress_fl_nonce']) || !wp_verify_nonce(wp_unslash($_POST['techpress_fl_nonce']), 'techpress_fl_save')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        foreach (['fl_url', 'fl_desc', 'fl_logo'] as $field) {
            if (isset($_POST[$field])) {
                $val = (in_array($field, ['fl_url', 'fl_logo']))
                    ? esc_url_raw(wp_unslash($_POST[$field]))
                    : sanitize_text_field(wp_unslash($_POST[$field]));
                update_post_meta($post_id, '_' . $field, $val);
            }
        }
    }

    public function get_all($limit = 50) {
        return new \WP_Query([
            'post_type'           => 'friend_link',
            'posts_per_page'      => $limit,
            'post_status'         => 'publish',
            'orderby'             => 'title',
            'order'               => 'ASC',
        ]);
    }
}
