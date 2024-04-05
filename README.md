# Plateforme de Recherche de Stages

## À propos du projet

Cette plateforme web est conçue pour simplifier et optimiser la recherche de stages pour les étudiants. En intégrant des offres provenant de diverses entreprises et en permettant une recherche filtrée par compétences et préférences, le site vise à rendre la correspondance entre les étudiants et les entreprises aussi fluide que possible. Le projet est construit avec PHP et le framework Slim 4, en utilisant Doctrine 2 pour l'interaction avec la base de données et Twig pour le templating.

## Prérequis

Pour exécuter ce projet, vous aurez besoin de :

- XAMPP (ou tout environnement local similaire avec PHP et MySQL)
- Composer pour gérer les dépendances PHP

## Installation

1. Clonez le dépôt dans votre dossier htdocs de XAMPP :
   ```bash
   git clone https://github.com/bytchady/Projet-Web-cesi-A2-2023-2034
   ```
2. Démarrez les serveurs Apache et MySQL dans XAMPP.
3. Créez une base de données via PHPMyAdmin.
4. Importez le fichier `.sql` fourni dans le dépôt git pour configurer votre base de données.
5. Installez les dépendances avec Composer :
   ```bash
   composer install
   ```
6. Configurez vos paramètres de connexion à la base de données dans le fichier de configuration Doctrine.
7. Démarrez le serveur Slim 4 :
   ```bash
   php -S localhost:8000 -t public
   ```
8. Accédez à `http://localhost:8000` dans votre navigateur.

## Structure du projet

- `public/` : Point d'entrée de l'application et ressources statiques.
- `src/` : Code source de l'application PHP.
- `templates/` : Fichiers de modèle Twig.
- `vendor/` : Bibliothèques tierces installées via Composer.

## Utilisation

Une fois connecté à la plateforme, les utilisateurs ont accès aux fonctionnalités en fonction de leur rôle :

### Administrateur
- **Gestion d'accès** : Accès complet pour authentifier les utilisateurs.
- **Gestion des entreprises** : Capacité à rechercher, créer, modifier, évaluer et supprimer des entreprises. Accès aux statistiques des entreprises.
- **Gestion des offres de stages** : Peut rechercher, créer, modifier et supprimer des offres de stage, et consulter leurs statistiques.
- **Gestion des Pilotes** : Peut rechercher et créer des comptes Pilotes.
- **Gestion des étudiants** : Peut rechercher et créer des comptes étudiants.
- **Gestion des candidatures** : Peut consulter et gérer les listes de souhaits et les candidatures aux offres de stage.

### Pilote
- **Gestion d'accès** : Capacité à authentifier les utilisateurs.
- **Gestion des entreprises** : Peut rechercher et évaluer des entreprises, avec un accès aux statistiques.
- **Gestion des offres de stages** : Accès pour rechercher des offres et consulter les statistiques.
- **Gestion des étudiants** : Peut rechercher des comptes étudiants.
- **Gestion des candidatures** : Peut consulter la wishlist des étudiants.

### Étudiant
- **Gestion d'accès** : Accès pour s'authentifier sur la plateforme.
- **Gestion des entreprises** : Capacité à rechercher et évaluer des entreprises.
- **Gestion des offres de stages** : Peut rechercher des offres et consulter les statistiques des offres.
- **Gestion des candidatures** : Peut ajouter des offres à la wish-list et postuler aux offres de stage.

Notez que certaines fonctionnalités ne sont pas disponibles pour tous les rôles. Les comptes Pilotes et Étudiants ont des droits restreints pour certaines actions administratives et de gestion, tandis que les Administrateurs ont un accès complet à toutes les fonctionnalités du site.

Lien du projet : https://github.com/bytchady/Projet-Web-cesi-A2-2023-2034
