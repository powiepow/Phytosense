<?php

class Database {
    private $host = "localhost";    
    private $user = "root";
    private $pass = "";
    private $dbname = "phytosenseDB";
    public $conn; 

    public function __construct() {
        $this->connect();          
    }

    private function connect() {  
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public static function getInstance() {
        static $instance = null;

        if ($instance === null) {
            $instance = new Database(); 
        }

        return $instance;
    }
}
?>