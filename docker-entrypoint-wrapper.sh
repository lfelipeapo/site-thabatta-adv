#!/bin/bash
set -e

# Aguarda o banco de dados ficar disponível
if [ -n "$WORDPRESS_DB_HOST" ]; then
  if echo "$WORDPRESS_DB_HOST" | grep -q ":"; then
    host=$(echo "$WORDPRESS_DB_HOST" | cut -d: -f1)
    port=$(echo "$WORDPRESS_DB_HOST" | cut -d: -f2)
  else
    host="$WORDPRESS_DB_HOST"
    port=3306
  fi
  echo "Esperando o banco de dados em $host:$port..."
  while ! nc -z "$host" "$port"; do
    sleep 5
  done
fi

# Checa se o WordPress já está instalado
if ! wp core is-installed --path=/var/www/html; then
  echo "WordPress não está instalado. Executando wp core install..."
  wp core install \
    --url="$SERVER_NAME" \
    --title="Thabatta Apolinário Advocacia" \
    --admin_user="admin" \
    --admin_password="2212" \
    --admin_email="lfelipeapo@gmail.com" \
    --skip-email \
    --allow-root \
    --path=/var/www/html
fi

# Instalar Jetpack apenas se não existir
if ! wp plugin is-installed jetpack --allow-root --path=/var/www/html; then
    wp plugin install jetpack --activate --allow-root --path=/var/www/html
fi

# Instalar Jetpack Boost e Protect apenas se não existirem
if ! wp plugin is-installed jetpack-boost --allow-root --path=/var/www/html; then
    wp plugin install jetpack-boost --activate --allow-root --path=/var/www/html
fi

if ! wp plugin is-installed jetpack-protect --allow-root --path=/var/www/html; then
    wp plugin install jetpack-protect --activate --allow-root --path=/var/www/html
fi

# Instala os demais plugins
echo "Instalando outros plugins..."
wp plugin install classic-editor custom-post-type-ui advanced-custom-fields acf-to-rest-api acf-extended advanced-custom-fields-table-field acf-quickedit-fields acf-better-search advanced-forms navz-photo-gallery admin-columns-for-acf-fields acf-rgba-color-picker pages-with-category-and-tag jwt-auth \
  --activate --allow-root --path=/var/www/html

# Executa o entrypoint original
exec docker-entrypoint.sh "$@"
