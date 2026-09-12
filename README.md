# Déploiement Docker

## Connexion

L'application demande une authentification avant l'accès aux salles et aux réservations.
Un utilisateur peut créer son compte depuis `/register`, puis se connecter depuis `/login`.
Les comptes sont stockés dans la table `users`.

Pour respecter la consigne actuelle, les mots de passe sont stockés sans hachage.
Cette décision est volontairement limitée à cet exercice et doit être remplacée par
`password_hash()` et `password_verify()` avant toute utilisation réelle.

phpMyAdmin est disponible à l'adresse http://localhost:8082. Utilisez l'hôte `db`,
l'utilisateur `gestion_user`, le mot de passe `gestion_password` et la base
`gestion_universitaire`.

## Lancement avec Docker

Copiez `.env.example` vers `.env`, puis démarrez l'application :

```bash
cp .env.example .env
docker compose up --build
```

Les migrations sont exécutées automatiquement au démarrage du conteneur. Pour
charger les salles initiales, exécutez `docker compose exec app php database/seed.php`.

Les tests se lancent avec :

```bash
./vendor/bin/phpunit
```

Les choix d'architecture sont détaillés dans [ARCHITECTURE.md](ARCHITECTURE.md).
La documentation complète du projet est disponible dans
[DOCUMENTATION.md](DOCUMENTATION.md).

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

Pour mettre aussi à jour le tag Docker Hub utilisé comme version courante :

```bash
docker login
./docker-publish.sh V2.0.1 blacksnakesn --latest
```

La commande publie `V2.0.1` et `latest`. Elle doit être relancée pour chaque
nouveau tag si vous publiez manuellement.

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

## Publication sur GitLab

Ajoutez le dépôt GitLab comme remote, puis envoyez la branche et les tags :

```bash
git remote add gitlab https://gitlab.com/<groupe>/<projet>.git
git push -u gitlab feature/authentification
git push gitlab V2.0.1
```

Le fichier `.gitlab-ci.yml` publie automatiquement l'image dans le Container
Registry GitLab lorsqu'un tag est envoyé. Dans GitLab, activez le registre du
projet si nécessaire, puis consultez `Deploy > Container Registry` pour obtenir
l'adresse de l'image.
