<?php
require "helper/user-session.php";
require_once "model/classes.php";
require_once "model/database.php";
user_session();
$db = Database::getInstance();
$con = $db->conn; 
$userId = $_SESSION['userId'];
$pvid;
if(isset($_GET['post'])){
    $pvid = $_GET['post'];
    $pv = new Post($pvid,$userId, $con);
}else{
    echo "<script>window.history.back();</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.css" integrity="sha512-OTcub78R3msOCtY3Tc6FzeDJ8N9qvQn1Ph49ou13xgA9VsH9+LRxoFU6EqLhW4+PKRfU+/HReXmSZXHEkpYoOA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Phytosense</title>
</head>
<body>
    <style>
        /* fullscreen modal dito na*/
        .fullscreen-modal {
            display: none; 
            position: fixed; 
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8); 
            z-index: 1000; 
            justify-content: center;
            align-items: center;
        }


        .fullscreen-img {
            max-width: 90%;
            max-height: 90%;
            margin: 0 auto;
            display: block;
        }


        .close {
            position: absolute;
            top: 15px;
            right: 25px;
            color: white;
            font-size: 36px;
            font-weight: bold;
            cursor: pointer;
            z-index: 1001; 
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }

    </style>
    <!-- Fullscreen Modal!!!!!! -->
    <div id="fullscreenModal" class="fullscreen-modal" onclick="closeFullscreen()">
        <span class="close" onclick="closeFullscreen()">&times;</span>
        <img class="fullscreen-img" src="" alt="Full Image">
    </div>
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
    <?php
        if(contentAvailability($pvid, $con) == 0){
            require "404/error_410.php";
            exit;
        }
    ?>
    <main class="cm-prnt-container">
        <div class="cm-post-container ">
            <section class="cm-bot-container">
                <p><a href="/community" style="text-decoration:none; color:black;"><i class="fa-solid fa-arrow-left"></i> Back</a></p>
                <div class="post-container view-post-container hidden">
                    <div class="v-pst-settings">
                            <i class="fa-solid fa-sliders v-settings" onclick="settings()"></i>
                            <ul class="vw-options" id="post__settings">
                                <?php echo $pv->isUserPosted();?>
                            </ul>
                    </div>
                    <!--yu whatevs-->
                    <?php
                    if($pv->getPostExtraOne() != "" && $pv->getPostExtratwo() ==""){
                        echo '<div class="owl-carousel owl-theme cm-m-img"> 
                                <div class="item"><img onclick="fullimageview(this.src)" src="assets/images/post/'. $pv->getPostImg() .'" alt=""></div>
                                <div class="item"><img onclick="fullimageview(this.src)" src="assets/images/post/'. $pv->getPostExtraOne() .'" alt=""></div>
                            </div>';
                    }elseif($pv->getPostExtraOne() != "" && $pv->getPostExtratwo() !=""){
                        echo '<div class="owl-carousel owl-theme cm-m-img"> 
                                <div class="item"><img onclick="fullimageview(this.src)" src="assets/images/post/'. $pv->getPostImg() .'" alt=""></div>
                                <div class="item"><img onclick="fullimageview(this.src)" src="assets/images/post/'. $pv->getPostExtraOne() .'" alt=""></div>
                                <div class="item"><img onclick="fullimageview(this.src)" src="assets/images/post/'. $pv->getPostExtratwo() .'" alt=""></div>
                            </div>';
                    }else{
                        echo '<div class="cm-img"> 
                                    <div class="item"><img onclick="fullimageview(this.src)" src="assets/images/post/'. $pv->getPostImg() .'" alt=""></div>
                              </div>';
                    }
                    
                    
                    ?>
                    <div class="cm-details">
                        <div class="cm-user">
                            <img src="assets/images/user/<?php echo $pv->getUserImg();?>" alt="User's Profile">
                            <span>
                                <h4><?php echo $pv->getFname() ." ".$pv->getLname();?></h4>
                                <small><?php echo $pv->postCreated();?></small>
                            </span>
                        </div>
                        <div class="cm-question">
                            <p>
                            <?php echo $pv->getPostDesc();?>
                            </p>
                        </div>
                    </div>
                    <div class="cm-additionals pst-addtionals">
                        <div class="cm-upvotes" id="up__vote">
                            
                        </div>
                        
                        <a href="#" class="cm-answers ps-view"><p id="comment_count"></p></a>
                    </div>
                    <small style="width:100%;"><b>Comment/s</b></small>
                    <div class="pst-comment-container" id="comments__container">
                        
                        <!--<div class="post-comments">
                            <img src="assets/images/me.png" alt="">
                            <div class="cmnt-details">
                                <b>User Name</b>
                                <small>Type</small>
                                <p>Hey everyone, has anyone else seen this weird spotting on their tomato leaves? They started out as small yellow dots, but now they're growing and turning brown.
                                    pen_spark</p>
                                    <small><b>March 19 2024</b></small>
                            </div>
                            <div class="cmnt-date">
                                <i class="fa-solid fa-trash"> </i> <small> Delete</small>
                                
                            </div>
                        </div>-->
                        
                    </div>
                    <form id="add__comment" class="post-comment">
                        <textarea name="" id="comment__area"></textarea>
                        <button>Post</button>
                    </form>
                    <small  style="width:100%;"><span id="cmnt__textCount">0</span>/250</small>
                </div>
            </section>
        </div>
        
    </main>
    
</body>
<script src="assets/js/style.js"></script>
<script src="assets/js/view.js"></script>
<script src="assets/js/function.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="assets/js/sweetalert.js"></script>
<script>
    add_comment();
    $(document).ready(function(){
        additionals();
        comments();
    });
</script>
<script>
    $('.owl-carousel').owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        dots: false,
        navText: ['', ''], // Leave empty since we're using Font Awesome arrows
        responsive: {
            0: {
                items: 1
            }
        }
    });
</script>
<script>
       // full screen
        function fullimageview(src) {
            document.querySelector('.fullscreen-img').src = src;

            document.getElementById('fullscreenModal').style.display = 'flex';
        }
        function closeFullscreen() {
            document.getElementById('fullscreenModal').style.display = 'none';
        }
</script>
</html>