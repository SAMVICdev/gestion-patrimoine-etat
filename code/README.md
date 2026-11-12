# Gestion du patrimoine de l’État

## Démarrage avec Docker

1. Copier `.env.example` vers `.env` dans ce dossier et choisir un mot de passe
   PostgreSQL aléatoire.
2. Copier `service-auth/.env.example` et `service-patrimoine/.env.example`
   vers leurs `.env`; générer une `APP_KEY` distincte avec
   `php artisan key:generate` dans chaque service.
3. Construire et démarrer les services :

   ```powershell
   docker compose up -d --build
   ```

4. Appliquer les migrations séparément :

   ```powershell
   docker compose exec service-auth php artisan migrate --force
   docker compose exec service-patrimoine php artisan migrate --force
   ```

5. Créer le premier administrateur national :

   ```powershell
   docker compose exec service-auth php artisan auth:create-national-administrator admin@example.test "Administrateur national"
   ```

   Le mot de passe est demandé de façon interactive et n’est pas écrit dans la
   commande. Ne pas utiliser d’adresse ou de mot de passe d’exemple en
   production.

L’interface est exposée sur le port `8002`, le service d’authentification sur
`8001`. Le fichier Compose crée deux bases PostgreSQL isolées à l’initialisation
du volume. Si le volume `pgdata` existait avant cette séparation, effectuer une
migration et une sauvegarde planifiées avant tout changement : ne pas supprimer
le volume pour forcer la recréation.

## Contrôle

```powershell
docker compose ps
docker compose logs --tail 100 service-auth service-patrimoine postgres
```

Pour la procédure locale sans Docker, les tests et les routes API, consulter
[`service-patrimoine/README.md`](./service-patrimoine/README.md).
