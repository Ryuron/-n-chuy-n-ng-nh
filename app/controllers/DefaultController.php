<?php
class DefaultController {
    public function index() {
        $title = "AdaptiveQuizSystem";
        include ROOT_PATH . "/app/views/shares/header.php";
        echo "<div class='container'><h1>$title</h1><p>Chào mừng bạn đến hệ thống trắc nghiệm thích ứng.</p>
        <p><a href='index.php?controller=account&action=login'>Đăng nhập</a> |
           <a href='index.php?controller=account&action=register'>Đăng ký</a></p></div>";
        include ROOT_PATH . "/app/views/shares/footer.php";
    }
}
