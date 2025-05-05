<?php
require "helper/admin-session.php";

//absolute pathing 
//require_once __DIR__ . "/../../../model/database.php";

require_once "model/classes.php";
require_once "model/database.php";
admin_session();
$db = Database::getInstance();
$con = $db->conn; 
//$userId = $_SESSION['userId'];
if(isset($_GET['u_id'])){
    $userId = intval($_GET['u_id']);
}else{
    exit;
}
$user = new User($userId, $con);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        include __DIR__ . "../../../includes/sidebar.php";

        //include "../../includes/sidebar.php";?>
        <section class="main-content">
           <div class="user-profile">
                <aside class="aside-btn">
                    <ul>
                        <li class="u__link active__link" data-target="#profile">Profile</li>
                        <li class="u__link" data-target="#post">Post</li>
                        <li class="u__link" data-target="#history">History</li>
                        <li class="u__link" data-target="#flag">Flag Post</li>
                    </ul>
                </aside>
                <div class="section-holder">
                    <section class="profile__section user-detail active__link" content id="profile">
                        <form id="manage__user__form">
                            <div class="up__top">
                                <p><b>Personal Information </b><small>(<?php echo $user->getUserType(); ?>)</small></p>
                                <div class="up__option">
                                    <i class="fa-solid fa-ellipsis" id="option"></i>
                                    <ul class="up_optn">
                                        <li><button onclick="m_user('delete', event)">Delete</button></li>
                                        <li><button onclick="m_user('update', event)">Update</button></li>
                                        <li><button onclick="m_user('flag', event)">Flag</button></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="up__bottom">
                                <input name="userImage" id="p__pic" type="file" hidden>
                                <label for="p__pic"><img src="../../../assets/images/user/<?php echo $user->getUserImg(); ?>" alt=""></label>
                                <div class="up__right">
                                    <label for="">
                                        First Name:
                                        <input name="userId" type="text" hidden value="<?php echo $user->getUserId(); ?>">
                                        <input name="fname" type="text" value="<?php echo $user->getFname(); ?>" placeholder="First name">
                                    </label>
                                    <label for="">
                                        Last Name:
                                        <input name="lname" type="text" value="<?php echo $user->getLname(); ?>" placeholder="Last name">
                                    </label>
                                    <label for=""> 
                                        Username:
                                        <input name="username" type="text" value="<?php echo $user->getUsername(); ?>" placeholder="Username">
                                    </label>
                                    <label for="">
                                        Email:
                                        <input name="email" type="text" value="<?php echo $user->getEmail(); ?>" placeholder="Email">
                                    </label>
                                </div>
                            </div>
                        </form>
                    </section>




                    <!--==========POST============-->
                    <section class="profile__section" content id="post">
                        <div class="top">
                            <p><b>Posts</b> <!--<big>(5)</big>--></p>
                            <div class="filter">
                                <i class="fa-solid fa-filter"></i>
                                <select name="" id="user__post_filter">
                                    <option value="">Filter</option>
                                    <option value="Oldest">Oldest</option>
                                    <option value="Latest">Latest</option>
                                </select>
                            </div>
                        </div>
                        <div class="up_container" id="up_card_parent">
                            <div class="p_card">
                                <img src="../../../assets/images/me.png" alt="plant image" width="80px" height="80px">
                                <div class="u_info">
                                    <h4>Andre Young</h4>
                                    <small>March 19, 2022</small><hr><br>
                                    <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit.  </p>
                                </div>
                                <i class="fa-solid fa-trash"></i>
                            </div>
                            <div class="p_card">
                                <img src="../../../assets/images/5.png" alt="" width="80px" height="80px">
                                <div class="u_info">
                                    <h4>Andre Young</h4>
                                    <small>March 19, 2022</small><hr><br>
                                    <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit.  </p>
                                </div>
                                <i class="fa-solid fa-trash"></i>

                            </div>
                        </div>
                    </section>


                    <!--==========HISTORY============-->
                    <section class="profile__section" content id="history">
                        <div class="top">
                            <p><b> Queries</b> <!--<big>(5)</big>--></p>
                            <div class="filter">
                                <i class="fa-solid fa-filter"></i>
                                <select name="" id="user__history_filter">
                                    <option value="">Filter</option>
                                    <option value="Oldest">Oldest</option>
                                    <option value="Latest">Latest</option>
                                </select>
                            </div>
                        </div>
                        <div class="up_container" id="uh_card_parent">
                            <div class="card">
                                <div class="h-info">
                                    <h3>Mosaic Virus</h3>
                                    <small>Nov 19 2022</small>
                                </div>
                                <i class="fa-solid fa-trash"></i>
                            </div>
                        </div>
                    </section>


                    <!--==========FLAG============-->
                    <section class="profile__section" content id="flag">
                        <div class="top">
                            <p><b>Flag Posts</b> <!--<big>(5)</big>--></p>
                            <div class="filter">
                                <i class="fa-solid fa-filter"></i>
                                <select name="" id="user__flagged_filter">
                                    <option value="">Filter</option>
                                    <option value="Oldest">Oldest</option>
                                    <option value="Latest">Latest</option>
                                </select>
                            </div>
                        </div>
                        <div class="up_container" id="uf_card_parent">
                            <div class="p_card">
                                <img src="../../../assets/images/1.png" alt="plant image" width="80px" height="80px">
                                <div class="u_info">
                                    <h4>Andre Young</h4>
                                    <small>March 19, 2022</small><hr><br>
                                    <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit.  </p>
                                </div>
                                <i class="fa-solid fa-trash"></i>

                            </div>
                            <div class="p_card">
                                <img src="../../assets/images/5.png" alt="" width="80px" height="80px">
                                <div class="u_info">
                                    <h4>Andre Young</h4>
                                    <small>March 19, 2022</small><hr><br>
                                    <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit.  </p>
                                </div>
                                <i class="fa-solid fa-trash"></i>

                            </div>
                        </div>
                    </section>
                </div>
           </div>
        </section>
    </main>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="../../../assets/js/style_2.js"></script>
<script src="../../../assets/js/sweetalert.js"></script>
<script src="../../../assets/js/function2.js"></script>
<script>
    show_user_post(<?php echo $user->getUserId(); ?>);
    show_user_histories(<?php echo $user->getUserId(); ?>);
    show_user_flagged(<?php echo $user->getUserId(); ?>);
    
</script>
</html>