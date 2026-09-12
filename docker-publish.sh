#!/usr/bin/env bash

set -euo pipefail

if [[ $# -lt 2 || $# -gt 3 ]]; then
    echo "Usage: $0 <tag-git> <dockerhub-utilisateur> [--latest]" >&2
    echo "Exemple: $0 V2.0.1 blacksnakesn --latest" >&2
    exit 2
fi

version="$1"
dockerhub_user="$2"
image="${dockerhub_user}/gestion-salle-universitaire:${version}"
publish_latest="${3:-}"

if [[ -n "${publish_latest}" && "${publish_latest}" != "--latest" ]]; then
    echo "Erreur : troisième argument attendu : --latest" >&2
    exit 2
fi

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
docker push "${image}"

if [[ "${publish_latest}" == "--latest" ]]; then
    latest_image="${dockerhub_user}/gestion-salle-universitaire:latest"
    docker tag "${image}" "${latest_image}"
    echo "Publication de ${latest_image}..."
    docker push "${latest_image}"
fi