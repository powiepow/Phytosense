<?php

require_once "database.php";
$db = Database::getInstance();
$con = $db->conn; 
require 'vendor/autoload.php';





if(isset($_POST['email'])){
     $email = $_POST['email'];
    
    $token = bin2hex(random_bytes(16));

    //$token_hash = hash("sha256", $token);

    $expiry = date("Y-m-d H:i:s", time() + 60*30);

    $query =  mysqli_prepare($con, "update tbluser set reset_token = ?, token_expiry = ?
    where email = ?");
    mysqli_stmt_bind_param($query, "sss", $token, $expiry, $email);
    $exe= mysqli_stmt_execute($query);

    if(mysqli_affected_rows($con) < 1){
        echo "Email !sent";
        exit;
    }else{

        $mail = require __DIR__ . "/mailer.php";

        $mail-> setFrom("noreply@example.com");
        $mail->addAddress($email);
        $mail->Subject = "Password Reset";
        $mail->Body = "Click <a href=\"https://phytosense.site/reset_code?token=$token\">here</a> 
        to reset your password.";


        /*
        Click <a href="https://phytosense.site/reset_code?token=$token">here</a> 
        to reset your password.
        */
        try{
            $mail->send();
        }catch(Exception $e){
            echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
        }
        echo "Reset code sent to " . $email;
    }
    mysqli_stmt_close($query);

}



if(isset($_POST['reset_code'])) {
    $reset_code = $_POST['reset_code'];

    $query = mysqli_prepare($con, "SELECT reset_token FROM tbluser WHERE reset_token = ? LIMIT 1");
    mysqli_stmt_bind_param($query, "s", $reset_code);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);

    if (mysqli_num_rows($result) == 1) {
        $change_pass = $_POST['new_pass'];

        $hashed_password = password_hash($change_pass, PASSWORD_BCRYPT);

        $update_query = mysqli_prepare($con, "UPDATE tbluser SET password = ? WHERE reset_token = ?");
        mysqli_stmt_bind_param($update_query, "ss", $hashed_password, $reset_code);

        $update_result = mysqli_stmt_execute($update_query);

        if ($update_result) {
            echo "<script>alert('Password changed. Please signin'); window.location.href='/signin'</script>";
        } else {
            echo "<script>alert('Error changing password. Please try again.'); window.location.href='/reset-password'</script>";
        }
    } else {
        // Invalid reset code
        //echo "<script>window.location.href='/signin'</script>";
    }
}
?>