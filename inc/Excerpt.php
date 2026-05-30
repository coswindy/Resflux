<?php

namespace TechPress;

class Excerpt {

    public function __construct() {
        add_filter('excerpt_more', [$this, 'more']);
        add_filter('excerpt_length', [$this, 'length']);
    }

    public function more() {
        return '...';
    }

    public function length() {
        return 60;
    }
}
