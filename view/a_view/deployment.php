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
    <link rel="stylesheet" href="../../assets/style/deployment.css">
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
        #deployment { background: rgb(88, 129, 60); color: white; }
    </style>
    <main class="main-parent-container">
        <?php 
            include __DIR__ . "../../includes/sidebar.php";
        ?>
        <section class="main-content">
            <div class="top_nav">
                <nav onclick="window.location.href='/a_view/page/retrain'">Configuration</nav>
                <nav onclick="window.location.href='/a_view/page/deployment'" id="deployment">Deployment</nav>
            </div>
            <div class="bottom_content">
                <section class="left_configuration">
                    <form id="uploadForm" class="deployment_form" enctype="multipart/form-data">
                        <h3>Add Model</h3>
                        <div>
                            <label for="upload_model">
                                <i class="fa-solid fa-upload"></i> <br> Upload Model
                            </label>
                        </div>
                        <input type="file" name="files[]" id="upload_model" webkitdirectory multiple hidden>

                        <p>Model Name:</p>
                        <input style="border-radius:3px; padding: 5px 10px; border:none;" type="text" id="modelName" name="modelName" placeholder="Model Name" required>

                        <button type="button" style="background: rgba(253, 34, 34, 0.404);" id="cancelBtn">Cancel</button>
                        <button type="submit">Upload</button>
                     </form>

                    <div class="model_select">
                        <h3>Deploy Model</h3><br>
                        <div class="ms_upper">
                            <select id="modelSelect">
                                <option value="">Select Model</option>
                                <?php getModelVersion($con);?>
                        
                            </select>
                            <button id="loadModel">Load Model</button>
                        </div>
                        <p id="modelStatus"></p> 

                        <div class="table_container">
                            <table class="class_view">
                                <thead>
                                    <tr>
                                        <th>Model Classes</th>
                                    </tr>
                                </thead>
                                <tbody id="classList">
                                </tbody>
                            </table>
                        </div>

                        <div class="bottom_btn">
                            <button id="deploy_model">Deploy</button>
                            <button id="delete_model" class="delete_model_btn" style="background: rgba(253, 34, 34, 0.404);">Delete</button>
                        </div>
                    </div>
                </section>

                <section class="right_configuration">

                    <div class="cp_box">
                        <div class="camera_preview">
                            <h3>Realtime Preview</h3><br>
                            <div class="camera_boxx">
                                <video class="cprev" id="camera_prev" width="250" height="250" autoplay></video>
                            </div>
                            <div class="buttons">
                                <button id="startCam">Start</button>
                                <button id="stopCam">Stop</button>
                            </div>
                        </div>
                        <div class="preview_details">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Prediction</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody id="prediction_table">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    
                    <div class="u_box">
                        <div class="upload_icon">
                            <label for="upload_image">
                                <b>Upload Image</b> <br><i class="fa-solid fa-image"></i>
                            </label>
                            <input type="file" hidden id="upload_image">
                            <div class="upload_preview">
                            </div>
                        </div>

                        <div class="preview_details">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Prediction</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody id="upload_prediction_results"> 
                                </tbody>
                            </table>
                        </div>
                    </div>


                </section>
            </div>
        </section>
    </main>
    
    <script src="../../assets/js/sweetalert.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>
    <script src="../../assets/ai/deployment.js"></script>
    <script src="../../assets/js/style_2.js"></script>

</body>
</html>
