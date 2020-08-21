<?php

// Start session
session_start();

/**
 * Require class
 * @TODO MAKE AUTOLOAD
 */
require_once "Abstract.php";
require_once "Router/Router.php";
require_once "Router/Route.php";
require_once "Router/RouterException.php";
require_once "Controllers/Controller.php";

// Instancies class
$abstract = new Core_Abstract();

// Get router url
$router = new Router($abstract->getUrl());

// Get routes
$router->get('/', function () {  echo "Bienvenue sur la page d'accueil"; });
$router->get('/register', function () {  echo "Bienvenue sur la page de création de compte"; });
$router->get('/account', function () {});
$router->post('/account', function () {});
$router->get('/tickets', function () {  echo "Ensemble des tickets"; });
$router->get('/tickets/:id', function ($idTicket) {  echo "Vous êtes actuellement sur le ticket " . $idTicket; });

// Logout route
$router->get('/logout', function () {
    if (!empty($_SESSION)) {
        // Destroy session
        $_SESSION = array();
        session_destroy();
        // Delete cookies
        setcookie('login', null);
        setcookie('pass_hache', null);
    }
    // Redirect
    header('Location: ./account');
});


// Run
$router->run();

