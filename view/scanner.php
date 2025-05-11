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
    <style>
        #webcam-container, #label-container {
            margin-top: 20px; 
        }
        
        .loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); 
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999; 
        }

        .loader {
          width: 90px;
          aspect-ratio: 1;
          position: relative;
        }
        .loader:before,
        .loader:after {
          content: "";
          position: absolute;
          top: 0;
          left: 0;
          margin: -8px 0 0 -8px;
          width: 50px;
          aspect-ratio: 1;
          background: #d5c8af;
          box-shadow:0 0px 10px #d5c8af;
          border-radius:10px;
          animation:
            l2-1 2s  infinite,
            l2-2 1s infinite ;
        }
        .loader:after {
          background:#1b241a;
          box-shadow:0 0px 10px #1b241a;
          border-radius:10px;
          animation-delay: -1s,0s;
        }
        @keyframes l2-1 {
          0%   {top:0   ;left:0}
          25%  {top:100%;left:0}
          50%  {top:100%;left:100%}
          75%  {top:0   ;left:100%}
          100% {top:0   ;left:0}
        }
        @keyframes l2-2 {
           40%,50% {transform: rotate(0.25turn) scale(0.5)}
           100%    {transform: rotate(0.5turn) scale(1)}
        }

        
        .loader-wrapper.hide {
            display: none; 
        }
    </style>
</head>
<body>
    <div id="loader-wrapper" class="loader-wrapper hide">
        <div class="loader"></div>
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
    <main>
        <section class="diagnose-prnt-container">
            <div class="sc-container">
                <div class="sc-upper">
                    <h3><i class="fa-solid fa-magnifying-glass"></i> Identifying Real-time</h3>
                    <h4 id="responseMessage" hidden></h4>
                    <div id="webcam-container" class="sc-video-container">
                        <video id="camera" autoplay playsinline></video>
                    </div>

                    <div class="btn-control">
                        <button id="startButton" >Start</button>
                        <button id="stopButton" >Stop</button>
                    </div>
                </div>
                <div class="sc-lower">
                    <b>Diagnosis Result:</b>
                    <div id="label-container"></div>
                    <br>
                    <br>
                    <a href="#" id="disease-name" >View Details >></a>
                    
                </div>
            </div>

            <img class="cp-prop hidden" src="assets/images/23.png" alt="">
            <img class="leaf-prop1 hidden" src="assets/images/9.png" alt="">
            <img class="leaf-prop2 hidden-2" src="assets/images/10.png" alt="">
        </section>
    </main>
    
</body>
<script src="assets/js/style.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script async src="https://docs.opencv.org/4.x/opencv.js" type="text/javascript"></script>
<script src="assets/js/model-predict.js"></script>
<script src="assets/js/sweetalert.js"></script>
</html>