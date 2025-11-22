<?php
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "crowd_zero";
    private $connection;

    public function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->database}", 
                $this->username, 
                $this->password,
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            // Better error handling for debugging
            error_log("Database Connection Failed: " . $e->getMessage());
            die(json_encode(["success" => false, "message" => "Database connection failed"]));
        }
    }

    public function getConnection() {
        return $this->connection;
    }
}
?>