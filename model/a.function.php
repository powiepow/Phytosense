<?php

function getDiseaseList($con){
    $query = mysqli_prepare($con, "select diseaseName from tbldisease");
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    while($row=mysqli_fetch_assoc($result)){
        echo "<option>". $row['diseaseName'] ."</option>";
    }
    mysqli_stmt_close($query);
}


function getModelVersion($con){
    $query = mysqli_prepare($con, "select * from modelversion");
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    while($row=mysqli_fetch_assoc($result)){
        echo "<option>". $row['modelName'] ."</option>";
    }
    mysqli_stmt_close($query);
}
?>