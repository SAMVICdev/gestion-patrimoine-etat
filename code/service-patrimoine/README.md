# Service patrimoine

Service Laravel chargé de la gestion des biens de l'État. Le dépôt parent
contient également le service d'authentification et le fichier Docker Compose.

## Développement

Depuis `code/`, démarrer les services :

```powershell
docker compose up -d --build
docker compose ps
```

Depuis `code/service-patrimoine/`, exécuter les tests :

```powershell
php artisan test
```

Les variables locales se configurent dans `.env` à partir de `.env.example`.
Ne jamais versionner `.env` ni des identifiants réels.

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
