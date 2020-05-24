#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
  set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'bin/console' ]; then
  until bin/console doctrine:query:sql "select 1" >/dev/null 2>&1; do
    (echo >&2 "Waiting for MySQL to be ready...")
    sleep 1
  done

  if [ "$(ls -A src/Migrations/*.php 2>/dev/null)" ]; then
    bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
    bin/console doctrine:schema:update --force --complete --dump-sql
  fi
fi

exec docker-php-entrypoint "$@"
