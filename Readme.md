# Installation
## Local
Launch `docker compose --env-file .env -f docker-compose.dev.yml up --build`

## Prod
Avant toute chose, placez le fichier d'environnement .env.dev à l'emplacement ci-après sur votre serveur : `/var/projects/env_files/backend.alexandrebonnin.fr.env`.
Remplacer les variables d'environnement en fonction de votre contexte de production.

Commande pour lancer le projet sur l'environnement de production :<br>
`docker compose --env-file /var/projects/env_files/backend.alexandrebonnin.fr.env -f docker-compose.prod.yml up -d --build`

Commande pour arrêter le container sur l'environnement de production :<br>
`docker compose --env-file /var/projects/env_files/backend.alexandrebonnin.fr.env -f docker-compose.prod.yml down`