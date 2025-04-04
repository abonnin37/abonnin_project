# Installation
## Local
Launch `docker compose --env-file .env.dev -f docker-compose.dev.yml up --build`

## Prod
Lancer le projet sur l'environnement de production :<br>
`docker compose --env-file /var/projects/env_files/backend.alexandrebonnin.fr.env -f docker-compose.prod.yml up -d --build`

Arrêter le container sur l'environnement de production :<br>
`docker compose --env-file /var/projects/env_files/backend.alexandrebonnin.fr.env -f docker-compose.prod.yml down`