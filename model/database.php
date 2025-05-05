<?php

class Database {
    private $host = "localhost";    // Replace with your actual credentials
    private $user = "root";
    private $pass = "";
    private $dbname = "phytosenseDB";
    public $conn; // Make the connection accessible

    public function __construct() {
        $this->connect();          // Call the connect method in the constructor
    }

    private function connect() {   // Make the connect method private
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public static function getInstance() {
        static $instance = null;

        if ($instance === null) {
            $instance = new Database(); // Create a new instance of the Database class
        }

        return $instance;
    }
}
?>