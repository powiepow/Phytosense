<?php
require_once "database.php";
$db = Database::getInstance();
$con = $db->conn; 
$random = rand(1, 999);

session_start();
require 'vendor/autoload.php';
use Dotenv\Dotenv;

$envDirectory = __DIR__ . '/../'; 
$dotenv = Dotenv::createImmutable($envDirectory);
$dotenv->load();

$secretKey = $_ENV['SECRET_KEY'];




if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['ad_signin'])){
    $E_username = $_POST['username'];
    $E_password = $_POST['password'];

    $nsk = $secretKey;

    $username = decrypt($E_username, $nsk);
    $password = decrypt($E_password, $nsk);

    checkEmptyField($username,$password);
    
    $query = mysqli_prepare($con, "select * from tbluser where username = ? and isAdmin = 1 and userType = 'Admin' limit 1");
    mysqli_stmt_bind_param($query, "s", $username);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_assoc($result);
        $hashedPass = $row['password'];

        if(password_verify($password, $hashedPass)){
            $userid = $row['userId'];
            $_SESSION['userId'] = $row['userId'];
            $_SESSION['isAdmin'] = $row['isAdmin'];
            $_SESSION['userType'] = $row['userType'];
            echo "<script> window.location.href='/a_view/page/index'</script>";
            exit;
        }else{
            echo "Password incorrect.";
        }
    }else{
        echo "Username doesn't exist.";
    }
    
}


function checkEmptyField($username,$pass) {
    if ($username == "" || $pass ="") {
        echo 'Please fill out all fields.';
        exit;
    }
}


function decrypt($encryptedData, $secretKey) {
    $cipher = "aes-256-cbc";

    list($encodedIV, $encodedCiphertext) = explode(':', $encryptedData);

    $iv = base64_decode($encodedIV);
    $ciphertext = base64_decode($encodedCiphertext);

    $decryptedData = openssl_decrypt($ciphertext, $cipher, hex2bin($secretKey), OPENSSL_RAW_DATA, $iv);

    return $decryptedData;
}

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['ad_b_signin'])){

    $E_username = $_POST['username'];
    $E_password = $_POST['password'];

    $nsk = $secretKey;

    $username = decrypt($E_username, $nsk);
    $password = decrypt($E_password, $nsk);

    checkEmptyField($username,$password);
    
    $query = mysqli_prepare($con, "select * from tbladmin where username = ? and isAdmin = 1 and userType = 'Admin' limit 1");
    mysqli_stmt_bind_param($query, "s", $username);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_assoc($result);
        $hashedPass = $row['password'];

        if(password_verify($password, $hashedPass)){
            $userid = $row['userId'];
            $_SESSION['userId'] = $row['adminId'];
            $_SESSION['isAdmin'] = $row['isAdmin'];
            $_SESSION['userType'] = $row['userType'];
            echo "<script> window.location.href='/a_view/page/index'</script>";
            exit;
        }else{
            echo "Password incorrect.";
        }
    }else{
        echo "Username doesn't exist.";
    }
    
}





