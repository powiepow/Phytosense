<?php
require "helper/admin-session.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="../../../assets/style/table_style.css">
    <link rel="stylesheet" href="../../../assets/style/index_2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Phytosense - Admin Panel</title>
</head>
<body>
    <header >
        <div class="brnd-container">
            <div class="logo">
                <img src="../../../assets/images/logo.png" alt="" >
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
        include __DIR__ . "../../../includes/sidebar.php";

        ?>
        <section class="main-content">
            
            <div class="table_container">
                <div class="u-upper">
                    <div class="filter">
                        <i class="fa-solid fa-filter"></i>
                        <select name="" id="reportedFilter">
                            <option value="">Filter</option>
                            <option value="Lowest">Lowest</option>
                            <option value="Highest">Highest</option>
                        </select>
                    </div>
                    <input hidden type="text" placeholder="Search">
                </div>
                <div class="lower_t">
                    
                    <div class="table-wrapper">
                        <table class="fl-table">
                            <thead>
                                
                            <caption style="padding:10px; background:#5e8e56; color:white;"><h2>Reported Posts</h2></caption>
                            
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Date Posted</th>
                                <th>Reported Count</th>
                                <th>Action</th>
                            </tr>
               
                            </thead>
                            <tbody id="table_report">
                            <tr>
                                <td><img src="../../../assets/images/post/" style="width:50px; border-radius:50px; height:50px;" alt="post img"></td>
                                <td>Content 1</td>
                                <td>Content 1</td>
                                <td>Content 1</td>
                                <td><button class="tbl_btn">View</button></td>
                            </tr>
                            <tr>
                                <td>Content 2</td>
                                <td>Content 2</td>
                                <td>Content 2</td>
                                <td>Content 2</td>
                                <td><button class="tbl_btn">View</button></td>
                            </tr>
                            
                            <tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="../../../assets/js/style_2.js"></script>
<script src="../../../assets/js/sweetalert.js"></script>
<script src="../../../assets/js/function2.js"></script>
<script>reportedPost();</script>
</html>