<?php
require "helper/admin-session.php";
require "helper/dashboard.php";
admin_session();
require_once __DIR__ . "/../../model/database.php";

$db = Database::getInstance();
$con = $db->conn; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/style/index_2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Phytosense - Admin Panel</title>
</head>
<body>
    <header>
        <div class="brnd-container">
            <div class="logo">
                <img src="../../assets/images/logo.png" alt="" >
            </div>
            <div class="brnd-nme">
                <h2>Dashboard</h2>
                <h5>Admin Panel</h5>
            </div>
        </div>
    </header>
    <style>
        .dash-btn{
            color: rgb(181, 211, 161);
            
        }
    </style>
    <main class="main-parent-container">
        <?php 
        include __DIR__ . "../../includes/sidebar.php";

        //include "../../includes/sidebar.php";?>
        <section class="main-content">
            <div class="dashboard">
                <div class="dash-upper">
                    <div class="dashcard">
                        <div class="d-left">
                            <i class="fa-solid fa-list"></i>
                        </div>
                        <div class="d-right">
                            <h2><?php echo historyCount($con); ?></h2>
                            <p>Querried</p>
                        </div>
                    </div>
                    <div class="dashcard">
                        <div class="d-left">
                            <i class="fa-solid fa-people-group"></i>
                        </div>
                        <div class="d-right">
                            <h2><?php echo userCount($con); ?></h2>
                            <p>Users</p>
                        </div>
                    </div>
                    <div class="dashcard">
                        <div class="d-left">
                            <i class="fa-solid fa-newspaper"></i>
                        </div>
                        <div class="d-right">
                            <h2><?php echo postCount($con); ?></h2>
                            <p>Post/s</p>
                        </div>
                    </div>
                    <div class="dashcard">
                        <div class="d-left">
                            <i class="fa-solid fa-file-circle-exclamation"></i>
                        </div>
                        <div class="d-right">
                            <h2><?php echo reportedPostCount($con); ?></h2>
                            <p>Report</p>
                        </div>
                    </div>
                </div>
                <div class="dash-bottom">
                    <h2>Recent Post</h2>
                    <div class="recent-post-container">
                        <?php getLatestPost($con); ?>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="../../assets/js/style_2.js"></script>
</html>