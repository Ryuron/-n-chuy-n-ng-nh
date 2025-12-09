<?php

class SessionHelper {
    /**
     * Bắt đầu session nếu chưa được bắt đầu.
     * @return bool Trả về true nếu session đã được khởi tạo thành công.
     */
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            return session_start();
        }
        return true;
    }

    /**
     * Đặt giá trị vào session.
     */
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Lấy giá trị từ session.
     */
    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Hủy session.
     */
    public static function destroy() {
        if (session_status() !== PHP_SESSION_NONE) {
            session_unset();
            session_destroy();
        }
    }
}