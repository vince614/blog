<?php

require_once "Router/Router.php";
require_once "Router/Route.php";
require_once "Router/RouterException.php";

$url = '/';
if (isset($_GET['url'])) {
    $url = $_GET['url'];
}

$router = new Router($url);
$router->get('/post', function($id) { echo "Bienvenue sur ma homepage !"; });
$router->run();

