<?php
require "helper/user-session.php";
require_once "model/classes.php";
require_once "model/database.php";
user_session();
$db = Database::getInstance();
$con = $db->conn; 
$userId = $_SESSION['userId'];
$pvid;
if(isset($_GET['pe'])){
    $pvid = $_GET['pe'];
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
    <title>Phytosense</title>
</head>
<body>
    <!--madami redundant tag sa header. srry XD-->
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
        }else if(contentAvailability($pvid, $con) == "invalid"){
            require "404/error_410.php";
            exit;
        }
    ?>
    <main class="cm-prnt-container">
        <div class="cm-post-container ">
            <section class="cm-top-container">
                <h3><i class="fa-solid fa-edit"></i> Edit Post</h3>
                <small id="responseMessage"></small>
                <span class="cm-filter" hidden>
                    <i class="fa-solid fa-filter"></i>
                    <select name="" id="">
                        <option value="">Filter</option>
                    </select>
                </span>
            </section>
            <section class="cm-bot-container">
                <form id="post-form" class="post-container " method="POST" enctype="multipart/form-data">
                    <div class="upper-text-post">
                        <p><a href="javascript:window.history.back();"><i class="fa-solid fa-arrow-left"></i></a><small> Choose to provide a good quality image</small></p>
                        <button hidden name="add_post" id="post-btn" >Edit</button>
                    </div>
                    <div class="upload-container p__file" >
                        <label for="upload-image" id="upload-preview">
                            
                        <?php
                            if($pv->getPostExtraOne() != "" && $pv->getPostExtratwo() ==""){
                                echo '
                                        <img style="width:170px;" src="assets/images/post/'. $pv->getPostImg() .'" alt="">
                                        <img style="width:170px;" src="assets/images/post/'. $pv->getPostExtraOne() .'" alt="">';
                            }elseif($pv->getPostExtraOne() != "" && $pv->getPostExtratwo() !=""){
                                echo '
                                        <img style="width:150px;" src="assets/images/post/'. $pv->getPostImg() .'" alt="">
                                        <img style="width:150px;" src="assets/images/post/'. $pv->getPostExtraOne() .'" alt="">
                                        <img style="width:150px;" src="assets/images/post/'. $pv->getPostExtratwo() .'" alt="">
                                    ';
                            }else{
                                echo '
                                           <img src="assets/images/post/'. $pv->getPostImg() .'" alt="">';
                                    
                            }
                        ?>
                            
                        </label>
                        <p>Upload</p>
                        <input type="file" id="upload-image" name="post_image[]" accept="image/*" hidden multiple>
                        <!--<input accept="image/*" name="post_image" type="file" id="upload-image" hidden>-->
                    </div>
                    <div class="up-details">
                        <b>Ask the community</b>
                        <div>
                            <textarea name="description" id="desc-area" class="add-details"><?php echo $pv->getPostDesc();?></textarea>
                            <button name="add_post" id="post-btn" style="float:right; width:70px; height:30px; color:white; outline:none; border:none; border-radius:3px; cursor:pointer; background:#5D8C55;">Post</button>
                        </div>
                        
                        <small><span id="char-count"></span>/250 Characters</small>
                    </div>
                </form>
            </section>
        </div>
        
    </main>
    
</body>
<script src="assets/js/style.js"></script>
<script src="assets/js/function.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="assets/js/sweetalert.js"></script>
<script>
    image_upload_preview();
    max_character();
    edit_post();
</script>
</html>