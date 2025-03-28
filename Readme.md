# Installation
## Local
Launch `docker compose --env-file .env.dev -f docker-compose.dev.yml up --build`

## Prod
`docker compose --env-file /var/projects/env_files/backend.alexandrebonnin.fr.env -f docker-compose.prod.yml up --build`