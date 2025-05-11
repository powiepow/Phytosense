<?php
require_once "model/database.php";
$db = Database::getInstance();
$con = $db->conn; 
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
                        <li> <a href="#about"><i class="fa-solid fa-circle-info"></i> About</a></li>
                        <li> <a href="/diagnose"><i class="fa-solid fa-leaf"></i> Diagnose</a></li>
                        <li><a href="/profile"><i class="fa-solid fa-user"></i> Profile</a></li> 
                        <li class="lgn"><a href="/signin"> Login</a></li>
                        <label for="menu" class="close-menu"><i class="fas fa-times"></i></label>
                    </ul>
                </div>
                
            </div>
            <label for="menu" class="open-menu"><i class="fas fa-bars"></i></label>
        </div>
    </header>
    
    <main>
        <section class="home" id="home">
            <div class="title-section hidden">
                <h1 id="js-text">
                    Phytosense
                </h1>
                <p>
                   Bridging <b>technology</b> and <b>agriculture</b> for <b>healthier crops</b>.
                </p>
                <a href="/diagnose" style="text-decoration:none;">Get Started</a>
            </div>
            <div class="front-images ">
                <img class="frnt-img img-1 hidden" src="assets/images/snippet/nursery.jpg" alt="">
                <img class="frnt-img img-2 hidden" src="assets/images/snippet/disease.jpg" alt="">
                <img class="frnt-img img-3 hidden-2" src="assets/images/snippet/tractor.jpg" alt="">
            </div>
        </section>
        <section id="about" class="about">
            <div class="abt-container">
                <div class="img-left ">
                    <img class="hidden" src="assets/images/13.png" alt="">
                </div>
                <article>
                    <div class="top-txt hidden-2">
                        <h2>Real-Time Crop Scanning</h2>
                        <img src="assets/images/20.png" alt="">
                    </div>
                    <div class="bot-txt hidden-2">
                        <p >Phytosense is a website aim to assist farmers in identifying crop health issues. 
                            Using machine learning, it helps detect common diseases in real-time through image analysis. 
                            Simply capture a live image of your crop, 
                            and the platform will identify potential issues and provide helpful insights to guide your next steps.</p>
                    </div>
                </article>
            </div>
        </section>
        <section class="abt-extnd">
            <div class="extnd-abt-container">
                <div class="img-left ">
                    <img class="hidden-2" src="assets/images/1.png" alt="">
                </div>
                <article>
                    <div class="top-txt hidden">
                        <h2>Diagnosis &

                            Treament</h2>
                        <img src="assets/images/20.png" alt="">
                    </div>
                    <div >
                        <p class="bot-txt hidden">The Phytosense will empower farmers to identify infected 
                            ​crops and provide guidance on preventing infestations. 
                            By ​analyzing images captured in real-time, Phytosense's 
                            ​analysis tools will then generate a detailed report outlining ​the 
                            specific issue, along with tailored recommendations ​for prevention and improved crop yield.</p>
                    </div>
                </article>
            </div>
        </section>
        <section class="feedbacks" >
            <div class="f-top-container">
                <p class="hidden"><b>Community </b>Collaboration</p>
                <img class="hidden-2" src="assets/images/14.png" alt="">
            </div>
            <div class="f-bot-container">
                <div class="user-card hidden">
                    <p>
                    Our community space lets farmers post updates, share experiences, and discuss crop-related challenges and insights.
                    </p>
                    <div class="u-container">
                        <img src="assets/images/ps.jpg" alt="">
                        <div class="u-info">
                            <b>
                                Post and Share
                            </b><br>
                            <small>
                                Feature 
                            </small>
                        </div>
                    </div>
                </div>
                <div class="user-card hidden">
                    <p>
                        Interact with other farmers by commenting on their posts. Share advice, encouragement, or ask questions.
                    </p>
                    <div class="u-container">
                        <img src="assets/images/Teamwork.jpg" alt="">
                        <div class="u-info">
                            <b>
                                Comment and Interact
                            </b><br>
                            <small>
                                Feature
                            </small>
                        </div>
                    </div>
                </div>
                <div class="user-card hidden-2">
                    <p>
                    Show appreciation for valuable posts by liking them. A small gesture that builds a strong, supportive community.
                    </p>
                    <div class="u-container">
                        <img src="assets/images/like.jpg" alt="">
                        <div class="u-info">
                            <b>
                                Like and Appreciate
                            </b><br>
                            <small>
                            Feature
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <footer>
            <img class="lbr" src="assets/images/12.png" alt="">
            <div class="footer-container">
                
                <div class="fter-left">
                    <div class="fter-title">
                        <img src="assets/images/logo.png" alt="">
                        <span>
                            <h2>Phytosense</h2>
                        <p>Crop Diagnostic Platform</p>
                        </span>
                    </div>
                    <p class="fter-text">
                        Our vision is to bridge the gap between technology and agriculture, 
                        empowering farmers with accessible tools to promote healthier crops 
                        and sustainable farming practices.
                        
                    </p>
                    <div class="fter-logo-links">
                        <img src="assets/images/15.png" alt="">
                        <img src="assets/images/16.png" alt="">
                        <img src="assets/images/17.png" alt="">
                    </div>
                </div>
                <div class="fter-right">
                    <b>Contact Us :</b>
                    <br><br>
                    <p><i class="fa-solid fa-city"></i> Zamboanga City, Philippines</p>
                    <p><i class="fa-solid fa-phone"></i> 098787654321</p>
                    <p><i class="fa-solid fa-envelope"></i> phytosense@gmail.com</p>
                    <p><i class="fa-solid fa-globe"></i> www.phytosense.com</p>
                </div>
            </div>
        </footer>
    </main>
</body>
<script src="assets/js/style.js"></script>
</html>