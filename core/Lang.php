<?php

class Lang {
    private static $translations = [];

    public static function load($lang) {
        $file = __DIR__ . '/../lang/' . $lang . '.php';
        if (file_exists($file)) {
            self::$translations = require $file;
        } else {
            // Fallback to English if file doesn't exist
            $fallback = __DIR__ . '/../lang/en.php';
            if (file_exists($fallback)) {
                self::$translations = require $fallback;
            } else {
                self::$translations = [];
            }
        }
    }

    public static function get($key) {
        return self::$translations[$key] ?? $key;
    }
}

// Global helper function for ease of use in views
function __($key) {
    return Lang::get($key);
}
