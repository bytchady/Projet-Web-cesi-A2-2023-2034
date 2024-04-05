<?php

namespace App\Controller;


use App\Entity\Center;
use App\Entity\Company;
use App\Entity\Location;
use App\Entity\Promotion;
use App\Entity\Users;
use App\Entity\Evaluation;
use App\Entity\Offer;
use App\Entity\Skill;
use Doctrine\ORM\EntityManager;
use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RKA\Session;
use Slim\Views\Twig;

class StatController
{
    private  $entityManager;
    private Twig $twig;
    private Session $session;

    public function __construct(ContainerInterface $container){

        $this->entityManager = $container->get(EntityManager::class);
        $this->twig = $container->get('view');
        $this->session = new Session();
    }


public function statCompany(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userLogin = $_COOKIE["user"];
        $userCookieRepository = $this->entityManager->getRepository(Users::class);
        $userCookie = $userCookieRepository->findOneBy(['Login' => $userLogin]);

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('c.Business_Sector as secteurActivite', 'COUNT(c.Id_Company) as nombreEntreprises')
            ->from('App\Entity\Company', 'c')
            ->Where('c.Del = :del')
            ->setParameter("del", 0)
            ->groupBy('c.Business_Sector');

        $results = $queryBuilder->getQuery()->getResult();

        $labels = [];
        $values = [];
        foreach ($results as $result) {
            $labels[] = $result['secteurActivite'];
            $values[] = $result['nombreEntreprises'];
        }


        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('l.Name_City as location', 'COUNT(c.Id_Company) as nombreEntreprises')
            ->from('App\Entity\Company', 'c')
            ->join('c.Location', 'l')
            ->Where('l.Del = :del')
            ->setParameter("del", 0)
            ->groupBy('l.Name_City');

        $results = $queryBuilder->getQuery()->getResult();

        $labels1 = [];
        $values1 = [];
        foreach ($results as $result) {
            $labels1[] = $result['location'];
            $values1[] = $result['nombreEntreprises'];
        }




    // Passer les données extraites à la vue Twig
        return $this->twig->render($response, 'StatCompany.twig', [
            'labels' => json_encode($labels),
            'values' => json_encode($values),
            'labels1' => json_encode($labels1),
            'values1' => json_encode($values1),
            'userTypeCookie' => $userCookie ? $userCookie->getTypeUsers() : null,
            'userIdCookie' => $userCookie ? $userCookie->getIdUsers() : null,
        ]);
    }


    public  function statOffer (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userLogin = $_COOKIE["user"];
        $userCookieRepository = $this->entityManager->getRepository(Users::class);
        $userCookie = $userCookieRepository->findOneBy(['Login' => $userLogin]);

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('s.Name_Skill as competence', 'COUNT(o.Id_Offer) as nombreOffres')
            ->from('App\Entity\Offer', 'o')
            ->innerJoin('o.Offer_Skill', 's') // Corrected the join condition
            ->where('o.Del = :del')
            ->setParameter("del", 0)
            ->groupBy('s.Name_Skill'); // Grouping by skill name

        $results = $queryBuilder->getQuery()->getResult();

        $labels1 = [];
        $values1 = [];
        foreach ($results as $result) {
            $labels1[] = $result['competence'];
            $values1[] = $result['nombreOffres'];
        }









        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('l.Name_City as city', 'COUNT(o.Id_Offer) as nombreOffres')
            ->from('App\Entity\Offer', 'o')
            ->innerJoin('o.Location', 'l')
            ->where('o.Del = :del')
            ->setParameter("del", 0)
            ->groupBy('l.Name_City');

        $results = $queryBuilder->getQuery()->getResult();

        $labels2 = [];
        $values2 = [];
        foreach ($results as $result) {
            $labels2[] = $result['city'];
            $values2[] = $result['nombreOffres'];
        }






        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('o.Promotion as Promotion', 'COUNT(o.Id_Offer) as nombreOffres')
            ->from('App\Entity\Offer', 'o')
            ->Where('o.Del = :del')
            ->setParameter("del", 0)
            ->groupBy('o.Promotion');

        $results = $queryBuilder->getQuery()->getResult();

        $labels3 = [];
        $values3 = [];

        foreach ($results as $result) {
            $labels3[] = $result['Promotion'];
            $values3[] = $result['nombreOffres'];
        }





        return $this->twig->render($response, 'StatOffer.twig', [
            'labels3' => json_encode($labels3),
            'values3' => json_encode($values3),
            'labels2' => json_encode($labels2),
            'values2' => json_encode($values2),
            'labels1' => json_encode($labels1),
            'values1' => json_encode($values1),
            'userTypeCookie' => $userCookie ? $userCookie->getTypeUsers() : null,
            'userIdCookie' => $userCookie ? $userCookie->getIdUsers() : null,
        ]);
    }


}