if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['u_search'])){
    $input = "%". clean($_POST['u_search']) . "%";

    $query = mysqli_prepare($con, "select 
                                        u.userId,
                                        u.fname, 
                                        u.lname, 
                                        u.dateJoined, 
                                        u.userImg, 
                                        count(h.historyId) as historyCount
                                    from 
                                        tbluser u
                                    left join 
                                        tblhistory h on u.userId = h.userIdfk
                                    where (u.fname like ? or u.lname like ?) and u.isAdmin != 1
                                    group by 
                                    u.userId;");
    mysqli_stmt_bind_param($query, "ss", $input, $input);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    if(mysqli_num_rows($result) <1){
        echo "<p>No result</p>";
    }
    while($row = mysqli_fetch_assoc($result)){
        echo '<div class="u-card" onclick="window.location.href=\'/a_view/page/user_profile?u_id='.$row['userId'].' \'">
                        <img src="../../assets/images/user/'.$row['userImg'].'" alt="User Image">
                        <div class="u-info">
                            <h4>'.$row['fname'].' '.$row['lname'].'</h4>
                            <small>Joined on</small>
                            <b><small><i>'.$row['dateJoined'].'</i></small></b>
                        </div>
                        <div class="u-add-info">
                            <b>Usage</b>
                            <p>'.$row['historyCount'].'</p>
                        </div>
                    </div>';
    }
}





if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['u_manage_type'])){
    $u_m_type = $_POST['u_manage_type'];

    switch($u_m_type){
        case "delete":
            $userId = $_POST['userId'];

            userExist($userId, $con);

            $query = mysqli_prepare($con, "delete from tbluser where userId = ?");
            mysqli_stmt_bind_param($query, "i", $userId);
            $exe = mysqli_stmt_execute($query);
            if($exe){
                echo "Deleted Successfully";
            }else{
                echo "Query to delete failed";
            }
            mysqli_stmt_close($query);
            break;







        case "update":

            $fname = clean(ucwords($_POST['fname']));
            $lname = clean(ucwords($_POST['lname']));
            $username = $_POST['username'];
            $email = $_POST['email'];
            $userId = $_POST['userId'];
                    

            validateEmail($userId, $email, $con);
            validateUsername($userId, $username, $con);
                    
            $image = $_FILES['userImage'];
            $random = rand(1, 999);
            $userImg = isset($image['name']) && !empty($image['name']) ? $random . $image['name'] : null;
                    
            if ($userImg != null) {
                $query = "update tbluser set fname = ?, lname = ?, username = ?, email = ?, userImg = ? where userId = ?";
            } else {
                $query = "update tbluser set fname = ?, lname = ?, username = ?, email = ? where userId = ?";
            }
            
            $stmt = mysqli_prepare($con, $query);
            
            if ($userImg != null) {
                $temp_name = $image['tmp_name'];
                $to_folder = "assets/images/user/" . $userImg;
                move_uploaded_file($temp_name, $to_folder);
                mysqli_stmt_bind_param($stmt, "sssssi", $fname, $lname, $username, $email, $userImg, $userId);
            } else {
                mysqli_stmt_bind_param($stmt, "ssssi", $fname, $lname, $username, $email, $userId);
            }
            
            $exe = mysqli_stmt_execute($stmt);
            
            if ($exe) {
                echo "Successfully Updated";
            } else {
                echo "Update failed!";
            }
        
            break;





        case "flag":
            $userId = $_POST['userId'];
            $flag_status = isUserFlag($userId, $con);

            $query = mysqli_prepare($con, "update tbluser set isFlag = ?, flagDate = NOW() where userId = ?");
            mysqli_stmt_bind_param($query, "ii", $flag_status, $userId);
            $exe = mysqli_stmt_execute($query);
            if($exe){
                echo "Flag Status Updated";
            }else{
                echo "Flag not updated";
            }
            mysqli_stmt_close($query);
            break;
        default:
            echo "Something Went Wrong";
            break;
    }


}


if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['manage_user_post'])){
    $p_user = $_POST['manage_user_post'];
    $userId= $_POST['u_id'];
    $query = "select u.fname, u.lname, p.postImg, p.postDesc, p.postCreated, p.postId
            from tblpost p
            inner join tbluser u on u.userId = p.userIdfk
            where p.userIdfk = ?
            order by p.postCreated desc";


    
    switch($p_user){
        case "Oldest":
            $query = "select u.fname, u.lname, p.postImg, p.postDesc, p.postCreated, p.postId
            from tblpost p
            inner join tbluser u on u.userId = p.userIdfk
            where p.userIdfk = ?
            order by p.postCreated asc";
            break;
        case "Latest":
            $query = "select u.fname, u.lname, p.postImg, p.postDesc, p.postCreated, p.postId
            from tblpost p
            inner join tbluser u on u.userId = p.userIdfk
            where p.userIdfk = ?
            order by p.postCreated desc";
            break;
        default:
            $query = "select u.fname, u.lname, p.postImg, p.postDesc, p.postCreated, p.postId
            from tblpost p
            inner join tbluser u on u.userId = p.userIdfk
            where p.userIdfk = ?
            order by p.postCreated desc";
            break;
    }

    $sql = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($sql, "i", $userId);
    mysqli_stmt_execute($sql);
    $result = mysqli_stmt_get_result($sql);
    if(mysqli_num_rows($result)<1){
        echo "<p>No Result</p>";
    }
    while($row = mysqli_fetch_assoc($result)){
        echo '<div class="p_card">
                  <img src="../../../assets/images/post/'.$row['postImg'].'" alt="plant image" width="80px" height="80px">
                  <div class="u_info">
                      <h4>'.$row['fname'].' '.$row['lname'].'</h4>
                      <small>'.$row['postCreated'].'</small><hr><br>
                      <p>'.$row['postDesc'].' </p>
                  </div>
                  <i class="fa-solid fa-trash" onclick="delete_post(\''.$row['postId'].'\');"></i>

              </div>';
    }


}

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['delete_posts'])){
    $postId = $_POST['delete_posts'];
    $query = mysqli_prepare($con, "delete from tblpost where postId = ?");
    mysqli_stmt_bind_param($query, "s", $postId);
    $exe = mysqli_stmt_execute($query);
    if($exe){
        echo "Success";
    }else{
        echo "failed";
    }
    mysqli_stmt_close($query);
}



