<?php
class SessionHelper {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) session_start();
    }
    public static function set($k, $v) {
        self::start(); $_SESSION[$k] = $v;
    }
    public static function get($k, $default = null) {
        self::start(); return $_SESSION[$k] ?? $default;
    }
    public static function destroy() {
        self::start(); session_destroy();
    }
}
