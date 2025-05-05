<?php
require "helper/user-session.php";
user_session();
//calls for the database connection
require_once "model/database.php";
$db = Database::getInstance();
$con = $db->conn; 
//call class disease
require_once "model/classes.php";
if(isset($_GET['log'])){
    $diseaseName = $_GET['log'];
    $disease = new Disease($diseaseName, $con);
}else{
    echo "none";
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
    <h2 class="rslt hidden">Result</h2>
    <main class="dgs-prnt-container">
        <div class="dgs-container dgs-frst-box hidden">
            <a href="/scanner">Go Back</a><br><br>
            <h3><i class="fa-solid fa-stethoscope"></i> Diagnosis</h3>
            <div class="dgns-img-box">
                <img src="assets/images/disease-image/<?php echo $disease->getDiseaseImage();?>" alt="">
                
            </div>
            <div class="disease-description">
                <b class="disease-name"><?php echo $disease->getDiseaseName();?></b>
                <p><?php echo $disease->getDiseaseDesc();?>
                </p>
            </div>
            
        </div>
        <div class="dgs-container dgs-scnd-box hidden">
            
            <div class="dgs-confidence-level">
                <p><?php echo $disease->getPercentage();?>%</p>
                <div class="cfd-lvl">
                    <b>Confidence Level</b>
                    <small>We are certain</small>
                    <a href="/community"><i class="fa-solid fa-circle-question"></i> Ask The Community</a>
                </div>
            </div>
            <br>
            <div class="dgs-treatment">
                <h3><i class="fa-solid fa-notes-medical"></i> Treatment</h3>
                <br>
                <p>
                <?php echo $disease->getDiseaseTreatment();?>
                </p>
            </div>

        </div>
        <div class="dgs-container dgs-thrd-box hidden-2">
            <div class="symp-bx">
                <h3><i class="fa-solid fa-disease"></i> Symptoms</h3><br>
                <ul>
                    <?php echo $disease->getDiseaseSymptom();?>   
                </ul>
            </div>
            <br>
            <div class="symp-bx">
                <h3> <i class="fa-solid fa-hand"></i> Prevention</h3><br>
                <ul>
                    <?php echo $disease->getDiseasePrevention();?>
                </ul>
            </div>
        </div>
    </main>
    
</body>
<script src="assets/js/style.js"></script>
</html>