if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['show_user_histories'])){
    $h_user = $_POST['show_user_histories'];
    $userId= $_POST['u_id'];
    $query = "select *
            from tblhistory h
            inner join tbldisease d on h.diseaseidfk = d.diseaseId
            where h.userIdfk = ?
            order by h.historyDate desc";


    
    switch($h_user){
        case "Oldest":
            $query = "select *
            from tblhistory h
            inner join tbldisease d on h.diseaseidfk = d.diseaseId
            where h.userIdfk = ?
            order by h.historyDate asc";
            break;
        case "Latest":
            $query = "select *
            from tblhistory h
            inner join tbldisease d on h.diseaseidfk = d.diseaseId
            where h.userIdfk = ?
            order by h.historyDate desc";
            break;
        default:
            $query = "select *
            from tblhistory h
            inner join tbldisease d on h.diseaseidfk = d.diseaseId
            where h.userIdfk = ?
            order by h.historyDate desc";
            break;
    }

    $sql = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($sql, "i", $userId);
    mysqli_stmt_execute($sql);
    $result = mysqli_stmt_get_result($sql);
    if(mysqli_num_rows($result)<1){
        echo "<p>No Result</p>";
    }
    while($row = mysqli_fetch_assoc($result)){
        echo '<div class="card" style="margin-bottom:10px;">
                  <div class="h-info">
                      <h3>'.$row['diseaseName'].'</h3>
                      <small>'.$row['historyDate'].'</small>
                  </div>
                  <i class="fa-solid fa-trash" onclick="delete_history(\''.$row['historyId'].'\');"></i>
              </div>';
    }
}

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['delete_histories'])){
   $historyId = $_POST['delete_histories'];
   $query = mysqli_prepare($con, "delete from tblhistory where historyId = ?");
   mysqli_stmt_bind_param($query, "s", $historyId);
   $exe = mysqli_stmt_execute($query);
   if($exe){
       echo "History Deleted";
   }else{
       echo "failed";
   }
   mysqli_stmt_close($query);

}

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['show_user_flagged'])){
    $f_user = $_POST['show_user_flagged'];
    $userId= $_POST['u_id'];
    $query = "select *
            from postreport pr
            left join tblpost p on pr.postIdfk = p.postId 
            left join tbluser u on u.userId = pr.userIdfk
            where pr.userIdfk = ?
            order by pr.reportDate desc";


    
    switch($f_user){
        case "Oldest":
            $query = "select *
            from postreport pr
            left join tblpost p on pr.postIdfk = p.postId 
            left join tbluser u on u.userId = pr.userIdfk
            where pr.userIdfk = ?
            order by pr.reportDate asc";
            break;
        case "Latest":
            $query = "select *
            from postreport pr
            left join tblpost p on pr.postIdfk = p.postId 
            left join tbluser u on u.userId = pr.userIdfk
            where pr.userIdfk = ?
            order by pr.reportDate desc";
            break;
        default:
            $query = "select *
            from postreport pr
            left join tblpost p on pr.postIdfk = p.postId 
            left join tbluser u on u.userId = pr.userIdfk
            where pr.userIdfk = ?
            order by pr.reportDate desc";
            break;
    }
    $sql = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($sql, "i", $userId);
    mysqli_stmt_execute($sql);
    $result = mysqli_stmt_get_result($sql);
    if(mysqli_num_rows($result)<1){
        echo "<p>No Result</p>";
    }
    while($row = mysqli_fetch_assoc($result)){
        echo '<div class="p_card">
                  <img src="../../../assets/images/post/'.$row['postImg'].'" alt="plant image" width="80px" height="80px">
                  <div class="u_info">
                      <h4>'.$row['fname'].' '.$row['lname'].'</h4>
                      <small>'.$row['reportDate'].'</small><hr><br>
                      <p>'.$row['postDesc'].' </p>
                  </div>
                  <i class="fa-solid fa-trash" onclick="delete_flagged(\''.$row['reportId'].'\');"></i>
              </div>';
    }
    
}
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['delete_report'])){
    $reportId = $_POST['delete_report'];
    $query = mysqli_prepare($con, "delete from postreport where reportId = ?");
    mysqli_stmt_bind_param($query, "s", $reportId);
    $exe = mysqli_stmt_execute($query);
    if($exe){
        echo "Success";
    }else{
        echo "failed";
    }
    mysqli_stmt_close($query);
}
function isUserFlag($userId, $con){
    $check = mysqli_prepare($con, "select isFlag from tbluser where userId = ?");
    mysqli_stmt_bind_param($check, "i", $userId);
    mysqli_stmt_execute($check);
    $result = mysqli_stmt_get_result($check);
    $row = mysqli_fetch_assoc($result);
    if($row['isFlag']==0 || $row['isFlag'] == ""){
        return 1;
    }else{
        return 0;
    }
    mysqli_stmt_close($check);
}

