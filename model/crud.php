<?php

//calls for the database connection
require_once "database.php";
$db = Database::getInstance();
$con = $db->conn;



//set timezone
mysqli_query($con, "SET time_zone = '+08:00'");


session_start();
require 'vendor/autoload.php';



use Dotenv\Dotenv;

$envDirectory = __DIR__ . '/../'; 
$dotenv = Dotenv::createImmutable($envDirectory);
$dotenv->load();

$secretKey = $_ENV['SECRET_KEY'];

//----------Direct Out



//_________________________@ Signup page @_______________________________//


//sign up
if(isset($_POST['signup'])){
    $fname = clean(ucfirst(strtolower($_POST['fname'])));
    $lname = clean(ucfirst(strtolower($_POST['lname'])));
    $username = $_POST['username'];
    $email = clean($_POST['email']);
    $pass = $_POST['password'];
    $userType = $_POST['userType'];
    $generatedId = mt_rand(100000, 999999);
    $checks = isUserIdExists($generatedId, $con);
    if($checks == 1){
        echo '<script>alert("An error occured please try again");</script>';
        exit;
    }
    checkEmptyFields($fname, $lname, $username, $email, $pass, $userType, 1);

    $password = password_hash($pass, PASSWORD_BCRYPT);
    validateEmail($con, $email, 1);
    validateUsername($con, $username, 1);

    $userImage = profile_pic();

    $query = mysqli_prepare($con, "insert into tbluser (userId, fname, lname, userImg, username, email, userType, isAdmin, password, dateJoined) values
    (?,?,?,?,?,?,?,0,?,NOW())");
    mysqli_stmt_bind_param($query, "isssssss", $generatedId, $fname, $lname, $userImage, $username, $email, $userType,$password);
    $result = mysqli_stmt_execute($query);

    if($result){
        echo "<script>window.location.href='/signin'</script>";
    }else{
        echo "failed";
    }

}


//Randomize profile
function profile_pic(){
    $rand = rand(1, 3);

    switch($rand){
        case 1:
            return "e1.png";
            break;
        case 2:
            return "e2.png";
            break;
        case 3: 
            return "e3.png";
            break;
        default:
            return "e1.png";
            break;
    }
}

function isUserIdExists($userId, $con){
    $query = mysqli_prepare($con, "select count(userId) as userIdCount from tbluser where userId = ?");
    mysqli_stmt_bind_param($query, 'i', $userId);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    $row = mysqli_fetch_assoc($result);
    if( $row['userIdCount']>= 1){
        return 1;
    }else{
        return 0;
    }
    mysqli_stmt_close($query);
}

//__________________________@ Signin page @________________________________//

if(isset($_POST['signin'])){
    $E_username = $_POST['username'];
    $E_password = $_POST['password'];

    $nsk = $secretKey;

    $username = decrypt($E_username, $nsk);
    $password = decrypt($E_password, $nsk);

    checkEmptyField($username,$password);
    
    $query = mysqli_prepare($con, "select * from tbluser where username = ? limit 1 ");
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
            echo "<script> window.location.href='/community'</script>";
            exit;
        }else{
            echo "Password incorrect.";
        }
    }else{
        echo "Username doesn't exist.";
    }
    
}
//check signin field input

function checkEmptyField($username,$pass) {
    if ($username == "" || $pass ="") {
        echo 'Please fill out all fields.';
        exit;
    }
}

//decryption
function decrypt($encryptedData, $secretKey) {
    $cipher = "aes-256-cbc";

    list($encodedIV, $encodedCiphertext) = explode(':', $encryptedData);

    $iv = base64_decode($encodedIV);
    $ciphertext = base64_decode($encodedCiphertext);

    $decryptedData = openssl_decrypt($ciphertext, $cipher, hex2bin($secretKey), OPENSSL_RAW_DATA, $iv);

    return $decryptedData;
}



//______________________@ Activity Log @____________________________//
/*


@ Pseudocode: eme

1. Start model
1.1 model predicts 
2. Show Prediction Name
3. View Details
3.1 Pass the prediction name in Url
3.2 Direct the url to crud file
4. Check the disease Id of the diagnose name or if it exist
5. Get the diseaseID
6. Insert it into tblhistory together with the userID 
7. Get the history id
8. pass to url directing to diagnosis file (GET)
9. use the class history to retrieve the information of the diagnosis result;

@ This approach tends to simplify saving the result to history and retrieving it at the same time

*/

if(isset($_POST['disease_prediction'])){
    $disse_name = ucwords($_POST['disease_prediction']);
    

    //Initialize variables (tblhistory)
    $historyId = uniqid();
    $diseaseId = getDiseaseId($disse_name, $con);
    $user_id = $_SESSION['userId'];
    $disse_percentage = floor($_POST['prediction_percentage']);
    
    //Create activity log / history
    $query = mysqli_prepare($con, "insert into tblhistory(historyId, userIdfk, diseaseIdfk, percentage, historyDate)
                                    value(?, ?, ?, ?, NOW())");
    mysqli_stmt_bind_param($query, 'siii', $historyId, $user_id, $diseaseId, $disse_percentage);
    $result = mysqli_stmt_execute($query);
    if($result){
        //echo $historyId;
        //echo $historyId;
        echo "<script>window.location.href='/diagnosis?log=$historyId'</script>";
    }else{
        echo "none";
    }
    mysqli_stmt_close($query);
}


//Retrieves Disease Id

function getDiseaseId($disease_name, $con){

    if(!empty($disease_name)){
        $query = mysqli_prepare($con, "select diseaseId from tbldisease where diseaseName = ?");
        mysqli_stmt_bind_param($query, "s", $disease_name);
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
            $diseaseId = $row['diseaseId'];
            return $diseaseId;
        }else{
            echo "!found";
            exit;
        }
        
    }else{
        echo "!found";
        exit;
    }
}








