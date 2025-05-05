<?php

require 'helper/uri.php';

// Define the path to the controller
$controllerPath = 'controller/controller.php';

// Get the current URI path
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Define the routes and their corresponding actions
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
    
    //__________@ To Functions @___________
    
    '/crud' => 'crud',
    '/reset_email' => 'reset_email',
    '/logout' => 'logout',

    //Admin

    '/a.crud' => "a_crud",

    //__________@ To Admin @___________
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

    //__________@ Retrain @___________
    '/a_view/page/retrain' => 'retrain',
    '/a_view/page/deployment' => 'deployment',


    
    '/a_view/page/a_b_signin' => 'a_brute_signin',
    //__________@ Test Route @___________
    '/test' => 'test',

    //error code route
    '/404' => 'error404',
    '/410' => 'error410',
];  

// Check if the URI exists in the routes array
if (array_key_exists($uri, $routes)) {
    // Require the central controller file
    require $controllerPath;

    // Call the corresponding action
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
    //echo "<script>window.location.href='view/404/error_404.html'</script>"
}