function validateUsername($userId, $username, $con){
    $stmt_check = mysqli_prepare($con, "select username, userId from tbluser where username = ?");
    mysqli_stmt_bind_param($stmt_check, "s", $username);
    mysqli_stmt_execute($stmt_check);
    $result = mysqli_stmt_get_result($stmt_check);
    $row=mysqli_fetch_assoc($result);
    if ((mysqli_num_rows($result) > 0)  && ($row['userId'] != $userId)) {
        echo "Username is already in use.";
        exit;
    }
}

function validateEmail($userId, $email, $con){
    $stmt_check = mysqli_prepare($con, "select email, userId from tbluser where email = ?");
    mysqli_stmt_bind_param($stmt_check, "s", $email);
    mysqli_stmt_execute($stmt_check);
    $result = mysqli_stmt_get_result($stmt_check);  
    $row=mysqli_fetch_assoc($result);
    if ((mysqli_num_rows($result) > 0) && ($row['userId'] != $userId)) {
        echo "Email is already in use.";
        exit;
    }

    
}


function userExist($userId, $con){
    $check = mysqli_prepare($con, "select * from tbluser where userId = ?");
    mysqli_stmt_bind_param($check, "i", $userId);
    mysqli_stmt_execute($check);
    $result = mysqli_stmt_get_result($check);
if(mysqli_num_rows($result) < 1){
    echo "User doesn't exist";
    exit;
}


}




if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['show_user_posting'])){
    $data = $_POST['show_user_posting'];
    switch($data){
        case "Latest":
            $query = "
               select u.userImg, p.postId, p.postDesc, u.userId, u.fname, u.lname, p.postImg, p.postExtraTwo, p.postExtraOne, p.postCreated,
               coalesce(likeCount, 0) as likeCount,
               coalesce(dislikeCount, 0) as dislikeCount,
               coalesce(commentCount, 0) as commentCount
               from tblpost p
               inner join tbluser u on u.userId = p.userIdfk
               left join (
                   select postIdfk, 
                          sum(case when isLike = 1 then 1 else 0 end) as likeCount,
                          sum(case when isLike = 0 then 1 else 0 end) as dislikeCount
                   from postlike
                   group by postIdfk
               ) l on l.postIdfk = p.postId
               left join (
                   select postIdfk, count(comment) as commentCount
                   from postcomment
                   group by postIdfk
               ) pc on pc.postIdfk = p.postId
               order by p.postCreated desc
            ";
            break;

            case "Oldest":
                $query = "
                   select u.userImg, p.postId, p.postDesc, u.userId, u.fname, u.lname, p.postImg, p.postExtraTwo, p.postExtraOne, p.postCreated,
                   coalesce(likeCount, 0) as likeCount,
                   coalesce(dislikeCount, 0) as dislikeCount,
                   coalesce(commentCount, 0) as commentCount
                   from tblpost p
                   inner join tbluser u on u.userId = p.userIdfk
                   left join (
                       select postIdfk, 
                              sum(case when isLike = 1 then 1 else 0 end) as likeCount,
                              sum(case when isLike = 0 then 1 else 0 end) as dislikeCount
                       from postlike
                       group by postIdfk
                   ) l on l.postIdfk = p.postId
                   left join (
                       select postIdfk, count(comment) as commentCount
                       from postcomment
                       group by postIdfk
                   ) pc on pc.postIdfk = p.postId
                   order by p.postCreated asc
                ";
                break;

            default:
            $query = "
               select u.userImg, p.postId, p.postDesc, u.userId, u.fname, u.lname, p.postImg, p.postExtraTwo, p.postExtraOne, p.postCreated,
               coalesce(likeCount, 0) as likeCount,
               coalesce(dislikeCount, 0) as dislikeCount,
               coalesce(commentCount, 0) as commentCount
               from tblpost p
               inner join tbluser u on u.userId = p.userIdfk
               left join (
                   select postIdfk, 
                          sum(case when isLike = 1 then 1 else 0 end) as likeCount,
                          sum(case when isLike = 0 then 1 else 0 end) as dislikeCount
                   from postlike
                   group by postIdfk
               ) l on l.postIdfk = p.postId
               left join (
                   select postIdfk, count(comment) as commentCount
                   from postcomment
                   group by postIdfk
               ) pc on pc.postIdfk = p.postId
               order by p.postCreated desc
            ";
            break;
    }
    $sql = mysqli_prepare($con, $query);
    mysqli_stmt_execute($sql);
    $result = mysqli_stmt_get_result($sql);
    if(mysqli_num_rows($result) < 1){
        echo "<p>No Result</p>";
    }
    while($row = mysqli_fetch_assoc($result)){
        echo '<div class="rp-box" >
                  <div class="___">
                      <img src="../../assets/images/post/'.$row['postImg'].'" alt="">
                      <div class="rp-post" onclick="window.location.href=\'/a_view/page/post?post='.$row['postId'].'\'">
                          <span style="display:flex; gap: 10px;"><h4>'.$row['fname'].' '.$row['lname'].' </h4>
                              <span class="adds_on" style="display: flex; align-items: center; gap: 10px;">| 
                                  <span>'.$row['likeCount'].' <i class="fa-regular fa-thumbs-up"></i></span> 
                                  <span>'.$row['dislikeCount'].' <i class="fa-regular fa-thumbs-down"></i></span> | 
                                  <span>'.$row['commentCount'].' <i class="fa-regular fa-comment"></i></span>
                              </span>
                          </span>
                          <b><small>'.$row['postCreated'].'</small></b>
                          <p>'.$row['postDesc'].'</p>
                      </div>
                  </div>
                  <i class="fa-solid fa-trash" onclick="deleteUserPost(\''.$row['postId'].'\')"></i>
              </div>';
            }

    
   
}

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['view_others'])){
    $query = "
               select u.userImg, p.postId, p.postDesc, u.userId, u.fname, u.lname, p.postImg, p.postExtraTwo, p.postExtraOne, p.postCreated,
               coalesce(likeCount, 0) as likeCount,
               coalesce(dislikeCount, 0) as dislikeCount,
               coalesce(commentCount, 0) as commentCount
               from tblpost p
               inner join tbluser u on u.userId = p.userIdfk
               left join (
                   select postIdfk, 
                          sum(case when isLike = 1 then 1 else 0 end) as likeCount,
                          sum(case when isLike = 0 then 1 else 0 end) as dislikeCount
                   from postlike
                   group by postIdfk
               ) l on l.postIdfk = p.postId
               left join (
                   select postIdfk, count(comment) as commentCount
                   from postcomment
                   group by postIdfk
               ) pc on pc.postIdfk = p.postId
               order by p.postCreated desc
            ";
    $sql = mysqli_prepare($con, $query);
    mysqli_stmt_execute($sql);
    $result = mysqli_stmt_get_result($sql);
    while($row=mysqli_fetch_assoc($result)){
        echo '<div class="o_card">
                        <img src="../../../assets/images/post/'.$row['postImg'].'" alt="">
                        <div class="o_detail">
                            <b>'.$row['fname'].' '.$row['lname'].'</b><br>
                            <small>'.$row['postCreated'].'</small>
                        </div>
                        <i class="fa-regular fa-eye" onclick="window.location.href=\'/a_view/page/post?post='.$row['postId'].'\'"> </i>
                    </div>';
    }
}

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['delete_user_post'])){
    $postId = $_POST['delete_user_post'];

    $query = mysqli_prepare($con, "delete from tblpost where postId = ?");
    mysqli_stmt_bind_param($query, "s", $postId);
    $exe = mysqli_stmt_execute($query);
    if($exe){
        echo "Success";
    }else{
        echo "failed";
    }
    mysqli_stmt_close($query);

}




