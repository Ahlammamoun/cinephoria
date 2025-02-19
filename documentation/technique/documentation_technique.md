# Documentation Technique de l'Application Cinéphoria

## 1. Architecture logicielle de l' application

### 1.1 Explication du choix des technologies ainsi que du fonctionnement global

L'architecture de **Cinéphoria** repose sur une approche **client-serveur**, avec un frontend léger et un backend robuste. Le système est conçu pour gérer une grande quantité de données et d'utilisateurs, tout en offrant une expérience utilisateur fluide et rapide.

#### **Backend** :
- **Langage** : **PHP** a été choisi pour sa fiabilité, sa flexibilité et sa forte communauté de développeurs.
- **Framework** : **Symfony**, un framework PHP très populaire, a été utilisé pour sa capacité à construire des API performantes et bien structurées, permettant de gérer les interactions avec la base de données et la logique métier.
- **Base de données** :
  - **MySQL/PostgreSQL** : Bases de données relationnelles utilisées pour stocker les informations critiques telles que les films, réservations et utilisateurs.
  - **MongoDB** : Utilisé pour les données non structurées et les statistiques sur l'utilisation des films.
  
#### **Frontend** :
- **React.js** : Framework JavaScript moderne pour la création de composants réactifs et dynamiques.
- **Flutter** : Utilisé pour développer une application mobile multiplateforme permettant aux utilisateurs d'interagir facilement avec l'application via des appareils mobiles.

#### **Architecture générale** :
- **Frontend** : Application React.js pour la gestion de l'interface web et Flutter pour l'interface mobile.
- **Backend** : API Symfony avec gestion de la logique métier et interaction avec la base de données.
- **Serveur web** : **Nginx** en tant que reverse proxy, pour gérer les requêtes du frontend et les API backend.

---

## 2. Réflexions initiales technologiques sur le sujet

Lors du choix des technologies pour ce projet, plusieurs critères ont été pris en compte :
- **Scalabilité** : Les technologies choisies permettent d'assurer une évolution horizontale pour gérer un grand nombre d'utilisateurs simultanés et d'éviter des problèmes de performance.
- **Performance** : Symfony et React.js sont des technologies réputées pour leurs performances, en particulier pour les applications avec un grand volume de requêtes.
- **Communauté et Support** : Symfony, React.js et MongoDB sont largement adoptés par les développeurs et bénéficient d'une grande documentation et de nombreuses ressources pour accélérer le développement.
- **Flexibilité** : L’utilisation de bases de données relationnelles et NoSQL permet d'adapter le stockage des données en fonction des besoins spécifiques du projet.

---

## 3. Configuration de votre environnement de travail

### 3.1 Outils utilisés
- **IDE** : Visual Studio Code pour le développement, avec des extensions adaptées à PHP, JavaScript et Docker.
- **Contrôle de version** : **Git** pour la gestion de versions, avec des repositories hébergés sur **GitHub**.
- **Outils de test** : PHPUnit pour le test du backend (Symfony), Jest pour tester les composants React et Flutter.
- **Conteneurisation** : **Docker** a été utilisé pour la conteneurisation des services (base de données, backend, frontend, Nginx).

### 3.2 Environnement de développement
- **Backend** : PHP, Symfony, MySQL/PostgreSQL, MongoDB.
- **Frontend** : React.js, Node.js.
- **Base de données** : MySQL/PostgreSQL pour les données structurées, MongoDB pour les statistiques.
- **Application mobile** : Flutter pour les versions Android et iOS.

### 3.3 Serveur de déploiement
- **Nginx** : Utilisé comme reverse proxy pour gérer les requêtes vers le frontend et le backend.
- **Docker** : Conteneurisation des services pour un environnement de développement production homogène et flexible.

---

## 4. Modèle conceptuel de données (MCD)

Le modèle conceptuel de données est une représentation des entités de l’application et des relations entre elles.

- **Film** :
  - Propriétés : `title`, `description`, `releaseDate`, `minimumAge`, `note`, `isFavorite`.
  - Relations : Many-to-many avec **Genre**, Many-to-many avec **Qualite**, One-to-many avec **Seance**.
  
- **Genre** :
  - Propriétés : `name`.
  - Relations : Many-to-many avec **Film**.
  
- **Incident** :
  - Propriétés : `description`, `dateSignalement`, `resolu`.
  - Relations : Many-to-one avec **Salle**.
  
