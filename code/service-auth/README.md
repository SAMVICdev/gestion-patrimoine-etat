# Service d’authentification

Source centrale des comptes, rôles et jetons Sanctum. L’inscription publique
n’est pas exposée : le premier administrateur national est créé avec la
commande Artisan, puis les autres comptes sont administrés par un
administrateur authentifié.

## Installation locale

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
php artisan auth:create-national-administrator admin@example.test "Administrateur national"
php artisan serve --host=127.0.0.1 --port=8001
```

Le mot de passe est saisi au prompt et doit comporter au moins 12 caractères,
des lettres majuscules/minuscules et des chiffres. Ne jamais le passer comme
argument de commande ni l’écrire dans le dépôt.

## API

| Méthode | Route | Accès | Rôle |
|---|---|---|---|
| `POST` | `/api/login` | Public, limité en fréquence | — |
| `GET` | `/api/user` | Jeton Sanctum | Tout utilisateur |
| `POST` | `/api/logout` | Jeton Sanctum | Tout utilisateur |
| `POST` | `/api/register` | Jeton Sanctum | Administrateur national |
| `PUT` | `/api/users/{user}` | Jeton Sanctum | Administrateur national |

Les jetons expirent après 60 minutes par défaut (`SANCTUM_EXPIRATION`). Le
service patrimoine les valide auprès de ce service avant de traiter les
requêtes web et API.

Depuis `code/`, le démarrage Docker et la séparation des bases PostgreSQL sont
documentés dans [`README.md`](../README.md).

## Tests

```powershell
php artisan test
```
