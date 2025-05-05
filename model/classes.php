<?php

class User{
    private $userId;
    private $con;

    public function __construct( $userId, $con){
        $this->userId = $userId;
        $this->con = $con;
    }

    private function fetchResult($column){
        $query = mysqli_prepare($this->con, "select $column from tbluser where
        userId = ?" );
        mysqli_stmt_bind_param($query, "i", $this->userId);
        mysqli_stmt_execute($query);
        mysqli_stmt_bind_result($query, $result);
        mysqli_stmt_fetch($query);
        mysqli_stmt_close($query);
        return $result ?? null;
    }
    public function getUserId(){
        return $this->fetchResult("userId");
    }
    public function getFname(){
        return $this->fetchResult("fname");
    }
    public function getLname(){
        return $this->fetchResult("lname");
    }
    public function getUsername(){
        return $this->fetchResult("username");
    }
    public function getEmail(){
        return $this->fetchResult("email");
    }
    public function getUserImg(){
        return $this->fetchResult("userImg");
    }
    public function getUserType(){
        return $this->fetchResult("userType");
    }
}








class Post{
    private $postId;
    private $userId;
    private $con;

    public function __construct($postId, $userId, $con){
        $this->postId = $postId;
        $this->userId = $userId;
        $this->con = $con;
    }

    private function fetchResult($column){
        $query = mysqli_prepare($this->con, "select $column from tblpost p 
        inner join tbluser u on p.userIdfk = u.userId 
        where p.postId = ?");
        mysqli_stmt_bind_param($query, "s", $this->postId);
        mysqli_stmt_execute($query);
        mysqli_stmt_bind_result($query, $result);
        mysqli_stmt_fetch($query);
        mysqli_stmt_close($query);
        return $result ?? null;
    }
    public function getUserId(){
        return $this->fetchResult("userId");
    }
    public function getPostId(){
        return $this->fetchResult("p.postId");
    }
    public function getFname(){
        return $this->fetchResult("u.fname");
    }
    public function getLname(){
        return $this->fetchResult("u.lname");
    }
    public function getPostImg(){
        return $this->fetchResult("p.postImg");
    }
    public function getPostExtraOne(){
        return $this->fetchResult("p.postExtraOne");
    }
    public function getPostExtraTwo(){
        return $this->fetchResult("p.postExtraTwo");
    }

    public function getPostDesc(){
        return $this->fetchResult("p.postDesc");
    }
    public function getUserImg(){
        return $this->fetchResult("u.userImg");
    }
    public function postCreated(){
        $date = $this->formatDateTime($this->fetchResult("p.postCreated"));
        return $date;
    }

    //post setting

    public function isUserPosted(){
        $user_Id =  $this->fetchResult("p.userIdfk");
        $postId = $this->getPostId();
        if($this->userId == $user_Id){
            return "<li onclick=\"window.location.href='/edit_post?pe=$postId'\">Edit</li>
                    <li onclick=\"delete_post('$postId')\">Delete</li>
                    <li onclick=\"report_post('$postId')\">Report</li>";
        }else{
            return "<li onclick=\"report_post('$postId')\">Report</li>";
        }
    }

    private function formatDateTime($dateTime){
        $date = new DateTime($dateTime);
        $dateFormat = $date ->format('F j, Y');
        $timeFormat = $date ->format('g:i a');
        return $dateFormat ."<br>". $timeFormat;
    }


}
//Diagnosing result and Activity Logging
class Disease{

    private $logId;
    private $con;
    
    public function __construct($logId, $con){
        $this->logId = $logId;
        $this->con = $con;
    }

    private function fetchResult($column){
        $query = mysqli_prepare($this->con, "select $column from tblhistory h inner join tbldisease d on 
                                            h.diseaseIdfk = d.diseaseId where historyId = ?");
        mysqli_stmt_bind_param($query, "s", $this->logId);
        mysqli_stmt_execute($query);
        mysqli_stmt_bind_result($query, $result);
        mysqli_stmt_fetch($query);
        mysqli_stmt_close($query);
        return $result ?? null;

    }

    public function getDiseaseId(){
        return $this->fetchResult("diseaseId");
    }
    public function getDiseaseName(){
        return $this->fetchResult("diseaseName");
    }
    public function getDiseaseDesc(){
        return $this->fetchResult("diseaseDesc");
    }
    public function getDiseaseSymptom(){
        return $this->fetchResult("diseaseSymptom");
    }
    public function getDiseaseTreatment(){
        return $this->fetchResult("diseaseTreatment");
    }
    public function getDiseasePrevention(){
        return $this->fetchResult("diseasePrevention");
    }
    public function getDiseaseImage(){
        return $this->fetchResult("diseaseImage");
    }
    public function getPercentage(){
        return $this->fetchResult("percentage");
    }

}


//Recyclable
function contentAvailability($postId, $con){
    $userId = $_SESSION['userId'];
    $check = mysqli_prepare($con, "select userIdfk, postId from tblpost where postId = ?");
    mysqli_stmt_bind_param($check, "s", $postId);
    mysqli_stmt_execute($check);
    $rsult = mysqli_stmt_get_result($check);
    $row = mysqli_fetch_assoc($rsult);
    if(mysqli_num_rows($rsult) < 1){
        return 0;
    }else if($row['userIdfk'] != $userId){
        $row['userIdfk'];
        return "invalid";
    }else{
        return 1;
    }
    mysqli_stmt_close($check);
    exit;
}