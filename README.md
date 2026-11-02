# Mini-blog Ostéopathie

Blog éditorial dédié à l'histoire et aux concepts fondateurs de l'ostéopathie, développé dans le cadre du **Dossier Professionnel** du titre DWWM (Studi).

**Démo locale** · [Dépôt GitHub](https://github.com/LaurentMARCdev/Mini-blog-osteo)

---

## Table des matières

- [Présentation](#présentation)
- [Stack technique](#stack-technique)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Démarrage](#démarrage)
- [Comptes de démonstration](#comptes-de-démonstration)
- [Structure du projet](#structure-du-projet)
- [Variables d'environnement](#variables-denvironnement)
- [Workflow Git](#workflow-git)
- [Évolutions prévues](#évolutions-prévues)

---

## Présentation

Mini-blog Symfony qui démontre l'intégralité d'un cycle de développement web, du cadrage à la livraison d'une application fonctionnelle. Il couvre :

- **Côté public** : accueil avec les 3 derniers articles, liste complète filtrable par catégorie (filtre JavaScript côté client, sans rechargement), fiche détail d'un article, dépôt de commentaires par les utilisateurs connectés
- **Côté administrateur** : back-office EasyAdmin pour gérer articles, catégories, utilisateurs et commentaires, avec un workflow de modération dédié (Valider / Refuser en un clic)
- **Thème clair/sombre** persistant via `localStorage`

---

## Stack technique

|       Couche       |               Technologie             |
|         ---        |                  ---                  |
|  Langage back-end  |                PHP 8.2+               |
| Framework back-end |              Symfony 7.1              |
|       ORM          |             Doctrine ORM              |
|  Base de données   |        SQLite (fichier portable)      |
|       Admin        |             EasyAdmin 5.8             |
|   Framework CSS    |        Tailwind CSS 3 (via CDN)       |
|        JS          | JavaScript vanilla (pas de framework) |
|      Templates     |                 Twig                  |

---

## Prérequis

- **PHP** 8.2 ou supérieur
- **Composer** 2.x
- **Git**
- (Optionnel mais recommandé) **Symfony CLI** pour le serveur de développement

---

## Installation

```bash
# 1. Cloner le dépôt
git clone https://github.com/LaurentMARCdev/Mini-blog-osteo.git
cd Mini-blog-osteo

# 2. Installer les dépendances PHP
composer install

# 3. Copier le fichier d'environnement local
#    (sous Windows PowerShell : Copy-Item .env .env.local)
cp .env .env.local

# 4. Créer la base de données SQLite + appliquer les migrations
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction

# 5. Charger les données de démonstration (utilisateurs, articles, catégories, commentaires)
php bin/console doctrine:fixtures:load --no-interaction

# 6. Vérifier que le schéma est cohérent
php bin/console doctrine:schema:validate
```

---

## Démarrage

```bash
# Avec Symfony CLI (recommandé)
symfony server:start

# Alternative sans Symfony CLI
php -S localhost:8000 -t public
```

Le blog est accessible sur **http://localhost:8000**.

Le back-office administrateur est accessible sur **http://localhost:8000/admin**.

---

## Comptes de démonstration

Les fixtures créent automatiquement deux comptes utilisables immédiatement :

|      Rôle      |            Email        | Mot de passe |
|       ---      |             ---         |      ---     |
| Administrateur |  `admin@blog-osteo.fr`  |    `admin`   |
|     Lecteur    | `lecteur@blog-osteo.fr` |   `lecteur`  |

> ⚠️ Ces mots de passe sont simples pour la démonstration. Ils seraient à proscrire pour une mise en production.

---

## Structure du projet

mini-blog-osteo/
├── config/ Configuration Symfony (services, routes, security…)
├── migrations/ Migrations Doctrine versionnées
├── public/ Point d’entrée HTTP, assets publics
├── src/
│ ├── Controller/
│ │ ├── Admin/ CrudControllers EasyAdmin
│ │ ├── ArticleController.php
│ │ ├── HomeController.php
│ │ └── RegistrationController.php
│ ├── DataFixtures/ Données de démonstration
│ ├── Entity/ Entités Doctrine (User, Article, Category, Comment)
│ ├── Form/ Form Types (CommentFormType, RegistrationFormType)
│ └── Repository/ Repositories Doctrine
├── templates/ Templates Twig
├── var/
│ └── data_dev.db Base SQLite (générée à l’installation, non versionnée)
├── composer.json
└── README.md


---

## Variables d'environnement

Les variables sont déclarées dans `.env` (versionné, valeurs par défaut) et surchargées dans `.env.local` (non versionné, valeurs locales). Les variables essentielles :

|     Variable   |                                  Rôle                                      |
|       ---      |                                  ---                                       |
|    `APP_ENV`   |                 Environnement (`dev` / `prod` / `test`)                    |
|  `APP_SECRET`  |           Clé de signature des tokens Symfony (à régénérer en prod)        |
| `DATABASE_URL` | DSN de connexion à la base. SQLite par défaut, prêt pour PostgreSQL/MySQL. |
|  `MAILER_DSN`  |        DSN du mailer (actuellement `null://null` = pas d'envoi réel)       |

Le `.gitignore` protège `.env.local`, `.env.*.local` et `.env.local.php` pour éviter toute fuite de secret.

---

## Workflow Git

Le projet suit la convention **Conventional Commits** pour les messages de commit, avec des préfixes qui indiquent la nature du changement et la portée :

|      Préfixe      |                Signification                |                   Exemple              |
|        ---        |                     ---                     |                     ---                |
|   `feat(scope)`   |            Nouvelle fonctionnalité          |  `feat(admin): installation EasyAdmin` |
|   `fix(scope)`    |              Correction de bug              |     `fix(auth): rôle admin mal écrit`  |
| `refactor(scope)` | Refactorisation sans changement fonctionnel | `refactor(repo): extract common query` |
|       `docs`      |                Documentation                |             `docs: add README`         |

Le projet utilise une **branche unique `main`**, les évolutions se font directement dessus (projet solo, pas de code review en équipe). Dans un contexte professionnel ou pluri-contributeur, l'ajout d'une branche `develop` et de pull requests serait indispensable.

---

## Évolutions prévues

- **Déploiement en production** sur Alwaysdata (hébergeur européen souverain) ou équivalent
- **Pipeline CI/CD** via GitHub Actions (lint PHP, PHPStan, tests PHPUnit, déploiement automatique)
- **Tests automatisés** : suite PHPUnit (unitaires sur les services, fonctionnels sur les controllers via `WebTestCase`)
- **Migration de Tailwind CDN vers AssetMapper** pour la production (build local optimisé)
- **Enum PHP 8** sur le statut des commentaires (en remplacement du `string(20)` actuel)
- **Hashage automatique du mot de passe depuis EasyAdmin** via un listener Doctrine
- **Index secondaires** sur `article.publie` et `comment.statut` quand le volume le justifiera

---

## Licence

Projet pédagogique développé par Laurent MARC dans le cadre du titre professionnel DWWM (Studi), promotion 2026.
Licence libre CC BY-SA .