<?php

namespace TechPress;

class RelatedPosts {

    public function __construct() {}

    public function get($post_id = 0, $count = 3) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        $categories = wp_get_post_categories($post_id);
        $tags       = wp_get_post_tags($post_id, ['fields' => 'ids']);

        return new \WP_Query([
            'post_type'           => 'post',
            'posts_per_page'      => $count,
            'post__not_in'        => [$post_id],
            'ignore_sticky_posts' => true,
            'no_found_rows'       => true,
            'update_post_meta_cache' => false,
            'update_term_meta_cache' => false,
            'tax_query'           => [
                'relation' => 'OR',
                ['taxonomy' => 'category', 'field' => 'term_id', 'terms' => $categories],
                ['taxonomy' => 'post_tag', 'field' => 'term_id', 'terms' => $tags],
            ],
            'orderby' => 'comment_count',
        ]);
    }
}
