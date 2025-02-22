# Votissimo

Votissimo est une application de gestion de scrutins permettant d'organiser des votes en ligne en utilisant plusieurs méthodes de décision, notamment le vote proportionnel, le vote majoritaire et le vote Condorcet.

## Table des matières
- [Introduction](#introduction)
- [Fonctionnalités](#fonctionnalit%C3%A9s)
- [Installation](#installation)
- [Configuration](#configuration)
- [Utilisation](#utilisation)
- [Méthodes de vote](#m%C3%A9thodes-de-vote)
- [Contribution](#contribution)
- [Licence](#licence)

## Introduction
Votissimo a été conçu dans le cadre d'un projet visant à proposer des solutions alternatives aux méthodes de vote classiques. En permettant aux organisations publiques et privées d’organiser des consultations et des votes en ligne, l’application facilite la mise en œuvre d’algorithmes de décision avancés.

## Fonctionnalités
- Authentification des utilisateurs (inscription, connexion, déconnexion).
- Gestion des utilisateurs avec différents rôles (Administrateur, Organisateur, Participant, Visiteur).
- Création et gestion des scrutins.
- Participation aux scrutins et enregistrement des votes.
- Affichage des résultats des votes avec différentes méthodes d’analyse.
- Statistiques et notifications sur les scrutins.
- Sécurisation des données avec stockage sécurisé des mots de passe.

## Installation
1. **Cloner le dépôt**
   ```sh
   git clone https://github.com/votre-utilisateur/votissimo.git
   cd votissimo
   ```
2. **Installer les dépendances PHP**
   ```sh
   composer install
   ```
3. **Configurer la base de données**
   - Créer une base de données MySQL.
   - Importer le fichier `database.sql` situé dans le dossier `database/`.
   - Configurer les accès à la base dans le fichier `.env`.

4. **Lancer l'application**
   - Démarrer un serveur local PHP :
     ```sh
     php -S localhost:8000 -t public/
     ```
   - Accéder à `http://localhost:8000` via un navigateur.

## Configuration
Les paramètres de configuration doivent être définis dans un fichier `.env` à la racine du projet :

```
DB_HOST=localhost
DB_NAME=votissimo
DB_USER=root
DB_PASSWORD=yourpassword
```

## Utilisation
- **Créer un compte** via la page d'inscription.
- **Se connecter** pour accéder aux fonctionnalités de création et de gestion des scrutins.
- **Créer un scrutin** en définissant une question et plusieurs options de réponse.
- **Voter** en fonction de la méthode de décision choisie.
- **Afficher les résultats** une fois le vote terminé.
- **Accéder à l'administration** pour gérer les scrutins et utilisateurs.

## Méthodes de vote
Votissimo prend en charge plusieurs méthodes de vote :
1. **Vote proportionnel** : Simple comptage des voix.
2. **Vote majoritaire** : Une option doit obtenir plus de 50% des voix.
3. **Vote Condorcet** : Comparaison des choix via un algorithme avancé.

## Contribution
Les contributions sont les bienvenues !
1. Forkez le projet
2. Créez une branche (`feature-nouvelle-fonctionnalité`)
3. Faites vos modifications
4. Soumettez une Pull Request

## Licence
Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus d'informations.

