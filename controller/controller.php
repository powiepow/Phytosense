<?php

//  routes
function index() {
    require 'view/index.php'; 
}


function forgot_pass(){
    require 'view/forgot_pass.php';
}
function reset_code(){
    require 'view/reset_code.php';
}



function diagnose() {
    require 'view/diagnose.php'; 
}
function profile() {
    require 'view/profile.php'; 
}
function community() {
    require 'view/community.php'; 
}
function post() {
    require 'view/post.php'; 
}
function signin() {
    require 'view/signin.php'; 
}
function signup() {
    require 'view/signup.php'; 
}
function scanner(){
    require 'view/scanner.php';
}
function diagnosis(){
    require 'view/diagnosis.php';
}
function post_view(){
    require 'view/post_view.php';
}
function edit_post(){
    require 'view/edit_post.php';
}


//__________________@ Functions @____________________
function crud(){
    require 'model/crud.php';
}
function model_used(){
    require 'model/model_used.php';
}
function logout(){
    require 'helper/logout.php';
}


// reset email

function reset_email(){
    require 'model/reset_email.php';
}



//ADMIN

function a_crud(){
    require 'model/a.crud.php';
}

//__________@ To Admin @___________

//normal
function a_signin(){
    require 'view/a_view/signin.php';
}
//case
function a_brute_signin(){
    require 'view/a_view/brute_signin.php';
}



function index_admin(){
    require 'view/a_view/index.php';
}
function community_admin(){
    require 'view/a_view/community.php';
}
function plant_disease_admin(){
    require 'view/a_view/plant_disease.php';
}
function user_admin(){
    require 'view/a_view/user.php';
}
function post_admin(){
    require 'view/a_view/a_sub_view/post.php';
}
function user_profile_admin(){
    require 'view/a_view/a_sub_view/user_profile.php';
}
function login_admin(){
    require 'view/a_view/login.php';
}

function analytic(){
    require 'view/a_view/analytic.php';
}
function histories(){
    require 'view/a_view/a_sub_view/histories.php';
}
function usertype(){
    require 'view/a_view/a_sub_view/usertype.php';
}
function reported(){
    require 'view/a_view/a_sub_view/reported.php';
}

function flagged(){
    require 'view/a_view/a_sub_view/flagged.php';
}


function retrain(){
    require 'view/a_view/retrain.php';
}
function deployment(){
    require 'view/a_view/deployment.php';
}

//404

function error404(){
    require 'view/404/error_404.html';
}
function error410(){
    require 'view/404/error_410.php';
}
function test(){
    require 'view/test_env.html';
}