<?php
require "helper/admin-session.php";


require_once "model/classes.php";
require_once "model/database.php";
admin_session();
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
    <link rel="stylesheet" href="../../../assets/style/index_2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.css" integrity="sha512-OTcub78R3msOCtY3Tc6FzeDJ8N9qvQn1Ph49ou13xgA9VsH9+LRxoFU6EqLhW4+PKRfU+/HReXmSZXHEkpYoOA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Phytosense - Admin Panel</title>
</head>
<body>
<style>
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
    <div id="fullscreenModal" class="fullscreen-modal" onclick="closeFullscreen()">
        <span class="close" onclick="closeFullscreen()">&times;</span>
        <img class="fullscreen-img" src="" alt="Full Image">
    </div>
    <header >
        <div class="brnd-container">
            <div class="logo">
                <img src="../../../assets/images/logo.png" alt="" >
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
        .post-comments{
            width: 100%;
            position: relative;
        }
        .cmnt-details{
            width: 100%;
        }
    </style>
    <?php
        if(contentAvailability($pvid, $con) == 0){
            echo "<script>window.location.href='/error410';</script>";
            exit;
        }
    ?>
    <main class="main-parent-container">
    <?php 
        include __DIR__ . "../../../includes/sidebar.php";

       ?>
        <section class="main-content post-others">
            <div class="post_container_holder">
                <div class="post-container view-post-container">
                    
                        
                    <div class="v-pst-settings">
                        <i class="fa-solid fa-sliders v-settings" onclick="settings()"></i>
                        <ul class="vw-options" id="post__settings">
                            
                            <li onclick="deleteUserPost('<?php echo $pv->getPostId();?>')">Delete</li>
                            <li onclick="flag_user('<?php echo $pv->getUserId();?>')">Flag User</li>
                       
                        </ul>
                    </div>
                    <?php
                    if($pv->getPostExtraOne() != "" && $pv->getPostExtratwo() ==""){
                        echo '<div class="owl-carousel owl-theme cm-m-img "> 
                                <div class="item"><img onclick="fullimageview(this.src)" src="../../../assets/images/post/'. $pv->getPostImg() .'" alt=""></div>
                                <div class="item"><img onclick="fullimageview(this.src)" src="../../../assets/images/post/'. $pv->getPostExtraOne() .'" alt=""></div>
                            </div>';
                    }elseif($pv->getPostExtraOne() != "" && $pv->getPostExtratwo() !=""){
                        echo '<div class="owl-carousel owl-theme cm-m-img"> 
                                <div class="item"><img onclick="fullimageview(this.src)" src="../../../assets/images/post/'. $pv->getPostImg() .'" alt=""></div>
                                <div class="item"><img onclick="fullimageview(this.src)" src="../../../assets/images/post/'. $pv->getPostExtraOne() .'" alt=""></div>
                                <div class="item"><img onclick="fullimageview(this.src)" src="../../../assets/images/post/'. $pv->getPostExtratwo() .'" alt=""></div>
                            </div>';
                    }else{
                        echo '<div class="cm-img"> 
                                    <div class="item"><img onclick="fullimageview(this.src)" src="../../../assets/images/post/'. $pv->getPostImg() .'" alt=""></div>
                              </div>';
                    }
                    ?>
                        
                        
                    
                    
                    <div class="cm-details">
                        <div class="cm-user">
                            <img src="../../../assets/images/user/<?php echo $pv->getUserImg();?>" alt="">
                            <span>
                                <h4><?php echo $pv->getFname() ." ".$pv->getLname();?></h4>
                            <small><?php echo $pv->postCreated();?></small>
                            </span>
                        </div>
                        <div class="cm-question" >
                            <p>
                            <?php echo $pv->getPostDesc();?>
                            </p>
                        </div>
                    </div>
                    <div class="cm-additionals pst-addtionals">
                        <div class="cm-upvotes" id="up__vote">
                        </div>
                        
                        <a href="" class="cm-answers ps-view"><p id="comment_count"></p></a>
                    </div>
                    <div style="width:100%;" class="pst-comment-container" id="comments__container">
                        
                    </div>
                    <form id="add__comment" class="post-comment" action="">
                        <textarea name="" id="comment__area"></textarea>
                        <button>Post</button>
                    </form>
                    <small  style="width:100%;"><span id="cmnt__textCount">0</span>/250</small>
                </div>
            </div>
            <div class="other_post">
                <h3>Others</h3>
                <div class="other-container" id="otherContainer">
                  
                    
                    
                </div>
            </div>
        </section>
    </main>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="../../../assets/js/style_2.js"></script>
<script src="../../../assets/js/sweetalert.js"></script>
<script src="../../../assets/js/view.js"></script>
<script src="../../../assets/js/function2.js"></script>
<script src="../../../assets/js/function.js"></script>
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
        navText: ['', ''], 
        responsive: {
            0: {
                items: 1
            }
        }
    });
</script>
<script>
        function fullimageview(src) {
            document.querySelector('.fullscreen-img').src = src;

            document.getElementById('fullscreenModal').style.display = 'flex';
        }
        function closeFullscreen() {
            document.getElementById('fullscreenModal').style.display = 'none';
        }
</script>
</html>