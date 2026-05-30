<?php

namespace TechPress;

class Autoloader {

    private static $registered = false;

    public static function register() {
        if (self::$registered) {
            return;
        }
        self::$registered = true;

        spl_autoload_register(function ($class) {
            $prefix = 'TechPress\\';
            $base_dir = get_template_directory() . '/inc/';

            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }

            $relative_class = substr($class, $len);
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            if (file_exists($file)) {
                require $file;
            }
        });
    }
}