if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['pd_srch_existing'])){
    $pd_response = [];
    $pd = "%" . clean($_POST['pd_srch_existing']) . "%";

    $query = mysqli_prepare($con, "select * from tbldisease
    where diseaseName like ? limit 1");
    mysqli_stmt_bind_param($query, "s", $pd);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);

    if(mysqli_num_rows($result) < 1){
        $pd_response["success"] = 0;
        
    }else{
        $row = mysqli_fetch_assoc($result);
        $pd_response["success"] = 1;
        $imgPath = "../assets/images/disease-image/".$row['diseaseImage'];
        $pd_response["image"] = $row['diseaseImage'];
        $pd_response['pd_id'] = $row['diseaseId'];
        $pd_response["desc"] = $row["diseaseDesc"];
        $pd_response["symptom"] = $row["diseaseSymptom"];
        $pd_response["treatment"] = $row["diseaseTreatment"];
        $pd_response["prevention"] = $row["diseasePrevention"];
        $pd_response["name"] = $row["diseaseName"];
    }
    echo json_encode($pd_response);

    mysqli_stmt_close($query);




}

if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['pd_manage_type'])){
    $manage_type = $_POST['pd_manage_type'];
    switch($manage_type){
        case "Add":
            $pd_image = isset($_FILES['pd_image']['name']) && !empty($_FILES['pd_image']['name']) ? $random . $_FILES['pd_image']['name'] : "null.png";
            $pd_name = clean(ucwords($_POST['pd_disease_name']));
            diseaseExists($pd_name, $con);


            $pd_desc = clean(ucfirst($_POST['pd_description']));
            $pd_symptom = clean(ucfirst($_POST['pd_symptom']));
            $pd_treatment = clean(ucfirst($_POST['pd_treatment']));
            $pd_prevention = clean(ucfirst($_POST['pd_prevention']));
            if($pd_image != "null.png"){
                $temp_name = $_FILES['pd_image']['tmp_name'];
                $to_folder = "assets/images/disease-image/" . $pd_image;
                move_uploaded_file($temp_name, $to_folder);
            }
            $query = mysqli_prepare($con, "insert into tbldisease(diseaseName,
            diseaseDesc, diseaseTreatment, diseaseSymptom, diseasePrevention, diseaseImage)
            value(?,?,?,?,?,?)");
            mysqli_stmt_bind_param($query, "ssssss", $pd_name, $pd_desc, $pd_treatment, 
            $pd_symptom, $pd_prevention, $pd_image);
            $exe = mysqli_stmt_execute($query);
            if($exe){
                echo "Disease Added";
            }else{
                echo "Query Failed";
            }
            break;
        case "Update":

            $pd_image = isset($_FILES['pd_image']['name']) && !empty($_FILES['pd_image']['name']) ? $random . $_FILES['pd_image']['name'] : null;
            $did = clean(ucwords($_POST['pd_id']));
            $pd_name = clean(ucwords($_POST['pd_disease_name']));
            $query = mysqli_prepare($con, "select diseaseId, diseaseImage from tbldisease where diseaseId = ?");
            mysqli_stmt_bind_param($query, "i", $did);
            mysqli_stmt_execute($query);
            mysqli_stmt_bind_result($query, $pd_id, $existing_image);
            mysqli_stmt_fetch($query);
            mysqli_stmt_close($query);

            if ($pd_id) {
                $pd_desc = clean(ucfirst($_POST['pd_description']));
                $pd_symptom = clean(ucfirst($_POST['pd_symptom']));
                $pd_treatment = clean(ucfirst($_POST['pd_treatment']));
                $pd_prevention = clean(ucfirst($_POST['pd_prevention']));
            
                if ($pd_image != null) {
                    $temp_name = $_FILES['pd_image']['tmp_name'];
                    $to_folder = "assets/images/disease-image/" . $pd_image;
                    move_uploaded_file($temp_name, $to_folder);
                } else {
                    $pd_image = $existing_image; 
                }
            
                $update_query = mysqli_prepare($con, "update tbldisease set diseaseName = ?, diseaseDesc = ?, diseaseTreatment = ?, diseaseSymptom = ?, diseasePrevention = ?, diseaseImage = ? where diseaseId = ?");
                mysqli_stmt_bind_param($update_query, "ssssssi", $pd_name, $pd_desc, $pd_treatment, $pd_symptom, $pd_prevention, $pd_image, $pd_id);
                $exe = mysqli_stmt_execute($update_query);
            
                if ($exe) {
                    echo "Disease Updated";
                } else {
                    echo "Update Query Failed";
                }
            } else {
                echo "Disease not found!";
            }
            mysqli_stmt_close($update_query);
            break;
        case "Delete":
            $diseaseId = $_POST["pd_id"];
            $query = mysqli_prepare($con,"delete from tbldisease where diseaseId = ?");
            mysqli_stmt_bind_param($query, "i", $diseaseId);
            $exe = mysqli_stmt_execute($query);
            if($exe){
                echo "Deleted Successfully";
            }else{
                echo "error";
            }
            
            mysqli_stmt_close($query);
            break;
        default:
            echo "failed";

            exit;
            break;
    }
}


