<?php

namespace TechPress;

class Thumbnail {

    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_meta_box']);
        add_action('save_post', [$this, 'save_meta']);
        add_action('publish_post', [$this, 'init_views'], 10, 2);
    }

    public function get_url($post_id = 0, $skip_content = false) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        if (has_post_thumbnail($post_id)) {
            $src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'large');
            if ($src) {
                return $src[0];
            }
        }

        $ext = get_post_meta($post_id, '_techpress_thumb_url', true);
        if ($ext) {
            return $ext;
        }

        if (!$skip_content) {
            $content = get_post_field('post_content', $post_id);
            if (preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', $content, $matches)) {
                return $matches[1];
            }
        }

        $default = techpress_setting('techpress_default_thumbnail', '');
        if ($default) {
            return $default;
        }

        return 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="800" height="450" viewBox="0 0 800 450"><rect fill="#1e293b" width="800" height="450"/><text x="400" y="225" fill="#475569" font-family="sans-serif" font-size="20" text-anchor="middle" dominant-baseline="middle">Resflux</text></svg>');
    }

    public function the_img($post_id = 0, $size = 'large', $class = '', $skip_content = false, $attr = []) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        $attr = array_merge([
            'class'   => $class,
            'alt'     => get_the_title($post_id),
            'loading' => 'lazy',
        ], $attr);

        if (has_post_thumbnail($post_id)) {
            echo wp_get_attachment_image(get_post_thumbnail_id($post_id), $size, false, $attr);
            return;
        }

        $url = $this->get_url($post_id, $skip_content);
        $attrs = '';
        foreach ($attr as $name => $value) {
            if ('' === $value || null === $value || false === $value) {
                continue;
            }
            $attrs .= ' ' . esc_attr($name) . '="' . esc_attr($value) . '"';
        }

        echo '<img src="' . esc_url($url) . '"' . $attrs . '>';
    }

    public function add_meta_box() {
        add_meta_box('techpress_thumb_url', __('外链缩略图 URL', 'techpress'), [$this, 'meta_box_cb'], 'post', 'side', 'low');
    }

    public function meta_box_cb($post) {
        wp_nonce_field('techpress_thumb_save', 'techpress_thumb_nonce');
        $val = get_post_meta($post->ID, '_techpress_thumb_url', true);
        echo '<input type="url" name="techpress_thumb_url" value="' . esc_attr($val) . '" style="width:100%" placeholder="https://...">';
        echo '<p class="howto" style="margin-top:6px;font-size:12px;color:#666;">' . __('未设置特色图像时使用此 URL，留空则自动取文章第一张图', 'techpress') . '</p>';
    }

    public function save_meta($post_id) {
        if (!isset($_POST['techpress_thumb_nonce']) || !wp_verify_nonce(wp_unslash($_POST['techpress_thumb_nonce']), 'techpress_thumb_save')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['techpress_thumb_url'])) {
            update_post_meta($post_id, '_techpress_thumb_url', esc_url_raw(wp_unslash($_POST['techpress_thumb_url'])));
        }
    }

    public function init_views($post_id, $post) {
        if ('post' !== $post->post_type) {
            return;
        }
        if (!metadata_exists('post', $post_id, '_techpress_views')) {
            update_post_meta($post_id, '_techpress_views', 0);
        }
    }
}
