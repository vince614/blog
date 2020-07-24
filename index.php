<?php

/**
 * Require class
 * @TODO MAKE AUTOLOAD
 */
require_once "Abstract.php";
require_once "Router/Router.php";
require_once "Router/Route.php";
require_once "Router/RouterException.php";

// Instancies class
$abstract = new Core_Abstract();

// Get router url
$router = new Router($abstract->getUrl());

// Get routes
$router->get('/register', function () {  echo "Bienvenue sur la page de création de compte"; });
$router->get('/login', function () {  echo "Bienvenue sur la page de connection sur votre compte"; });
$router->get('/tickets', function () {  echo "Ensemble des tickets"; });
$router->get('/tickets/:id', function ($idTicket) {  echo "Vous êtes actuellement sur le ticket " . $idTicket; });


// Run
$router->run();

