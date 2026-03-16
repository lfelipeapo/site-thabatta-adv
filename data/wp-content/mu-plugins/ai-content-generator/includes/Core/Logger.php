<?php
/**
 * Sistema de logging estruturado
 *
 * @package AICG\Core
 * @since   1.0.0
 */

namespace AICG\Core;

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Logger
 *
 * Implementa logging estruturado em JSON para auditoria
 *
 * @package AICG\Core
 * @since   1.0.0
 */
class Logger
{
    /**
     * Níveis de log
     *
     * @var array
     */
    private array $levels = [
        'DEBUG' => 0,
        'INFO' => 1,
        'WARNING' => 2,
        'ERROR' => 3,
        'CRITICAL' => 4,
    ];

    /**
     * Nível mínimo para log
     *
     * @var string
     */
    private string $min_level;

    /**
     * Construtor
     */
    public function __construct()
    {
        $this->min_level = defined('WP_DEBUG') && WP_DEBUG ? 'DEBUG' : 'INFO';
    }

    /**
     * Log de debug
     *
     * @param string $message Mensagem
     * @param array $context Contexto adicional
     * @return void
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }

    /**
     * Log de info
     *
     * @param string $message Mensagem
     * @param array $context Contexto adicional
     * @return void
     */
    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    /**
     * Log de warning
     *
     * @param string $message Mensagem
     * @param array $context Contexto adicional
     * @return void
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    /**
     * Log de erro
     *
     * @param string $message Mensagem
     * @param array $context Contexto adicional
     * @return void
     */
    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    /**
     * Log crítico
     *
     * @param string $message Mensagem
     * @param array $context Contexto adicional
     * @return void
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log('CRITICAL', $message, $context);
    }

    /**
     * Log de requisição API
     *
     * @param array $data Dados da requisição
     * @return void
     */
    public function log_request(array $data): void
    {
        global $wpdb;

        $table = $wpdb->prefix . 'aicg_logs';

        $wpdb->insert(
            $table,
            [
                'level' => $data['error'] ? 'ERROR' : 'INFO',
                'component' => $data['component'] ?? 'API',
                'event' => $data['event'] ?? 'request',
                'request_id' => $data['request_id'] ?? null,
                'user_id' => get_current_user_id() ?: null,
                'method' => $data['method'] ?? null,
                'endpoint' => $data['endpoint'] ?? null,
                'duration_ms' => $data['duration_ms'] ?? null,
                'status_code' => $data['status_code'] ?? null,
                'tokens_input' => $data['tokens_input'] ?? null,
                'tokens_output' => $data['tokens_output'] ?? null,
                'error' => isset($data['error']) ? $this->redact_sensitive($data['error']) : null,
                'context' => !empty($data['context']) ? wp_json_encode($this->redact_sensitive($data['context'])) : null,
            ],
            [
                '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s',
            ]
        );
    }

    /**
     * Registra log no banco de dados
     *
     * @param string $level Nível do log
     * @param string $message Mensagem
     * @param array $context Contexto
     * @return void
     */
    private function log(string $level, string $message, array $context = []): void
    {
        // Verifica nível mínimo
        if ($this->levels[$level] < $this->levels[$this->min_level]) {
            return;
        }

        global $wpdb;

        $table = $wpdb->prefix . 'aicg_logs';

        $wpdb->insert(
            $table,
            [
                'level' => $level,
                'component' => $context['component'] ?? 'General',
                'event' => $context['event'] ?? 'log',
                'request_id' => $context['request_id'] ?? null,
                'user_id' => get_current_user_id() ?: null,
                'error' => $this->redact_sensitive($message),
                'context' => !empty($context) ? wp_json_encode($this->redact_sensitive($context)) : null,
            ],
            [
                '%s', '%s', '%s', '%s', '%d', '%s', '%s',
            ]
        );

        // Log adicional em arquivo se WP_DEBUG estiver ativo
        if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            $log_entry = sprintf(
                '[%s] %s: %s %s',
                current_time('mysql'),
                $level,
                $message,
                !empty($context) ? wp_json_encode($this->redact_sensitive($context)) : ''
            );
            error_log($log_entry);
        }
    }

    /**
     * Remove dados sensíveis do contexto
     *
     * @param mixed $data Dados a processar
     * @return mixed
     */
    private function redact_sensitive($data)
    {
        if (is_string($data)) {
            // Redact API keys
            $patterns = [
                '/sk-[a-zA-Z0-9]{20,}/' => '***REDACTED_API_KEY***',
                '/[a-zA-Z0-9_-]*key[a-zA-Z0-9_-]*["\']?\s*[:=]\s*["\']?[a-zA-Z0-9]{10,}/i' => '***REDACTED_KEY***',
                '/Bearer\s+[a-zA-Z0-9_-]+/' => 'Bearer ***REDACTED***',
            ];

            foreach ($patterns as $pattern => $replacement) {
                $data = preg_replace($pattern, $replacement, $data);
            }

            return $data;
        }

        if (is_array($data)) {
            $sensitive_keys = ['api_key', 'password', 'token', 'secret', 'authorization', 'key'];
            
            foreach ($data as $key => $value) {
                if (is_string($key) && in_array(strtolower($key), $sensitive_keys, true)) {
                    $data[$key] = '***REDACTED***';
                } else {
                    $data[$key] = $this->redact_sensitive($value);
                }
            }
        }

        return $data;
    }

    /**
     * Obtém logs recentes
     *
     * @param array $filters Filtros
     * @param int $limit Limite de resultados
     * @return array
     */
    public function get_logs(array $filters = [], int $limit = 100): array
    {
        global $wpdb;

        $table = $wpdb->prefix . 'aicg_logs';
        $where = ['1=1'];
        $args = [];

        if (!empty($filters['level'])) {
            $where[] = 'level = %s';
            $args[] = $filters['level'];
        }

        if (!empty($filters['component'])) {
            $where[] = 'component = %s';
            $args[] = $filters['component'];
        }

        if (!empty($filters['user_id'])) {
            $where[] = 'user_id = %d';
            $args[] = $filters['user_id'];
        }

        if (!empty($filters['date_from'])) {
            $where[] = 'timestamp >= %s';
            $args[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where[] = 'timestamp <= %s';
            $args[] = $filters['date_to'];
        }

        $where_clause = implode(' AND ', $where);

        $sql = $wpdb->prepare(
            "SELECT * FROM {$table} WHERE {$where_clause} ORDER BY timestamp DESC LIMIT %d",
            array_merge($args, [$limit])
        );

        return $wpdb->get_results($sql, ARRAY_A);
    }

    /**
     * Limpa logs antigos
     *
     * @return void
     */
    public function cleanup_old_logs(): void
    {
        global $wpdb;

        $retention_days = (int) get_option('aicg_log_retention_days', 90);
        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$retention_days} days"));

        $table = $wpdb->prefix . 'aicg_logs';

        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$table} WHERE timestamp < %s",
                $cutoff_date
            )
        );
    }
}
