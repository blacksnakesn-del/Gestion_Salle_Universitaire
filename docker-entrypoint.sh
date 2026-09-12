#!/bin/sh

set -eu

port="${PORT:-80}"
case "${port}" in
	''|*[!0-9]*)
		echo "PORT doit être un nombre." >&2
		exit 1
		;;
esac

if [ "${port}" != "80" ]; then
	sed -ri "s/Listen 80/Listen ${port}/" /etc/apache2/ports.conf
	sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${port}>/" /etc/apache2/sites-enabled/000-default.conf
fi

php database/migrate.php

exec "$@"