<?php

declare(strict_types=1);

use App\Controller\AccountController;
use App\Controller\CompanyController;
use App\Controller\HomePageController;
use App\Controller\LogController;
use App\Controller\OfferController;
use App\Controller\StatController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use RKA\Session;
use Slim\App;
//use Slim\Psr7\Request;
//use Slim\Psr7\Response;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Twig\Loader\FilesystemLoader;


// Chargement de autoloader
require __DIR__ . '/../vendor/autoload.php';

$loader = new FilesystemLoader(__DIR__ . '/../src/templates');
$twig = new Twig($loader);
$authMiddleware = require_once __DIR__ . '/../src/Middlewares/AuthMiddleware.php';

// Fonction de configuration des routes
return function (App $app) {
    global $authMiddleware;

    $app->addBodyParsingMiddleware();
    $app->add(function (Request $request, RequestHandlerInterface $handler): Response {
        $routeContext = RouteContext::fromRequest($request);
        $routingResults = $routeContext->getRoutingResults();
        $methods = $routingResults->getAllowedMethods();
        $requestHeaders = $request->getHeaderLine('Access-Control-Request-Headers');

        $response = $handler->handle($request);

        $response = $response->withHeader('Access-Control-Allow-Origin', '*');
        $response = $response->withHeader('Access-Control-Allow-Methods', implode(',', $methods));
        $response = $response->withHeader('Access-Control-Allow-Headers', $requestHeaders);
        $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');

        return $response;
    });
    $app->addRoutingMiddleware();

    // Authentification
    $app->get('/Connection', [LogController::class, 'login'])->setName('Connection');
    $app->post('/Connection', LogController::class . ':Authentification')->setName('Connection');

    if ($_COOKIE['logged'] == 'true') {
        //Gestion du footer et page d'accueil
        $app->get('/Page-d-accueil', [HomePageController::class, 'homePage'])->setName('Page-d-accueil');
        $app->get('/Trouver-une-entreprise', [HomePageController::class, 'findcompanyFooter'])->setName('Trouver-une-entreprise');
        $app->get('/A-propos-de-FindMe', [HomePageController::class, 'aboutfindmeFooter'])->setName('A-propos-de-FindMe');
        $app->get('/Politique-de-confidentialité', [HomePageController::class, 'privacypolicyFooter'])->setName('Politique-de-confidentialité');

        // Gestion des comptes
        $app->get('/Compte', [AccountController::class, 'findAccount'])->setName('Comptes');
        $app->get('/Compte/Profil/{id}/{name}-{surname}', [AccountController::class, 'showAccount']);
        $app->get('/Compte/Profil/{id}', [AccountController::class, 'showAccount']);
        $app->get('/Compte/Modifier/{id}/{name}-{surname}', [AccountController::class, 'editAccount']);
        $app->post('/Compte/Modifier/{id}/{name}-{surname}', [AccountController::class, 'submitEditAccount'])->setName('Modifier-un-compte');
        $app->get('/Creer-un-compte', [AccountController::class, 'newAccount']);
        $app->post('/Creer-un-compte', [AccountController::class, 'createAccount'])->setName('Creer-un-compte');


        // Gestion des entreprises
        $app->get('/Creer-une-entreprise', [CompanyController::class, 'newcompanyLog']);
        $app->post('/Creer-une-entreprise', [CompanyController::class, 'createCompany'])->setName('Creer-une-entreprise');
        $app->get('/Entreprise/{id}/{name}', [CompanyController::class, 'companyPage']);
        $app->get('/Entreprise/Modifier/{id}/{name}', [CompanyController::class, 'editCompany']);
        $app->post('/Entreprise/Modifier/{id}/{name}', [CompanyController::class, 'submitEditCompany'])->setName('Modifier-une-entreprise');


        // Gestion des offres
        $app->get('/Offres', [OfferController::class, 'findOffer']);
        $app->post('/postuler/{offerId}', [OfferController::class, 'applyOffer'])->setName('Postuler');
        $app->get('/Offre/Modifier/{offerId}', [OfferController::class, 'editOffer']);
        $app->post('/Offre/Modifier/{offerId}', [OfferController::class, 'submitEditOffer'])->setName('Modifier-une-offre');
        //    $app->post('/Offre/Modifier/{id}/{title}', [OfferController::class, 'submitEditOffer'])->setName('Modifier-une-offre');
        $app->get('/Creer-une-offre', [OfferController::class, 'newOffer']);
        $app->get('/Creer-une-offre/{companyId}', [OfferController::class, 'getCompanyAddresses']);
        $app->post('/Creer-une-offre', [OfferController::class, 'createOffer'])->setName('Creer-une-offre');


        // Gestion des statistiques
        $app->get('/Stat-Entreprise', [StatController::class, 'StatCompany'])->setName('Stat-Entreprise');
        $app->get('/Stat-Offre', [StatController::class, 'StatOffer'])->setName('Stat-Offer');
    }else{
             header('Location: http://findme.fr/Connection');
         }


//    $app->group('/',function ($group){
//        $group->get('disconnect', function ($request, $response) {
//            Session::destroy();
//            return $response;
//        });
//
//        // Route pour la page "Postuler"
//        $group->get('/Postuler', function ( $request,  $response) use ($twig) {
//        return $twig->render($response,'ApplyOffer.twig' );
//        })->setName('Postuler');
//
//
//        // Route pour la page "Creer-une-entreprise"
//        $group->get('/Creer-une-entreprise', function ( $request,  $response) use ($twig) {
//        return $twig->render($response,'CreateCompany.twig' );
//        })->setName('Creer-une-entreprise');
//
//        // Route pour la page "Modifier-un-compte"
//        $group->get('/Modifier-un-compte', function ( $request,  $response) use ($twig) {
//        return $twig->render($response,'EditAccount.twig' );
//        })->setName('Modifier-un-compte');
//
//        // Route pour la page "Modifier-un-entreprise"
//        $group->get('/Modifier-une-entreprise', function ( $request,  $response) use ($twig) {
//        return $twig->render($response,'EditCompany.twig' );
//        })->setName('Modifier-un-entreprise');
//
//        // Route pour la page "Modifier-un-offre"
//        $group->get('/Modifier-une-offre', function ( $request,  $response) use ($twig) {
//        return $twig->render($response,'EditOffer.twig' );
//        })->setName('Modifier-un-offre');
//
//
//        // Route pour la page "Profil-pilote"
//        $group->get('/Profil-pilote', function ( $request,  $response) use ($twig) {
//        return $twig->render($response,'PiloteProfile.Twig' );
//        })->setName('Profil-pilote');
//
//        // Route pour la page "Rechercher-un-profil"
//        $group->get('/Rechercher-un-profil', function ($request, $response) use ($twig) {
//        return $twig->render($response, 'SearchProfile.Twig');
//        })->setName('Rechercher-un-profil');
//
//    })->add($authMiddleware);
};