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
                <h2>Community Post</h2>
                <h5>Admin Panel</h5>
            </div>
        </div>
    </header>
    <style>
        .post-btn{
            color: rgb(181, 211, 161);
        }
    </style>
    <main class="main-parent-container">
        <?php 
        include __DIR__ . "../../includes/sidebar.php";

        ?>
        <section class="main-content">
            <div class="community__post">
                <div class="u-upper">
                    <div class="filter">
                        <i class="fa-solid fa-filter"></i>
                        <select name="" id="post__filter">
                            <option value="">Filter</option>
                            <option value="Latest">Latest</option>
                            <option value="Oldest">Oldest</option>
                        </select>
                    </div>
                    <input type="text" placeholder="Search" hidden>
                </div>
                <div class="c-bot" id="post_parent_container">
                    
                </div>
            </div>
        </section>
    </main>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="../../assets/js/style_2.js"></script>
<script src="../../assets/js/sweetalert.js"></script>
<script src="../../assets/js/function2.js"></script>
</html>