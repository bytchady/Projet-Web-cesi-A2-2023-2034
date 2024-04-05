<?php

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface;
use RKA\Session;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

$loggedInMiddleware = function (Request $request, $handler): ResponseInterface {
    $session = new Session();
    if (!isset($session->user)) {
        $response = new Response();
        return $response->withHeader('Location', '/Connection')->withStatus(302);
    }
    $request = $request->withAttribute("user", $session->user);

    return $handler->handle($request);
};
return $loggedInMiddleware;
