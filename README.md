<p align="center">
  <img src="code/service-patrimoine/public/images/blason_rt.png" alt="Blason de la République Togolaise" width="140">
</p>

<h1 align="center">Gestion du Patrimoine de l'État</h1>

<p align="center">
  <strong>République Togolaise</strong> — <em>Travail · Liberté · Patrie</em>
</p>

<p align="center">
  <a href="https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white"><img src="https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white" alt="PHP 8.4"></a>
  <a href="https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white"><img src="https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white" alt="Laravel 12"></a>
  <a href="https://img.shields.io/badge/PostgreSQL-15-4169E1?logo=postgresql&logoColor=white"><img src="https://img.shields.io/badge/PostgreSQL-15-4169E1?logo=postgresql&logoColor=white" alt="PostgreSQL 15"></a>
  <a href="https://img.shields.io/badge/Docker-Compose-2496ED?logo=docker&logoColor=white"><img src="https://img.shields.io/badge/Docker%20Compose-2496ED?logo=docker&logoColor=white" alt="Docker Compose"></a>
  <a href="https://img.shields.io/badge/Vite-7-646CFF?logo=vite&logoColor=white"><img src="https://img.shields.io/badge/Vite-7-646CFF?logo=vite&logoColor=white" alt="Vite 7"></a>
  <a href="https://img.shields.io/badge/Composer-2-DCDCDC?logo=composer&logoColor=black"><img src="https://img.shields.io/badge/Composer-2-DCDCDC?logo=composer&logoColor=black" alt="Composer 2"></a>
  <a href="https://img.shields.io/badge/Render-Deployed-46E3B7?logo=render&logoColor=white"><img src="https://img.shields.io/badge/Render-Deployed-46E3B7?logo=render&logoColor=white" alt="Render"></a>
  <a href="https://img.shields.io/badge/PWA-Installable-5A0FC8?logo=googlechrome&logoColor=white"><img src="https://img.shields.io/badge/PWA-Installable-5A0FC8?logo=googlechrome&logoColor=white" alt="PWA"></a>
</p>

<p align="center">
  <em>Application web de gestion des biens publics (mobilier, immobilier, équipements)
  pour la Direction Générale du Patrimoine de l'État : recensement, affectation,
  transfert, inventaire physique, valorisation et audit de l'ensemble du parc
  d'actifs publics.</em>
</p>

<p align="center">
  <em>Interface aux couleurs de la charte graphique de la République Togolaise,
  armoiries officielles et photographies réelles de Lomé.</em>
</p>

<p align="center">
  <img src="code/service-patrimoine/public/images/monument_independance.jpg" alt="Monument de l'Indépendance, Lomé" width="240">
  &nbsp;&nbsp;&nbsp;
  <img src="code/service-patrimoine/public/images/place_independance.jpg" alt="Place de l'Indépendance, Lomé" width="240">
  &nbsp;&nbsp;&nbsp;
  <img src="code/service-patrimoine/public/images/palais_congres.jpg" alt="Palais des Congrès, Lomé" width="240">
</p>

<p align="center">
  <sub>Monument de l'Indépendance · Place de l'Indépendance · Palais des Congrès — Lomé, Togo</sub>
</p>

---

## Fonctionnalités

### Registre des biens
- Fiches patrimoniales complètes (identification, localisation, ministère, état)
- **QR code unique** par bien + étiquette imprimable
- **Photos** jointes à chaque fiche (preuve visuelle)
- Historique des états et des mouvements

### Inventaires
- Campagnes d'inventaire physique par service
- **Mode hors ligne (PWA)** : constats saisis sans réseau, synchronisation automatique
- Application installable sur mobile et poste de travail

### Pilotage
- **Tableau de bord personnalisable** : widgets réordonnables et masquables
- **Dépréciation linéaire automatique** selon la durée de vie de chaque catégorie
- Indicateurs : parc par état, par catégorie, valeur d'acquisition et résiduelle

### Reporting
- **Export PDF** : fiche patrimoniale officielle et registre complet (A4)
- **Export Excel** (.xlsx) et CSV du registre
- Filtrage par service selon les droits de l'utilisateur

### Comptes & sécurité
- **Authentification centralisée** (microservice dédié, tokens Sanctum)
- **Mot de passe oublié** par e-mail (lien de réinitialisation sécurisé)
- Trois rôles : administrateur national, gestionnaire de service, agent
- Journal d'audit des opérations

---

## Architecture

Deux microservices Laravel 12 + PostgreSQL, orchestrés par Docker :

```
┌─────────────────────┐        ┌──────────────────────┐
│  service-auth       │        │  service-patrimoine  │
│  API d'auth         │◄───────│  Interface web + API │
│  (comptes, rôles,   │  HTTP  │  (biens, inventaires,│
│   jetons Sanctum)   │        │   rapports, exports) │
└─────────┬───────────┘        └──────────┬───────────┘
          │                               │
          └──────────► PostgreSQL ◄───────┘
              (deux bases isolées : auth / patrimoine)
```

| Service              | Port local | Rôle                                  |
|----------------------|------------|---------------------------------------|
| `service-auth`       | 8001       | Comptes utilisateurs, jetons d'accès  |
| `service-patrimoine` | 8002       | Application métier (web + API REST)   |
| `postgres`           | 5432       | Base de données                       |

L'authentification est **centralisée** : `service-patrimoine` délègue la vérification des
identifiants à `service-auth` (`AUTH_SERVICE_URL`), puis synchronise l'utilisateur dans sa
base locale pour établir la session.

