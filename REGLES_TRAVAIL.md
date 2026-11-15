# Règles de travail — Gestion du patrimoine de l'État

**Version** : 1.0  
**Date** : 14 novembre 2026  
**Responsable** : ADN  
**Statut** : Document officiel de référence

---

## 📋 Table des matières

1. [Vue d'ensemble du projet](#1-vue-densemble-du-projet)
2. [Prérequis techniques](#2-prérequis-techniques)
3. [Structure du projet](#3-structure-du-projet)
4. [Règles Git et versioning](#4-règles-git-et-versioning)
5. [Workflow de développement](#5-workflow-de-développement)
6. [Standards de code](#6-standards-de-code)
7. [Tests obligatoires](#7-tests-obligatoires)
8. [Design et charte graphique](#8-design-et-charte-graphique)
9. [Gestion des environnements](#9-gestion-des-environnements)
10. [Procédures de déploiement](#10-procédures-de-déploiement)
11. [Dépannage courant](#11-dépannage-courant)
12. [Contacts et ressources](#12-contacts-et-ressources)

---

## 1. Vue d'ensemble du projet

### 🎯 Objectif
Application web de gestion du patrimoine public pour une administration nationale (République Togolaise). Le système permet de recenser, affecter, transférer et auditer l'ensemble des biens de l'État.

### 🏗️ Architecture
- **Microservices Laravel 12** : 2 services indépendants (auth + patrimoine)
- **Base de données** : PostgreSQL 15 (production), SQLite (tests)
- **Conteneurisation** : Docker + Docker Compose
- **Frontend** : Blade templates + Vite + CSS vanilla
- **Authentification** : Laravel Sanctum (tokens Bearer)

### 📦 Services

| Service | Port | Responsabilité |
|---------|------|----------------|
| `service-auth` | 8001 | Gestion comptes, mots de passe, jetons |
| `service-patrimoine` | 8002 | Application principale (UI + API) |
| `postgres` | 5432 | Base de données partagée |

---

## 2. Prérequis techniques

### ⚙️ Logiciels obligatoires

| Logiciel | Version minimale | Lien |
|----------|------------------|------|
| **Docker Desktop** | 24.0+ | https://www.docker.com/products/docker-desktop/ |
| **Git** | 2.40+ | https://git-scm.com/downloads |
| **PHP** | 8.2+ | https://www.php.net/downloads |
| **Composer** | 2.6+ | https://getcomposer.org/download/ |
| **Node.js** | 20.x LTS | https://nodejs.org/ |
| **npm** | 10.x | Inclus avec Node.js |

### ✅ Vérification de l'installation

```powershell
# Vérifier les versions installées
docker --version          # Docker version 24.0+
git --version            # git version 2.40+
php --version            # PHP 8.2+
composer --version       # Composer version 2.6+
node --version           # v20.x
npm --version            # 10.x
```

### 🔧 Extensions PHP requises

```ini
# Vérifier avec : php -m
- pdo_pgsql
- pdo_sqlite
- mbstring
- openssl
- curl
- json
- xml
- zip
- gd
```

---

## 3. Structure du projet

```
gestion-patrimoine-etat/
├── code/                           # Code source des services
│   ├── docker-compose.yml          # Orchestration Docker
│   ├── .env.example                # Variables d'environnement exemple
│   ├── service-auth/               # Microservice authentification
│   │   ├── app/
│   │   │   ├── Http/Controllers/Api/AuthController.php
│   │   │   ├── Models/User.php
│   │   │   └── Console/Commands/CreateNationalAdministrator.php
│   │   ├── database/migrations/
│   │   ├── routes/api.php
│   │   ├── .env
│   │   ├── composer.json
│   │   └── Dockerfile
│   │
│   └── service-patrimoine/         # Microservice principal
│       ├── app/
│       │   ├── Features/           # Organisation par fonctionnalité
│       │   │   ├── Biens/
│       │   │   ├── Categories/
│       │   │   ├── Services/
│       │   │   ├── Affectations/
│       │   │   ├── Inventaires/
│       │   │   ├── Rapports/
│       │   │   └── Parametres/
│       │   └── Http/
│       │       ├── Controllers/Web/
│       │       └── Middleware/
│       ├── resources/
│       │   ├── views/              # Templates Blade
│       │   │   ├── auth/
│       │   │   ├── layouts/
│       │   │   ├── biens/
│       │   │   ├── dashboard/
│       │   │   └── parametres/
│       │   ├── css/app.css         # Styles (charte 2017)
│       │   └── js/app.js
│       ├── public/
│       │   └── images/
│       │       ├── armoiries_togo.png
│       │       └── charte/
│       ├── database/migrations/
│       ├── routes/
│       │   ├── web.php
│       │   └── api.php
│       ├── .env
│       ├── composer.json
│       ├── package.json
│       └── Dockerfile
│
├── docs/                           # Documentation (NE PAS MODIFIER)
│   └── 2017-Charte_graphique_Republique_TogolaiseA4_-compressed.pdf
│
├── DOCUMENTATION.md                # Documentation technique complète
├── HISTORIQUE.local.md             # Journal de travail (GIT IGNORED)
├── GUIDE_PROJET.local.md           # Guide de contribution (GIT IGNORED)
├── REGLES_TRAVAIL.md               # Ce fichier (À LIRE OBLIGATOIREMENT)
└── .gitignore
```

---

## 4. Règles Git et versioning

### 🌿 Stratégie de branches

```
main                    ← Production (protégée)
  ├── develop          ← Développement (intégration)
  ├── feature/xxx      ← Nouvelles fonctionnalités
  ├── bugfix/xxx       ← Corrections de bugs
  └── hotfix/xxx       ← Corrections urgentes production
```

### ⚠️ RÈGLES STRICTES

#### 🚫 INTERDIT

1. **NE JAMAIS pusher directement sur `main`**
2. **NE JAMAIS modifier l'historique Git** (`git rebase`, `git reset --hard`)
3. **NE JAMAIS commiter des secrets** (.env, mots de passe, clés API)
4. **NE JAMAIS forcer un push** (`git push --force`)
5. **NE JAMAIS commiter des fichiers locaux** (HISTORIQUE.local.md, GUIDE_PROJET.local.md)
6. **NE JAMAIS modifier la charte graphique** (docs/2017-Charte_graphique_*.pdf)

#### ✅ OBLIGATOIRE

1. **TOUJOURS créer une branche** pour chaque tâche
2. **TOUJOURS tester** avant de commiter
3. **TOUJOURS utiliser des messages de commit explicites**
4. **TOUJOURS synchroniser avec `main`** avant de créer une branche
5. **TOUJOURS exécuter les linters** (Pint, ESLint)
6. **TOUJOURS mettre à jour HISTORIQUE.local.md** (journal personnel)

### 📝 Format des messages de commit

**Convention** : [Conventional Commits](https://www.conventionalcommits.org/)

```
<type>(<scope>): <sujet>

<description détaillée>

<footer>
```

**Types autorisés** :

| Type | Usage | Exemple |
|------|-------|---------|
| `feat` | Nouvelle fonctionnalité | `feat(biens): ajouter recherche par numéro inventaire` |
| `fix` | Correction de bug | `fix(auth): corriger validation email` |
| `refactor` | Refactoring sans changement fonctionnel | `refactor(services): extraire logique métier` |
| `style` | Changements de style/formatage | `style(css): appliquer Pint sur app.css` |
| `docs` | Documentation uniquement | `docs(readme): mettre à jour installation Docker` |
| `test` | Ajout/modification de tests | `test(biens): ajouter tests unitaires création` |
| `chore` | Tâches techniques | `chore(deps): mettre à jour Laravel 12.1` |

**Exemples de bons commits** :

```bash
feat(ui): appliquer charte graphique officielle 2017 République Togolaise

- Intégrer armoiries officielles extraites de la charte 2017
- Appliquer couleurs nationales: Vert #006A4A, Rouge #D21034, Jaune #FFCE00
- Redesign page de connexion avec identité institutionnelle
- Améliorer toutes les interfaces (sidebar, boutons, formulaires)
- Design responsive avec header mobile institutionnel

Refs: #12
```

```bash
fix(inventaire): corriger calcul total des biens actifs

Le calcul incluait par erreur les biens archivés.
Ajout d'un filtre WHERE archived_at IS NULL dans la requête.

Fixes: #45
```

### 🔄 Workflow standard

```bash
# 1. Se placer sur main et synchroniser
git checkout main
git pull origin main

# 2. Créer une branche de travail
git checkout -b feature/ma-fonctionnalite

# 3. Travailler et commiter régulièrement
git add fichier1.php fichier2.blade.php
git commit -m "feat(scope): description"

# 4. Pousser la branche
git push origin feature/ma-fonctionnalite

# 5. Créer une Pull Request sur GitHub
# (via interface web GitHub)

# 6. Après validation, merger dans main
# (via GitHub ou en local après review)

# 7. Supprimer la branche locale
git branch -d feature/ma-fonctionnalite
```

---

## 5. Workflow de développement

### 🚀 Démarrage du projet

#### Première fois (setup initial)

```powershell
# 1. Cloner le dépôt
git clone https://github.com/SAMVICdev/gestion-patrimoine-etat.git
cd gestion-patrimoine-etat

# 2. Copier les fichiers .env
cd code
Copy-Item .env.example .env
cd service-auth
Copy-Item .env.example .env
cd ../service-patrimoine
Copy-Item .env.example .env
cd ..

# 3. Éditer code/.env et définir le mot de passe PostgreSQL
# POSTGRES_PASSWORD=votre_mot_de_passe_securise

# 4. Démarrer Docker Compose
docker compose up -d

# 5. Attendre que les services soient prêts (1-2 minutes)
docker compose ps

# 6. Exécuter les migrations
docker compose exec service-auth php artisan migrate
docker compose exec service-patrimoine php artisan migrate

# 7. Créer un administrateur national
docker compose exec service-auth php artisan auth:create-national-administrator admin@test.tg "Admin National"

# 8. Builder les assets frontend (service-patrimoine)
docker compose exec service-patrimoine npm install
docker compose exec service-patrimoine npm run build

# 9. Accéder à l'application
# → http://localhost:8002/connexion
```

#### Démarrage quotidien

```powershell
# Démarrer les services Docker
cd code
docker compose up -d

# Vérifier que tout fonctionne
docker compose ps

# Accéder à l'application
# → http://localhost:8002
```

#### Arrêt des services

```powershell
# Arrêter les conteneurs (conserve les données)
docker compose stop

# Arrêter ET supprimer les conteneurs (conserve les données)
docker compose down

# ⚠️ Supprimer TOUT (conteneurs + volumes/données)
# À utiliser SEULEMENT pour reset complet
docker compose down -v
```

### 🔧 Développement quotidien

#### Travailler sur le backend (PHP/Laravel)

```powershell
cd code

# Les modifications PHP sont prises en compte immédiatement
# (Docker monte les fichiers en volume)

# Exécuter les commandes Artisan
docker compose exec service-patrimoine php artisan route:list
docker compose exec service-patrimoine php artisan make:controller MonController

# Créer une migration
docker compose exec service-patrimoine php artisan make:migration create_table_name

# Exécuter les migrations
docker compose exec service-patrimoine php artisan migrate

# Rollback migration
docker compose exec service-patrimoine php artisan migrate:rollback
```

#### Travailler sur le frontend (CSS/JS/Blade)

```powershell
cd code

# Mode développement (watch - recompile automatiquement)
docker compose exec service-patrimoine npm run dev

# Build production
docker compose exec service-patrimoine npm run build

# Les vues Blade sont rechargées automatiquement
# Pas besoin de redémarrer Docker
```

#### Installer une dépendance

```powershell
# Dépendance PHP (Composer)
docker compose exec service-patrimoine composer require vendor/package

# Dépendance JavaScript (npm)
docker compose exec service-patrimoine npm install package-name
```

---

## 6. Standards de code

### 📐 PHP (Laravel)

#### Style de code : PSR-12

**Outil** : Laravel Pint (inclus dans le projet)

```powershell
# Vérifier le code
docker compose exec service-patrimoine ./vendor/bin/pint --test

# Formater automatiquement
docker compose exec service-patrimoine ./vendor/bin/pint
```

**Règles spécifiques** :

```php
<?php

namespace App\Features\Biens\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle représentant un bien du patrimoine.
 */
class Bien extends Model
{
    // 1. Propriétés de classe d'abord
    protected $fillable = ['code_inventaire', 'designation'];
    
    // 2. Puis méthodes publiques
    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }
    
    // 3. Puis méthodes protected/private
    protected function getFullNameAttribute(): string
    {
        return "{$this->code_inventaire} - {$this->designation}";
    }
}
```

**Conventions de nommage** :

| Élément | Convention | Exemple |
|---------|-----------|---------|
| Classes | PascalCase | `BienController`, `UserModel` |
| Méthodes | camelCase | `getBiens()`, `createUser()` |
| Variables | camelCase / snake_case | `$userName`, `$code_inventaire` |
| Constantes | UPPER_SNAKE_CASE | `MAX_UPLOAD_SIZE` |
| Routes | kebab-case | `/biens/creer`, `/inventaire-physique` |
| Fichiers Blade | kebab-case | `login.blade.php`, `biens-index.blade.php` |

### 🎨 CSS

**Règles** :

1. **Pas de framework CSS** (pas de Tailwind inline, Bootstrap, etc.)
2. **Fichier unique** : `resources/css/app.css`
3. **Variables CSS** pour les couleurs de la charte
4. **Classes BEM-like** pour la structure

```css
/* ✅ BON */
:root {
    --togo-vert: #006A4A;
    --togo-rouge: #D21034;
    --togo-jaune: #FFCE00;
}

.login-card {
    background: var(--togo-vert);
}

.login-card__title {
    color: var(--togo-jaune);
}

/* ❌ MAUVAIS - pas de Tailwind inline */
<div class="bg-green-500 text-yellow-400">...</div>
```

### 🖼️ HTML/Blade

```blade
{{-- ✅ BON - Commentaire descriptif --}}
<section class="login-card">
    {{-- En-tête institutionnel --}}
    <header class="login-card__header">
        <img src="{{ asset('images/armoiries_togo.png') }}" 
             alt="Armoiries de la République Togolaise">
    </header>
    
    {{-- Formulaire de connexion --}}
    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        {{-- Champs --}}
    </form>
</section>

{{-- ❌ MAUVAIS - Blade non indenté --}}
<div>
@if($condition)
<p>Texte</p>
@endif
</div>
```

### 📊 Base de données

**Conventions de nommage** :

| Élément | Convention | Exemple |
|---------|-----------|---------|
| Tables | snake_case pluriel | `biens`, `users`, `parametres_plateforme` |
| Colonnes | snake_case | `code_inventaire`, `created_at` |
| Clés étrangères | `{table_singulier}_id` | `categorie_id`, `service_id` |
| Indexes | `idx_{table}_{colonne}` | `idx_biens_code_inventaire` |

**Migrations** :

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biens', function (Blueprint $table) {
            // 1. ID toujours en premier
            $table->id();
            
            // 2. Colonnes métier
            $table->string('code_inventaire', 50)->unique();
            $table->string('designation');
            $table->text('description')->nullable();
            
            // 3. Clés étrangères
            $table->foreignId('categorie_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->nullOnDelete();
            
            // 4. Colonnes système
            $table->enum('etat', ['bon', 'etat_moyen', 'a_reparer'])->default('bon');
            $table->softDeletes(); // archived_at
            $table->timestamps();  // created_at, updated_at
            
            // 5. Indexes
            $table->index('code_inventaire');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biens');
    }
};
```

---

## 7. Tests obligatoires

### ⚡ Avant CHAQUE commit

```powershell
# 1. Linter PHP
docker compose exec service-patrimoine ./vendor/bin/pint --test

# 2. Tests unitaires et feature
docker compose exec service-patrimoine php artisan test

# 3. Vérifier qu'il n'y a pas d'espaces en fin de ligne
git diff --check

# 4. Build assets (vérifier qu'il n'y a pas d'erreur)
docker compose exec service-patrimoine npm run build
```

### 🧪 Écrire des tests

**Localisation** : `tests/Feature/` et `tests/Unit/`

```php
<?php

namespace Tests\Feature\Biens;

use Tests\TestCase;
use App\Features\Biens\Models\Bien;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BienControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_bien(): void
    {
        $response = $this->post('/api/biens', [
            'code_inventaire' => 'INV-2026-001',
            'designation' => 'Ordinateur portable',
            'categorie_id' => 1,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('biens', [
            'code_inventaire' => 'INV-2026-001',
        ]);
    }
}
```

**Commandes** :

```powershell
# Exécuter tous les tests
docker compose exec service-patrimoine php artisan test

# Tests spécifiques
docker compose exec service-patrimoine php artisan test --filter=BienControllerTest

# Avec couverture
docker compose exec service-patrimoine php artisan test --coverage
```

---

## 8. Design et charte graphique

### 🇹🇬 Charte graphique officielle 2017

**Document de référence** : `docs/2017-Charte_graphique_Republique_TogolaiseA4_-compressed.pdf`

⚠️ **CE DOCUMENT EST SACRÉ - NE JAMAIS LE MODIFIER**

### 🎨 Couleurs officielles

```css
:root {
    /* Couleurs nationales République Togolaise */
    --togo-vert: #006A4A;        /* Pantone 355 - CMYK 93/0/100/0 */
    --togo-vert-sombre: #004d35;
    --togo-vert-clair: #e6f4ee;
    
    --togo-rouge: #D21034;        /* Pantone 032 - CMYK 0/90/76/0 */
    --togo-rouge-clair: #fce8ec;
    
    --togo-jaune: #FFCE00;        /* Pantone 109 - CMYK 0/5/100/0 */
    --togo-jaune-clair: #fff9d6;
}
```

### 🖼️ Ressources graphiques

| Ressource | Emplacement | Usage |
|-----------|-------------|-------|
| Armoiries PNG | `public/images/armoiries_togo.png` | Logo institutionnel (pages de connexion, headers) |
| Drapeau SVG | Inline dans `login.blade.php` | Identité visuelle |
| Polices | Google Fonts | DM Sans (corps), Playfair Display (titres) |

### 📏 Règles de design

#### ✅ À FAIRE

1. **Toujours utiliser les couleurs officielles** de la charte
2. **Conserver les armoiries visibles** sur toutes les pages publiques
3. **Respecter la hiérarchie typographique** : titres en Playfair Display, corps en DM Sans
4. **Design responsive** : mobile-first
5. **Accessibilité** : contraste suffisant (WCAG AA minimum)

#### ❌ À NE PAS FAIRE

1. **Ne jamais modifier les armoiries** (rotation, déformation, recoloriage)
2. **Ne pas utiliser d'autres couleurs** pour les éléments institutionnels
3. **Ne pas ajouter de frameworks CSS** (Tailwind, Bootstrap) sans validation
4. **Ne pas utiliser de polices fantaisistes**

### 🎭 Composants UI

Les composants réutilisables sont dans `resources/css/app.css` :

- `.login-card` : Carte de connexion
- `.button-primary` : Bouton principal (vert)
- `.metric-card` : Carte métrique du dashboard
- `.panel` : Panneau de contenu
- `.data-table` : Tableau de données

**Utilisation** :

```blade
{{-- ✅ Réutiliser les classes existantes --}}
<button class="button-primary">
    <span>Créer un bien</span>
</button>

{{-- ❌ Ne pas créer de nouvelles classes inline --}}
<button style="background: green; padding: 10px;">Bouton</button>
```

---

## 9. Gestion des environnements

### 🔐 Fichiers .env

**⚠️ ATTENTION : Les fichiers .env contiennent des secrets - NE JAMAIS LES COMMITER**

#### Fichier `code/.env` (Docker Compose)

```env
# Mot de passe PostgreSQL (partagé par tous les services)
POSTGRES_PASSWORD=votre_mot_de_passe_tres_securise
```

#### Fichier `code/service-auth/.env`

```env
APP_NAME="Service Auth"
APP_ENV=local
APP_KEY=base64:...  # Généré par: php artisan key:generate
APP_DEBUG=true
APP_TIMEZONE=Africa/Lome
APP_URL=http://localhost:8001

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=auth_service
DB_USERNAME=admin
DB_PASSWORD=${POSTGRES_PASSWORD}  # Référence au mot de passe Docker

SESSION_DRIVER=database
```

#### Fichier `code/service-patrimoine/.env`

```env
APP_NAME="Gestion Patrimoine État"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_TIMEZONE=Africa/Lome
APP_URL=http://localhost:8002

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=patrimoine_etat
DB_USERNAME=admin
DB_PASSWORD=${POSTGRES_PASSWORD}

# URL du service d'authentification
AUTH_SERVICE_URL=http://service-auth:8000

VITE_APP_NAME="${APP_NAME}"
```

### 🌍 Environnements

| Environnement | Branche Git | URL | DB |
|---------------|-------------|-----|-----|
| **Local** | feature/*, develop | localhost:8002 | PostgreSQL Docker |
| **Staging** | develop | staging.patrimoine.tg | PostgreSQL distant |
| **Production** | main | patrimoine.tg | PostgreSQL haute disponibilité |

---

## 10. Procédures de déploiement

### 📦 Déploiement local (tests)

```powershell
# 1. Pull dernières modifications
git pull origin main

# 2. Reconstruire les images Docker
docker compose build --no-cache

# 3. Redémarrer les services
docker compose up -d

# 4. Exécuter les migrations
docker compose exec service-auth php artisan migrate
docker compose exec service-patrimoine php artisan migrate

# 5. Builder les assets
docker compose exec service-patrimoine npm run build

# 6. Clear cache
docker compose exec service-patrimoine php artisan cache:clear
docker compose exec service-patrimoine php artisan config:clear
docker compose exec service-patrimoine php artisan view:clear
```

### 🚀 Déploiement production

**⚠️ PROCÉDURE CRITIQUE - À SUIVRE STRICTEMENT**

#### Pré-déploiement (checklist)

- [ ] Tous les tests passent (`php artisan test`)
- [ ] Code formaté (`./vendor/bin/pint`)
- [ ] Build assets sans erreur (`npm run build`)
- [ ] Migration testée en local
- [ ] Backup base de données effectué
- [ ] Changelog mis à jour
- [ ] Tag Git créé (ex: `v1.2.0`)

#### Déploiement

```bash
# 1. Se connecter au serveur de production
ssh user@patrimoine.tg

# 2. Aller dans le répertoire du projet
cd /var/www/gestion-patrimoine-etat

# 3. Mettre l'application en maintenance
php artisan down

# 4. Pull dernière version
git pull origin main

# 5. Installer dépendances
composer install --no-dev --optimize-autoloader
npm install --production
npm run build

# 6. Exécuter les migrations
php artisan migrate --force

# 7. Clear et recréer les caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Redémarrer les workers (si queues)
php artisan queue:restart

# 9. Remettre l'application en ligne
php artisan up

# 10. Vérifier que tout fonctionne
curl -I https://patrimoine.tg
```

#### Post-déploiement

- [ ] Vérifier les logs (`storage/logs/laravel.log`)
- [ ] Tester les fonctionnalités critiques
- [ ] Monitoring actif pendant 1h

---

## 11. Dépannage courant

### 🔧 Problèmes fréquents et solutions

#### 1. Docker : "Cannot connect to the Docker daemon"

**Cause** : Docker Desktop n'est pas démarré

**Solution** :
```powershell
# Démarrer Docker Desktop manuellement
# Ou vérifier qu'il est bien lancé
docker ps
```

#### 2. Erreur : "could not find driver (pdo_pgsql)"

**Cause** : Extension PostgreSQL manquante dans le conteneur

**Solution** :
```powershell
# Reconstruire les images Docker
docker compose build --no-cache
docker compose up -d
```

#### 3. Page blanche / Erreur 500

**Causes possibles** :
- Fichier `.env` manquant
- `APP_KEY` non généré
- Permissions fichiers

**Solutions** :
```powershell
# Vérifier les logs
docker compose logs service-patrimoine

# Regénérer la clé
docker compose exec service-patrimoine php artisan key:generate

# Vérifier permissions storage
docker compose exec service-patrimoine chmod -R 775 storage bootstrap/cache
```

#### 4. Assets CSS/JS ne chargent pas

**Cause** : Build Vite non exécuté ou manifest manquant

**Solution** :
```powershell
# Rebuild assets
docker compose exec service-patrimoine npm run build

# Vérifier que public/build existe
docker compose exec service-patrimoine ls -la public/build
```

#### 5. "Base de données introuvable"

**Cause** : Migrations non exécutées ou bases non créées

**Solution** :
```powershell
# Vérifier que les bases existent
docker compose exec postgres psql -U admin -l

# Recréer les bases (si besoin)
docker compose exec postgres psql -U admin -c "CREATE DATABASE auth_service;"
docker compose exec postgres psql -U admin -c "CREATE DATABASE patrimoine_etat;"

# Exécuter les migrations
docker compose exec service-auth php artisan migrate
docker compose exec service-patrimoine php artisan migrate
```

#### 6. Git : "Your local changes would be overwritten"

**Cause** : Fichiers modifiés localement en conflit avec le pull

**Solution** :
```powershell
# Option 1 : Stash (sauvegarder temporairement)
git stash
git pull origin main
git stash pop

# Option 2 : Commit d'abord
git add .
git commit -m "WIP: travail en cours"
git pull origin main
```

#### 7. Erreur de permissions Windows

**Cause** : Certains fichiers nécessitent des permissions spécifiques

**Solution** :
```powershell
# Exécuter PowerShell en administrateur
# Puis dans le conteneur :
docker compose exec service-patrimoine chmod -R 775 storage bootstrap/cache
```

### 📞 Contacts d'urgence

| Problème | Contact | Disponibilité |
|----------|---------|---------------|
| Bug critique production | ADN | 24/7 |
| Question technique | ADN | Lun-Ven 9h-18h |
| Problème serveur/infra | DevOps Team | 24/7 |

---

## 12. Contacts et ressources

### 👥 Équipe

| Rôle | Nom | Email | Responsabilités |
|------|-----|-------|-----------------|
| **Lead Developer** | ADN | dev@gestion-patrimoine.tg | Architecture, code review, décisions techniques |
| **DevOps** | À définir | - | Infrastructure, CI/CD, monitoring |
| **Product Owner** | À définir | - | Spécifications, validation fonctionnelle |

### 📚 Ressources externes

| Ressource | Lien | Usage |
|-----------|------|-------|
| Laravel 12 Docs | https://laravel.com/docs/12.x | Documentation officielle |
| PHP.net | https://www.php.net/manual/fr/ | Référence PHP |
| PostgreSQL Docs | https://www.postgresql.org/docs/15/ | Documentation base de données |
| Docker Docs | https://docs.docker.com/ | Conteneurisation |
| Conventional Commits | https://www.conventionalcommits.org/ | Format messages commit |
| Charte graphique Togo | `docs/2017-Charte_graphique_*.pdf` | Design système |

### 🔗 Liens utiles

| Lien | Description |
|------|-------------|
| **GitHub** | https://github.com/SAMVICdev/gestion-patrimoine-etat |
| **Local (dev)** | http://localhost:8002 |
| **Staging** | https://staging.patrimoine.tg (à configurer) |
| **Production** | https://patrimoine.tg (à configurer) |

---

## 📝 Changelog de ce document

| Date | Version | Auteur | Modifications |
|------|---------|--------|---------------|
| 2026-11-14 | 1.0 | ADN | Création initiale du document |

---

## ✅ Acceptation

**En travaillant sur ce projet, je m'engage à :**

- ✅ Avoir lu et compris ce document dans son intégralité
- ✅ Respecter toutes les règles énoncées
- ✅ Demander de l'aide en cas de doute
- ✅ Documenter mes modifications dans HISTORIQUE.local.md
- ✅ Ne jamais compromettre la sécurité ou l'intégrité du code
- ✅ Respecter la charte graphique officielle de la République Togolaise

**Nom** : ___________________________  
**Date** : ___________________________  
**Signature** : ___________________________

---

**FIN DU DOCUMENT**

*Pour toute question ou suggestion d'amélioration de ce document, contacter ADN.*
