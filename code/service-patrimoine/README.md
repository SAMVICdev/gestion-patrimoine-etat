# Service patrimoine

Service Laravel chargé de la gestion des biens de l'État. Le dépôt parent
contient également le service d'authentification et le fichier Docker Compose.

## Application web locale

Prérequis : PHP 8.2+, Composer, Node.js et npm. Depuis
`code/service-patrimoine/` :

```powershell
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

Créer au moins un utilisateur autorisé localement avec Tinker :

```powershell
php artisan tinker
```

Puis, dans Tinker :

```php
App\Models\User::create([
    'name' => 'Gestionnaire',
    'email' => 'gestionnaire@example.test',
    'password' => 'choisir-un-mot-de-passe',
]);
```

Le mot de passe est haché par le modèle. Aucun compte ou mot de passe par
défaut n'est fourni. Ouvrir `http://127.0.0.1:8000`, puis se connecter.
Les variables locales se configurent dans `.env`; ne jamais versionner ce
fichier ni des identifiants réels.

Les services Docker et PostgreSQL sont définis dans `../docker-compose.yml`.
Le branchement de ces services sur une base partagée nécessite de configurer
les URL de base et identifiants de chaque service dans leurs environnements.
Ne pas utiliser les valeurs de démonstration de Docker en production.

## Vérification

```powershell
php artisan test
npm run build
```

Le tableau de bord, le registre, les fiches de bien, les catégories et les
services sont protégés par une connexion Laravel. Cette première interface
n'implémente pas encore les rôles métier ni l'authentification centralisée
avec `service-auth`.

## API des biens

Les routes `/api/biens` requièrent une session ou un jeton Laravel Sanctum.
La connexion entre le service d'authentification et ces routes reste à mettre
en place.

| Méthode | Route | Action |
|---|---|---|
| `GET` | `/api/biens` | Liste paginée des biens actifs |
| `POST` | `/api/biens` | Crée un bien |
| `GET` | `/api/biens/{bien}` | Consulte un bien actif |
| `PUT`, `PATCH` | `/api/biens/{bien}` | Modifie un bien |
| `DELETE` | `/api/biens/{bien}` | Archive un bien sans effacer son historique |

Les catégories et services disposent également de routes protégées pour les
opérations `GET` (liste et détail), `POST` et `PUT`/`PATCH` :
`/api/categories` et `/api/services`. Leur nom est obligatoire et unique.
La suppression n'est pas exposée afin de préserver les références utilisées
par les biens.

La liste accepte `categorie_id`, `service_id`, `etat`, `q` et `per_page`
(maximum 100). La recherche `q` porte sur le numéro d'inventaire, la
désignation et la localisation.

À la création, fournir `numero_inventaire`, `designation`, `categorie_id`,
`service_id` et `localisation`. Les identifiants de catégorie et de service
doivent déjà exister. Les autres champs disponibles sont `description`,
`date_acquisition`, `valeur_acquisition`, `source_financement`,
`responsable` et `etat`.

Valeurs possibles pour `etat` : `bon_etat`, `etat_moyen`, `a_reparer`,
`en_reparation`, `hors_service`, `introuvable`. La réponse est une ressource
JSON ; la liste inclut les métadonnées de pagination. La suppression est
logique (`deleted_at`) et les biens archivés ne sont plus renvoyés dans
l'API.
