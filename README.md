# Gestion du Patrimoine de l'État

**République Togolaise** — *Travail · Liberté · Patrie*

Application web de gestion des biens publics (mobilier, immobilier, équipements) pour la
Direction Générale du Patrimoine de l'État : recensement, affectation, transfert,
inventaire physique, valorisation et audit de l'ensemble du parc d'actifs publics.

> Interface aux couleurs de la charte graphique de la République Togolaise,
> armoiries officielles et photographies réelles de Lomé.

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

| Couche       | Technologies                                             |
|--------------|----------------------------------------------------------|
| Backend      | Laravel 12, PHP 8.2+, Sanctum                            |
| Frontend     | Blade, Vite, CSS Vanilla (charte graphique Togo)         |
| Base         | PostgreSQL                                               |
| Export       | Dompdf (PDF), PhpSpreadsheet (Excel), endroid/qr-code    |
| PWA          | Manifest, Service Worker, file hors-ligne localStorage   |
| Infra        | Docker, Docker Compose, Render (déploiement)             |

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
