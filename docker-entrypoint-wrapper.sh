#!/bin/bash

set -e

export WP_CLI_ALLOW_ROOT=1
WP_CMD="wp --allow-root --skip-plugins --skip-themes --path=/var/www/html --url=dev.local"

# Diretórios do banco de dados
DB_DIR="/var/www/html/wp-content/database"
DB_FILE="database.sqlite"
BACKUP_DIR="/var/www/html/wp-content/database/backups"
BACKUP_FILE="$BACKUP_DIR/database.sqlite.backup"

# Função para verificar se o banco de dados tem conteúdo
check_db_content() {
    if [ -f "$DB_DIR/$DB_FILE" ] && [ -s "$DB_DIR/$DB_FILE" ]; then
        # Verifica integridade do banco
        if sqlite3 "$DB_DIR/$DB_FILE" "PRAGMA integrity_check;" | grep -q "ok"; then
            if sqlite3 "$DB_DIR/$DB_FILE" ".tables" | grep -q "posts"; then
                return 0
            fi
        fi
    fi
    return 1
}

# Função para criar backup do banco de dados
create_backup() {
    echo "📦 Verificando necessidade de backup..."

    # Só cria backup se o banco tiver conteúdo e não existir backup
    if check_db_content && [ ! -f "$BACKUP_FILE" ]; then
        echo "📦 Criando backup do banco de dados..."
        mkdir -p "$BACKUP_DIR"
        cp "$DB_DIR/$DB_FILE" "$BACKUP_FILE"
        echo "✅ Backup criado com sucesso em $BACKUP_FILE"
    elif [ -f "$BACKUP_FILE" ]; then
        echo "ℹ️ Backup já existe, pulando criação..."
    else
        echo "ℹ️ Banco de dados vazio, pulando backup..."
    fi
}

# Função para restaurar backup do banco de dados
restore_backup() {
    echo "🔄 Verificando backup do banco de dados..."

    # Só restaura se existir backup e o banco atual estiver vazio/corrompido
    if [ -f "$BACKUP_FILE" ] && ! check_db_content; then
        echo "📥 Restaurando backup do banco de dados..."
        mkdir -p "$DB_DIR"
        cp "$BACKUP_FILE" "$DB_DIR/$DB_FILE"
        chmod 666 "$DB_DIR/$DB_FILE"
        echo "✅ Backup restaurado com sucesso"
        return 0
    elif [ -f "$BACKUP_FILE" ]; then
        echo "ℹ️ Banco de dados atual está ok, pulando restauração..."
        return 0
    else
        echo "⚠️ Nenhum backup encontrado"
        return 1
    fi
}

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

# Função para instalar o WordPress
install_wordpress() {
    # Remove o banco de dados SQLite se existir
    rm -f "$DB_DIR/$DB_FILE"

    $WP_CMD core multisite-install \
        --url="dev.local" \
        --title="Thabatta Apolinário Advocacia" \
        --admin_user="admin" \
        --admin_password="2212" \
        --admin_email="lfelipeapo@gmail.com" \
        --skip-email

    echo "✅ WordPress multisite instalado com sucesso!"
    create_backup
}

# Configuração do SQLite
SQLITE_DROPIN="/var/www/html/wp-content/db.php"
if [ ! -f "$SQLITE_DROPIN" ]; then
    echo "🔽 Baixando suporte automático SQLite (plugin oficial WordPress)..."
    curl -fsSL -o /tmp/sqlite.zip https://downloads.wordpress.org/plugin/sqlite-database-integration.latest-stable.zip
    unzip -q /tmp/sqlite.zip -d /var/www/html/wp-content/plugins/
    cp /var/www/html/wp-content/plugins/sqlite-database-integration/db.copy /var/www/html/wp-content/db.php
    rm /tmp/sqlite.zip
fi

# Criar diretório do banco de dados
mkdir -p "$DB_DIR"
chmod 777 "$DB_DIR"

# Verifica se o WordPress está instalado e funcionando
if ! check_wp_installation; then
    echo "⚠️ WordPress não está instalado ou não está funcionando corretamente."

    # Tenta restaurar o backup primeiro
    if restore_backup; then
        echo "✅ Backup restaurado, aguardando estabilização..."
        sync
        sleep 2
        chmod 666 "$DB_DIR/$DB_FILE"

        echo "✅ Verificando instalação após restauração..."
        if check_wp_installation; then
            echo "✅ WordPress restaurado com sucesso do backup!"
        else
            echo "⚠️ Restauração do backup falhou, instalando WordPress em modo multisite..."
            install_wordpress
        fi
    else
        echo "⚠️ Nenhum backup encontrado, instalando WordPress em modo multisite..."
        install_wordpress
    fi
else
    echo "✅ WordPress já está instalado e funcionando, verificando necessidade de backup..."
    create_backup
fi

# Plugins essenciais
PLUGINS=(
    jetpack
    jetpack-boost
    jetpack-protect
    classic-editor
    custom-post-type-ui
    secure-custom-fields
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
