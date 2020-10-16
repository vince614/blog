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
require_once 'vendor/autoload.php';

// Instancies class
$abstract = new Core_Abstract();

// Get router url
$router = new Router($abstract->getUrl());

// Try to connect
$abstract->tryConnectionWithCookie();

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
 * @GET & @POST route
 */
$router->get('/chapters', function () {});

/**
 * Get chapter by ID
 * @GET & @POST route
 */
$router->get('/chapters/:ticketId', function ($idTicket) {
    require_once 'Controllers/ChaptersController.php';
    new ChaptersController('chapters', $idTicket);
});
$router->post('/chapters/:ticketId', function ($idTicket) {
    require_once 'Controllers/ChaptersController.php';
    new ChaptersController('chapters', $idTicket);
});

/**
 * Add new chapter
 * @GET & @POST route
 */
$router->get('/new', function () {});
$router->post('/new', function () {});

/**
 * Edit chapter
 * @GET & @POST route
 */
$router->get('/edit/:chapterId', function ($idTicket) {
    require_once 'Controllers/NewController.php';
    new NewController('new', $idTicket);
});
$router->post('/edit/:chapterId', function ($idTicket) {
    require_once 'Controllers/NewController.php';
    new NewController('new', $idTicket);
});

/**
 * Pagination
 * @GET route
 */
$router->get('/chapters/page/:page', function ($page) {
    require_once 'Controllers/ChaptersController.php';
    new ChaptersController('chapters', $page, true);
});

/**
 * Logout route
 * @GET route
 */
$router->get('/logout', function () {
    if (isset($_SESSION)) {
        // Destroy session
        unset($_SESSION['user']);
        // Delete cookies
        setcookie('login', null);
        setcookie('pass_hache', null);
        setcookie('remember_key', null, null, '/');
    }
    // Redirect
    header('Location: ./');
});


// Run
$router->run();

