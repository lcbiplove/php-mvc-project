<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

/**
 * Composer
 */
require dirname(__FILE__) . '/vendor/autoload.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * Session start
 */
session_start();

/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);

if (\App\Auth::getUser()) {
    $router->add('{user_name:[a-z-_]+}/', ['controller' => 'Logged', 'action' => 'home']); 
} else {
    $router->add('{user_name:[a-z-_]+}/', ['controller' => 'Admin', 'action' => 'show-login']); 
}
$router->add('{user_name:[a-z-_]+}/go-login/', ['controller' => 'Admin', 'action' => 'go-login']);
$router->add('{user_name:[a-z-_]+}/logout/', ['controller' => 'Logged', 'action' => 'logout']);
$router->add('{user_name:[a-z-_]+}/add/', ['controller' => 'Logged', 'action' => 'show-add']);  
$router->add('{user_name:[a-z-_]+}/go-upload/', ['controller' => 'Logged', 'action' => 'go-upload']);  
$router->add('{user_name:[a-z-_]+}/{title_id:\d+}/delete/', ['controller' => 'Logged', 'action' => 'delete']);
$router->add('{user_name:[a-z-_]+}/{title_id:\d+}/make-private-public/', ['controller' => 'Logged', 'action' => 'make-private-public']);
$router->add('{user_name:[a-z-_]+}/{title_id:\d+}/', ['controller' => 'Logged', 'action' => 'show-titles']);  



$router->add('{controller}/{action}/');

//var_dump($router->getRoutes());

$url = $_SERVER['QUERY_STRING'];
if (!preg_match('/[&]/', $url) && $url !== "") {
    $len = strlen($url);
    if ($url[$len-1] !== "/") {
        $url = "/". $url . "/";
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
        exit;
    }
}

$router->dispatch($_SERVER['QUERY_STRING']);

