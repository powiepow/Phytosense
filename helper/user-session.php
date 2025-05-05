<?php
session_start();

function user_session(){
    if(isset($_SESSION['userId'])){
        return 1;
    }else{
        echo "<script>window.location.href='/signin'</script>";
        exit;
    }
}

?>