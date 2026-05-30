<?php

namespace TechPress;

class PostViews {

    public function __construct() {}

    public function get($post_id = 0) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        return (int) get_post_meta($post_id, '_techpress_views', true);
    }
}