---

## Stack technique

### Framework & backend

| Outil | Version | Rôle |
|---|---|---|
| PHP | 8.4 (image Docker) — requis `^8.2` | Langage serveur |
| Laravel | 12.72.0 | Framework des 2 services |
| Laravel Sanctum | 4.4.0 | Authentification par tokens API |
| Laravel Tinker | 2.11.1 | Console interactive |
| Laravel Pint | 1.30.4 | Formatage du code PHP |
| Composer | 2.x | Gestionnaire de dépendances PHP |

### Base de données

| Outil | Version | Rôle |
|---|---|---|
| PostgreSQL | 15 | Base de données principale |
| Extensions PHP | `pdo_pgsql`, `zip`, `gd` | Connexion PG, exports Excel, images/QR |

### Packages métier (service-patrimoine)

| Package | Version | Rôle |
|---|---|---|
| barryvdh/laravel-dompdf | 3.1.2 | Génération PDF (dompdf 3.1.6) |
| endroid/qr-code | 5.1.0 | QR codes des biens |
| phpoffice/phpspreadsheet | 5.11.0 | Export Excel .xlsx |

### Frontend

| Outil | Version | Rôle |
|---|---|---|
| Vite | ^7.0.7 | Build des assets |
| laravel-vite-plugin | ^2.0.0 | Intégration Vite/Laravel |
| Tailwind CSS | 4.3.5 | Framework CSS (prévu) |
| Node.js | 22-alpine (build Docker) | Exécution du build |
| Blade | — | Moteur de templates PHP |
| Service Worker + Web App Manifest | — | Mode hors-ligne (PWA) |

### Infrastructure & déploiement

| Outil | Usage | Rôle |
|---|---|---|
| Docker | images `php:8.4-cli`, `node:22-alpine`, `postgres:15` | Conteneurisation |
| Docker Compose | v2 | Orchestration locale (3 services) |
| Render | Blueprint `render.yaml` | Hébergement cloud |
| Git | — | Versioning (GitHub) |

---

## Démarrage rapide (Docker)

```powershell
cd code

# 1. Configurer les variables d'environnement
docker compose up -d postgres
Copy-Item .env.example .env          # si absent : DB_PASSWORD aléatoire
Copy-Item service-auth/.env.example service-auth/.env
Copy-Item service-patrimoine/.env.example service-patrimoine/.env

# 2. Générer une APP_KEY distincte par service
#    (dans chaque .env : APP_KEY=base64:...)

# 3. Construire et démarrer
docker compose up -d --build

# 4. Migrations
docker compose exec service-auth php artisan migrate --force
docker compose exec service-patrimoine php artisan migrate --force
```

- **Interface** : http://localhost:8002
- **API auth** : http://localhost:8001

### Premier administrateur

```powershell
docker compose exec service-auth php artisan auth:create-national-administrator admin@exemple.tg "Administrateur national"
```

Le mot de passe est demandé de façon interactive. Ne jamais utiliser d'identifiants
d'exemple en production.

> Procédure détaillée : [`code/README.md`](code/README.md)

---

## Déploiement (Render — gratuit)

Le fichier [`render.yaml`](render.yaml) déclare l'infrastructure complète :

- `patrimoine-db` — PostgreSQL managé
- `patrimoine-auth` — service d'authentification
- `patrimoine-app` — application principale

**Étapes** : pousser le dépôt sur GitHub → Render Dashboard → **New → Blueprint** →
sélectionner le dépôt → Apply. Les migrations s'exécutent au démarrage des conteneurs.

> ⚠️ Après le premier déploiement, vérifier que `APP_URL` et `FRONTEND_URL` correspondent
> aux URL `*.onrender.com` réellement attribuées.

---

## Structure du dépôt

```
├── render.yaml                  # Blueprint de déploiement Render
├── DOCUMENTATION.md             # Documentation fonctionnelle complète
├── REGLES_TRAVAIL.md            # Règles de travail du projet
├── docs/                        # Analyse de besoin, UML, maquettes
└── code/
    ├── docker-compose.yml       # Orchestration locale (postgres + 2 services)
    ├── README.md                # Procédure Docker détaillée
    └── service-patrimoine/      # Application principale (Laravel 12)
        ├── app/Features/        # Modules métier (Biens, Catégories…)
        ├── resources/views/     # Interfaces Blade (charte Togo)
        └── public/              # Images officielles, PWA (manifest, sw.js)
    └── service-auth/            # Microservice d'authentification (API)
```

---

## Rôles et permissions

| Rôle                      | Périmètre                                        |
|---------------------------|--------------------------------------------------|
| Administrateur national   | Tous les services, tous les biens, paramétrage   |
| Gestionnaire de service   | Biens de son service, campagnes d'inventaire     |
| Agent                     | Saisie des constats d'inventaire                 |

---

## Documentation

- [`DOCUMENTATION.md`](DOCUMENTATION.md) — documentation fonctionnelle et technique complète
- [`code/README.md`](code/README.md) — démarrage Docker et création du compte admin
- [`code/service-patrimoine/README.md`](code/service-patrimoine/README.md) — dev local sans Docker
- [`code/service-auth/README.md`](code/service-auth/README.md) — service d'authentification

---

## Licence & propriété

Projet développé pour la **Direction Générale du Patrimoine de l'État** —
République Togolaise. Usage institutionnel.
