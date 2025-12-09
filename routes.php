<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__);
}


function callAction($controller, $action) {
    // Chuẩn hóa tên controller: admin → AdminController
    $controllerName = ucfirst(strtolower($controller)) . "Controller";
    $controllerFile = ROOT_PATH . "/app/controllers/{$controllerName}.php";

    if (!file_exists($controllerFile)) {
        http_response_code(404);
        echo "⚠️ Controller <b>$controllerName</b> không tồn tại.";
        exit;
    }
    require_once $controllerFile;

    if (!class_exists($controllerName)) {
        http_response_code(500);
        echo "⚠️ Class $controllerName không tồn tại trong $controllerFile.";
        exit;
    }

    $ctrl = new $controllerName();

    if (!method_exists($ctrl, $action)) {
        http_response_code(404);
        echo "⚠️ Action <b>$action</b> không tồn tại trong $controllerName.";
        exit;
    }

    // Gọi action
    $ctrl->$action();
}
