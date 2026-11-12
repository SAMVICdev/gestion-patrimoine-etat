# Service patrimoine

Application web et API de gestion des biens, des affectations et des inventaires.

## Développement local

Prérequis : PHP 8.2+, Composer, Node.js et npm. Depuis `code/`, installer les
dépendances et créer la configuration locale des deux services :

```powershell
Set-Location service-auth
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
Start-Process php -ArgumentList 'artisan serve --host=127.0.0.1 --port=8001'

Set-Location ..\service-patrimoine
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
npm run build
php artisan serve --host=127.0.0.1 --port=8002
```

Le service patrimoine utilise `AUTH_SERVICE_URL=http://127.0.0.1:8001` en
développement local. Les deux `.env` et bases SQLite restent locales et ne
doivent pas être versionnées.

Créer le premier administrateur national dans `service-auth` :

```powershell
php artisan auth:create-national-administrator admin@example.test "Administrateur national"
```

La commande demande un mot de passe robuste sans l’afficher. Aucun compte de
production ou mot de passe par défaut n’est fourni. Ensuite, les gestionnaires
et agents se créent depuis l’écran « Utilisateurs & habilitations ».

## Docker et PostgreSQL

Voir le [guide de démarrage Docker](../README.md). Il utilise deux bases
distinctes dans PostgreSQL (`auth_service` et `patrimoine_etat`) pour garder
les migrations et les données de chaque microservice isolées. Les bases sont
créées au premier démarrage du volume PostgreSQL.

## Vérification

Depuis `service-auth/` et `service-patrimoine/` :

```powershell
php artisan test
```

Depuis `service-patrimoine/` :

```powershell
npm run build
php vendor\bin\pint --test app routes tests
```

## Authentification et routes

Le service `service-auth` est la source des comptes, mots de passe, rôles et
jetons Sanctum. Le service patrimoine valide le jeton centralisé sur les
requêtes web et API et synchronise uniquement l’identité nécessaire à ses
références locales. Si le service d’authentification est indisponible, l’accès
est refusé et l’application renvoie une erreur explicite.

Les routes `/api/biens`, `/api/categories` et `/api/services` nécessitent un
jeton `Authorization: Bearer …` obtenu par `POST /api/login` sur le service
d’authentification. Les écritures sont réservées aux rôles autorisés et
filtrées selon le périmètre de service.
