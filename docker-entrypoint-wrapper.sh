#!/bin/bash

set -e

export WP_CLI_ALLOW_ROOT=1
WP_CMD="wp --allow-root --skip-plugins --skip-themes --path=/var/www/html --url=dev.local"

echo "⏳ Aguardando o banco de dados em ${WORDPRESS_DB_HOST:-db}:3306..."

# Extrai host e porta do DB
if [ -n "$WORDPRESS_DB_HOST" ]; then
  if echo "$WORDPRESS_DB_HOST" | grep -q ":"; then
    host=$(echo "$WORDPRESS_DB_HOST" | cut -d: -f1)
    port=$(echo "$WORDPRESS_DB_HOST" | cut -d: -f2)
  else
    host="$WORDPRESS_DB_HOST"
    port=3306
  fi

  # Espera o banco de dados ficar acessível
  until nc -z "$host" "$port"; do
    echo "⏳ Aguardando $host:$port..."
    sleep 5
  done
fi

echo "✅ Banco de dados disponível em $host:$port"

# Verifica se o WordPress multisite está instalado
if ! $WP_CMD core is-installed >/dev/null 2>&1; then
  echo "⚠️ WordPress ainda não está instalado. Instalando em modo multisite..."

  $WP_CMD core multisite-install \
    --url="dev.local" \
    --title="Thabatta Apolinário Advocacia" \
    --admin_user="admin" \
    --admin_password="2212" \
    --admin_email="lfelipeapo@gmail.com" \
    --skip-email

  echo "✅ WordPress multisite instalado com sucesso!"
else
  echo "✅ WordPress já está instalado, seguindo com os plugins e tema..."
fi

# Plugins essenciais
PLUGINS=(
  jetpack
  jetpack-boost
  jetpack-protect
  classic-editor
  custom-post-type-ui
  advanced-custom-fields
  acf-to-rest-api
  acf-extended
  advanced-custom-fields-table-field
  acf-quickedit-fields
  acf-better-search
  advanced-forms
  navz-photo-gallery
  admin-columns-for-acf-fields
  acf-rgba-color-picker
  pages-with-category-and-tag
  jwt-auth
  woocommerce
)

echo "🔌 Instalando e ativando plugins..."
for plugin in "${PLUGINS[@]}"; do
  $WP_CMD plugin install "$plugin" --activate || echo "⚠️ Erro ao instalar plugin: $plugin"
done

# Ativar tema
echo "🎨 Ativando o tema thabatta-adv-theme..."
$WP_CMD theme activate thabatta-adv-theme

# Executar entrypoint padrão do container
echo "🚀 Iniciando WordPress com entrypoint oficial..."
exec docker-entrypoint.sh "$@"
