#!/bin/bash
set -e

# Aguarda o banco de dados ficar disponível
if [ -n "$WORDPRESS_DB_HOST" ]; then
  host=$(echo $WORDPRESS_DB_HOST | cut -d: -f1)
  port=$(echo $WORDPRESS_DB_HOST | cut -d: -f2)
  if [ -z "$port" ]; then
    port=3306
  fi
  echo "Esperando o banco de dados em $host:$port..."
  while ! nc -z $host $port; do
    sleep 5
  done
fi

# Instala os plugins se ainda não estiverem instalados
wp plugin install jetpack jetpack-boost classic-editor custom-post-type-ui advanced-custom-fields acf-to-rest-api acf-extended table-field-add-on-for-scf-and-acf acf-quick-edit-fields acf-better-search advanced-forms acf-photo-gallery-field admin-columns-acf acf-rgba-color-picker jetpack-protect pages-with-category-and-tag jwt-auth --activate --allow-root --path=/var/www/html

# Executa o entrypoint original
exec docker-entrypoint.sh "$@"
