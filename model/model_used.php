<?php
require_once "database.php";
$db = Database::getInstance();
$con = $db->conn; 


//session_start();
require 'vendor/autoload.php';


if ($_SERVER["REQUEST_METHOD"] == 'GET') {
    $query = mysqli_prepare($con, "SELECT modelName FROM modelversion WHERE deployed = 1 LIMIT 1");
    if ($query) {
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if (!empty($row['modelName'])) {
                echo $row['modelName'];
            } else {
                echo "original_backup"; 
            }
        } else {
            echo "original_backup"; 
        }

        mysqli_stmt_close($query);
    } else {
        echo "Error: Unable to execute query.";
    }
}

?>