<?php

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
$router->get('/tickets', function () {  echo "Ensemble des tickets"; });
$router->get('/tickets/:id', function ($idTicket) {  echo "Vous êtes actuellement sur le ticket " . $idTicket; });


// Run
$router->run();

