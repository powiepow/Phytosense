<?php
require "helper/admin-session.php";
admin_session();
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
                <h2>Users</h2>
                <h5>Admin Panel</h5>
            </div>
        </div>
    </header>
    <style>
        .user-btn{
            color: rgb(181, 211, 161);
        }
    </style>
    <main class="main-parent-container">
    <?php 
        include __DIR__ . "../../includes/sidebar.php";

        //include "../../includes/sidebar.php";?>
        <section class="main-content">
            <div class="users">
                <div class="u-upper">
                    
                    <div class="filter" style="border:none; color:transparent; background:transparent;">
                        <i  class="fa-solid fa-filter"></i>
                        <select name="" id="" hidden>
                            <option value="">Filter</option>
                            <option value="">Test</option>
                            <option value="">Test 2</option>
                        </select>
                    </div>
                    <span><input style="height:30px; margin-right:5px;" type="text" name="search_user" id="search_user" placeholder="Search">
                    <button style="height:30px; border:1px solid lightgray; background:transparent; border-radius:5px; padding:5px; " id="search_btn"><i class="fa-solid fa-magnifying-glass"></i></button></span>
                </div>
                <div class="u-lower" id="user_card">
                    <div class="u-card" onclick="window.location.href='/a_view/page/user_profile'">
                        <img src="../../assets/images/me.png" alt="User Image">
                        <div class="u-info">
                            <h4>Andre Young</h4>
                            <small>Joined on</small>
                            <b><small><i>March 19, 2022</i></small></b>
                        </div>
                        <div class="u-add-info">
                            <b>Usage</b>
                            <p>12</p>
                        </div>
                    </div>
                    <div class="u-card">
                        <img src="../../assets/images/9.png" alt="User Image">
                        <div class="u-info">
                            <h4>Andre Young</h4>
                            <small>Joined on</small>
                            <b><small><i>March 19, 2022</i></small></b>
                        </div>
                        <div class="u-add-info">
                            <b>Usage</b>
                            <p>12</p>
                        </div>
                    </div>
                    
                </div>
            </div>
        </section>
    </main>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="../../assets/js/function2.js"></script>
<script src="../../assets/js/style_2.js"></script>
<script> 
        showUsers();
</script>
</html>