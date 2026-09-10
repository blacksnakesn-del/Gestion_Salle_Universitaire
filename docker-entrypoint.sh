#!/bin/sh

set -eu

php database/migrate.php

exec "$@"