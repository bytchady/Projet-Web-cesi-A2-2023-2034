<?php

namespace App\Controller;

use App\Entity\Center;
use App\Entity\Promotion;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;

abstract class AbstractController
{
    protected EntityManager $entityManager;
    protected ContainerInterface $container;
    protected Twig $view;

    // constructor receives container instance
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->entityManager = $container->get(EntityManager::class);
        $this->view = $container->get(Twig::class);
    }

    public function withJson(ResponseInterface $response, $data): ResponseInterface
    {
        $json = json_encode($data);

        // Retourner les offres en JSON dans la réponse HTTP
        $response->getBody()->write($json);
        return $response->withHeader('Content-Type', 'application/json');
    }

    protected function getHeaderData(): array {
        // Récupération des promotions et des centres depuis les repositories
        $promotionRepository = $this->entityManager->getRepository(Promotion::class);
        $centerRepository = $this->entityManager->getRepository(Center::class);
        $promotions = $promotionRepository->findAll();
        $centers = $centerRepository->findAll();

        // Convertissez les entités en un tableau pour Twig
        $promotionData = array_map(function ($promotion) {
            return $promotion->getPromotion(); // Supposons que c'est le nom de la promotion
        }, $promotions);

        $centerData = array_map(function ($center) {
            return $center->getNameCenter(); // Supposons que c'est le nom du centre
        }, $centers);

        // Retournez ces données pour qu'elles puissent être utilisées dans Twig
        return [
            'promotions' => $promotionData,
            'centers' => $centerData
        ];
    }
}