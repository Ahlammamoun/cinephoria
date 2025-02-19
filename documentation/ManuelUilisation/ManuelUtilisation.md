
# Manuel d'utilisation de Cinéphoria

## Introduction
Bienvenue dans le manuel d'utilisation de Cinéphoria. Ce document vous guidera à travers les différentes fonctionnalités de l'application, tout en vous fournissant des identifiants nécessaires pour réaliser les différents parcours.

## Table des matières

1. [Introduction à l'application](#introduction-à-lapplication)
2. [Création de compte et identification](#création-de-compte-et-identification)
3. [Navigation dans l'application](#navigation-dans-lapplication)
4. [Parcours possibles](#parcours-possibles)
5. [Parcours en locale](#parcours-en-locale)
   1. [Cloner le dépôt](#cloner-le-dépôt)
   2. [Se déplacer sur la branche développe](#se-déplacer-sur-la-branche-développe)
   3. [Lancer les containers](#lancer-les-containers)
   4. [Accéder au serveur local](#accéder-au-serveur-local)
   5. [Tester l’application bureautique avec Electron](#tester-lapplication-bureautique-avec-electron)
   6. [Tester l’application mobile avec Flutter](#tester-lapplication-mobile-avec-flutter)

L'application a été conçue pour sélectionner un cinéma, consulter ses derniers films ainsi que tous les films. Un utilisateur peut réserver une séance, consulter ses commandes et les noter. Un administrateur va avoir accès à toutes les fonctionnalités.

## 2. Création de compte et identification
Pour commencer à utiliser l'application, vous devez vous connecter en tant qu’administrateur :
[https://cinephora.fr/](https://cinephora.fr/)
- **Compte administrateur** :
  - **Login** : 
  - **Mot de passe** : f

## 3. Navigation dans l'application
Une fois connecté, voici comment naviguer dans l'application :
- **Écran principal** : Vous y trouverez un aperçu des parcours disponibles dans la navbar.
- **Menu** : Le menu en haut vous permet d'accéder à différentes fonctions en fonction de votre rôle.

## 4. Parcours possibles
L'application propose plusieurs parcours selon vos besoins. Voici quelques exemples :
- **Parcours 1** : Page d'accueil, liste des derniers films.
- **Parcours 2** : Cliquez sur "Movies" dans le menu, affichage de tous les films.
  - Possibilité de trier par cinéma, catégories et dates.
  - Cliquez sur le "+" du film pour avoir une description complète.
  - Cliquez sur le film pour pouvoir réserver.
  - Choisissez le cinéma.
  - Choisissez la séance.
  - Choisissez le film.
  - Choisissez le nombre de places.
  - Cliquez sur "Réserver".
- **Parcours 3** : Cliquez sur "Espaces" dans le menu en haut.
  - Visualiser ses commandes.
  - Noter les commandes passées.
- **Parcours 5** : Cliquez sur "Contact" pour envoyer un message au cinéma.
- **Parcours 6** : Section "Admin menu" en haut dans le menu, cliquez pour ajouter un film.
- **Parcours 7** : Allez dans "Admin menu", cliquez pour modifier/supprimer des films.
- **Parcours 8** : Allez dans "Admin menu", cliquez pour modifier les salles.
- **Parcours 9** : Allez dans "Admin menu", cliquez pour créer des comptes.
- **Parcours 10** : Allez dans "Admin menu", cliquez pour consulter le chiffre d’affaires détaillé.

## 5. Parcours en locale

### Cloner le dépôt
Cloner le dépôt sur votre machine locale avec la commande suivante :
```bash
git clone https://github.com/Ahlammamoun/cinephoria.git

Se déplacer sur la branche develop
git checkout develop

Lancer les containers Docker
Se placer à la racine du projet et lancer les containers Docker avec la commande suivante :
docker-compose up --build

Accéder au serveur local
Une fois les containers lancés, accédez à l'application via le serveur local à l'adresse suivante : http://localhost:8000

Tester l’application bureautique avec Electron
Se placer dans le répertoire frontend :
cd frontend
Installer les dépendances nécessaires
npm install
Lancer l'application bureautique avec Electron :
npm run electron


Tester l’application mobile avec Flutter
Se placer dans le répertoire mobile/cinephoria_app_mobile 
cd mobile/cinephoria_app_mobile
Installer les dépendances Flutter 
flutter pub get
Lancer l’application mobile :
flutter run








