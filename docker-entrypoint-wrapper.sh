#!/bin/bash

set -e

export WP_CLI_ALLOW_ROOT=1
WP_CMD="wp --allow-root --skip-plugins --skip-themes --path=/var/www/html --url=dev.local"

# Função para verificar se o WordPress está funcionando corretamente
check_wp_installation() {
    if ! $WP_CMD core is-installed >/dev/null 2>&1; then
        return 1
    fi
    
    # Verifica se o banco de dados está acessível
    if ! $WP_CMD db check >/dev/null 2>&1; then
        return 1
    fi
    
    # Verifica se as tabelas principais existem
    if ! $WP_CMD db tables | grep -q "wp_posts"; then
        return 1
    fi
    
    return 0
}

# Configuração do SQLite
SQLITE_DROPIN="/var/www/html/wp-content/db.php"
if [ ! -f "$SQLITE_DROPIN" ]; then
    echo "🔽 Baixando suporte automático SQLite (plugin oficial WordPress)..."
    curl -fsSL -o /tmp/sqlite.zip https://downloads.wordpress.org/plugin/sqlite-database-integration.latest-stable.zip
    unzip -j /tmp/sqlite.zip "sqlite-database-integration/db.copy" -d /var/www/html/wp-content/
    mv /var/www/html/wp-content/db.copy /var/www/html/wp-content/db.php
    rm /tmp/sqlite.zip
fi

mkdir -p /var/www/html/wp-content/database
chmod 777 /var/www/html/wp-content/database

# Verifica se o WordPress está instalado e funcionando
if ! check_wp_installation; then
    echo "⚠️ WordPress não está instalado ou não está funcionando corretamente. Instalando em modo multisite..."
    
    # Remove o banco de dados SQLite se existir
    rm -f /var/www/html/wp-content/database/.ht.sqlite
    
    $WP_CMD core multisite-install \
        --url="dev.local" \
        --title="Thabatta Apolinário Advocacia" \
        --admin_user="admin" \
        --admin_password="2212" \
        --admin_email="lfelipeapo@gmail.com" \
        --skip-email

    echo "✅ WordPress multisite instalado com sucesso!"
else
    echo "✅ WordPress já está instalado e funcionando, verificando plugins e tema..."
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
    if ! $WP_CMD plugin is-installed "$plugin" >/dev/null 2>&1; then
        echo "📦 Instalando plugin: $plugin"
        $WP_CMD plugin install "$plugin" --activate || echo "⚠️ Erro ao instalar plugin: $plugin"
    elif ! $WP_CMD plugin is-active "$plugin" >/dev/null 2>&1; then
        echo "🔄 Ativando plugin: $plugin"
        $WP_CMD plugin activate "$plugin" || echo "⚠️ Erro ao ativar plugin: $plugin"
    else
        echo "✅ Plugin já instalado e ativo: $plugin"
    fi
done

# Verifica e ativa o tema
if ! $WP_CMD theme is-active thabatta-adv-theme >/dev/null 2>&1; then
    echo "🎨 Ativando o tema thabatta-adv-theme..."
    $WP_CMD theme activate thabatta-adv-theme || echo "⚠️ Erro ao ativar o tema"
else
    echo "✅ Tema thabatta-adv-theme já está ativo"
fi

# Executar entrypoint padrão do container
echo "🚀 Iniciando WordPress com entrypoint oficial..."
exec docker-entrypoint.sh "$@"