if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['barcharts'])){
    $query = mysqli_prepare($con, "select d.diseaseName, count(h.historyId) as predictionCount 
    from tblhistory h left join tbldisease d on h.diseaseIdfk = d.diseaseId 
    group by d.diseaseName order by predictionCount desc");
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    $diseaseNameList = [];
    $diseasePredictCount = [];
    while($row = mysqli_fetch_assoc($result)){
        $diseaseNameList[] = $row['diseaseName'];
        $diseasePredictCount[] = $row['predictionCount']; 
    }

    mysqli_stmt_close($query);
    echo json_encode(['cropDisease' => $diseaseNameList, 'predictCount' => $diseasePredictCount]);
}





if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['showUserTypes'])){
    $userType = $_POST['showUserTypes'];

    switch($userType){
        case "Farmer":
            $query = "select * from tbluser where userType = 'Farmer'";
            break;
        case "Non-Farmer":
            $query = "select * from tbluser where userType = 'Individual' ";
            break;
        default:
            $query = "select * from tbluser where userType != 'Admin'";
            break;
    }

    $sql = mysqli_prepare($con, $query);
    mysqli_stmt_execute($sql);
    $result = mysqli_stmt_get_result($sql);
    if(mysqli_num_rows($result) < 1){
        echo '<p>No Results</p>';
    }
    while($row = mysqli_fetch_assoc($result)){
        echo '<tr>
                  <td>'.$row['fname'].'</td>
                  <td>'.$row['lname'].'</td>
                  <td>'.$row['email'].'</td>
                  <td>'.$row['userType'].'</td>
                  <td><button onclick="window.location.href=\'/a_view/page/user_profile?u_id='.$row['userId'].'\'" class="tbl_btn">View</button></td>
              </tr>';
    }
    mysqli_stmt_close($sql);
}





