<?php
session_start();

function admin_session(){
    if (isset($_SESSION['userId']) && isset($_SESSION['userType']) && isset($_SESSION['isAdmin'])) {
        if($_SESSION['userType'] == 'Admin' && $_SESSION['isAdmin'] == 1){
            return 1;
        }else{
            echo "<script>window.location.href='/signin'</script>";
            exit;
        }
        
    }else{
        echo "<script>window.location.href='/signin'</script>";
        exit;
    }
}

?>