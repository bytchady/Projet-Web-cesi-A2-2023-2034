<?php

namespace App\Controller;

use App\Entity\Center;
use App\Entity\Promotion;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Entity\Users;
use RKA\Session;
use Slim\Views\Twig;

class AccountController
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

    public function findAccount(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userLogin = $_COOKIE["user"];
        $userCookieRepository = $this->entityManager->getRepository(Users::class);
        $userCookie = $userCookieRepository->findOneBy(['Login' => $userLogin]);

        $params = $request->getQueryParams();
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $limit = 9; // Nombre d'utilisateurs par page
        $offset = ($page - 1) * $limit;

        $userRepository = $this->entityManager->getRepository(Users::class);

        // Modifier ici pour filtrer par la colonne `Del`
        $totalUsers = $userRepository->createQueryBuilder('u')
            ->select('count(u.Id_Users)')
            ->where('u.Del = :del')
            ->setParameter('del', 0)
            ->getQuery()
            ->getSingleScalarResult();

        $users = $userRepository->createQueryBuilder('u')
            ->where('u.Del = :del')
            ->setParameter('del', 0)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $usersArray = array_map(function ($user) {
            return [
                'id' =>$user->getIdUsers(),
                'nom' => $user->getFirstName(),
                'prenom' => $user->getLastName(),
                'type' => $user->getTypeUsers(),
            ];
        }, $users);
        $totalPages = ceil($totalUsers / $limit);

        // Rendez le template Twig en passant les utilisateurs et les données de pagination
        return $this->twig->render($response, 'FindAccount.twig', [
            'users' => $usersArray,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'userTypeCookie' => $userCookie ? $userCookie->getTypeUsers() : null,
            'userIdCookie' => $userCookie ? $userCookie->getIdUsers() : null,
        ]);
    }

    public function showAccount(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userLogin = $_COOKIE["user"];
        $userCookieRepository = $this->entityManager->getRepository(Users::class);
        $userCookie = $userCookieRepository->findOneBy(['Login' => $userLogin]);

        $id  = $request->getAttribute('id');

        $userRepository = $this->entityManager->getRepository(Users::class);
        $user = $userRepository->findOneBy([
            'Id_Users'=> $id,
        ]);

        $promotionName = $user->getPromotion() ? $user->getPromotion()->getPromotion() : null;
        $centerName = $user->getPromotion() && $user->getPromotion()->getCenter() ? $user->getPromotion()->getCenter()->getNameCenter() : null;

        return $this->twig->render($response, 'Profile.twig', [
            'userType' => $user->getTypeUsers(),
            'prenom' => $user->getLastName(),
            'nom' => $user->getFirstName(),
            'promotion' => $promotionName,
            'centre' => $centerName,
            'id'=> $id,
            'userTypeCookie' => $userCookie ? $userCookie->getTypeUsers() : null,
            'userIdCookie' => $userCookie ? $userCookie->getIdUsers() : null,
        ]);
    }

    public function editAccount(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userLogin = $_COOKIE["user"];
        $userCookieRepository = $this->entityManager->getRepository(Users::class);
        $userCookie = $userCookieRepository->findOneBy(['Login' => $userLogin]);

        $id  = $request->getAttribute('id');
        $nom = $request->getAttribute('name');
        $prenom = $request->getAttribute('surname');

        $userRepository = $this->entityManager->getRepository(Users::class);
        $user = $userRepository->findOneBy([
            'Id_Users'=> $id,
            'First_Name' => $nom,
            'Last_Name' => $prenom
        ]);

        $promotionName = $user->getPromotion() ? $user->getPromotion()->getPromotion() : null;
        $centerName = $user->getPromotion() && $user->getPromotion()->getCenter() ? $user->getPromotion()->getCenter()->getNameCenter() : null;

        return $this->twig->render($response, 'EditAccount.twig', [
            'id' => $id,
            'userType' => $user->getTypeUsers(),
            'prenom' => $user->getLastName(),
            'nom' => $user->getFirstName(),
            'promotion' => $promotionName,
            'centre' => $centerName,
            'userTypeCookie' => $userCookie ? $userCookie->getTypeUsers() : null,
            'userIdCookie' => $userCookie ? $userCookie->getIdUsers() : null,
        ]);
    }

    public function newAccount(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userLogin = $_COOKIE["user"];
        $userCookieRepository = $this->entityManager->getRepository(Users::class);
        $userCookie = $userCookieRepository->findOneBy(['Login' => $userLogin]);

        return $this->twig->render($response, 'CreateAccount.twig',[
            'userTypeCookie' => $userCookie ? $userCookie->getTypeUsers() : null,
            'userIdCookie' => $userCookie ? $userCookie->getIdUsers() : null,
            ]);
    }

    public function submitEditAccount(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $id  = $request->getAttribute('id');

        try {
            // Récupération des données du formulaire
            $formData = $request->getParsedBody();

            $requiredFields = ['First_Name', 'Last_Name', 'Status', 'Promotion', 'Center'];
            foreach ($requiredFields as $field) {
                if (empty($formData[$field])) {
                    $response->getBody()->write(json_encode(['success' => false, 'message' => "Le champ '{$field}' est requis."]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
            }

            $userRepository = $this->entityManager->getRepository(Users::class);
            $user = $userRepository->find($id);

            if (!$user) {
                $response->getBody()->write(json_encode(['success' => false, 'message' => "Utilisateur non trouvé"]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            if (isset($formData['DeleteAccount']) && $formData['DeleteAccount'] === 'yes') {
                $user->setDel(1); // Marquez l'utilisateur comme supprimé
                $this->entityManager->flush();

                $response->getBody()->write(json_encode(['success' => true, 'message' => 'Compte supprimé avec succès, redirection en cours...']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }

            // Mettre à jour les champs de l'utilisateur
            $user->setFirstName($formData['First_Name']);
            $user->setLastName($formData['Last_Name']);
            $user->setTypeUsers($formData['Status']);

            if (!empty($formData['Password'])) {
                $hashedPassword = password_hash($formData['Password'], PASSWORD_BCRYPT);
                $user->setPassword($hashedPassword);
            }
            $centerName = $formData['Center'];
            $promotionName = $formData['Promotion'];

            $centerRepository = $this->entityManager->getRepository(Center::class);
            $promotionRepository = $this->entityManager->getRepository(Promotion::class);

            $center = $centerRepository->findOneBy(['Name_Center' => $centerName]);
            if (!$center) {
                $center = new Center();
                $center->setNameCenter($centerName);
                $this->entityManager->persist($center);
            }

            $promotion = $promotionRepository->findOneBy(['Promotion' => $promotionName, 'Center' => $center]);
            if (!$promotion) {
                $promotion = new Promotion();
                $promotion->setPromotion($promotionName);
                $promotion->setCenter($center);
                $this->entityManager->persist($promotion);
            }

            $user->setPromotion($promotion);
            $this->entityManager->persist($user);

            $this->entityManager->flush();

            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Compte mis à jour avec succès, Redirection en cours...']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de la mise à jour.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function createAccount(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $formData = $request->getParsedBody();

            $requiredFields = ['First_Name', 'Last_Name', 'Promotion', 'Center', 'Login', 'Password'];
            foreach ($requiredFields as $field) {
                if (empty($formData[$field])) {
                    $response->getBody()->write(json_encode(['success' => false, 'message' => "Le champ '{$field}' est requis."]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
            }

            $centerName = $formData['Center'];
            $centerRepository = $this->entityManager->getRepository(Center::class);
            $center = $centerRepository->findOneBy(['Name_Center' => $centerName]);

            if (!$center) {
                $center = new Center();
                $center->setNameCenter($centerName);
                $this->entityManager->persist($center);
            }

            $promotionName = $formData['Promotion'];
            $existingPromotion = null;
            foreach ($center->getPromotion() as $promotion) {
                if ($promotion->getPromotion() === $promotionName) {
                    $existingPromotion = $promotion;
                    break;
                }
            }

            if (!$existingPromotion) {
                $newPromotion = new Promotion();
                $newPromotion->setPromotion($promotionName);

                $center->getPromotion()->add($newPromotion);
                $newPromotion->setCenter($center);
                $this->entityManager->persist($newPromotion);
            } else {
                $newPromotion = $existingPromotion;
            }

            $userRepository = $this->entityManager->getRepository(Users::class);
            $user = $userRepository->findOneBy(['Login' => $formData['Login']]);

            if ($user) {
                $responseData = ['success' => false, 'message' => "Nom d'utilisateur existant"];
                $response->getBody()->write(json_encode($responseData));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
            }

            // Création d'une nouvelle entité Users
            $user = new Users();
            $user->setFirstName($formData['First_Name']);
            $user->setLastName($formData['Last_Name']);
            $user->setLogin($formData['Login']);
            $user->setTypeUsers($formData['hidden_Type']);
            $password = $formData['Password'];
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $user->setPassword($hashedPassword);
            $user->setDel($formData['Del']);
            $user->setPromotion($newPromotion);
            $this->entityManager->persist($user);

            $this->entityManager->flush();

            // Retournez une réponse avec un message indiquant que le compte a été créé avec succès
            $responseData = ['success' => true, 'message' => 'Succes, Redirection en cours...'];
            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);

        }catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Une erreur est survenue, veuillez reessayer.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
