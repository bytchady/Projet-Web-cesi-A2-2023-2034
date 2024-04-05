<?php

namespace App\Middlewares;
use App\Entity\Center;
use App\Entity\Promotion;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Views\Twig;

class HeaderDataMiddleware implements MiddlewareInterface
{
    private Twig $view;
    private EntityManager $entityManager;

    public function __construct(Twig $view, EntityManager $entityManager)
    {
        $this->view = $view;
        $this->entityManager = $entityManager;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Ici, récupérez vos données, par exemple, les promotions et les centres
        $promotionRepository = $this->entityManager->getRepository(Promotion::class);
        $centerRepository = $this->entityManager->getRepository(Center::class);
        $promotions = $promotionRepository->findAll();
        $centers = $centerRepository->findAll();

        // Ajoutez les données d'en-tête au contenu global de Twig
        $this->view->getEnvironment()->addGlobal('promotions', $promotions);
        $this->view->getEnvironment()->addGlobal('centers', $centers);

        // Continuez le traitement de la requête
        return $handler->handle($request);
    }
}

