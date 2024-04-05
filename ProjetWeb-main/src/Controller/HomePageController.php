<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Users;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RKA\Session;
use Slim\Views\Twig;

class HomePageController
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
    public function homePage(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userLogin = $_COOKIE["user"];
        $userCookieRepository = $this->entityManager->getRepository(Users::class);
        $userCookie = $userCookieRepository->findOneBy(['Login' => $userLogin]);

        $params = $request->getQueryParams();
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $limit = 6;
        $offset = ($page - 1) * $limit;

        $companyRepository = $this->entityManager->getRepository(Company::class);

        $totalCompanies =  $companyRepository->createQueryBuilder('c')
            ->select('count(c.Id_Company)')
            ->where('c.Del = :del')
            ->setParameter('del', 0)
            ->getQuery()
            ->getSingleScalarResult();

        $companies = $companyRepository->createQueryBuilder('c')
            ->where('c.Del = :del')
            ->setParameter('del', 0)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $companieArray = array_map(function ($companies) {
            return [
                'id' =>$companies->getIdCompany(),
                'nom' => $companies->getNameCompany(),
                'logoPath' => $companies->getLogoPath(),
            ];
        }, $companies);
        $totalPages = ceil($totalCompanies / $limit);

        return $this->twig->render($response, 'Homepage.twig', [
            'entreprises' => $companieArray,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'userTypeCookie' => $userCookie ? $userCookie->getTypeUsers() : null,
            'userIdCookie' => $userCookie ? $userCookie->getIdUsers() : null,
        ]);
    }
    public function findcompanyFooter(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->twig->render($response, 'FindOffer.twig');
    }
    public function aboutfindmeFooter(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->twig->render($response, 'AboutFindMe.twig');
    }
    public function privacypolicyFooter(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->twig->render($response, 'PrivacyPolicy.twig');
    }
}