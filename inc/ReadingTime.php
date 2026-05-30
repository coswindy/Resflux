<?php

namespace TechPress;

class ReadingTime {

    public function __construct() {}

    public function get($post_id = 0) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        $content = get_post_field('post_content', $post_id);
        $words   = mb_strlen(wp_strip_all_tags($content)) / 4;
        $wpm     = apply_filters('techpress_reading_wpm', 300);
        $minutes = max(1, ceil($words / $wpm));
        return sprintf(__('%d 分钟阅读', 'techpress'), $minutes);
    }
}
