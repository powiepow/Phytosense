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
  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.css" integrity="sha512-OTcub78R3msOCtY3Tc6FzeDJ8N9qvQn1Ph49ou13xgA9VsH9+LRxoFU6EqLhW4+PKRfU+/HReXmSZXHEkpYoOA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Phytosense</title>
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

    
    <header>
        <div class="sticky" id="sidebar">
            <div class="brnd-container">
                <div class="logo">
                    <img src="../assets/images/logo.png" alt="">
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
            <section class="cm-top-container hidden-2">
                <h3><i class="fa-solid fa-group-arrows-rotate"></i> Community</h3>
                <span class="cm-filter" hidden>
                    <i class="fa-solid fa-filter"></i>
                    <select name="" id="">
                        <option value="">Filter</option>
                    </select>
                </span>
            </section>
            <section class="cm-bot-container" id="feed_container">
                
            </section>
        </div>
        <div class="ask-community">
        <a href="/post"><i class="fa-solid fa-circle-question"></i> Ask Community </a>
        </div>
    </main>
    
</body>
<script src="assets/js/style.js"></script>
<script src="assets/js/sweetalert.js"></script>
<script src="assets/js/view.js"></script>
<script src="assets/js/function.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
$(document).ready(function() {
    
    community_post();

    
    const feedContainer = document.getElementById('feed_container');

    const observer = new MutationObserver(function(mutationsList) {
        for (let mutation of mutationsList) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                mutation.addedNodes.forEach(function(node) {
                    if ($(node).find('.owl-carousel').length > 0) {
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
                    }
                });
            }
        }
    });

    
    observer.observe(feedContainer, { childList: true, subtree: true });
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