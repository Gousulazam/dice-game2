<?php
class Database {
    private $host = 'localhost';     // Database host
    private $db_name = 'dice-game'; // Database name
    private $username = 'root'; // Database username
    private $password = ''; // Database password
    private $conn;

    // Database connection
    public function connect() {
        $this->conn = null;

        try {
            // Create PDO instance
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            
            // Set PDO error mode to exception for easier debugging
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Optional: Set the default fetch mode to associative array
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            echo 'Connection Error: ' . $e->getMessage();
        }

        return $this->conn;
    }
}