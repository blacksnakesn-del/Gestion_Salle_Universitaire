#!/usr/bin/env bash

set -euo pipefail

if [[ $# -ne 2 ]]; then
    echo "Usage: $0 <tag-git> <dockerhub-utilisateur>" >&2
    echo "Exemple: $0 V0.12.0 blacksnakesn" >&2
    exit 2
fi

version="$1"
dockerhub_user="$2"
image="${dockerhub_user}/gestion-salle-universitaire:${version}"

if ! git rev-parse --verify --quiet "refs/tags/${version}" >/dev/null; then
    echo "Erreur : le tag Git '${version}' n'existe pas localement." >&2
    echo "Tags disponibles :" >&2
    git tag --list 'V*' --sort=version:refname >&2
    exit 1
fi

if ! git cat-file -e "${version}:Dockerfile" 2>/dev/null; then
    echo "Erreur : le tag '${version}' ne contient pas de Dockerfile." >&2
    exit 1
fi

context="$(mktemp -d)"
cleanup() {
    rm -rf "${context}"
}
trap cleanup EXIT

git archive "${version}" | tar -x -C "${context}"

echo "Construction de ${image} depuis le tag Git ${version}..."
docker build --tag "${image}" "${context}"
echo "Connexion Docker requise avant l'envoi : docker login"
docker push "${image}"