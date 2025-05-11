<?php
require "helper/admin-session.php";
admin_session();

require_once __DIR__ . "/../../model/a.function.php";
require_once __DIR__ . "/../../model/database.php";

$db = Database::getInstance();
$con = $db->conn; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <link rel="stylesheet" href="../../assets/style/index_2.css">
    <link rel="stylesheet" href="../../assets/style/ai_model.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Phytosense - Admin Panel</title>
</head>
<body>
    <header>
        <div class="brnd-container">
            <div class="logo">
                <img src="../../assets/images/logo.png" alt="">
            </div>
            <div class="brnd-nme">
                <h2>AI Model</h2>
                <h5>Admin Panel</h5>
            </div>
        </div>
    </header>
    <style>
        .model-btn { color: rgb(181, 211, 161); }
        #retrain { background: rgb(88, 129, 60); color: white; }

        .progress-bar-container {
            width: 100%;
            background-color: #ccc; 
            border-radius: 5px;
            overflow: hidden;
            height: 20px;
            margin-top: 5px;
        }   

        .progress-bar {
            height: 100%;
            background-color: #2d6a4f; 
            width: 0%; 
            transition: width 0.2s ease-in-out;
        }
    </style>

    <main class="main-parent-container">
        <?php 
            include __DIR__ . "../../includes/sidebar.php";
        ?>

        <section class="main-content">
            <div class="top_nav">
                <nav onclick="window.location.href='/a_view/page/retrain'" id="retrain">Configuration</nav>
                <nav onclick="window.location.href='/a_view/page/deployment'" >Deployment</nav>
            </div>
            <div class="bottom_content">
                <section class="left_configuration">
                    <div class="step_one">
                        <small><b>1. Select Model for Retraining</b></small><br>
                        <div class="one_box">
                            <div class="o_left">
                                <select id="modelSelect">
                                    <option value="">Select Model</option>
                                    <?php getModelVersion($con);?>
                                </select><br>
                                <button id="loadModel">Load Model</button><br><br>
                            </div>
                            <div class="o_right">
                                <p>Current Classes in this Model:</p>
                                <ol class="current_class" id="classList"></ol>
                            </div>
                        </div>
                        <hr>
                        <small><b>Status:</b> <span id="modelStatus">Waiting for Action...</span></small>
                    </div>

                    <div class="step_two">
                        <small><b>2. Upload Datasets for Retraining</b></small><br>
                        <div class="two_box">
                            <input type="file" id="rootFolder" webkitdirectory multiple>
                            <p style="margin-top: 10px;">Training:</p>
                            <div class="configs">
                                <small>Epoch:</small> <input id="epochInput" value="3" type="number">
                                <small>Batchsize:</small> <input id="batchSizeInput" value="4" type="number">
                            </div><br>
                            <hr>
                            <div class="classnames">
                                <p id="classNames">Classnames: {None}</p>
                            </div>
                            <hr>
                            <div class="btn">
                                <button id="startTraining">Retrain Model</button>
                                <button id="cancelTraining">Cancel</button>
                            </div>
                            <div class="train_status">
                                <p>Training Status:</p>
                                <div id="progressBarContainer" class="progress-bar-container" style="display: none;">
                                    <div class="progress-bar" id="progressBar" style="width: 0%;"></div>
                                </div>
                                <small><b id="trainingProgressText">Waiting for Action...</b></small>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="right_configuration">
                    

                    <div class="download_model">
                        <div class="step_two">
                            <small><b>3. Download Model</b></small><br>
                            <p>Model Name:</p>
                            <input id="modelNameInput" class="model_name" type="text" placeholder="Model Name">
                            <div class="btn">
                                <button id="downloadModel">Download</button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </section>
    </main>
    <script src="../../assets/js/sweetalert.js"></script>
    <script src="../../assets/ai/retrain.js" type="module"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>

    <script src="../../assets/js/style_2.js"></script>
    
    <script src="../../assets/js/function2.js"></script>
</body>
</html>
