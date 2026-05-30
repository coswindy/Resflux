<?php

namespace TechPress;

class QueryHooks {

    public function __construct() {
        add_action('pre_get_posts', [$this, 'sticky_handling']);
        add_action('pre_get_posts', [$this, 'search_filter']);
        add_filter('posts_search', [$this, 'include_taxonomy_matches'], 10, 2);
    }

    public function sticky_handling($query) {
        if (is_admin() || !$query->is_main_query()) {
            return;
        }
        if ($query->is_home() && $query->is_front_page()) {
            $query->set('ignore_sticky_posts', false);
        }
    }

    public function search_filter($query) {
        if (is_admin() || !$query->is_main_query() || !$query->is_search()) {
            return;
        }

        $query->set('post_type', ['post', 'page']);
    }

    public function include_taxonomy_matches($search, $query) {
        if (is_admin() || !$query->is_main_query() || !$query->is_search() || empty($search)) {
            return $search;
        }

        global $wpdb;

        $keyword = trim((string) $query->get('s'));
        if ('' === $keyword) {
            return $search;
        }

        $like = '%' . $wpdb->esc_like($keyword) . '%';
        $base_search = preg_replace('/^\s*AND\s*/', '', $search);
        $password_check = is_user_logged_in() ? '' : " AND {$wpdb->posts}.post_password = ''";

        $taxonomy_search = $wpdb->prepare(
            "{$wpdb->posts}.ID IN (
                SELECT tr.object_id
                FROM {$wpdb->term_relationships} tr
                INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
                WHERE tt.taxonomy IN ('category', 'post_tag')
                  AND (t.name LIKE %s OR t.slug LIKE %s)
            ){$password_check}",
            $like,
            $like
        );

        return " AND (({$base_search}) OR {$taxonomy_search})";
    }
}
