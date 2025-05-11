<?php
require "helper/user-session.php";
user_session();
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
    <main class="cm-prnt-container">
        <div class="cm-post-container ">
            <section class="cm-top-container">
                <h3><i class="fa-solid fa-group-arrows-rotate"></i> Community Forum</h3>
                <small id="responseMessage"></small>
                <span class="cm-filter" hidden>
                    <i class="fa-solid fa-filter"></i>
                    <select name="" id="">
                        <option value="">Filter</option>
                    </select>
                </span>
            </section>
            <section class="cm-bot-container">
                <form id="post-form" class="post-container p__file__container" method="POST" enctype="multipart/form-data">
                    <div class="upper-text-post">
                        <p><a href="javascript:window.history.back();"><i class="fa-solid fa-arrow-left"></i></a><small> Choose to provide a good quality image</small></p>
                        <button hidden name="add_post" id="post-btn" >Post</button>
                    </div>
                    <div class="upload-container p__file" >
                        <label for="upload-image" id="upload-preview">
                            <i class="fa-solid fa-upload"></i>
                        </label>
                        <p>Upload</p>
                        <input type="file" id="upload-image" name="images[]" accept="image/*" hidden multiple>
                        
                    </div>
                    <div class="up-details">
                        <b>Ask the community</b>
                        <div><textarea name="description" id="desc-area" class="add-details"></textarea> 
                        <button name="add_post" id="post-btn" style="float:right; width:70px; height:30px; color:white; outline:none; border:none; border-radius:3px; cursor:pointer; background:#5D8C55;">Post</button>
                    </div>
                        
                        <small><span id="char-count">0</span>/250 Characters</small>
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
    post_query();
</script>
</html>