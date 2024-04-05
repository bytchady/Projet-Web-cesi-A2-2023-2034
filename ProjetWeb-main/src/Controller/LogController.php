<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Entity\Users;
use RKA\Session;
use Slim\Views\Twig;
setcookie('logged', 'false', time() + 3600, '/');
setcookie('user');

class LogController
{
    private  $entityManager;
    private Twig $twig;
    private Session $session;

    public function __construct(ContainerInterface $container)
    {
        $this->entityManager = $container->get(EntityManager::class);
        $this->twig = $container->get('view');
        $this->session = new Session();
    }
    public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->twig->render($response, 'Login.twig');
    }

    public function Authentification(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $formData = $request->getParsedBody();

        $username = $formData['Login'];
        $password = $formData['Password'];

        // Récupérer l'utilisateur par son nom d'utilisateur
        $userRepository = $this->entityManager->getRepository(Users::class);
        $user = $userRepository->findOneBy(['Login' => $username]);

        // Si l'authentification est réussie
        if ($user && password_verify($password, $user->getPassword())) {
            //$this->session->set('user', $user);
            setcookie('user', $user->getLogin(), time() + 3600, '/');
            setcookie('logged', 'true', time() + 3600, '/');

            $responseData = ['success' => true, 'message' => 'Connexion reussie'];
        }
        else {
            $responseData = ['success' => false, 'message' => 'Identifiants invalides'];
        }
        $response->getBody()->write(json_encode($responseData));
        return $response->withHeader('Content-Type', 'application/json');
    }
}