//______________________@ User Posting @____________________________//

// @ Add post

//Updated 10/5/2024 --------------------


if (isset($_POST['add_post'])) {
    // Create random numbers
    $random = strval(rand(1,999));
    $post_desc = clean($_POST['description']);
    $user_id = $_SESSION['userId'];
    isUserFlagged($user_id, $con);
    // Post Id
    $randomBytes = bin2hex(random_bytes(32));
    $randId = uniqid();
    $postId = $randId . $randomBytes;

    // Initialize variables to store image names
    $post_image = "null.png";
    $post_extra_one = null;
    $post_extra_two = null;

    if (!empty($_FILES['images']['name'][0])) {
        // Process each uploaded image and store them in the appropriate columns
        foreach ($_FILES['images']['name'] as $key => $image_name) {
            $file_name = $random . $image_name;
            $temp_nme = $_FILES['images']['tmp_name'][$key];
            $to_folder = "assets/images/post/" . $file_name;

            if (move_uploaded_file($temp_nme, $to_folder)) {
                // Assign the uploaded image to the respective columns based on the number of uploads
                if ($key == 0) {
                    $post_image = $file_name; // First image goes to postImg
                } elseif ($key == 1) {
                    $post_extra_one = $file_name; // Second image goes to postExtraOne
                } elseif ($key == 2) {
                    $post_extra_two = $file_name; // Third image goes to postExtraTwo
                }
            }
        }
    }

    // Insert post with the appropriate image columns
    $query = mysqli_prepare($con, "INSERT INTO tblpost(postId, userIdfk, postDesc, postImg, postExtraOne, postExtraTwo, postCreated) 
                                   VALUES(?,?,?,?,?,?,NOW())");
    mysqli_stmt_bind_param($query, "sissss", $postId, $user_id, $post_desc, $post_image, $post_extra_one, $post_extra_two);
    $result = mysqli_stmt_execute($query);

    if ($result) {
        echo "<script>window.location.href='/community'</script>";
    } else {
        echo "Query Failed";
    }
    mysqli_stmt_close($query);
}


/*if(isset($_POST['add_post'])){
    //create random numbers
    $random = strval(rand(1,999));
    $post_image;
    $temp_nme;
    $post_desc = clean($_POST['description']);
    $user_id = $_SESSION['userId'];

    //post Id
    $randomBytes = bin2hex(random_bytes(32));
    $randId = uniqid();
    $postId = $randId . $randomBytes;

    if(empty($_FILES['post_image']['name'])){
        $post_image = "null.png";
    }else{
        $post_image = $random . $_FILES['post_image']['name'];
        $temp_nme = $_FILES['post_image']['tmp_name'];
        $to_folder = "assets/images/post/" . $post_image;
        move_uploaded_file($temp_nme, $to_folder);
    }


    $query = mysqli_prepare($con, "insert into tblpost(postId, userIdfk, postDesc, postImg, postCreated)
                                   values(?,?,?,?,NOW())");
    mysqli_stmt_bind_param($query, "siss", $postId, $user_id, $post_desc, $post_image);
    $result = mysqli_stmt_execute($query);

    if($result){
        echo "<script> window.location.href='/community'</script>";
    }else{
        echo "Query Failed";
    }
    mysqli_stmt_close($query);
}*/


// @ Show post Diagnosis


if(isset($_POST['show_feed'])){
    $g_userid = $_SESSION['userId'];
    $query = mysqli_prepare($con, "
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
               order by p.postCreated desc;
            ");
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);


    while($row = mysqli_fetch_assoc($result)) {
        $formatDate = formatDateTime($row['postCreated']);
        $userLike = userLike($row['postId'], $_SESSION['userId'], $con);
        $tmb_up = '';
        $tmb_down = '';
        if($userLike != 0){
            if($userLike == "like"){
                $tmb_up = '<p id="likeCount_' . htmlspecialchars($row['postId'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($row['likeCount'], ENT_QUOTES, 'UTF-8') . ' <i class="fa-solid fa-thumbs-up" onclick="like_dislike(\'like\', \'' . htmlspecialchars($row['postId'], ENT_QUOTES, 'UTF-8') . '\');"></i></p>';
                $tmb_down = '<p id="dislikeCount_' . htmlspecialchars($row['postId'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($row['dislikeCount'], ENT_QUOTES, 'UTF-8') . ' <i class="fa-regular fa-thumbs-down" onclick="like_dislike(\'dislike\', \'' . htmlspecialchars($row['postId'], ENT_QUOTES, 'UTF-8') . '\');"></i></p>';
            }elseif($userLike == "dislike"){
                $tmb_up = '<p id="likeCount_' . htmlspecialchars($row['postId'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($row['likeCount'], ENT_QUOTES, 'UTF-8') . ' <i class="fa-regular fa-thumbs-up" onclick="like_dislike(\'like\', \'' . htmlspecialchars($row['postId'], ENT_QUOTES, 'UTF-8') . '\');"></i></p>';
                $tmb_down = '<p id="dislikeCount_' . htmlspecialchars($row['postId'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($row['dislikeCount'], ENT_QUOTES, 'UTF-8') . ' <i class="fa-solid fa-thumbs-down" onclick="like_dislike(\'dislike\', \'' . htmlspecialchars($row['postId'], ENT_QUOTES, 'UTF-8') . '\');"></i></p>';
            }elseif($userLike == "noReact"){
                $tmb_down = '<p id="dislikeCount_' . htmlspecialchars($row['postId'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($row['dislikeCount'], ENT_QUOTES, 'UTF-8') . ' <i class="fa-regular fa-thumbs-down" onclick="like_dislike(\'dislike\', \'' . htmlspecialchars($row['postId'], ENT_QUOTES, 'UTF-8') . '\');"></i></p>';
                $tmb_up = '<p id="likeCount_' . htmlspecialchars($row['postId'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($row['likeCount'], ENT_QUOTES, 'UTF-8') . ' <i class="fa-regular fa-thumbs-up" onclick="like_dislike(\'like\', \'' . htmlspecialchars($row['postId'], ENT_QUOTES, 'UTF-8') . '\');"></i></p>';
            }
        }else{
            $tmb_down = '<p id="dislikeCount_' . htmlspecialchars($row['postId'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($row['dislikeCount'], ENT_QUOTES, 'UTF-8') . ' <i class="fa-regular fa-thumbs-down" onclick="like_dislike(\'dislike\', \'' . htmlspecialchars($row['postId'], ENT_QUOTES, 'UTF-8') . '\');"></i></p>';
            $tmb_up = '<p id="likeCount_' . htmlspecialchars($row['postId'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($row['likeCount'], ENT_QUOTES, 'UTF-8') . ' <i class="fa-regular fa-thumbs-up" onclick="like_dislike(\'like\', \'' . htmlspecialchars($row['postId'], ENT_QUOTES, 'UTF-8') . '\');"></i></p>';
        }
        //User distinction
        if($row["userId"] == $g_userid){
            $style = 'style="box-shadow: rgb(204, 219, 232) 3px 3px 6px 0px inset, rgba(255, 255, 255, 0.5) -3px -3px 6px 1px inset; border-radius:5px;"';
        }else{
            $style = "";
        }
        //Check if the user has multiple images
        if($row['postExtraOne']!="" && $row['postExtraTwo'] == ""){
            $pstimg = '<div class="owl-carousel owl-theme cm-m-img"> 
                            <div class="item"><img <img onclick="fullimageview(this.src)" src="assets/images/post/'. htmlspecialchars($row['postImg'], ENT_QUOTES, 'UTF-8') .'" alt=""></div>
                            <div class="item"><img <img onclick="fullimageview(this.src)" src="assets/images/post/'. htmlspecialchars($row['postExtraOne'], ENT_QUOTES, 'UTF-8') .'" alt=""></div>
                        </div>';
        }elseif($row['postExtraTwo'] !="" && $row['postExtraOne'] != ""){
             $pstimg = '<div class="owl-carousel owl-theme cm-m-img"> 
                            <div class="item"><img  <img onclick="fullimageview(this.src)" src="assets/images/post/'. htmlspecialchars($row['postImg'], ENT_QUOTES, 'UTF-8') .'" alt=""></div>
                            <div class="item"><img  <img onclick="fullimageview(this.src)" src="assets/images/post/'. htmlspecialchars($row['postExtraOne'], ENT_QUOTES, 'UTF-8') .'" alt=""></div>
                            <div class="item"><img  <img onclick="fullimageview(this.src)" src="assets/images/post/'. htmlspecialchars($row['postExtraTwo'], ENT_QUOTES, 'UTF-8') .'" alt=""></div>
                        </div>';
        }else{
            $pstimg = '<div class="cm-img" >
                            <img onclick="fullimageview(this.src)" src="assets/images/post/'. htmlspecialchars($row['postImg'], ENT_QUOTES, 'UTF-8') .'" alt="">
                       </div>';
        }
        echo '<div class="post-container ">
                <a class="report" data-tooltip="Report" onclick=\'report_post("' . htmlspecialchars($row['postId'], ENT_QUOTES, 'UTF-8') . '")\'><i class="fa-solid fa-triangle-exclamation "></i></a>
                '.$pstimg.'
                <div class="cm-details">
                    <div class="cm-user" '.$style.'>
                        <img src="assets/images/user/'. htmlspecialchars($row['userImg'], ENT_QUOTES, 'UTF-8') .'" alt="">
                        <span>
                            <h4>' . htmlspecialchars($row['fname'], ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($row['lname'], ENT_QUOTES, 'UTF-8') . '</h4>
                            <small>' . htmlspecialchars($formatDate['date'], ENT_QUOTES, 'UTF-8') . '<br>' . htmlspecialchars($formatDate['time'], ENT_QUOTES, 'UTF-8') . '</small>
                        </span>
                    </div>
                    <div class="cm-question">
                        <p>' . $row['postDesc'] . '</p>
                    </div>
                </div>
                <div class="cm-additionals">
                    <div class="cm-upvotes">
                        '.$tmb_up.'
                        '.$tmb_down.'
                        
                    </div>
                    <a href="/post_view?post=' . urlencode($row['postId']) . '" class="cm-answers"><p><b>Answers</b> ' . htmlspecialchars($row['commentCount'], ENT_QUOTES, 'UTF-8') . '</p></a>
                </div>
            </div>';
    }
    mysqli_stmt_close($query);
}



// @ -----------  Like post: shet nakakaloko
//ps. dont judge 

if (isset($_POST['likePost'])) {
    $postId = htmlspecialchars($_POST['likePost'], ENT_QUOTES, 'UTF-8');
    $act_type = htmlspecialchars($_POST['act_type'], ENT_QUOTES, 'UTF-8');
    $user_id = $_SESSION['userId'];

    updateLikeStatus($act_type, $user_id, $postId, $con);

    $query = mysqli_prepare($con, "select
        count(case when isLike = 1 then 1 end) as likeCount,
        count(case when isLike = 0 then 1 end) as dislikeCount
        from postlike
        where postIdfk = ?");
    mysqli_stmt_bind_param($query, 's', $postId);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    $row = mysqli_fetch_assoc($result);

    $like = htmlspecialchars($row['likeCount'], ENT_QUOTES, 'UTF-8');
    $dislike = htmlspecialchars($row['dislikeCount'], ENT_QUOTES, 'UTF-8');

    echo json_encode(['likeCount' => $like, 'dislikeCount' => $dislike]);
}

// @ Functions 

// check if nilike ba ni user ang post 
function updateLikeStatus($act_type, $user_id, $postId, $con) {
    $query = mysqli_prepare($con, "select * from postlike where userIdfk = ? and postIdfk = ?");
    mysqli_stmt_bind_param($query, "is", $user_id, $postId);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (($act_type == "like" && $row['isLike'] == 1) || ($act_type == "dislike" && $row['isLike'] == 0)) {
            $query = mysqli_prepare($con, "delete from postlike where userIdfk = ? and postIdfk = ?");
            mysqli_stmt_bind_param($query, "is", $user_id, $postId);
        } else {
            $newAction = ($act_type == "like") ? 1 : 0;
            $query = mysqli_prepare($con, "update postlike set isLike = ?, dateLike = now() where userIdfk = ? and postIdfk = ?");
            mysqli_stmt_bind_param($query, "iis", $newAction, $user_id, $postId);
        }
    } else {
        $newAction = ($act_type == "like") ? 1 : 0;
        $query = mysqli_prepare($con, "insert into postlike (isLike, userIdfk, postIdfk, dateLike) values (?, ?, ?, now())");
        mysqli_stmt_bind_param($query, "iis", $newAction, $user_id, $postId);
    }

    mysqli_stmt_execute($query);
    mysqli_stmt_close($query);
}

//changes like icon if set user has interact 
function userLike($postId, $userId, $con){
    $query = mysqli_prepare($con, "select isLike, count(likeId) as userLike from postlike
        where postIdfk = ? and useridfk = ?");
        mysqli_stmt_bind_param($query, "si", $postId, $userId);
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        $row = mysqli_fetch_assoc($result);
        if(mysqli_num_rows($result) > 0){
            if($row['isLike'] === 1){
                return "like";
            }elseif($row['isLike'] === 0){
                return "dislike";
            }  
            
            //return 1;
        }else{
            return "noReact";
        }
        mysqli_stmt_close($query);
}
//______________________@ Post Viewing Function @____________________________//


//my brain aint braining
//imma do this instead

//like and comment counts
if(isset($_POST['additionals_post_viewId'])){
    $postId = $_POST['additionals_post_viewId'];
    $userId = $_SESSION['userId'];

    $query = mysqli_prepare($con, "
    select *,
           coalesce(count(case when pl.isLike = 1 then 1 end),0) as likeCount,
           coalesce(count(case when pl.isLike = 0 then 1 end),0) as dislikeCount 
    from tblpost p
    left join postlike pl on pl.postIdfk = p.postId
    where postId = ? ");
    mysqli_stmt_bind_param($query, "s", $postId);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);   

    $row = mysqli_fetch_assoc($result); 

    // deflt icons
   // $likeIcon = '<i class="fa-regular fa-thumbs-up" onclick="alert(\''.$row['postId'].'\')"></i>';
    $likeIcon = '<i class="fa-regular fa-thumbs-up" onclick="like_dislike(\'like\', \''.$row['postId'].'\');"></i>';
    $dislikeIcon = '<i class="fa-regular fa-thumbs-down" onclick="like_dislike(\'dislike\', \''.$row['postId'].'\' );"></i>'; 

    // naglike ba ang user or not
    $userLikeQuery = mysqli_prepare($con, "
        select isLike from postlike 
        where postIdfk = ? and userIdfk = ?");
    mysqli_stmt_bind_param($userLikeQuery, "si", $postId, $userId);
    mysqli_stmt_execute($userLikeQuery);
    $userLikeResult = mysqli_stmt_get_result($userLikeQuery);   

    if ($userLikeRow = mysqli_fetch_assoc($userLikeResult)) {
        if ($userLikeRow['isLike'] == 1) {
            $likeIcon = '<i class="fa-solid fa-thumbs-up" onclick="like_dislike(\'like\', \'' . htmlspecialchars($row['postIdfk'], ENT_QUOTES, 'UTF-8') . '\');"></i>';
        } elseif ($userLikeRow['isLike'] == 0) {
            $dislikeIcon = '<i class="fa-solid fa-thumbs-down" onclick="like_dislike(\'dislike\', \'' . htmlspecialchars($row['postIdfk'], ENT_QUOTES, 'UTF-8') . '\');"></i>';
        }
    }   

    $likeResult = '<p>' . htmlspecialchars($row['likeCount'], ENT_QUOTES, 'UTF-8') . ' ' . $likeIcon . '</p>
               <p>' . htmlspecialchars($row['dislikeCount'], ENT_QUOTES, 'UTF-8') . ' ' . $dislikeIcon . '</p>';

    
    //get comment count
    $query = mysqli_prepare($con, "select count(commentId) as commentCount
    from postcomment
    where postIdfk = ? ");
    mysqli_stmt_bind_param($query, "s", $postId);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);

    $row = mysqli_fetch_assoc($result);
    $commentCount =  '<b>Answers</b> '.htmlspecialchars($row['commentCount'], ENT_QUOTES, 'UTF-8').'';

    echo json_encode(['like_result' => $likeResult, 'comment_count'=> $commentCount]);

    mysqli_stmt_close($query);

}

//===========Gloval root for image
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
$domain = $_SERVER['HTTP_HOST'];
$root = $protocol . '://' . $domain . '/';

if(isset($_POST['pv_comment'])){
    $postId = $_POST['pv_comment'];
    $userId = $_SESSION['userId'];

    // Prepare and execute the main query
    $query = mysqli_prepare($con, "
        select u.userImg, c.commentId, c.comment, c.dateCommented, u.fname, u.lname, u.userType, c.userIdfk, p.postId
        from postcomment c
        inner join tbluser u on c.userIdfk = u.userId
        inner join tblpost p on c.postIdfk = p.postId
        where c.postIdfk = ?
        order by c.dateCommented");

    mysqli_stmt_bind_param($query, "s", $postId);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);

    if(mysqli_num_rows($result) == 0){
        echo "<i style=\"color:gray;\">No Comments</i>";
    }

    // Fetch and display comments
    while($row = mysqli_fetch_assoc($result)){
        $showDel = '';
        $f_date = formatDateTime($row['dateCommented']);
        // Check if the current logged-in user is the one who made this comment
        if((isset($_SESSION['userType']) && isset($_SESSION['isAdmin'])) && ($_SESSION['userType'] == "Admin" && $_SESSION["isAdmin"] == 1)){
            $isUser = 1;
        }else{
            $isUser = $row['userIdfk'] == $userId ? 1 : 0;
        }
        

        //echo "User ID: ".$row['userIdfk'].", Post ID: " . $row['postId'] . ", Comment ID: " . $row['commentId'] . " Is User Comment: $isUser <br>";

        if($isUser == 1){
            $showDel = '<i class="fa-solid fa-trash" onclick="delete_comment(\''.clean($row['commentId']).'\')"> </i> <small></small>';
        }

        echo '<div class="post-comments">
                  <img src="'.$root.'assets/images/user/'.$row['userImg'].'" alt="">
                  <div class="cmnt-details">
                      <b>'.htmlspecialchars($row['fname'].' '.$row['lname'], ENT_QUOTES, 'UTF-8').'</b>
                      <small>('.htmlspecialchars($row['userType'], ENT_QUOTES, 'UTF-8').')</small>
                      <p>'.$row['comment'].'</p>
                          <small><b>'.$f_date['date'].'<br>'.$f_date['time'].'</b></small>
                  </div>
                  <div class="cmnt-date">
                      '.$showDel.'
                  </div>
              </div>';
    }

    mysqli_stmt_close($query);
}



//Add comment

if(isset($_POST['cm_postViewId'])){
    $postId = $_POST['cm_postViewId'];
    $userId = $_SESSION['userId'];
    $comment =clean($_POST['comment']);

    $query = mysqli_prepare($con, "insert into postcomment(comment, userIdfk, postIdfk, dateCommented) 
    value(?, ?, ?, NOW())");
    mysqli_stmt_bind_param($query, "sis", $comment, $userId, $postId);
    $result = mysqli_stmt_execute($query);
    if($result){
        echo "success";
    }else{
        echo "error: query failed";
    }
    mysqli_stmt_close($query);

    
}


// Report 
if(isset($_POST['report_post'])){
    $postId = $_POST['report_post'];
    $userId = $_SESSION['userId'];
    
    isReported($postId, $userId, $con);

    $query = mysqli_prepare($con, "insert into postreport(postIdfk, userIdfk, reportDate)
    value(?,?,NOW())");
    mysqli_stmt_bind_param($query, "si", $postId, $userId);
    $execute = mysqli_stmt_execute($query);
    if($execute){
        echo 0;
    }else{
        echo "Query Failed";
    }
    mysqli_stmt_close($query);
}


//cehck if user has already reported the post

function isReported($postId, $userId, $con){
    $check = mysqli_prepare($con, "select * from postreport where
    postIdfk = ? and userIdfk =?");
    mysqli_stmt_bind_param($check, "si", $postId, $userId);
    mysqli_stmt_execute($check);
    $result = mysqli_stmt_get_result($check);
    if(mysqli_num_rows($result) > 0){
        echo 1;
        mysqli_stmt_close($check);
        exit;
    }

}
//================================edit post
if(isset($_POST['edit_post']) && isset($_POST['postId'])){
    $postId = clean($_POST['postId']);
    $userId = $_SESSION['userId'];
    $post_desc = clean($_POST['description']);

    $uploaded_images = [];
    $random = rand(1,999);

    // Vlidaye ownership
    $valid_pid = checkPost($postId, 1, $con);
    if($valid_pid != 1){
        echo "!image";
        exit;
    }

   
    if(!empty($_FILES['post_image']['name'][0])) {
        foreach ($_FILES['post_image']['name'] as $key => $image_name) {
            $file = [
                'name' => $_FILES['post_image']['name'][$key],
                'type' => $_FILES['post_image']['type'][$key],
                'tmp_name' => $_FILES['post_image']['tmp_name'][$key],
                'error' => $_FILES['post_image']['error'][$key],
                'size' => $_FILES['post_image']['size'][$key]
            ];

            // Validate the image file
            $img_validate = isImageAndWithinSize($file);
            switch($img_validate){
                case "!image":
                    echo "image file type error";
                    exit;
                case "maxSize":
                    echo "file exceeds maximum size";
                    exit;
                case 1:
                    // Image? Is valid
                    $post_image = $random . $file['name'];
                    $temp_nme = $file['tmp_name'];
                    $to_folder = "assets/images/post/" . $post_image;
                    move_uploaded_file($temp_nme, $to_folder);

                    // store array
                    $uploaded_images[] = $post_image;
                    break;
            }
        }
    }
    $empty = "";
    switch(count($uploaded_images)) {
        case 1:
            $query = mysqli_prepare($con, "UPDATE tblpost SET postDesc = ?, postImg = ?, postExtraOne = ?, postExtraTwo = ? WHERE postId = ?");
            mysqli_stmt_bind_param($query, "sssss", $post_desc, $uploaded_images[0],$empty, $empty, $postId);
            break;
        case 2:
            $query = mysqli_prepare($con, "UPDATE tblpost SET postDesc = ?, postImg = ?, postExtraOne = ?, postExtraTwo = ? WHERE postId = ?");
            mysqli_stmt_bind_param($query, "sssss", $post_desc, $uploaded_images[0], $uploaded_images[1], $empty, $postId);
            break;
        case 3:
            $query = mysqli_prepare($con, "UPDATE tblpost SET postDesc = ?, postImg = ?, postExtraOne = ?, postExtraTwo = ? WHERE postId = ?");
            mysqli_stmt_bind_param($query, "sssss", $post_desc, $uploaded_images[0], $uploaded_images[1], $uploaded_images[2], $postId);
            break;
        default:
           
            $query = mysqli_prepare($con, "UPDATE tblpost SET postDesc = ? WHERE postId = ?");
            mysqli_stmt_bind_param($query, "ss", $post_desc, $postId);
            break;
    }

    // Execute the query
    $ex = mysqli_stmt_execute($query);
    if($ex){
        echo "success";
    }else{
        echo "query failed";
    }

    mysqli_stmt_close($query);
}

//----------------Old single post 10/6/2024

/*if(isset($_POST['edit_post']) && isset($_POST['postId'])){
    $postId = clean($_POST['postId']);
    $userId = $_SESSION['userId'];
    $post_desc = clean($_POST['description']);

    $postImg = $_FILES['post_image'];
    $random = rand(1,999);

    $img_validate = isImageAndWithinSize($postImg);
    
    $valid_pid = checkPost($postId, 1, $con);
    if($valid_pid != 1){
        echo "!image";
        exit;
    }

    switch($img_validate){
        case "none":
            $query = mysqli_prepare($con, "update tblpost set postDesc = ? where postId = ?");
            mysqli_stmt_bind_param($query, "ss", $post_desc, $postId);
            $ex = mysqli_stmt_execute($query);
            if($ex){
                echo "success1";
            }else{
                echo "query failed";
            }
            mysqli_stmt_close($query);
            break;
        
        case "!image";
            echo "image file type error";
            break;
            exit;
        case "maxSize":
            echo "file exceeds maximum size";
            break;
            exit;
            

        case 1:
            $post_image = $random . $_FILES['post_image']['name'];
            $temp_nme = $_FILES['post_image']['tmp_name'];
            $to_folder = "assets/images/post/" . $post_image;
            move_uploaded_file($temp_nme, $to_folder);

            $query =mysqli_prepare($con, "update tblpost set postDesc = ?, postImg =? where postId = ?");
            mysqli_stmt_bind_param($query, "sss", $post_desc, $post_image, $postId);
            $ex = mysqli_stmt_execute($query);
            if($ex){
                echo "success";
            }else{
                echo "query failed";
            }
            mysqli_stmt_close($query);
            break;
    }

}*/







// delete post


if(isset($_POST['delete_post'])){
    $postId = clean($_POST['delete_post']);
    $userId = $_SESSION['userId'];

    postDeleteByUser($postId, $userId, $con);

    $query = mysqli_prepare($con, "delete from tblpost where postId = ?");
    mysqli_stmt_bind_param($query, "s", $postId);
    $exec = mysqli_stmt_execute($query);

    if($exec){
        echo "Deleted";
    }else{
        echo "Query Failed";
    }


}

//check if current user is the one who prcess the deletion
function postDeleteByUser($postId, $userId, $con){
    if((isset($_SESSION['userType']) && isset($_SESSION['isAdmin'])) && ($_SESSION['userType'] == "Admin" && $_SESSION["isAdmin"] == 1)){
        //then show
    }else{
        $check = mysqli_prepare($con, "select * from tblpost where postId = ? and userIdfk =?");
        mysqli_stmt_bind_param($check, "si", $postId, $userId);
        mysqli_stmt_execute($check);
        $result = mysqli_stmt_get_result($check);
        if(mysqli_num_rows($result) < 1){
            //uhm kuan, if dili sya ang nag delete then nope
            echo "Invalid";
            mysqli_stmt_close($check);
          exit;
        }else{
            mysqli_stmt_close($check);
        }
    }
    
}

//delete comment

if(isset($_POST['delete_comment'])){
    $commentId = clean($_POST['delete_comment']);
    $userId = $_SESSION['userId'];

    cmntDeleteByUser($commentId, $userId, $con);

    $query = mysqli_prepare($con, "delete from postcomment where commentId = ?");
    mysqli_stmt_bind_param($query, "i", $commentId);
    $exe = mysqli_stmt_execute($query);
    if($exe){
        echo "Deleted";
    }else{
        echo "Query Failed";
    }
    mysqli_stmt_close($query);


}

//check if the current user is the owner of the comment

function cmntDeleteByUser($commentId, $userId, $con){
    if((isset($_SESSION['userType']) && isset($_SESSION['isAdmin'])) && ($_SESSION['userType'] == "Admin" && $_SESSION["isAdmin"] == 1)){
        //then show
    }else{
        $check = mysqli_prepare($con, "select * from postcomment where commentId = ? and userIdfk =?");
        mysqli_stmt_bind_param($check, "ii", $commentId, $userId);
        mysqli_stmt_execute($check);
        $result = mysqli_stmt_get_result($check);
        if(mysqli_num_rows($result) < 1){
            //uhm kuan, if dili sya ang nag delete then nope
            echo "Invalid";
            mysqli_stmt_close($check);
          exit;
        }else{
            mysqli_stmt_close($check);
        }
    }
}



// /profile @history

if($_SERVER["REQUEST_METHOD"] === 'POST' && isset($_POST["keyup"])){
    $data = '%' . clean($_POST['datas']) . '%';
    $userId = userId();
    if($_POST['keyup'] == 1){
        $query = mysqli_prepare($con, "select d.diseaseName, h.historyDate, h.historyId from tblhistory
        h inner join tbldisease d on h.diseaseIdfk = d.diseaseId where h.userIdfk = ? and d.diseaseName like ?");
        mysqli_stmt_bind_param($query, "is", $userId, $data);
    }else{
        $data = clean($_POST['datas']);
        if($data === "oldest"){
            $data = "ASC";
        }else{
            $data = "DESC";
        }
        $query = mysqli_prepare($con, "select d.diseaseName, h.historyDate, h.historyId from tblhistory
        h inner join tbldisease d on h.diseaseIdfk = d.diseaseId where h.userIdfk = ? order by h.historyDate $data ");
        mysqli_stmt_bind_param($query, "i", $userId);
    }
    
    
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    if(mysqli_num_rows($result) < 1){
        echo "No Result";
    }
    if($result){
        while($row=mysqli_fetch_assoc($result)){
            $frmat = formatDateTime($row['historyDate']);
            echo '<div class="history-box">
                        <span>
                            <b>'.$row['diseaseName'].'</b><br>
                            <small>'.$frmat['date'].' '.$frmat['time'].'</small>
                        </span>
                        <span>
                            <a href="/diagnosis?log='.$row['historyId'].'">View</a>
                        </span>
                    </div>';

        }
    }
}

// display post

if($_SERVER["REQUEST_METHOD"] === 'POST' && isset($_POST["pdp"])){

    $userId = userId();
    if($_POST['pdp'] == "oldest"){
        $data = "asc";
    }else{
        $data = "desc";
    }
        $query = mysqli_prepare($con, "select *, 
        coalesce(likeCount, 0) as likeCount,
        coalesce(dislikeCount, 0) as dislikeCount,
        coalesce(commentCount, 0) as commentCount from tblpost p
        left join (select postIdfk, 
            sum(case when isLike = 1 then 1 end) as likeCount,
            sum(case when isLike = 0 then 1 end) as disLikeCount
            from postlike 
            group by postIdfk
        ) l on l.postIdfk = p.postId
        left join(select postIdfk,
            count(commentId) as commentCount 
            from postcomment 
            group by postIdfk 
         ) pc on pc.postIdfk = p.postId
          where p.userIdfk = ? order by postCreated $data");

        mysqli_stmt_bind_param($query, "i", $userId);
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        if(mysqli_num_rows($result) < 1){
            echo "<br><p>No Post</p>";
        }
        while($row = mysqli_fetch_assoc($result)){
            $f_date = formatDateTime($row['postCreated']);
            echo '<div class="posts__box">
                        <div class="posts-upper">
                            <div class="image-side">
                                <img src="assets/images/post/'.$row['postImg'].'" alt="" width="100px">
                            </div>
                            <div class="post-detail">
                                <b>'.$f_date['date'].'</b><br> <h6>'.$f_date['time'].'</h6><br>
                                <small>'.$row['postDesc'].'</small>
                            </div>
                            <a href="/post_view?post='.$row['postId'].'">View</a>
                        </div>
                        <div class="post-additionals">
                            <span>
                                <b>'.$row['likeCount'].'</b> <i class="fa-regular fa-thumbs-up"></i>
                            </span>
                            <span>
                                <b>'.$row['dislikeCount'].'</b> <i class="fa-regular fa-thumbs-down"></i>
                            </span>
                            <span>
                                <p></p><b>'.$row['commentCount'].'</b> <small>Answers</small></p>
                            </span>
                        </div>
                    </div>';
        }

}



//=========Update profile===============

if($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['profile_update_btn'])){
    checkEmptyFields($_POST['fname'], $_POST['lname'], $_POST['username'], $_POST['email'], "", "", 2);
    
    $fname = clean(ucwords($_POST['fname']));
    $lname = clean(ucwords($_POST['lname']));
    $username = $_POST['username'];
    $email = $_POST['email'];
    $userId = userId();
    validateUsername($con, $username, 2);
    $image = $_FILES['profile_pic'];
    $check = isImageAndWithinSize($image);
    $query = "";
    $random = rand(1,999);
    switch($check){
        case "none":
            $query = mysqli_prepare($con, "update tbluser set fname = ?, lname = ?, 
            username = ?, email = ? where userId = ?");
            mysqli_stmt_bind_param($query, "ssssi", $fname, $lname, $username, $email, $userId);
            $exe = mysqli_stmt_execute($query);
            if($exe){
                echo "success";
            }
            mysqli_stmt_close($query);
            break;
        case "!image":
            echo "!image";
            break;
            exit;
        case "maxSize":
            echo "maxSize";
            break;
            exit;
        case 1:
            $userImg = $random . $_FILES['profile_pic']['name'];
            $tmpNme = $_FILES['profile_pic']['tmp_name'];
            $to_folder = "assets/images/user/" . $userImg;
            move_uploaded_file($tmpNme, $to_folder);

            $query = mysqli_prepare($con, "update tbluser set fname = ?, lname = ?, 
            username = ?, email = ?, userImg = ? where userId = ?");
            mysqli_stmt_bind_param($query, "sssssi", $fname, $lname, $username, $email,$userImg, $userId);
            $exe = mysqli_stmt_execute($query);
            if($exe){
                echo "success";
            }
            mysqli_stmt_close($query);
            break;
    }
    
    


}



//______________________@ Recyclable Function @____________________________//
//input validation
function clean($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;

 }

 //date formatting

 function formatDateTime($dateTime){
    $date = new DateTime($dateTime);
    $dateFormat = $date ->format('F j, Y');
    $timeFormat = $date ->format('g:i a');
    return [
        'date' => $dateFormat,
        'time' => $timeFormat
    ];
}

function userId(){
    return $_SESSION['userId'];
}

//Case 1 if the function is used in registration or login
//Case 2 if the function is used for profile update

//check email
function validateEmail($con, $email, $type){
    switch($type){
        case 1:
            $stmt_check = mysqli_prepare($con, "select email from tbluser where email = ?");
            mysqli_stmt_bind_param($stmt_check, "s", $email);
            $result = mysqli_stmt_execute($stmt_check);
            mysqli_stmt_store_result($stmt_check);
                
            if (mysqli_stmt_num_rows($stmt_check) > 0) {
                echo "Email is already in use.";
                //echo "<script>window.location.href='/signup?exists'</script>";
                exit;
            }
            break;
        case 2:
            break;

    }
    
}

//check username
function validateUsername($con, $username, $type){
    switch($type){
        case 1:
            $stmt_check = mysqli_prepare($con, "select username from tbluser where username = ?");
            mysqli_stmt_bind_param($stmt_check, "s", $username);
            $result = mysqli_stmt_execute($stmt_check);
            mysqli_stmt_store_result($stmt_check);

            if (mysqli_stmt_num_rows($stmt_check) > 0) {
                echo "Username is already in use.";
                //echo "<script>window.location.href='/signup?exists'</script>";
                exit;
            }
            break;
        case 2:

            $userId = userId();

            $stmt_check = mysqli_prepare($con, "select username from tbluser where username = ? and userId != ? ");
            mysqli_stmt_bind_param($stmt_check, "si", $username, $userId);
            $result = mysqli_stmt_execute($stmt_check);
            mysqli_stmt_store_result($stmt_check);

            if (mysqli_stmt_num_rows($stmt_check) > 0) {
                echo "Username is already in use.";
                //echo "<script>window.location.href='/signup?exists'</script>";
                exit;
            }
            break;
    }
    
}

//check signup field input
function checkEmptyFields($fname, $lname, $username, $email, $pass, $userType, $type) {
    switch($type){
        case 1:
            if (empty($fname) || empty($lname) || empty($username) || empty($email) || empty($pass) || empty($userType)) {
                echo 'Please fill out all fields.';
                exit;
            }
            break;
        case 2:
            if (empty($fname) || empty($lname) || empty($username) || empty($email)) {
                echo 'Please fill out all fields.';
                exit;
            }
            break;
    }
}

//check file

function isImageAndWithinSize($file) {
    // Check if the file is actually uploaded
    if (empty($file['name']) || $file['error'] != UPLOAD_ERR_OK) {
        return "none";
        
    }
    // Check if the file is an image
    $imageTypes = ['image/jpg','image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($file['tmp_name']);
    if (!in_array($fileType, $imageTypes)) {
        return "!image";
        
    }
    // Check if the file size exceeds 5MB (5 * 1024 * 1024 bytes)
    if ($file['size'] > 5 * 1024 * 1024) {
        return "maxSize";
        
    }
    return 1;
}


//check if the post is in the database

function checkPost($postId, $isUser, $con){
    switch($isUser){
        case 1:
            $userId = userId();
            $query = mysqli_prepare($con, "select * from tblpost where postId = ? and userIdfk = ?");
            mysqli_stmt_bind_param($query, "si", $postId, $userId);
            mysqli_stmt_execute($query);
            $result = mysqli_stmt_get_result($query);
            if(mysqli_num_rows($result) > 0){
                return 1;
            }else{
                return 0;
                exit;
            }
            break;
        case 0;

            $query = mysqli_prepare($con, "select * from tblpost where postId = ?");
            mysqli_stmt_bind_param($query, "s", $postId);
            mysqli_stmt_execute($query);
            $result = mysqli_stmt_get_result($query);
            if(mysqli_num_rows($result) > 0){
                return 1;
            }else{
                return 0;
                exit;
            }
            break;
    }
}

//check if user is flagged


function isUserFlagged($userId, $con){
    $query = mysqli_prepare($con, "select isFlag from tbluser where userId = ? and isFlag = 1");
    mysqli_stmt_bind_param($query, "i", $userId);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    
    if(mysqli_num_rows($result) > 0){
        echo "disabled";
        exit;
    }
    mysqli_stmt_close($query);
}



?>