- **Qualite** :
  - Propriétés : `name`.
  - Relations : Many-to-many avec **Film**, One-to-many avec **Salle**.

- **Reservation** :
  - Propriétés : `nombreSieges`, `siegesReserves`, `prixTotal`.
  - Relations : Many-to-one avec **Utilisateur**, Many-to-one avec **Seance**.

- **Salle** :
  - Propriétés : `numero`, `capaciteTotale`, `capacitePMR`.
  - Relations : Many-to-one avec **Qualite**, One-to-many avec **Seance**, One-to-many avec **Incident**.

- **Seance** :
  - Propriétés : `dateDebut`, `dateFin`.
  - Relations : Many-to-one avec **Salle**, Many-to-one avec **Film**, One-to-many avec **Reservation**.

- **Utilisateur** :
  - Propriétés : `role`, `login`, `name` , `lastname` .
  - Relations : Many-to-one avec **Reservation**.

---

## 5. Diagramme d’utilisation et diagramme de séquence

### 5.1 Diagramme d’utilisation
Le diagramme d’utilisation décrit les interactions entre les utilisateurs et le système. Par exemple :
- **Utilisateur** : L'utilisateur peut consulter les films, réserver des billets et accéder à ses billets via un QR code.
- **Administrateur** : L’administrateur peut ajouter des films, gérer les séances et consulter les réservations.

### 5.2 Diagramme de séquence
Le diagramme de séquence montre comment les différentes entités interagissent dans le système :
1. L'utilisateur se connecte et consulte les films disponibles.
2. L'utilisateur choisit un film et réserve une place.
3. L'application génère un QR code pour le billet.
4. L'administrateur peut consulter et gérer les réservations via un tableau de bord.

---

## 6. Explication de votre plan de test ainsi que de votre déploiement

### 6.1 Plan de test
Les tests incluent :
- **Tests unitaires** : Vérification du bon fonctionnement des méthodes et fonctions isolées (par exemple, validation des réservations).
- **Tests d'intégration** : Vérification du bon fonctionnement des interactions entre les différentes parties du système (par exemple, réservation d'un film via l'API).
- **Tests de performance** : Pour vérifier la capacité de l’application à supporter un grand nombre d'utilisateurs et de requêtes simultanées.
- **Tests fonctionnels** : Pour s'assurer que les fonctionnalités du frontend et du backend répondent aux exigences de l'utilisateur.

### 6.2 Déploiement
Le déploiement se fait en plusieurs étapes :
1. **Développement local** : Les développeurs travaillent dans un environnement local, en utilisant Docker pour garantir une compatibilité avec l’environnement de production.
2. **Tests automatisés** : Les tests sont effectués sur GitHub Actions à chaque commit pour assurer la qualité du code.
3. **Mise en production** : Le déploiement sur un serveur VPS est effectué avec **Nginx** comme reverse proxy pour gérer le trafic entre le frontend et le backend.

---

## 7. Explication de la démarche que vous avez eue afin de proposer un déploiement continu (CI/CD)

### 7.1 Démarche CI/CD
Le processus de déploiement continu a été mis en place en utilisant **GitHub Actions** pour automatiser le pipeline de déploiement :
1. **Commit et push** : À chaque modification du code, les commits sont poussés vers GitHub.
2. **Exécution des tests** : Avant chaque déploiement, des tests automatisés sont exécutés pour garantir la stabilité de l’application.
3. **Déploiement automatique** : Une fois que les tests sont réussis, le code est déployé automatiquement sur le serveur via Docker.

### 7.2 Conteneurisation avec Docker
Chaque composant de l'application (backend, frontend, base de données, Nginx) est conteneurisé avec **Docker**. Cela permet de garantir que l’application fonctionne de manière homogène dans les environnements de développement, de test et de production.

### 7.3 Gestion du pipeline CI/CD
Le pipeline CI/CD permet de :
- Tester le code à chaque commit.
- Assurer l'intégration continue via des build automatiques.
- Déployer l’application sur le serveur de production sans interruption.

---

Ce document présente les principales étapes techniques liées à la création, aux choix des technologies, à la mise en œuvre du plan de tests et à la configuration du déploiement continu pour l’application Cinéphoria. Vous pouvez l'adapter en fonction des spécifications réelles et de l'évolution de votre projet.
