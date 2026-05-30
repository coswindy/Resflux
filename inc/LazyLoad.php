<?php

namespace TechPress;

class LazyLoad {

    public function __construct() {
        add_filter('wp_content_img_tag', [$this, 'add_lazy']);
        add_filter('script_loader_tag', [$this, 'async_scripts'], 10, 3);
    }

    public function add_lazy($image) {
        if (strpos($image, 'loading=') === false) {
            $image = str_replace('<img ', '<img loading="lazy" ', $image);
        }
        return $image;
    }

    public function async_scripts($tag, $handle, $src) {
        $async = ['techpress-carousel', 'techpress-dark-mode', 'techpress-search-modal', 'techpress-mobile-menu', 'techpress-back-to-top', 'techpress-progress-bar', 'techpress-view-counter', 'techpress-share-buttons', 'techpress-category-tabs', 'techpress-auth', 'techpress-submit-post'];
        if (in_array($handle, $async) && strpos($tag, 'async') === false) {
            $tag = str_replace('<script ', '<script async defer ', $tag);
        }
        return $tag;
    }
}
