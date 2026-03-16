<?php
/**
 * Sistema de migrações de banco de dados
 *
 * @package AICG\Database
 * @since   1.0.0
 */

namespace AICG\Database;

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Migrations
 *
 * Gerencia atualizações do schema do banco de dados
 *
 * @package AICG\Database
 * @since   1.0.0
 */
class Migrations
{
    /**
     * Instância do wpdb
     *
     * @var \wpdb
     */
    private \wpdb $db;

    /**
     * Prefixo das tabelas
     *
     * @var string
     */
    private string $prefix;

    /**
     * Versão atual do schema
     *
     * @var string
     */
    private string $current_version;

    /**
     * Construtor
     */
    public function __construct()
    {
        global $wpdb;
        
        $this->db = $wpdb;
        $this->prefix = $wpdb->prefix . 'aicg_';
        $this->current_version = get_option('aicg_db_version', '0.0.0');
    }

    /**
     * Executa migrações pendentes
     *
     * @return void
     */
    public function run(): void
    {
        $target_version = AICG_VERSION;

        if (version_compare($this->current_version, $target_version, '>=')) {
            return;
        }

        // Executa migrações em ordem
        $migrations = $this->get_migrations();
        
        foreach ($migrations as $version => $method) {
            if (version_compare($this->current_version, $version, '<')) {
                if (method_exists($this, $method)) {
                    $this->$method();
                    update_option('aicg_db_version', $version);
                }
            }
        }

        // Atualiza para versão final
        update_option('aicg_db_version', $target_version);
    }

    /**
     * Obtém lista de migrações disponíveis
     *
     * @return array
     */
    private function get_migrations(): array
    {
        return [
            '1.0.0' => 'migrate_1_0_0',
        ];
    }

    /**
     * Migração inicial (v1.0.0)
     *
     * @return void
     */
    private function migrate_1_0_0(): void
    {
        $charset_collate = $this->db->get_charset_collate();

        // Tabela de jobs
        $sql_jobs = "CREATE TABLE IF NOT EXISTS {$this->prefix}jobs (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            job_id varchar(32) NOT NULL,
            user_id bigint(20) UNSIGNED NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            prompt_hash varchar(64) DEFAULT NULL,
            content_type varchar(20) NOT NULL DEFAULT 'post',
            result_data longtext DEFAULT NULL,
            error_message text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            started_at datetime DEFAULT NULL,
            completed_at datetime DEFAULT NULL,
            post_id bigint(20) UNSIGNED DEFAULT NULL,
            metadata longtext DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY job_id (job_id),
            KEY status (status),
            KEY user_id (user_id),
            KEY created_at (created_at),
            KEY post_id (post_id)
        ) {$charset_collate};";

        // Tabela de logs
        $sql_logs = "CREATE TABLE IF NOT EXISTS {$this->prefix}logs (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            level varchar(10) NOT NULL,
            component varchar(50) NOT NULL,
            event varchar(50) NOT NULL,
            request_id varchar(32) DEFAULT NULL,
            user_id bigint(20) UNSIGNED DEFAULT NULL,
            method varchar(10) DEFAULT NULL,
            endpoint varchar(255) DEFAULT NULL,
            duration_ms int(11) DEFAULT NULL,
            status_code smallint(5) DEFAULT NULL,
            tokens_input int(11) DEFAULT NULL,
            tokens_output int(11) DEFAULT NULL,
            error text DEFAULT NULL,
            context longtext DEFAULT NULL,
            PRIMARY KEY (id),
            KEY timestamp (timestamp),
            KEY level (level),
            KEY component (component),
            KEY event (event),
            KEY user_id (user_id),
            KEY request_id (request_id)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        
        dbDelta($sql_jobs);
        dbDelta($sql_logs);
    }

    /**
     * Verifica se tabela existe
     *
     * @param string $table Nome da tabela (sem prefixo)
     * @return bool
     */
    public function table_exists(string $table): bool
    {
        $table_name = $this->prefix . $table;
        
        return $this->db->get_var(
            $this->db->prepare(
                "SHOW TABLES LIKE %s",
                $table_name
            )
        ) === $table_name;
    }

    /**
     * Adiciona coluna a tabela
     *
     * @param string $table Nome da tabela
     * @param string $column Nome da coluna
     * @param string $definition Definição SQL
     * @return void
     */
    private function add_column(string $table, string $column, string $definition): void
    {
        $table_name = $this->prefix . $table;
        
        // Verifica se coluna já existe
        $column_exists = $this->db->get_results(
            $this->db->prepare(
                "SELECT COLUMN_NAME 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = DATABASE() 
                 AND TABLE_NAME = %s 
                 AND COLUMN_NAME = %s",
                $table_name,
                $column
            )
        );

        if (empty($column_exists)) {
            $this->db->query("ALTER TABLE {$table_name} ADD COLUMN {$column} {$definition}");
        }
    }

    /**
     * Remove coluna de tabela
     *
     * @param string $table Nome da tabela
     * @param string $column Nome da coluna
     * @return void
     */
    private function drop_column(string $table, string $column): void
    {
        $table_name = $this->prefix . $table;
        
        $this->db->query("ALTER TABLE {$table_name} DROP COLUMN IF EXISTS {$column}");
    }

    /**
     * Cria índice
     *
     * @param string $table Nome da tabela
     * @param string $index Nome do índice
     * @param string $columns Colunas
     * @return void
     */
    private function add_index(string $table, string $index, string $columns): void
    {
        $table_name = $this->prefix . $table;
        
        $this->db->query("CREATE INDEX {$index} ON {$table_name} ({$columns})");
    }
}
