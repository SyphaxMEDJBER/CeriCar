# CeriCar

Application web de covoiturage développée avec **Yii2 (PHP)** dans un contexte académique, avec une architecture MVC complète, une base PostgreSQL et des parcours utilisateurs en AJAX.

## Objectif

Permettre aux utilisateurs de :
- créer un compte et se connecter ;
- rechercher des trajets directs ou avec correspondances ;
- réserver une ou plusieurs places ;
- proposer un voyage (si permis renseigné) ;
- consulter leur profil, leurs réservations et leurs voyages proposés.

## Stack technique

- PHP `>= 7.4`
- Framework `yiisoft/yii2 ~2.0.45`
- Bootstrap 5 + jQuery
- PostgreSQL (schéma `fredouil`)
- Codeception (tests)

## Fonctionnalités implémentées

- Authentification
- Inscription et connexion (AJAX + validations)
- Gestion de session Yii (`IdentityInterface`)
- Recherche de voyages
- Recherche directe et avec correspondances (2 ou 3 segments)
- Calcul des places restantes, contraintes horaires, prix total
- Réservation
- Réservation atomique (transaction DB)
- Réservation multi-segments pour correspondances
- Espace utilisateur
- Profil
- Mes réservations
- Mes voyages
- Proposition de voyage (avec validation serveur)
- Navigation dynamique
- Chargement partiel de pages et formulaires via AJAX

## Architecture du projet

```
CeriCar/
├── controllers/          # Contrôleurs HTTP (principalement SiteController)
├── models/               # Modèles métier (ActiveRecord + formulaires)
├── views/                # Vues et partiels
├── web/                  # Point d’entrée, assets, JS, CSS, images
├── config/               # Configuration app/DB/env
├── tests/                # Tests Codeception
├── Diagrammes UML/       # Documentation UML du projet
└── yii                   # Console Yii
```

## Modèles métier principaux

- `internaute` : utilisateur, identité Yii, mot de passe, relations réservations/voyages.
- `trajet` : liaison ville de départ/arrivée + distance.
- `voyage` : offre de covoiturage (conducteur, places, tarif, horaire, véhicule).
- `reservation` : réservation d’un utilisateur sur un voyage.
- `typevehicule` / `marquevehicule` : référentiels véhicule.

## Installation

1. Cloner le dépôt.
2. Installer les dépendances :

```bash
composer install
```

3. Vérifier la configuration DB dans `config/db.php`.
4. Lancer l’application :

```bash
php yii serve --port=8080
```

5. Ouvrir `http://localhost:8080`.

## Configuration

### Base de données

Le projet utilise Yii ActiveRecord avec PostgreSQL.

Fichier : `config/db.php`
- DSN
- utilisateur
- mot de passe
- schéma SQL ciblé (`fredouil.*` dans les modèles)

### Paramètres applicatifs

Fichier : `config/params.php`
- email administrateur
- expéditeur mail

## Routes applicatives clés

- `site/index` : accueil
- `site/signup` : inscription
- `site/login` / `site/logout` : authentification
- `site/recherche` : recherche de trajets
- `site/correspondance-details` : détail AJAX d’une correspondance
- `site/reserver` : réservation AJAX (transaction)
- `site/profil` : profil utilisateur
- `site/reservations` : réservations utilisateur
- `site/mes-voyages` : voyages proposés par l’utilisateur
- `site/proposer` : création d’un voyage

## Front-end dynamique

Scripts principaux :
- `web/js/auth.js` : login/signup AJAX
- `web/js/recherche.js` : recherche, détails correspondance, réservation
- `web/js/profil.js` : chargement embarqué du profil et formulaire proposer
- `web/js/navigation.js` : navigation AJAX globale et notifications

## Documentation UML

Le dossier `Diagrammes UML/` contient :
- diagramme de cas d’utilisation ;
- diagramme de classes ;
- diagramme d’état-transition.

## Tests

Le projet inclut Codeception (`tests/`, `codeception.yml`).

Exemples :

```bash
vendor/bin/codecept run
vendor/bin/codecept run unit
vendor/bin/codecept run functional
```

## Qualité et sécurité (état actuel)

- Le hash mot de passe est géré en logique héritée (`md5`) et doit être migré vers `password_hash`.
- Les identifiants DB ne devraient pas être versionnés en clair.
- Une externalisation des secrets via variables d’environnement est recommandée.

## Licence

Voir `LICENSE.md`.
