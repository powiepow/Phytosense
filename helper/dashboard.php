<?php

function userCount($con) {
    $query = "select count(*) as user_count from tbluser where userType != 'Admin' and isAdmin != 1";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['user_count'];
}

function postCount($con) {
    $query = "select count(*) as post_count from tblpost";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['post_count'];
}

function reportedPostCount($con) {
    $query = mysqli_prepare($con, "select count(distinct postIdfk) as report_count from postreport");
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    $row = mysqli_fetch_assoc($result);
    return $row['report_count'];
}

function historyCount($con) {
    $query = "select count(*) as history_count from tblhistory";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['history_count'];
}

function isFlagUser($con){
    $query = "select count(isFlag) as flagged from tbluser where isFlag = 1";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['flagged'];
}

function getLatestPost($con) {
    $query = "
        select tblpost.postDesc, tblpost.postCreated, tblpost.postImg, tblpost.postId, 
               tbluser.fname, tbluser.lname
        from tblpost
        inner join tbluser on tblpost.userIdfk = tbluser.userId
        order by tblpost.postCreated desc
        limit 2
    ";
    
    $result = mysqli_query($con, $query);
    while($row=mysqli_fetch_assoc($result)){
        echo '<div class="rp-box" onclick="window.location.href=\'/a_view/page/post?post='.$row['postId'].'\'">
                  <img src="../../assets/images/post/'.$row['postImg'].'" alt="">
                  <div class="rp-post">
                      <h4>'.$row['fname'].' '.$row['lname'].'</h4>
                      <b><small>'.$row['postCreated'].'</small></b>
                      <p>'.$row['postDesc'].'</p>
                  </div>
              </div>';
    }
}

?>