if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['reportedPosts'])){
    $reportedPosts = $_POST['reportedPosts'];

    switch($reportedPosts){
        case "Highest":
            $query = "select *,count(r.reportId) as reportCount from postreport r
            inner join tblpost p on p.postId = r.postIdfk 
            inner join tbluser u on u.userId = r.userIdfk 
            group by r.postIdfk order by reportCount desc";
            break;
        case "Lowest":
            $query = "select *,count(r.reportId) as reportCount from postreport r
            inner join tblpost p on p.postId = r.postIdfk 
            inner join tbluser u on u.userId = r.userIdfk 
            group by r.postIdfk order by reportCount asc";
            break;
        default:
            $query = "select *,count(r.reportId) as reportCount from postreport r
            inner join tblpost p on p.postId = r.postIdfk 
            inner join tbluser u on u.userId = r.userIdfk 
            group by r.postIdfk order by reportCount desc";
            break;
    }

    $sql = mysqli_prepare($con, $query);
    mysqli_stmt_execute($sql);
    $result = mysqli_stmt_get_result($sql);
    if(mysqli_num_rows($result) < 1){
        echo '<p>No Results</p>';
    }
    while($row = mysqli_fetch_assoc($result)){
        echo '<tr>
                  <td><img src="../../../assets/images/post/'.$row['postImg'].'" style="width:50px; border-radius:50px; height:50px;" alt="post img"></td>
                  <td>'.$row['fname'].' '.$row['lname'].'</td>
                  <td>'.$row['postDesc'].'</td>
                  <td>'.$row['postCreated'].'</td>
                  <td>'.$row['reportCount'].'</td>
                  <td><button onclick="window.location.href=\'/a_view/page/post?post='.$row['postId'].'\'" class="tbl_btn">View</button></td>
              </tr>';
    }
    mysqli_stmt_close($sql);
}

if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['flaggedUsers'])){

    $flaggedUsers = $_POST['flaggedUsers'];

    switch($flaggedUsers){
        case "Oldest":
            $query = "select * from tbluser where isFlag = 1 order by flagDate desc";
            break;
        case "Newest":
            $query = "select * from tbluser where isFlag = 1 order by flagDate asc";
            break;
        default:
            $query = "select * from tbluser where isFlag = 1 order by flagDate desc";
            break;
    }

    $sql = mysqli_prepare($con, $query);
    mysqli_stmt_execute($sql);
    $result = mysqli_stmt_get_result($sql);
    if(mysqli_num_rows($result) < 1){
        echo '<td>No Result</td>';
    }
    while($row = mysqli_fetch_assoc($result)){
        echo '<tr>
                  <td><img src="../../../assets/images/user/'.$row['userImg'].'" style="width:50px; border-radius:50px; height:50px;" alt="post img"></td>
                  <td>'.$row['fname'].' </td>
                  <td>'.$row['lname'].'</td>
                  <td>'.$row['email'].'</td>
                  <td>'.$row['flagDate'].'</td>
                  <td><button onclick="clearFlag(\''.$row['userId'].'\')" class="tbl_btn">Clear</button></td>
              </tr>';
    }
    mysqli_stmt_close($sql);

}


if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['searchFlagged'])){
    $search = "%" . $_POST['searchFlagged'] . "%";

    $query = mysqli_prepare($con, "select * from tbluser where (fname like ? or lname like ?) and isFlag = 1 order by flagDate asc");
    mysqli_stmt_bind_param($query, "ss", $search, $search);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    if(mysqli_num_rows($result) < 1){
        echo '<td>No Result</td>';
    }
    while($row = mysqli_fetch_assoc($result)){
        echo '<tr>
                  <td><img src="../../../assets/images/user/'.$row['userImg'].'" style="width:50px; border-radius:50px; height:50px;" alt="post img"></td>
                  <td>'.$row['fname'].' </td>
                  <td>'.$row['lname'].'</td>
                  <td>'.$row['email'].'</td>
                  <td>'.$row['flagDate'].'</td>
                  <td><button onclick="clearFlag(\''.$row['userId'].'\')" class="tbl_btn">Clear</button></td>
              </tr>';
    }


}




if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['unflagUser'])){
    $userId = $_POST['unflagUser'];
    $flag_status = isUserFlag($userId, $con);
    if($flag_status == 1){
        echo "unflag";
        exit;
    }
    $query = mysqli_prepare($con, "update tbluser set isFlag = ?, flagDate = '' where userId = ?");
    mysqli_stmt_bind_param($query, "ii", $flag_status, $userId);
    $exe = mysqli_stmt_execute($query);
    if($exe){
        echo "success";
    }else{
        echo "Flag not updated";
    }
    mysqli_stmt_close($query);
    }


    if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['flaggedID'])){
        $userId = $_POST['flaggedID'];
        $flag_status = isUserFlag($userId, $con);
        if($flag_status == 0){
            echo "flagged";
            exit;
        }
        $query = mysqli_prepare($con, "update tbluser set isFlag = ?, flagDate = NOW() where userId = ?");
        mysqli_stmt_bind_param($query, "ii", $flag_status, $userId);
        $exe = mysqli_stmt_execute($query);
        if($exe){
            echo "success";
        }else{
            echo "Flag not updated";
        }
        mysqli_stmt_close($query);
        }
    
    







