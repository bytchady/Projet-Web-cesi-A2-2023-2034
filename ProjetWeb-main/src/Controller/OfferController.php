<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Location;
use App\Entity\Offer;
use App\Entity\Skill;
use App\Entity\Users;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface ;
use Psr\Http\Message\ServerRequestInterface ;
use DateTime;
use RKA\Session;
use Slim\Views\Twig;


class OfferController
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
    public function findOffer(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userLogin = $_COOKIE["user"];
        $userCookieRepository = $this->entityManager->getRepository(Users::class);
        $userCookie = $userCookieRepository->findOneBy(['Login' => $userLogin]);

        $params = $request->getQueryParams();
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $limit = 5; // Offers per page
        $offset = ($page - 1) * $limit;

        $offerRepository = $this->entityManager->getRepository(Offer::class);

        $totalOffers = $offerRepository->createQueryBuilder('o')
            ->select('count(o.Id_Offer)')
            ->where('o.Del = :del')
            ->setParameter('del', 0)
            ->getQuery()
            ->getSingleScalarResult();

        $offers = $offerRepository->createQueryBuilder('o')
            ->join('o.Company', 'company')
            ->join('o.Location', 'location')
            ->addSelect('company', 'location') // Ajoutez cette ligne pour inclure les données de l'entreprise et de la localisation dans le résultat
            ->where('o.Del = :del')
            ->setParameter('del', 0)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $offerArray = array_map(function ($offer) {
            $company = $offer->getCompany();
            $location = $offer->getLocation();
            $skills = $offer->getOfferSkill()->map(function($skill) {
                return $skill->getNameSkill();
            })->toArray();

            return [
                'id' => $offer->getIdOffer(),
                'description' => $offer->getDescription(),
                'promotion' => $offer->getPromotion(),
                'duration' => $offer->getDuration(),
                'remuneration' => $offer->getRemuneration(),
                'date' => $offer->getDateOffer()->format('d-m-Y'),
                'place' => $offer->getPlaces(),
                'skills' => $skills,
                'companyName' => $company ? $company->getNameCompany() : 'Non spécifié',
                'companyLogo' => $company ? $company->getLogoPath() : '/Bootstrap/images/LogoEntreprise/Logo-par-defaut.jpg',
                'fullAddress' => $location ? $location->getFullAddress() : 'Non spécifié',

            ];
        }, $offers);
        $totalPages = ceil($totalOffers / $limit);

        return $this->twig->render($response, 'FindOffer.twig',[
            'offres' => $offerArray,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'userTypeCookie' => $userCookie ? $userCookie->getTypeUsers() : null,
            'userIdCookie' => $userCookie ? $userCookie->getIdUsers() : null,
        ]);
    }

    public function newOffer(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userLogin = $_COOKIE["user"];
        $userCookieRepository = $this->entityManager->getRepository(Users::class);
        $userCookie = $userCookieRepository->findOneBy(['Login' => $userLogin]);

        $companyRepository = $this->entityManager->getRepository(Company::class);
        $companies = $companyRepository->findAll();

        // Passez les entreprises et leurs adresses à la vue Twig
        return $this->twig->render($response, 'CreateOffer.twig', [
            'companies' => $companies,
            'userTypeCookie' => $userCookie ? $userCookie->getTypeUsers() : null,
            'userIdCookie' => $userCookie ? $userCookie->getIdUsers() : null,        ]);
    }
    public function getCompanyAddresses(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $companyId = $request->getAttribute('companyId');
        $locationRepository = $this->entityManager->getRepository(Location::class);
        $addresses = $locationRepository->findBy(['Company' => $companyId]);

        $addressesArray = [];
        foreach ($addresses as $address) {
            $addressesArray[] = [
                'idLocation' => $address->getIdLocation(),
                'fullAddress' => "{$address->getNumStreet()} {$address->getNameStreet()}, {$address->getZipcode()} {$address->getNameCity()}, {$address->getAddAdr()}",
            ];
        }
        $response->getBody()->write(json_encode(['addresses' => $addressesArray]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function createOffer(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $requiredFields = ['Entreprise','Adresse','Promotion', 'Durée', 'Date', 'Description', 'Remuneration',  'Place'];
            foreach ($requiredFields as $field) {
                if (empty($formData[$field])) {
                    $response->getBody()->write(json_encode(['success' => false, 'message' => "Le champ '{$field}' est requis."]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
            }

            //Création de l'offre
            $offer = new Offer();
            $offer->setDescription($formData['Description']);
            $offer->setPromotion($formData['Promotion']);
            $offer->setDuration($formData['Durée']);
            $offer->setRemuneration($formData['Remuneration']);
            $startDate = new DateTime($formData['Date']);
            $offer->setDateOffer($startDate);
            $offer->setPlaces($formData['Place']);
            $offer->setDel(0);

            // Récupérer et assigner l'entreprise et la localité
            $companyRepository = $this->entityManager->getRepository(Company::class);
            $company = $companyRepository->find($formData['Entreprise']);
            $offer->setCompany($company);

            $locationRepository = $this->entityManager->getRepository(Location::class);
            $location = $locationRepository->find($formData['Adresse']);
            $offer->setLocation($location);

            // Enregistrement de l'offre dans la base de données
            $this->entityManager->persist($offer);
            $this->entityManager->flush();

            $skillsData = $formData['skills'] ?? [];
            foreach ($skillsData as $skillName) {
                $skillName = trim($skillName);
                if (empty($skillName)) {
                    continue; // Sauter les noms de compétences vides
                }

                // Vérifiez si la compétence existe déjà
                $skill = $this->entityManager->getRepository(Skill::class)->findOneBy(['Name_Skill' => $skillName]);
                if (!$skill) {
                    // Si elle n'existe pas, créez-la
                    $skill = new Skill();
                    $skill->setNameSkill($skillName);
                    $this->entityManager->persist($skill);
                }

                // Ajoutez la compétence à l'offre
                $offer->addSkill($skill);
            }

            // Persistez l'offre avec ses compétences liées
            $this->entityManager->persist($offer);
            $this->entityManager->flush();

            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Succès, Redirection en cours...']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Une erreur est survenue, veuillez réessayer.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
    public function editOffer(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userLogin = $_COOKIE["user"];
        $userCookieRepository = $this->entityManager->getRepository(Users::class);
        $userCookie = $userCookieRepository->findOneBy(['Login' => $userLogin]);

        $offerId  = $request->getAttribute('offerId');
        $offerRepository = $this->entityManager->getRepository(Offer::class);
        $offer = $offerRepository->findOneBy([
            'Id_Offer'=> $offerId,

        ]);

        $companyName = $offer->getCompany() ? $offer->getCompany()->getNameCompany() : null;
        $location = $offer->getLocation();

        $skills = [];
        if ($offer) {
            foreach ($offer->getOfferSkill() as $skill) {
                $skills[] = $skill;
            }
        }


        // Passez les entreprises et leurs adresses à la vue Twig
        return $this->twig->render($response, 'EditOffer.twig', [
            'offerId' => $offerId,
            'promotion' => $offer->getPromotion(),
            'duration' => $offer->getDuration(),
            'remuneration' => $offer->getRemuneration(),
            'date' => $offer->getDateOffer()->format('Y-m-d'),
            'places' => $offer->getPlaces(),
            'description' => $offer->getDescription(),
            'company' => $companyName,
            'address' => $location ? $location->getFullAddress() : 'Non spécifié',
            'skills' => $skills,
            'userTypeCookie' => $userCookie ? $userCookie->getTypeUsers() : null,
            'userIdCookie' => $userCookie ? $userCookie->getIdUsers() : null,
        ]);
    }

    public function submitEditOffer(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $offerId  = $request->getAttribute('offerId');
        try {
            $formData = $request->getParsedBody();

            $requiredFields = ['Description', 'Promotion', 'Durée', 'Date', 'Remuneration', 'Place'];
            foreach ($requiredFields as $field) {
                if (empty($formData[$field])) {
                    $response->getBody()->write(json_encode(['success' => false, 'message' => "Le champ '{$field}' est requis."]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
            }

            $offerRepository = $this->entityManager->getRepository(Offer::class);
            $offer = $offerRepository->find($offerId);
            if (!$offer) {
                $response->getBody()->write(json_encode(['success' => false, 'message' => "Offre non trouvée"]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            //Supprimer l'offre
            if (isset($formData['DeleteOffer']) && $formData['DeleteOffer'] === 'yes') {
                $offer->setDel(1);
                $this->entityManager->flush();

                $response->getBody()->write(json_encode(['success' => true, 'message' => 'Offre supprimé avec succès, redirection en cours...']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }

            //Supprimer une compétence
            if (isset($formData['DeleteSkill']) && $formData['DeleteSkill'] === 'yes') {
                $skillId  = $formData['SkillId'];
                $skillRepository = $this->entityManager->getRepository(Skill::class);
                $skill = $skillRepository->find($skillId);

                if ($skill && $offer) {
                    // Supprimez la compétence de l'offre
                    $offer->removeSkill($skill);
                    $this->entityManager->flush();

                    $response->getBody()->write(json_encode(['success' => true, 'message' => 'Compétence supprimé avec succès']));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
                }

                $response->getBody()->write(json_encode(['success' => true, 'message' => 'Entreprise supprimé avec succès, redirection en cours...']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }

            //Compétences supplémentaires
            if (isset($request->getParsedBody()['additionalSkill']) && is_array($request->getParsedBody()['additionalSkill'])) {
                foreach ($request->getParsedBody()['additionalSkill'] as $addSkill) {
                    if (isset($addSkill['SkillName'])) {
                        $skillName = trim($addSkill['SkillName']);
                        if (empty($skillName)) {
                            continue;
                        }
                        $skill = $this->entityManager->getRepository(Skill::class)->findOneBy(['Name_Skill' => $skillName]);
                        if (!$skill) {
                            $skill = new Skill();
                            $skill->setNameSkill($skillName);
                            $this->entityManager->persist($skill);
                        }
                        $offer->addSkill($skill);
                    }
                }
            }
            $offer->setPromotion($formData['Promotion']);
            $offer->setDuration($formData['Durée']);
            $offer->setRemuneration($formData['Remuneration']);
            $date= new \DateTime($formData['Date']);
            $offer->setDateOffer($date);
            $offer->setPlaces($formData['Place']);
            $this->entityManager->persist($offer);
            $this->entityManager->flush();

            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Succès, Redirection en cours...']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de la mise à jour, veuillez réessayer.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
    public function applyOffer(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $formData = $request->getParsedBody();

            $offerId = $request->getAttribute('offerId');
            $offerRepository = $this->entityManager->getRepository(Offer::class);
            $offer = $offerRepository->find($offerId);
            if (!$offer) {
                $response->getBody()->write(json_encode(['success' => false, 'message' => 'Offre non trouvée.']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $requiredFields = ['Nom','Prenom','Login'];
            foreach ($requiredFields as $field) {
                if (empty($formData[$field])) {
                    $response->getBody()->write(json_encode(['success' => false, 'message' => "Le champ '{$field}' est requis."]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
            }

            $userRepository = $this->entityManager->getRepository(Users::class);
            $user = $userRepository->findOneBy([
                'First_Name' => $formData['Nom'],
                'Last_Name' => $formData['Prenom'],
                'Login' => $formData['Login']
            ]);
            if (!$user) {
                $response->getBody()->write(json_encode(['success' => false, 'message' => 'Utilisateur non trouvé.']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }


//            $directoryCV = __DIR__ . '/../../public/Bootstrap/CV';
//            $directoryMotivation = __DIR__ . '/../../public/Bootstrap/Motivation';
//            $uploadedFiles = $request->getUploadedFiles();
//            $cv = $uploadedFiles['CV'];
//            $motivation = $uploadedFiles['Motivation'];
//
//            if ($cv->getError() === UPLOAD_ERR_OK) {
//                $extension = pathinfo($cv->getClientFilename(), PATHINFO_EXTENSION);
//                if (!in_array($extension, ['pdf'])) {
//                    $responseData = ['success' => false, 'message' => 'Format de fichier non supporte'];
//                    $response->getBody()->write(json_encode($responseData));
//                    return $response->withHeader('Content-Type', 'application/json')->withStatus(415);
//                }
//
//                $firstName = $user->getFirstName();
//                $lastName = $user->getLastName();
//                $filename = sprintf('%s_%s_%s.%s', $firstName,$lastName,"CV", $extension);
//
//                $cv->moveTo($directoryCV . DIRECTORY_SEPARATOR . $filename);
//                $user->setCVPath('/Bootstrap/CV/' . $filename);
//            }
//            if ($motivation->getError() === UPLOAD_ERR_OK) {
//                $extension = pathinfo($motivation->getClientFilename(), PATHINFO_EXTENSION);
//                if (!in_array($extension, ['pdf'])) {
//                    $responseData = ['success' => false, 'message' => 'Format de fichier non supporte'];
//                    $response->getBody()->write(json_encode($responseData));
//                    return $response->withHeader('Content-Type', 'application/json')->withStatus(415);
//                }
//
//                $firstName = $user->getFirstName();
//                $lastName = $user->getLastName();
//                $filename = sprintf('%s_%s_%s.%s', $firstName,$lastName,"Motivation", $extension);
//
//                $motivation->moveTo($directoryMotivation . DIRECTORY_SEPARATOR . $filename);
//                $user->setMotivationPath('/Bootstrap/Motivation/' . $filename);
//            }


            $offer->addStudent($user);
            $user->addOffer($offer);

            // Persistez les modifications
            $this->entityManager->persist($offer);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Succès de l\' opération']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Une erreur est survenue, veuillez réessayer.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}






