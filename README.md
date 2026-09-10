# Déploiement Docker

## Lancement avec Docker

Copiez `.env.exemple` vers `.env`, puis démarrez l'application :

```bash
cp .env.exemple .env
docker compose up --build
```

L'application est disponible sur [http://localhost:8081](http://localhost:8081).

## Publication manuelle sur Docker Hub

La publication se fait directement avec Docker. Pour publier une version correspondant
à un tag GitHub, connectez-vous à Docker Hub puis construisez l'image avec le même tag :

```bash
export DOCKERHUB_USERNAME=votre_identifiant_dockerhub
export VERSION=V0.12.0

docker login
docker build \
--tag "$DOCKERHUB_USERNAME/gestion-salle-universitaire:$VERSION" \
.
docker push "$DOCKERHUB_USERNAME/gestion-salle-universitaire:$VERSION"
```

Pour construire exactement le contenu d'un tag Git, même si votre répertoire de
travail contient des modifications, utilisez le script fourni :

```bash
chmod +x docker-publish.sh
./docker-publish.sh V0.12.0 blacksnakesn
```

Le script vérifie que le tag existe, exporte son contenu, puis exécute le build et
le push Docker avec le même tag.

Pour publier un tag GitHub existant, remplacez simplement `VERSION`, par exemple
`V0.8.0`, `V0.10.0`, `V0.11.0` ou `V0.12.0`. Les tags `V0.0.0` à `V0.7.0` ne
contiennent pas de `Dockerfile`.

Pour créer une nouvelle version :

```bash
git tag V0.13.0
git push origin V0.13.0
```

Pour utiliser une image publiée avec Compose, définissez `DOCKER_IMAGE` et
`IMAGE_TAG`, puis lancez `docker compose up -d`.
