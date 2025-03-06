<?php

require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../core/Middleware.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/View.php';
require_once __DIR__ . '/../core/Response.php';
require_once __DIR__ . '/../core/DB.php';

use Core\Router;
use Core\Middleware;

// Router nesnesini oluştur
$router = new Router();

// Örnek route tanımları
$router->get('/user/{id}', 'UserController@show');
$router->get('/blog/{id}/translations/{language_id}', 'BlogController@translations');
$router->get('/user/{id?}', 'UserController@optional');
$router->get('/user/{\d+}', 'UserController@numeric');

// Middleware tanımları
$router->middleware('/user/{id}', function() {
    Middleware::handleAuth();
});

// Gelen isteği işle
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
