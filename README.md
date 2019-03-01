# trio
Jeu de calcul mental

Il s'agit d'une version numérique enrichie d'un jeu créé par Heinz Wittenberg et anciennement commercialisé par Ravensburger dont la version en "production" se trouve à l'adresse : http://trio.acamus.net .

La plupart des variantes proposées dans le jeu sont inspirées d'activités présentées dans les excellentes brochures "JEUX" de l'APMEP.

La partie html/javascript qui compose l'interface du jeu est programmée par Julien Pavageau (professeur de mathématiques au collège Albert Camus de Frontenay Rohan-Rohan en France) qui maintient à présent également la partie PHP dont les premiers scripts ont été initiés par le webmaster du site du collège (professeur de physique-chimie).

Le code a été nettoyé et modifié pour être vraiment utilisable dans un autre contexte que celui du site de notre collège (liste des établissements paramétrable à l'aide d'un fichier csv).

A l'exception des fonctions charger/sauver, qui nécessitent l'installation sur un serveur web avec PHP, toutes les autres fonctionnalités du jeu sont parfaitement utilisables hors ligne en décompressant simplement l'archive et en ouvrant le fichier index.html dans un navigateur.

Pour profiter de toutes les fonctionnalités il est cependant préférable de l'installer sur un serveur PHP comme expliqué ci-dessous.

Comment installer le jeu ?
--------------------------
- copier le contenu de l'archive sur le serveur
- renseigner le fichier etab.csv avec les différents établissements ou classes et leurs coordonnateurs
- placer dans les dossiers des établissements, dont vous voulez contrôler l'accès, un fichier mdp.csv avec les identifiants des élèves (si en entête on laisse md5 alors il faut fournir les mots de passe sous cette forme sinon on peut les fournir en clair en remplaçant par exemple md5 par mdp)
- les élèves pourront choisir librement leurs identifiants dans les établissements où ce fichier n'est pas présent (il n'est alors pas indispensable de créer le dossier)
- en utilisant les identifiants des coordonnateurs indiqués dans etab.csv on peut afficher et extraire un résumé (score, date,...) des sauvegardes des élèves de l'établissement
- l'identifiant placé en début du fichier etab.csv donne accès à des affichages et des extractions plus complètes (affichage des logs, export des élèves de tous les établissements, ...)
- pour activer la notification par mail il reste à modifier les lignes 16 à 19 du script sauvegarde.php

Historique des modifications
----------------------------

Version 1.0 du 18 05 2017
* version d'origine telle qu'elle était présente sur le site du collège

Version 1.1 du 18 05 2017
* ajout de la version Duel Ping Pong qui est indépendante

Version 2.0 du 12 02 2018
* nettoyage du code de index.html (en particulier le code javascript est à présent réuni après le html)
* nouvel affichage sous forme d'onglets pour faciliter l'intégration dans un iframe et améliorer l'utilisation sur tablette avec possibilité de passer en plein écran
* intégration de Font Awesome (https://fortawesome.com) pour agrémenter l'interface avec quelques icônes
* tous les établissements peuvent à présent utiliser l'identification en plaçant un fichier mdp.csv dans leur dossier
* la liste des établissements est à présent dynamique (renseignée par un script PHP qui consulte etab.csv) et le formulaire "Arrêter ou reprendre une partie :" ne s'affiche que si les scripts PHP sont actifs
* les coordonnateurs peuvent afficher les sauvegardes de leurs élèves et télécharger ces informations sous la forme d'un csv
* le coordonnateur du 1er établissement peut faire de même mais avec tous les établissements et consulter les logs sur la même page
* il y a un avertissement si on tente de charger un score plus bas que celui de la partie en cours...

Version 2.1 du 12 04 2018
* Concours 2018 + Corrections petits bugs
* Mode Compétition : Création, Chargement, Sauvegarde et consultation des scores

Version 2.2 du 01 03 2019
* Corrections petites failles potentielles dans sauvegarde.php
* Concours 2019