# Gestion du Projet - Processus de Développement

## 1. Lecture du Projet et Analyse des Besoins
La première étape dans la gestion de ce projet a été une lecture approfondie du projet et de ses objectifs. J'ai commencé par analyser les **besoins du client** et les **exigences fonctionnelles** du produit final. Cela m'a permis de bien comprendre les attentes et de poser les bases de la conception.



## 2. Rédaction du Cahier des Charges
Une fois les besoins compris, j'ai rédigé un **cahier des charges** pour formaliser le projet. Le cahier des charges a détaillé les objectifs du projet cinéphoria, les spécifications techniques, les exigences fonctionnelles, les contraintes (par exemple, contraintes de temps, de budget), ainsi que les attentes .

## 3. Choix des Technologies et Outils
Sur la base des besoins et des spécifications du projet, j'ai sélectionné les technologies et les outils les plus adaptés.

### Choix des technologies :
- **Backend :** symfony
- **Frontend :**  React
- **Base de données :**  MySQL
- **API :** API

### Choix des outils de gestion :
- **Jira :** Pour la gestion du projet, j'ai utilisé **Jira** afin de suivre les tâches, organiser les sprints et faciliter le développement(même si c'est plus un outils d'équipe).
- **GitHub / GitLab :** Utilisé pour la gestion du code source, le contrôle de version, et la collaboration entre les membres de l'équipe.


## 4. Documents de Conception
Une fois les technologies et outils choisis, j'ai entamé la phase de conception, en produisant les documents nécessaires à la réalisation du projet.

- **MCD (Modèle Conceptuel de Données) :** J'ai créé un **MCD** pour structurer et représenter les données du projet, afin de définir les entités principales, leurs relations, et la manière dont elles interagiront.

- **Dictionnaire des Routes :** J'ai également préparé un dictionnaire des routes de l'application, qui liste toutes les routes du backend (API endpoints), leurs méthodes (GET, POST, PUT, DELETE), et les données attendues et retournées. Cela a permis de garantir une compréhension commune de l'architecture du projet et des différentes interactions.

## 5. Création des Maquettes et Wireframes
Avant de commencer à développer l'application, des **maquettes** et des **wireframes** ont été créés pour visualiser l'interface utilisateur (UI) et définir l'expérience utilisateur (UX).

- **Wireframes :** Des wireframes ont été réalisés pour définir la structure de base des pages sans se concentrer sur les détails visuels. Cela a permis de valider la disposition générale et les flux d'utilisateurs.
- **Maquettes :** Des maquettes plus détaillées ont été créées pour affiner le design, en incluant les couleurs, les typographies et les éléments interactifs.

## 6. Création des User Stories
En parallèle, j'ai créé des **user stories** pour décrire les fonctionnalités du point de vue de l'utilisateur final. Chaque user story a été rédigée sous la forme suivante :

- **En tant qu'utilisateur**, je veux pouvoir [fonctionnalité] afin de [bénéfice].


## 7. Début du Développement
Une fois la phase de conception terminée, le développement a pu commencer.

- **Création de la branche principale :** Une branche principale **`main`** a été créée pour la version stable du projet.
- **Branche de développement (dev) :** Une branche **`dev`** a été mise en place pour regrouper les développements de fonctionnalités avant leur intégration dans la branche principale.
- **Branche par fonctionnalité :** Pour chaque nouvelle fonctionnalité, une **branche spécifique** a été créée (par exemple, **`feature/login`**, **`feature/dashboard`**, etc.). Cela permettait de travailler sur des fonctionnalités indépendantes sans perturber la stabilité de la branche `dev`.
- **Commit et push régulier :** Des commits réguliers ont été effectués avec des messages clairs et explicites pour suivre l'évolution du code. Les changements ont été poussés sur **GitHub** pour que l'équipe puisse travailler de manière collaborative.

## 8. Intégration des Fonctionnalités dans la Branche `dev`
Une fois qu'une fonctionnalité était terminée et testée localement, elle était intégrée dans la branche **`dev`** après une revue de code. Cela permettait de s'assurer que le code était conforme aux attentes et sans conflits.

- **Pull requests (PR) :** Chaque fonctionnalité a été intégrée via des **pull requests** afin d'effectuer une revue de code avant de fusionner les changements dans la branche `dev`.
- **Tests :** Des tests unitaires et des tests d'intégration ont été effectués sur la branche `dev` pour garantir que les nouvelles fonctionnalités n'introduisaient pas de régressions.

## 9. Phase de Test et Déploiement
Une fois que toutes les fonctionnalités étaient intégrées et que les tests étaient concluants, la branche **`dev`** était fusionnée dans la branche **`main`** pour être déployée en production.

- **Tests finaux :** Des tests manuels et des tests automatisés ont été effectués pour valider la stabilité du projet avant la mise en production.
- **Déploiement :** Le projet a été déployé en production à l'aide d'un pipeline CI/CD.

## Conclusion
En suivant cette méthodologie structurée, j'ai pu gérer efficacement chaque étape du projet, de la lecture des besoins à la mise en production. L'utilisation de Jira pour le suivi des tâches et de GitHub pour la gestion du code m'a permis d'assurer une collaboration fluide et un développement de qualité.
