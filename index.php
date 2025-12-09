<?php
// Bật lỗi khi dev
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Bắt buộc phải start session trước khi dùng
session_start();

// Định nghĩa đường dẫn gốc
define('ROOT_PATH', __DIR__);

// Autoload rất đơn giản cho helpers
spl_autoload_register(function ($class) {
    $paths = [
        ROOT_PATH . "/helpers/$class.php",
        ROOT_PATH . "/app/config/$class.php",
        ROOT_PATH . "/app/models/$class.php",
        ROOT_PATH . "/app/controllers/$class.php",
    ];
    foreach ($paths as $p) {
        if (file_exists($p)) { 
            require_once $p; 
            return; 
        }
    }
});

require_once ROOT_PATH . "/routes.php";

$controller = $_GET['controller'] ?? 'default';
$action     = $_GET['action'] ?? 'index';

callAction($controller, $action);
