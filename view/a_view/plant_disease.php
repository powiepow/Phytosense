
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Phytosense - Admin Panel</title>
</head>
<body>
    <!--<div class="img-view">
        <i>x</i>
        <img src="../../assets/images/1.png" alt="" width="200px">
    </div>-->
    <header >
        <div class="brnd-container">
            <div class="logo">
                <img src="../../assets/images/logo.png" alt="" >
            </div>
            <div class="brnd-nme">
                <h2>Plant Disease</h2>
                <h5>Admin Panel</h5>
            </div>
        </div>
    </header>
    <style>
        .pd-btn{
            color: rgb(181, 211, 161);
        }
        /*.main-content{
            background: url(../../assets/images/stacked-steps-haikei.svg);
            backdrop-filter: blur(223px);
            background-repeat: no-repeat;
            background-size: cover;
            
        }*/
    </style>
    <main class="main-parent-container">
    <?php 
        include __DIR__ . "../../includes/sidebar.php";

        //include "../../includes/sidebar.php";?>
        <section class="main-content">
            <form class="plant__disease" id="pd-form">
                <div class="pd_upper">
                    <div class="pd-list-con">
                        <input placeholder="Plant Disease" list="plant-disease" id="p_disease" name="plant_disease">
                        <datalist id="plant-disease">
                            <?php getDiseaseList($con); ?>
                        </datalist>
                        <i class="fa-solid fa-search" id="pd-search"></i>
                      
                    </div>
                    <div class="up__option">
                        <i class="fa-solid fa-ellipsis" id="option"></i>
                        <ul class="up_optn">
                            <li><button onclick="pdManage('Add')">Add</button></li>
                            <li><button onclick="pdManage('Update')">Update</button></li>
                            <li><button onclick="pdManage('Delete')">Delete</button></li>
                        </ul>
                    </div>
                </div>
                <div class="pd_lower">
                    <div class="pd__left">
                    
                        <input type="file"  name="pd_image" id="pd-image" hidden>
                        <label class="img-con" for="pd-image" id="pd_img_label">
                            <i class="fa-solid fa-upload"></i>
                            <img src="" id="pd_image" alt="">
                        </label>
                        <label for="">
                            Disease Name:
                        </label>
                        <input type="text" hidden name="pd_id" id="pd_id">
                        <input required type="text" name="pd_disease_name" placeholder="Disease Name" id="pd_disease_name">
                    </div>
                    <div class="pd__middle">
                        <label for="">Description:</label>
                        <textarea required name="pd_description" id="pd_description"></textarea>
                        <label for="">Symptoms:</label>
                        <textarea required name="pd_symptom" id="pd_symptom"></textarea>
                    </div>
                    <div class="pd__right">
                        <label for="">Treatment:</label>
                        <textarea required name="pd_treatment" id="pd_treatment"></textarea>
                        <label for="">Prevention:</label>
                        <textarea required name="pd_prevention" id="pd_prevention"></textarea>
                    </div>
                </div>
            </form>
        </section>
    </main>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="../../assets/js/style_2.js"></script>
<script src="../../assets/js/sweetalert.js"></script>
<script src="../../assets/js/function2.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    image_upload_preview();
    $("#pd-search").click(function(){
        var p_disease = document.getElementById("p_disease").value;
        $.ajax({
            method: "POST",
            url: "/a.crud",
            data:{pd_srch_existing: p_disease},
            success: function(response){
                var pd = JSON.parse(response);
                if(pd.success == 1){
                    $("#pd_image").attr('src', "../../assets/images/disease-image/" + pd.image);
                    $("#pd_disease_name").val(pd.name);
                    $("#pd_id").val(pd.pd_id);
                    $("#pd_description").val(pd.desc);
                    $("#pd_symptom").val(pd.symptom);
                    $("#pd_treatment").val(pd.treatment);
                    $("#pd_prevention").val(pd.prevention);
                 
                }else{  
                    $("#pd_image").attr('src', "");
                    $("#pd_disease_name").val("");
                    $("#pd_id").val("");
                    $("#pd_description").val("");
                    $("#pd_symptom").val("");
                    $("#pd_treatment").val("");
                    $("#pd_prevention").val("");
                }
            },
        });
    });
</script>
</html>