if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['model_name'])){
    $model_name = $_POST['model_name'];
    
    disableModel($con);

    $query = mysqli_prepare($con, "update modelversion set deployed = 1 where modelName = ? ");
    mysqli_stmt_bind_param($query, "s", $model_name);
    $execute = mysqli_stmt_execute($query);
     if($execute){
        echo "Success";
     }else{
        echo "Failed";
     }

     mysqli_stmt_close($query);
}

try {
   
    if (isset($_FILES['files']) && isset($_POST['modelName'])) {
        $targetDir = __DIR__ . '../../machine-learning/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }else{
            $response = "Failed";
        }

        

        $modelName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $_POST['modelName']); 
        $uploadDir = $targetDir . $modelName . '/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $isValid = true;

        foreach ($_FILES['files']['name'] as $key => $filename) {
            $tmpFilePath = $_FILES['files']['tmp_name'][$key];
            $destinationPath = $uploadDir . basename($filename);

            
            if (!preg_match('/\.(json|bin)$/i', $filename)) {
                $isValid = false;
                break;
            }

            if (!move_uploaded_file($tmpFilePath, $destinationPath)) {
                throw new Exception("Failed to move file: $filename");
            }
        }

        if (!$isValid) {
            throw new Exception("Only .json and .bin files are allowed.");
        }

        $response = "Success";
        addModel($modelName, $con);
        echo $response;
        
    }
} catch (Exception $e) {
    echo $response = $e->getMessage();
}


if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['deleteModel'])){
    $modelNme = $_POST['deleteModel'];
    deleteModel($modelNme);

    $query = mysqli_prepare($con, "delete from modelversion where modelName = ?");
    mysqli_stmt_bind_param($query, "s", $modelNme);
    $execute = mysqli_stmt_execute($query);
    if($execute){
        echo "Success";
    }else{
        echo "Failed";
    }
    mysqli_stmt_close($query);

}

function addModel($modelName, $con){
    $query = mysqli_prepare ($con, "insert into modelversion(modelName, deployed, modelCreated)
    values(?,0,NOW())");
    mysqli_stmt_bind_param($query, "s", $modelName);
    $exe = mysqli_stmt_execute($query);
    if($exe){
        return 1;
    }else{
        echo "Failed";
        exit;
    }
    mysqli_stmt_close($query);
}

function disableModel($con){
    $query = mysqli_prepare($con, "update modelversion set deployed = 0");
    $exe = mysqli_stmt_execute($query);
    if($exe){
        return 1;
    }else{
        echo "Failed";
        exit;
    }
    mysqli_stmt_close($query);
}



function deleteModel($modelName) {
    $tDir = __DIR__ . '/../machine-learning/';
    $mDir = $tDir . $modelName;

    if (!is_dir($mDir)) {
        echo "Folder does not exist.";
        exit;
    }

    if (deleteDirectory($mDir)) {
        return 1;
    } else {
        echo "Failed to delete model";
        exit;
    }
}

function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    if (!is_dir($dir)) {
        return unlink($dir);
    }

    $items = array_diff(scandir($dir), array('.', '..'));
    foreach ($items as $item) {
        $itemPath = $dir . DIRECTORY_SEPARATOR . $item;

        if (is_dir($itemPath)) {
            if (!deleteDirectory($itemPath)) {
                return false;
            }
        } else {
            if (!unlink($itemPath)) {
                return false;
            }
        }
    }

    return rmdir($dir);
}

function clean($data){
    $data = trim($data);
    $data = stripslashes($data);
    return $data;

 }


 function formatDateTime($dateTime){
    $date = new DateTime($dateTime);
    $dateFormat = $date ->format('F j, Y');
    $timeFormat = $date ->format('g:i a');
    return [
        'date' => $dateFormat,
        'time' => $timeFormat
    ];
}


function diseaseExists($diseaseName, $con){
    $check = mysqli_prepare($con, "select * from tbldisease where diseaseName = ?");
    mysqli_stmt_bind_param($check, "s", $diseaseName );
    mysqli_stmt_execute($check);
    $res = mysqli_stmt_get_result($check);
    $rowCount = mysqli_num_rows($res);

    if($rowCount >= 1){
        echo "Disease Exists";
        exit;
    }
    $query = mysqli_stmt_close($check);
}
?>

