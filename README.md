# Introduction
Ce projet est fait en Symfony / React. Il est le début d'un formulaire de questions.

# Installation
- Cloner le projet :
  - `git clone git@github.com:farpat/questions-tunnel`
  - `cd questions-tunnel`
- Installer les dépendances (PHP et JavaScript) : 
  - `make install` 
- Mettre en place la base de données :
  - `make purge-database` : Créer la base de données
  - `make migrate` : Remplir la base de données

# Utilisation
Pour lancer le projet : `make dev`.

| :warning:        Attention à la fin de ne pas oublier à lancer `make stop-dev` pour arrêter les conteneurs de développement.          |
|---------------------------------------------------------------------------------------------------------------------------------------|

# Tests automatiques
Pour lancer les tests : `make test`