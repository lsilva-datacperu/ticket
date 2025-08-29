<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $host = "localhost";
        $dbname = "datacper_ticketing";
        $user = "datacper_root";
        $pass = "4PHlz5QPW&%M";

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}

