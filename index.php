<?php

// Start session
session_start();

/**
 * Required class
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

/**
 * Home route
 * @GET route
 */
$router->get('/', function () {});

/**
 * Create account & login
 * @POST & @GET routes
 */
$router->post('/account', function () {});
$router->get('/account', function () {});

/**
 * All chapters
 * @GET route
 */
$router->get('/chapters', function () {  echo "Ensemble des chapitres"; });

/**
 * Get chapter by ID
 * @GET route
 */
$router->get('/chapters/:page', function ($idTicket) {
    require_once 'Controllers/ChaptersController.php';
    new ChaptersController('chapters');
});

/**
 * Add new chapter
 * @GET & @POST route
 */
$router->get('/new', function () {});
$router->post('/new', function () {});

/**
 * Logout route
 * @GET route
 */
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

