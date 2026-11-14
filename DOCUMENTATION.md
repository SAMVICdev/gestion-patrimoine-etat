# Documentation du projet — Gestion du patrimoine de l'État

## Table des matières

1. [Vue d'ensemble](#1-vue-densemble)
2. [Objectifs du projet](#2-objectifs-du-projet)
3. [Architecture technique](#3-architecture-technique)
4. [Fonctionnalités](#4-fonctionnalités)
5. [Rôles et permissions](#5-rôles-et-permissions)
6. [Structure des dossiers](#6-structure-des-dossiers)
7. [Base de données](#7-base-de-données)
8. [Démarrage et déploiement](#8-démarrage-et-déploiement)
9. [Authentification](#9-authentification)
10. [API REST](#10-api-rest)
11. [Flux métier](#11-flux-métier)
12. [Évolutions prévues](#12-évolutions-prévues)

---

## 1. Vue d'ensemble

**Gestion du patrimoine de l'État** est une application web de gestion des biens publics
(mobilier, immobilier, équipements) pour une administration nationale.
Elle permet de recenser, affecter, transférer, inventorier et auditer
l'ensemble du parc d'actifs publics d'un pays.

Le projet est organisé en deux microservices Laravel 12 indépendants,
orchestrés par Docker Compose avec une base PostgreSQL partagée mais
logiquement séparée en deux bases distinctes.

| Microservice        | Port  | Rôle                                      |
|---------------------|-------|-------------------------------------------|
| `service-auth`      | 8001  | Comptes, mots de passe, rôles, jetons     |
| `service-patrimoine`| 8002  | Application principale (interface + API)  |

L'interface utilisateur est accessible sur **http://localhost:8002**.

---

## 2. Objectifs du projet

### Objectif principal
Donner à une Direction Générale du Patrimoine de l'État un outil numérique
centralisé pour gérer l'intégralité des biens publics, de leur acquisition
à leur archivage, en passant par leurs transferts entre services.

### Objectifs spécifiques

| # | Objectif                                                                 |
|---|--------------------------------------------------------------------------|
| 1 | Constituer un registre numérique exhaustif de tous les biens de l'État   |
| 2 | Tracer chaque mouvement d'un bien (transfert, changement d'état)         |
| 3 | Organiser les campagnes d'inventaire physique sur le terrain             |
| 4 | Produire des rapports de valorisation du patrimoine public               |
| 5 | Garantir un journal d'audit immuable de toutes les opérations            |
| 6 | Séparer les accès par rôle (national, gestionnaire de service, agent)    |
| 7 | Fournir une API REST pour l'intégration avec d'autres systèmes           |

### Contexte d'utilisation
L'application est destinée à des administrations publiques d'Afrique
francophone (devise FCFA, langue française). Elle peut être adaptée
à tout pays en modifiant les paramètres de plateforme.

---

## 3. Architecture technique

### Stack technologique

| Couche            | Technologie                          |
|-------------------|--------------------------------------|
| Backend           | PHP 8.2+, Laravel 12                 |
| Frontend          | Blade templates, Vite, CSS vanilla   |
| Base de données   | PostgreSQL 15 (production), SQLite (tests) |
| Auth              | Laravel Sanctum (jetons Bearer)      |
| Conteneurisation  | Docker, Docker Compose               |
| Qualité du code   | Laravel Pint (PSR-12), PHPUnit 11    |

### Schéma d'architecture

```
Navigateur
    │
    ▼
service-patrimoine (port 8002)
    │   Interface web (Blade + Vite)
    │   API REST (/api/*)
    │
    ├── Vérifie chaque requête auprès de ──► service-auth (port 8001)
    │                                            │
    │                                            └── Base : auth_service (PostgreSQL)
    │
    └── Base : patrimoine_etat (PostgreSQL)
```

### Communication inter-services
`service-patrimoine` ne gère pas les mots de passe ni les jetons.
À chaque requête, il appelle `service-auth` via HTTP (classe `CentralAuthClient`)
pour valider le jeton et récupérer l'identité de l'utilisateur,
puis synchronise localement les informations nécessaires (nom, e-mail, rôle).

---

## 4. Fonctionnalités

### 4.1 Tableau de bord
Page d'accueil après connexion. Affiche :
- Total des biens au registre et leur valeur d'acquisition cumulée
- Répartition des biens par état (bon état, à réparer, hors service…)
- Top 5 des catégories les plus représentées
- 5 derniers biens enregistrés

Le tableau de bord est **scopé** : un gestionnaire ne voit que les biens
de son service, l'administrateur national voit tout.

### 4.2 Gestion des biens (`/biens`)
Le cœur du registre patrimonial.

**Champs d'un bien :**

| Champ               | Description                                      |
|---------------------|--------------------------------------------------|
| Numéro d'inventaire | Identifiant unique du bien (ex. PAT-2026-00123)  |
| Désignation         | Nom court du bien                                |
| Description         | Description détaillée                            |
| Catégorie           | Type de bien (Informatique, Mobilier, Véhicule…) |
| Service responsable | Service auquel le bien est affecté               |
| Localisation        | Emplacement physique (bureau, bâtiment, ville)   |
| Responsable         | Nom de la personne en charge                     |
| État                | État physique courant (voir états ci-dessous)    |
| Date d'acquisition  | Date d'entrée dans le patrimoine                 |
| Valeur d'acquisition| Coût d'achat en FCFA                             |
| Source financement  | Budget de l'État, don, coopération…              |

**États possibles d'un bien :**
`bon_etat` · `etat_moyen` · `a_reparer` · `en_reparation` · `hors_service` · `introuvable`

**Opérations disponibles :**
- Créer, modifier, consulter un bien
- Archiver (suppression douce — le bien reste dans la base)
- Consulter l'historique complet des changements d'état
- Télécharger les justificatifs de changement d'état

### 4.3 Transferts et affectations (`/transferts`)
Gère les mouvements de biens entre services.

**Cycle de vie d'un transfert :**
```
Gestionnaire crée la demande (statut : en_attente)
        │
        ▼
Administrateur national approuve ou rejette
        │
   ┌────┴────┐
approuvé    rejeté
        │
        ▼
Le bien est réaffecté au service destination
```

Chaque transfert enregistre : bien concerné, service d'origine,
service de destination, localisations, motif, justificatif (fichier),
et la décision avec commentaire.

### 4.4 Inventaires terrain (`/inventaires`)
Campagnes d'inventaire physique périodiques.

**Déroulement :**
1. L'administrateur ou le gestionnaire crée une campagne d'inventaire
2. Les agents se rendent sur le terrain et enregistrent des constats
   (état observé, commentaire, photo)
3. Une fois tous les biens vérifiés, la campagne est clôturée
4. Les états des biens sont automatiquement mis à jour

### 4.5 Rapports et audit (`/rapports`)
- **Tableau de synthèse** : statistiques par ministère, service, catégorie
- **Export CSV** : registre complet téléchargeable (compatible Excel)
- **Journal d'audit** : toutes les opérations horodatées avec l'auteur
  (accessible uniquement à l'administrateur national)

### 4.6 Référentiels administratifs
- **Catégories** (`/categories`) : types de biens (Mobilier, Informatique…)
- **Services** (`/services`) : services publics responsables des biens
- **Ministères** (`/ministeres`) : ministères de tutelle des services

### 4.7 Utilisateurs et habilitations (`/utilisateurs`)
Gestion des comptes depuis l'interface.
Le compte est créé simultanément dans `service-auth` (source de vérité)
et synchronisé dans `service-patrimoine`.

### 4.8 Paramètres de la plateforme (`/parametres`)
Configuration de l'organisme : nom, pays, devise, préfixe d'inventaire,
coordonnées de contact. Ces informations apparaissent dans les exports.

### 4.9 Profil utilisateur (`/parametres/profil`)
Chaque utilisateur connecté peut modifier son nom d'affichage
et changer son mot de passe.

---

## 5. Rôles et permissions

| Permission                                      | Administrateur national | Gestionnaire | Agent |
|-------------------------------------------------|:-----------------------:|:------------:|:-----:|
| Voir les biens de son service                   | ✓ (tous services)       | ✓            | ✓     |
| Créer / modifier un bien                        | ✓                       | ✓            |       |
| Archiver un bien                                | ✓                       | ✓            |       |
| Demander un transfert                           | ✓                       | ✓            |       |
| Approuver / rejeter un transfert                | ✓                       |              |       |
| Créer une campagne d'inventaire                 | ✓                       | ✓            |       |
| Enregistrer un constat d'inventaire             | ✓                       | ✓            | ✓     |
| Clôturer une campagne d'inventaire              | ✓                       | ✓            |       |
| Gérer les catégories                            | ✓                       | ✓            |       |
| Gérer les services et ministères                | ✓                       |              |       |
| Gérer les utilisateurs                          | ✓                       |              |       |
| Voir le journal d'audit complet                 | ✓                       |              |       |
| Exporter le registre CSV                        | ✓                       | ✓            |       |
| Modifier les paramètres de la plateforme        | ✓                       |              |       |
| Modifier son propre profil                      | ✓                       | ✓            | ✓     |

---

## 6. Structure des dossiers

```
gestion-patrimoine-etat/
├── code/
│   ├── docker-compose.yml          # Orchestration Docker
│   ├── docker/postgres/init/       # Script SQL de création des bases
│   │
│   ├── service-auth/               # Microservice d'authentification
│   │   ├── app/
│   │   │   ├── Console/Commands/   # CreateNationalAdministrator
│   │   │   ├── Http/Controllers/Api/AuthController.php
│   │   │   ├── Http/Middleware/EnsureRole.php
│   │   │   └── Models/User.php
│   │   ├── database/migrations/
│   │   └── Dockerfile
│   │
│   └── service-patrimoine/         # Application principale
│       ├── app/
│       │   ├── Features/           # Architecture par domaine métier
│       │   │   ├── Audit/          # Journal d'audit
│       │   │   ├── Biens/          # Biens patrimoniaux
│       │   │   ├── Categories/     # Catégories de biens
│       │   │   ├── Inventaires/    # Campagnes d'inventaire
│       │   │   ├── Ministeres/     # Ministères
│       │   │   ├── Parametres/     # Paramètres plateforme + profil
│       │   │   ├── Services/       # Services publics
│       │   │   ├── Transferts/     # Transferts et historique d'état
│       │   │   └── Users/          # Gestion des utilisateurs
│       │   ├── Http/
│       │   │   ├── Controllers/    # Dashboard, Rapports, Auth session
│       │   │   └── Middleware/     # EnsureCentralAuth, EnsureRole
│       │   ├── Models/User.php     # Modèle utilisateur local (synchronisé)
│       │   └── Services/CentralAuthClient.php  # Client HTTP vers service-auth
│       ├── database/migrations/    # 14 migrations
│       ├── resources/views/        # Templates Blade
│       │   ├── layouts/app.blade.php
│       │   ├── auth/, biens/, categories/, dashboard.blade.php
│       │   ├── inventaires/, ministeres/, parametres/
│       │   ├── rapports/, services/, transferts/, users/
│       └── routes/web.php          # Toutes les routes web et API
```

---

## 7. Base de données

### Base `auth_service` (service-auth)

| Table                   | Description                              |
|-------------------------|------------------------------------------|
| `users`                 | Comptes centralisés (email, password, rôle) |
| `personal_access_tokens`| Jetons Sanctum                           |
| `cache`                 | Cache Laravel                            |
| `jobs`                  | File de travaux asynchrones              |

### Base `patrimoine_etat` (service-patrimoine)

| Table                    | Description                                        |
|--------------------------|----------------------------------------------------|
| `users`                  | Copie locale synchronisée des identités            |
| `ministeres`             | Ministères de tutelle                              |
| `services`               | Services publics responsables des biens            |
| `categories`             | Types de biens                                     |
| `biens`                  | Registre central des biens (soft delete)           |
| `transferts`             | Demandes de transfert entre services               |
| `historique_etats`       | Historique des changements d'état d'un bien        |
| `campagnes_inventaire`   | Campagnes d'inventaire physique                    |
| `constats_inventaire`    | Constats enregistrés lors des campagnes            |
| `journal_audit`          | Journal immuable de toutes les opérations          |
| `parametres_plateforme`  | Configuration de l'organisme (clé/valeur)          |
| `personal_access_tokens` | Jetons API du service patrimoine                   |
| `cache`                  | Cache Laravel                                      |
| `jobs`                   | File de travaux asynchrones                        |

---

## 8. Démarrage et déploiement

### Avec Docker (recommandé)

```powershell
# 1. Créer le fichier d'environnement Docker
Set-Location "code"
Copy-Item .env.example .env
# Éditer .env et choisir un mot de passe PostgreSQL fort

# 2. Construire et démarrer
docker compose up -d --build

# 3. Appliquer les migrations
docker compose exec service-auth php artisan migrate --force
docker compose exec service-patrimoine php artisan migrate --force

# 4. Créer le premier administrateur national
docker compose exec service-auth php artisan auth:create-national-administrator admin@exemple.tg "Administrateur national"
# La commande demande un mot de passe interactif (12 car. min., maj., min., chiffre)
```

L'application est disponible sur **http://localhost:8002**.

### Arrêt et redémarrage

```powershell
docker compose down          # Arrêt (conserve les données)
docker compose down -v       # Arrêt + suppression du volume (repart à zéro)
docker compose up -d         # Redémarrage sans rebuild
docker compose logs --tail 100 service-patrimoine service-auth postgres
```

### Sans Docker (développement local)
Prérequis : PHP 8.2+, Composer, Node.js, npm.

```powershell
# Terminal 1 — service-auth
Set-Location "code\service-auth"
composer install ; php artisan migrate
php artisan serve --host=127.0.0.1 --port=8001

# Terminal 2 — service-patrimoine
Set-Location "code\service-patrimoine"
composer install ; npm install ; npm run build ; php artisan migrate
php artisan serve --host=127.0.0.1 --port=8002
```

---

## 9. Authentification

L'authentification est entièrement centralisée dans `service-auth`.
`service-patrimoine` ne stocke ni mot de passe ni jeton — il délègue tout.

### Flux de connexion web

```
1. Utilisateur soumet email + mot de passe sur /connexion
2. service-patrimoine POST /api/login → service-auth
3. service-auth valide et renvoie { user, token }
4. service-patrimoine stocke le token en session
5. Sur chaque requête : EnsureCentralAuth GET /api/user → service-auth
6. service-auth valide le token et renvoie le profil
7. service-patrimoine synchronise l'identité localement
```

### Flux API (Bearer token)

```
POST http://localhost:8001/api/login
  Body: { "email": "...", "password": "..." }
  Réponse: { "token": "...", "user": { ... } }

GET http://localhost:8002/api/biens
  Header: Authorization: Bearer <token>
```

---

## 10. API REST

Toutes les routes API de `service-patrimoine` sont préfixées par `/api/`
et nécessitent un token Bearer obtenu via `service-auth`.

### Endpoints disponibles

| Méthode | Route                          | Description                        | Rôles autorisés         |
|---------|--------------------------------|------------------------------------|-------------------------|
| GET     | `/api/biens`                   | Liste des biens (filtrable)        | Tous                    |
| POST    | `/api/biens`                   | Créer un bien                      | Admin, Gestionnaire     |
| GET     | `/api/biens/{id}`              | Détail d'un bien                   | Tous                    |
| PUT     | `/api/biens/{id}`              | Modifier un bien                   | Admin, Gestionnaire     |
| DELETE  | `/api/biens/{id}`              | Archiver un bien                   | Admin, Gestionnaire     |
| GET     | `/api/categories`              | Liste des catégories               | Tous                    |
| POST    | `/api/categories`              | Créer une catégorie                | Admin, Gestionnaire     |
| GET     | `/api/services`                | Liste des services                 | Tous                    |
| POST    | `/api/services`                | Créer un service                   | Admin national          |

### Filtres disponibles sur `/api/biens`

| Paramètre     | Exemple                   | Description                     |
|---------------|---------------------------|---------------------------------|
| `q`           | `?q=bureau`               | Recherche dans la désignation   |
| `etat`        | `?etat=hors_service`      | Filtre par état                 |
| `categorie_id`| `?categorie_id=3`         | Filtre par catégorie            |
| `service_id`  | `?service_id=5`           | Filtre par service              |

---

## 11. Flux métier

### Enregistrer un nouveau bien

```
Gestionnaire ou Admin
        │
        ▼
Remplit le formulaire /biens/create
(numéro d'inventaire, désignation, catégorie, service, état, valeur…)
        │
        ▼
Bien créé → JournalAudit enregistre l'événement "creation"
        │
        ▼
Bien visible dans l'inventaire /biens
```

### Transférer un bien entre services

```
Gestionnaire du service A
        │
        ▼
Crée la demande /transferts/nouveau
(bien, service destination, motif, justificatif)
        │
        ▼
Statut : en_attente
        │
        ▼
Administrateur national reçoit la demande sur /transferts
        │
   ┌────┴────────────┐
Approuve           Rejette (avec commentaire)
   │
   ▼
Bien réaffecté au service destination
JournalAudit enregistre la décision
```

### Réaliser un inventaire terrain

```
Admin ou Gestionnaire
        │
        ▼
Crée la campagne /inventaires/nouveau
(titre, date de début, date de fin prévue)
        │
        ▼
Les agents se connectent et enregistrent leurs constats
/inventaires/{id}/constats
(bien, état observé, commentaire)
        │
        ▼
Admin ou Gestionnaire clôture la campagne
→ Les états des biens sont mis à jour automatiquement
→ JournalAudit enregistre la clôture
```

---

## 12. Évolutions prévues

Ces fonctionnalités sont identifiées mais pas encore implémentées :

| # | Fonctionnalité                   | Description                                                        |
|---|----------------------------------|--------------------------------------------------------------------|
| 1 | Export PDF                       | Rapports et fiches de bien en PDF (via DOMPDF ou Browsershot)      |
| 2 | Export Excel                     | Registre en .xlsx avec mise en forme (via PhpSpreadsheet)          |
| 3 | QR codes                         | QR code unique par bien, imprimable sur étiquette physique         |
| 4 | Photos des biens                 | Galerie de photos attachées à chaque bien                          |
| 5 | Fonctionnement hors ligne        | PWA ou cache Service Worker pour les agents sur le terrain         |
| 6 | Notifications                    | Alertes e-mail (transfert en attente, campagne ouverte…)           |
| 7 | Tableau de bord personnalisable  | Widgets réorganisables par l'utilisateur                           |
| 8 | Dépréciation automatique         | Calcul de la valeur résiduelle selon la catégorie                  |

---

*Document généré le 14 novembre 2026 — Projet gestion-patrimoine-etat*
*Dépôt GitHub : https://github.com/SAMVICdev/gestion-patrimoine-etat*
