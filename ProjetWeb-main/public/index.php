<?php

use RKA\SessionMiddleware;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';

// Démarrer la session PHP
//session_start();

// Création d'un conteneur d'injection de dépendances
$container = require_once __DIR__ . '/../bootstrap.php';

$app = \DI\Bridge\Slim\Bridge::create($container);

$twig = Twig::create(__DIR__ . "/../src/templates", ['cache' => false]);
$container->set('view', $twig);
$app->add(TwigMiddleware::create($app, $twig));

$app->add(new SessionMiddleware(['name' => 'MySessionName']));
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// Chargement des routes depuis le fichier de configuration
$routes = require __DIR__ . '/../src/routes.php';
$routes($app,$twig);

// Exécution de l'application Slim
$app->run();