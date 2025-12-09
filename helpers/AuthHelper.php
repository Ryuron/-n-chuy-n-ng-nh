<?php
class AuthHelper {
    public static function requireLogin() {
        SessionHelper::start();
        if (!SessionHelper::get('user')) {
            header("Location: index.php?controller=account&action=login");
            exit;
        }
    }
    public static function requireAdmin() {
        SessionHelper::start();
        $u = SessionHelper::get('user');
        if (!$u || ($u['Role'] ?? 'User') !== 'Admin') {
            header("Location: index.php");
            exit;
        }
    }
}
