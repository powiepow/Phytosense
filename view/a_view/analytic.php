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
    <header >
        <div class="brnd-container">
            <div class="logo">
                <img src="../../assets/images/logo.png" alt="" >
            </div>
            <div class="brnd-nme">
                <h2>Analytics</h2>
                <h5>Admin Panel</h5>
            </div>
        </div>
    </header>
    <style>
        .analytic-btn{
            color: rgb(181, 211, 161);
        }
    </style>
    <main class="main-parent-container">
        <?php 
            include __DIR__ . "../../includes/sidebar.php";
           ?>
        <section class="main-content">
            <div class="analytic_area">
                <div class="analytic_left">
                    <p>Disease Analytics</p>
                    <div class="chart_area">
                        
                        <canvas id="myBarChart"></canvas>
                        

                    </div>
                </div>
                <div class="analytic_right">


                    <div class="a_card" onclick="window.location.href='/a_view/page/user'">
                        <div class="t_a_card">
                            <p>User Count</p>
                        </div>
                        <div class="m_a_card">
                            <b><?php echo userCount($con); ?></b>
                            <p>Users</p>
                        </div>
                    </div>

                    <div class="a_card" onclick="window.location.href='/a_view/page/community'">
                        <div class="t_a_card">
                            <p>User Activity</p>
                        </div>
                        <div class="m_a_card">
                            <b><?php echo postCount($con); ?></b>
                            <p>Posts</p>
                        </div>
                    </div>

                    <div class="a_card" onclick="">
                        <div class="t_a_card">
                            <p>Total Query Histories</p>
                        </div>
                        <div class="m_a_card">
                            <b><?php echo historyCount($con); ?></b>
                            <p>Queries</p>
                        </div>
                    </div>


                    <div class="a_card" onclick="window.location.href='/a_view/page/flagged'">
                        <div class="t_a_card">
                            <p>Flagged Users</p>
                        </div>
                        <div class="m_a_card">
                            <b><?php echo isFlagUser($con); ?></b>
                            <p>Users</p>
                        </div>
                    </div>

                    <div class="a_card" onclick="window.location.href='/a_view/page/usertype'">
                        <div class="t_a_card">
                            <p><i class="fa-solid fa-person"></i> Check</p>
                        </div>
                        <div class="m_a_card">
                            <b>User Type</b>
                            
                        </div>
                    </div>

                    <div class="a_card" onclick="window.location.href='/a_view/page/reported'">
                        <div class="t_a_card">
                            <p>Reported Post</p>
                        </div>
                        <div class="m_a_card">
                            <b><?php echo reportedPostCount($con); ?></b>
                            <p>Posts</p>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="../../assets/js/style_2.js"></script>
<script src="../../assets/js/function2.js"></script>
<script>analyticBarChart();</script>
</html>