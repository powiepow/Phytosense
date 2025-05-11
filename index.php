<?php

require 'helper/uri.php';

$controllerPath = 'controller/controller.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routes = [
    '/' => 'index',
    
    
    '/forgot_pass' => 'forgot_pass',
    '/reset_code' => 'reset_code',
    
    
    
    '/diagnose' => 'diagnose',
    '/profile' => 'profile',
    '/community' => 'community',
    '/post' => 'post',
    '/signin' => 'signin',
    '/signup' => 'signup',
    '/scanner' => 'scanner',
    
    '/diagnosis' => 'diagnosis',
    '/post_view' => 'post_view',

    '/edit_post' => 'edit_post',


    '/model_used' => 'model_used',
    
    
    '/crud' => 'crud',
    '/reset_email' => 'reset_email',
    '/logout' => 'logout',

   

    '/a.crud' => "a_crud",

    '/a_view/page/login' => 'login_admin',
    '/a_view/page/index' => 'index_admin',
    '/a_view/page/community' => 'community_admin',
    '/a_view/page/plant_disease' => 'plant_disease_admin',
    '/a_view/page/user' => 'user_admin',
    '/a_view/page/post' => 'post_admin',
    '/a_view/page/user_profile' => 'user_profile_admin',
    '/a_view/page/signin' => 'a_signin',
    '/a_view/page/analytic' => 'analytic',
    '/a_view/page/histories' => 'histories',
    '/a_view/page/usertype' => 'usertype',
    '/a_view/page/reported' => 'reported',
    '/a_view/page/flagged' => 'flagged',

    '/a_view/page/retrain' => 'retrain',
    '/a_view/page/deployment' => 'deployment',


    
    '/a_view/page/a_b_signin' => 'a_brute_signin',
    '/test' => 'test',

    '/404' => 'error404',
    '/410' => 'error410',
];  

if (array_key_exists($uri, $routes)) {
    require $controllerPath;

    $action = $routes[$uri];
    if (function_exists($action)) {
        $action();
    } else {
        echo "Action $action not found";
    }
} else {
    require $controllerPath;

    $action = $routes['/404'];
    if (function_exists($action)) {
        $action();
    }
}
