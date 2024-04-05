<?php

namespace App\Controller;

use App\Entity\Center;
use App\Entity\Company;
use App\Entity\Location;
use App\Entity\Promotion;
use App\Entity\Users;
use Doctrine\ORM\EntityManager;
use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RKA\Session;
use Slim\Views\Twig;

class CompanyController
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
    public function companyPage(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userLogin = $_COOKIE["user"];
        $userCookieRepository = $this->entityManager->getRepository(Users::class);
        $userCookie = $userCookieRepository->findOneBy(['Login' => $userLogin]);

        $id  = $request->getAttribute('id');
        $nom = $request->getAttribute('name');

        $companyRepository = $this->entityManager->getRepository(Company::class);
        $company = $companyRepository->findOneBy([
            'Id_Company' => $id,
            'Name_Company' => $nom,
        ]);

        $Offers = [];
        if ($company) {
            foreach ($company->getOffer() as $offer) {
                if (!$offer->isDel()) {
                    $Offers[] = $offer;
                }

            }
        }

        $adresses = [];
        if ($company) {
            foreach ($company->getLocation() as $location) {
                if (!$location->isDel()) {
                    $adresses[] = $location;
                }

            }
        }
        $evaluations = $company->getEvaluations();

        $totalRating = 0;
        foreach ($evaluations as $evaluation) {
            $totalRating += $evaluation->getEvaluationRate();
        }
        $averageRating = count($evaluations) > 0 ? $totalRating / count($evaluations) : 0;

        return $this->twig->render($response, 'CompanyPage.twig', [

            'company' => $company,
            'adresses' => $adresses,
            'averageRating' => $averageRating, // Passer la moyenne des notes au template
            'userTypeCookie' => $userCookie ? $userCookie->getTypeUsers() : null,
            'userIdCookie' => $userCookie ? $userCookie->getIdUsers() : null,
        ]);
    }

    public function editCompany(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userLogin = $_COOKIE["user"];
        $userCookieRepository = $this->entityManager->getRepository(Users::class);
        $userCookie = $userCookieRepository->findOneBy(['Login' => $userLogin]);

        $id  = $request->getAttribute('id');
        $nom = $request->getAttribute('name');

        $companyRepository = $this->entityManager->getRepository(Company::class);
        $company = $companyRepository->findOneBy([
            'Id_Company' => $id,
            'Name_Company' => $nom,
        ]);

        $adresses = [];
        if ($company) {
            foreach ($company->getLocation() as $location) {
                if (!$location->isDel()) {
                    $adresses[] = $location;
                }
            }
        }
        return $this->twig->render($response, 'EditCompany.twig', [
            'company' => $company,
            'adresses' => $adresses,
            'userTypeCookie' => $userCookie ? $userCookie->getTypeUsers() : null,
            'userIdCookie' => $userCookie ? $userCookie->getIdUsers() : null,
        ]);
    }
    public function submitEditCompany(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $id  = $request->getAttribute('id');
        try {
            $formData = $request->getParsedBody();

            $requiredFields = ['Name_Company', 'Business_Sector'];
            foreach ($requiredFields as $field) {
                if (empty($formData[$field])) {
                    $response->getBody()->write(json_encode(['success' => false, 'message' => "Le champ '{$field}' est requis."]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
            }

            $companyRepository = $this->entityManager->getRepository(Company::class);
            $company = $companyRepository->find($id);
            if (!$company) {
                $response->getBody()->write(json_encode(['success' => false, 'message' => "Entreprise non trouvée"]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            //Supprimer l'entreprise
            if (isset($formData['DeleteCompany']) && $formData['DeleteCompany'] === 'yes') {
                $company->setDel(1);
                $this->entityManager->flush();

                $response->getBody()->write(json_encode(['success' => true, 'message' => 'Entreprise supprimé avec succès, redirection en cours...']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }

            //Supprimer une Adresse
            if (isset($formData['DeleteLocation']) && $formData['DeleteLocation'] === 'yes') {
                $locationId = $formData['locationId'];
                $locationRepository = $this->entityManager->getRepository(Location::class);
                $location = $locationRepository->find($locationId);

                if ($location) {
                    $location->setDel(1);
                    $this->entityManager->flush();

                    $response->getBody()->write(json_encode(['success' => true, 'message' => "Adresse supprimée avec succès"]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
                } else {
                    $response->getBody()->write(json_encode(['success' => false, 'message' => "Adresse non trouvée"]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
                }
            }

            $company->setNameCompany($formData['Name_Company']);
            $company->setBusinessSector($formData['Business_Sector']);
            $this->entityManager->persist($company);

            $directory = __DIR__ . '/../../public/Bootstrap/images/LogoEntreprise';
            $uploadedFiles = $request->getUploadedFiles();

            //Nouveau logo
            if (isset($uploadedFiles['companyLogo'])) {
                $logo = $uploadedFiles['companyLogo'];

                if ($logo->getError() === UPLOAD_ERR_OK) {
                    $extension = pathinfo($logo->getClientFilename(), PATHINFO_EXTENSION);
                    if (!in_array($extension, ['jpg', 'png', 'svg'])) {
                        $responseData = ['success' => false, 'message' => 'Format de fichier non supporte'];
                        $response->getBody()->write(json_encode($responseData));
                        return $response->withHeader('Content-Type', 'application/json')->withStatus(415);
                    }
                        $basename = $company->getNameCompany();
                        $random= bin2hex(random_bytes(8));
                        $filename = sprintf('%s_%s.%s', $basename, $random, $extension);

                        $logo->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

                        $company->setLogoPath('/Bootstrap/Images/LogoEntreprise/' . $filename);
                }
            }

            //Adresses supplémentaires
            if (isset($request->getParsedBody()['additionalAddresses']) && is_array($request->getParsedBody()['additionalAddresses'])) {
                foreach ($request->getParsedBody()['additionalAddresses'] as $addressData) {
                    if (isset($addressData['StreetNumber'], $addressData['StreetName'], $addressData['City'], $addressData['Zipcode'])) {
                        $location = new Location();
                        $location->setNumStreet($addressData['StreetNumber']);
                        $location->setNameStreet($addressData['StreetName']);
                        $location->setNameCity($addressData['City']);
                        $location->setZipcode($addressData['Zipcode']);
                        $location->setAddAdr(isset($addressData['AddLocation']) ? $addressData['AddLocation'] : false);
                        $location->setDel($addressData['DelLocation']);
                        $location->setCompany($company);

                        $this->entityManager->persist($location);
                    }
                }
            }

            $this->entityManager->flush();

            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Succès, Redirection en cours...']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de la mise à jour, veuillez réessayer.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function newcompanyLog(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userLogin = $_COOKIE["user"];
        $userCookieRepository = $this->entityManager->getRepository(Users::class);
        $userCookie = $userCookieRepository->findOneBy(['Login' => $userLogin]);

        return $this->twig->render($response, 'CreateCompany.twig',[
            'userTypeCookie' => $userCookie ? $userCookie->getTypeUsers() : null,
            'userIdCookie' => $userCookie ? $userCookie->getIdUsers() : null,
        ]);
    }

    public function createCompany(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            // Récupération et validation des données du formulaire
            $formData = $request->getParsedBody();

            $requiredFields = ['Name_Company', 'Sector', 'StreetNumber', 'StreetName', 'City', 'Zipcode'];
            foreach ($requiredFields as $field) {
                if (empty($formData[$field])) {
                    $response->getBody()->write(json_encode(['success' => false, 'message' => "Le champ '{$field}' est requis."]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
            }


            $companyRepository = $this->entityManager->getRepository(Company::class);
            $company = $companyRepository->findOneBy(['Name_Company' => $formData['Name_Company']]);

            if ($company) {
                // L'entreprise existe déjà, on renvoie un message d'erreur
                $responseData = ['success' => false, 'message' => 'Entreprise existante'];
                $response->getBody()->write(json_encode($responseData));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
            }

            // Création de la nouvelle entreprise
            $company = new Company();
            $company->setNameCompany($formData['Name_Company']);
            $company->setBusinessSector($formData['Sector']);
            $company->setDel($formData['DelCompany']);
            $this->entityManager->persist($company);

            // Création de l'emplacement associé à l'entreprise
            $location = new Location();
            $location->setNumStreet($formData['StreetNumber']);
            $location->setNameStreet($formData['StreetName']);
            $location->setNameCity($formData['City']);
            $location->setZipcode($formData['Zipcode']);
            $location->setAddAdr(isset($formData['AddLocation']) ? $formData['AddLocation'] : false);
            $location->setDel($formData['DelLocation']);

            // Associer l'emplacement à l'entreprise
            $location->setCompany($company);
            $this->entityManager->persist($location);

            if (isset($request->getParsedBody()['additionalAddresses']) && is_array($request->getParsedBody()['additionalAddresses'])) {
                foreach ($request->getParsedBody()['additionalAddresses'] as $addressData) {
                    if (isset($addressData['StreetNumber'], $addressData['StreetName'], $addressData['City'], $addressData['Zipcode'])) {
                        $location = new Location();
                        $location->setNumStreet($addressData['StreetNumber']);
                        $location->setNameStreet($addressData['StreetName']);
                        $location->setNameCity($addressData['City']);
                        $location->setZipcode($addressData['Zipcode']);
                        $location->setAddAdr(isset($addressData['AddLocation']) ? $addressData['AddLocation'] : false);
                        $location->setDel($addressData['DelLocation']);
                        $location->setCompany($company);

                        $this->entityManager->persist($location);
                    }
                }
            }

            $directory = __DIR__ . '/../../public/Bootstrap/images/LogoEntreprise';
            $uploadedFiles = $request->getUploadedFiles();
            $logo = $uploadedFiles['companyLogo'];

            if ($logo->getError() === UPLOAD_ERR_OK) {
                $extension = pathinfo($logo->getClientFilename(), PATHINFO_EXTENSION);
                if (!in_array($extension, ['jpg', 'png', 'svg'])) {
                    $responseData = ['success' => false, 'message' => 'Format de fichier non supporte'];
                    $response->getBody()->write(json_encode($responseData));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(415);
                }

                $basename = $company->getNameCompany(); // Générer un nom de fichier aléatoire pour éviter tout conflit
                $filename = sprintf('%s.%s', $basename, $extension);

                $logo->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

                // Enregistrez le chemin du fichier dans votre base de données
                $company->setLogoPath('/Bootstrap/Images/LogoEntreprise/' . $filename);
            }

            $this->entityManager->flush();

            $responseData = ['success' => true, 'message' => 'Succes, Redirection en cours...'];
            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);

        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Une erreur est survenue pendant la création, veuillez réessayer.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}