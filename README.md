# Déploiement de l'application Cinephoria

## Introduction

Ce projet utilise **Docker** pour créer un environnement de développement local. Ce guide explique les étapes pour déployer et tester l'application **Cinephoria** qui utilise les services suivants :

- **Backend** : Une API Symfony (PHP).
- **Frontend** : Une interface utilisateur React.
- **Base de données** : MySQL pour stocker les données.
- **Nginx** : Un serveur web pour servir les fichiers frontaux.

Les services définis dans le fichier `docker-compose.yml` :

- **symfony-backend** : Le backend Symfony.
- **react-frontend** : Le frontend React.
- **mysql-db** : La base de données MySQL.
- **symfony-nginx** : Le serveur web Nginx qui sert le frontend et fait le proxy vers le backend.

## Prérequis

Avant de commencer, assurez-vous d'avoir installé les outils suivants sur votre machine locale :

- **Docker** : pour la gestion des conteneurs.
- **Docker Compose** : pour faciliter la gestion de plusieurs conteneurs.

## Structure du projet

Voici la structure du projet :

├── backend/ ├── frontend/ ├── nginx/ ├── node_modules/ ├── vendor/ ├── .gitignore ├── composer.json ├── composer.lock ├── config.inc.php ├── docker-compose.yml ├── mysql-backup.tar.gz ├── package.json ├── package-lock.json └── README.md

- `backend/` : Contient le code source du backend basé sur Symfony.
- `frontend/` : Contient le code source du frontend basé sur React.
- `nginx/` : Contient la configuration du serveur Nginx.
- `docker-compose.yml` : Le fichier principal pour configurer et démarrer tous les services.
- `mysql-backup.tar.gz` : Sauvegarde de la base de données MySQL.

# Étapes pour déployer en local

## 1. Cloner le repository

Commande Bash : git clone https://votre-repository.git
cd cinephoria

## 2. Construire les conteneurs Docker

Lancez Docker Compose pour construire et démarrer les services :
- **docker-compose up --build** 

Cela construira et démarrera les services définis dans le fichier docker-compose.yml.

## 3. Accéder à l'application

Une fois les conteneurs executer , vous pouvez accéder à l'application via le serveur local : http://localhost:8000/
port nginx définis dans le fichier docker-compose.yml. 

## 4. Importer la base de données

Si vous avez une sauvegarde de la base de données MySQL, vous pouvez l'importer dans le conteneur MySQL :
Commande Bash : docker exec -i cinephoria_mysql-db_1 mysql -u root -p cinephoria < /path/to/mysql-backup.tar.gz
Cela permettra de restaurer la base de données à partir du fichier de sauvegarde.

## 5. Tester l'application

Une fois l'application déployée et les services en cours d'exécution, vous pouvez tester les fonctionnalités : http://localhost:8000/

Vous pouvez également exécuter les tests fonctionnels et unitaires via PHPUnit pour le backend :
- **./vendor/bin/phpunit**  


## 6. Docker Dashboard

Vous pouvez également suivre l'état de vos conteneurs en utilisant le tableau de bord Docker. Voici un exemple de tableau de bord avec les conteneurs en cours d'exécution :
Le conteneur Cinephoria contient tous les services liés à l'application, y compris symfony-backend, symfony-nginx, react-frontend et mysql-db.

###  Configuration
Configuration du Backend Symfony
Les configurations spécifiques pour Symfony se trouvent dans le dossier backend. Vous pouvez ajuster la configuration de la base de données et autres services dans le fichier config.inc.php.

###  Configuration de Nginx
Le serveur Nginx est configuré pour servir le frontend React et interagir avec le backend Symfony. Vous pouvez personnaliser la configuration dans le dossier nginx.

### Logs
Les logs des services Docker sont stockés dans le répertoire des logs de Docker. Pour voir les logs d'un conteneur particulier, utilisez la commande suivante :
- **docker logs [nom_du_conteneur]**  

