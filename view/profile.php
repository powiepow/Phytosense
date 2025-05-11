<?php
require "helper/user-session.php";
require_once "model/classes.php";
require_once "model/database.php";
user_session();
$db = Database::getInstance();
$con = $db->conn; 
$userId = $_SESSION['userId'];
$user = new User($userId, $con);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Phytosense</title>
</head>
<body>
    <header>
        <div class="sticky" id="sidebar">
            <div class="brnd-container">
                <div class="logo">
                    <img src="assets/images/logo.png" alt="">
                </div>
                <div class="brnd-nme">
                    <h2>Phytosense</h2>
                    <h5>Crop Disease Diagnostic Platform</h5>
                </div>
            </div>
            <div class="nav-lnk">
                <div class="hdr-links lnk">
                    <input type="checkbox" id="menu" hidden>
                    <ul class="ul">
                        <li></i><a href="/"><i class="fa-solid fa-house"></i> Home</a> </li>
                        <li> <a href="/community"><i class="fa-solid fa-circle-info"></i> Community</a></li>
                        <li> <a href="/diagnose"><i class="fa-solid fa-leaf"></i> Diagnose</a></li>
                        <li><a href="/profile"><i class="fa-solid fa-user"></i> Profile</a></li>      
                        <li class="lgn"><a href="/logout"> Log out</a></li>
                        
                        <label for="menu" class="close-menu"><i class="fas fa-times"></i></label>
                    </ul>
                </div>
                
            </div>
            <label for="menu" class="open-menu"><i class="fas fa-bars"></i></label>
        </div>
    </header>
    
    <main class="p-prnt-container">
        
        <div class="profile-container hidden">
            <aside class="p__sidebar">
                <ul>
                    <li class="p__link active__link" data-target="#profile">Profile</li>
                    <li class="p__link" data-target="#history">History</li>
                    <li class="p__link" data-target="#post">Posts</li>
                </ul>
            </aside>



            <section class="profile__section user__profile active__link" content id="profile">
                <big><p><b>Hello,</b> <?php echo $user->getLname(); ?></p></big>
                <form class="p__form" id="update_profile">
                    <div class="p-img-upload">
                        <label for="upload-profile" id="upload-preview">
                            <img  class="pf-image" src="assets/images/user/<?php echo $user->getUserImg(); ?>" alt="">
                        </label>
                        <p>Upload</p>
                        <input name="profile_pic" id="upload-profile" type="file" hidden>
                    </div>
                    <div class="p-inputs">
                        <div class="p-left">
                            <label for="">
                                <p>Firstname</p>
                                <input required name="fname" value="<?php echo $user->getFname(); ?>" type="text">
                            </label>
                            <label for="">
                                <p>Email</p>
                                <input required name="email" value="<?php echo $user->getEmail(); ?>" type="text">
                            </label>
                            
                        </div>
                        <div class="p-right">
                            <label for="">
                                <p>Lastname</p>
                                <input required name="lname" value="<?php echo $user->getLname(); ?>" type="text">
                            </label>
                            <label for="">
                                <p>Username</p>
                                <input required name="username" value="<?php echo $user->getUsername(); ?>" type="text">
                            </label><br>
                            
                            
                            <button name="update_btn">Save Changes</button>
                        </div>
                        
                    </div>
                    
                </form>
            </section>




            <section class="profile__section profile-history" content id="history">
                <div class="h-search">
                    <span class="h-filter">
                        <i class="fa-solid fa-filter"></i>
                        <select name="" id="history-filter">
                            <option value="">Filter</option>
                            <option value="latest">Latest</option>
                            <option value="oldest">Oldest</option>
                        </select>
                    </span>
                    <input id="history-search" type="text" placeholder="Search">
                </div>
                <div class="history-container" id="history_container">
                    <div class="history-box">
                        <span>
                            <b>Disease Name</b><br>
                            <small>Date Querried</small>
                        </span>
                        <span>
                            <a href="diagnosis.html">View</a>
                        </span>
                    </div>
                    
                </div>
                
            </section>

            <section class="profile__section profile-post" content id="post">
                <div class="h-search">
                    <h2>Community Posts</h2>
                    <span class="h-filter">
                        <i class="fa-solid fa-filter"></i>
                        <select name="" id="post-filter">
                            <option value="">Filter</option>
                            <option value="latest">Latest</option>
                            <option value="oldest">Oldest</option>
                        </select>
                    </span>
                </div>
                <div class="p-post-container" id="p_post_container">
                    <div class="posts__box">
                        <div class="posts-upper">
                            <div class="image-side">
                                <img src="assets/images/2.png" alt="" width="100px">
                            </div>
                            <div class="post-detail">
                                <b>March 9 2024</b><br><br>
                                <small>Hey everyone, has anyone else seen this weird spotting on their tomato leaves? They started out as small yellow dots, but now they're growing and turning brown..</small>
                            </div>
                            <a href="">View</a>
                        </div>
                        <div class="post-additionals">
                            <span>
                                <b>10</b> <i class="fa-regular fa-thumbs-up"></i>
                            </span>
                            <span>
                                <b>5</b> <i class="fa-regular fa-thumbs-down"></i>
                            </span>
                            <span>
                                <p></p><b>3</b> <small>Answers</small></p>
                            </span>
                        </div>
                    </div>
                    
                    
                </div>
            </section>
            
        </div>
       
    </main>
    
</body>
<script src="assets/js/style.js"></script>
<script src="assets/js/sweetalert.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="assets/js/function.js" ></script>
<script>
    image_upload_preview();
    show_history();
    update_profile();
</script>

</html>