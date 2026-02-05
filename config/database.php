<?php
// config/database.php

class Database {
    private $host = "localhost";
    private $db_name = "deped_silay_hris";
    private $username = "root";
    private $password = "";
    public $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username, 
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
    
    public function generateEmployeeID() {
        $year = date('Y');
        $month = date('m');
        $random = rand(1000, 9999);
        return "DEPED-SILAY-" . $year . $month . "-" . $random;
    }
    
    public function generateDepEdID($firstName, $lastName) {
        $firstThree = substr(strtoupper($firstName), 0, 3);
        $lastThree = substr(strtoupper($lastName), -3);
        $random = rand(100, 999);
        return $firstThree . $lastThree . $random;
    }
}
?>