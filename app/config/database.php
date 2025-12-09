<?php
class Database {
    private $host = "localhost";
    private $db   = "adaptivequizdb"; // đúng theo SQL bạn gửi
    private $user = "root";
    private $pass = "";
    private $charset = "utf8mb4";
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            $dsn = "mysql:host=localhost;dbname=adaptivequizdb;charset=utf8mb4";
            $opts = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            try {
                self::$instance = new PDO($dsn, "root", "", $opts);
            } catch (PDOException $e) {
                die("Kết nối DB thất bại: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
    public static function getConnection() {
        $host = 'localhost';
        $dbname = 'AdaptiveQuizDB'; // sửa lại tên database
        $username = 'root';           // sửa lại username
        $password = '';           // sửa lại password
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
        return new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, $options);
    }
}
