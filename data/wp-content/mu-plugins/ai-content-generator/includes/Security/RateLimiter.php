<?php
/**
 * Sistema de rate limiting para proteção contra abuso
 *
 * @package AICG\Security
 * @since   1.0.0
 */

namespace AICG\Security;

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class RateLimiter
 *
 * Implementa limitação de taxa em múltiplas camadas
 *
 * @package AICG\Security
 * @since   1.0.0
 */
class RateLimiter
{
    /**
     * Prefixo para chaves de transient
     *
     * @var string
     */
    private string $prefix = 'aicg_rate_';

    /**
     * Limites configurados
     *
     * @var array
     */
    private array $limits;

    /**
     * Construtor
     */
    public function __construct()
    {
        $this->limits = [
            'user_minute' => (int) get_option('aicg_rate_limit_user_minute', 10),
            'user_hour' => (int) get_option('aicg_rate_limit_user_hour', 50),
            'ip_minute' => (int) get_option('aicg_rate_limit_ip_minute', 5),
            'global_hour' => (int) get_option('aicg_rate_limit_global_hour', 1000),
        ];
    }

    /**
     * Verifica se a requisição está dentro dos limites
     *
     * @param \WP_REST_Request|null $request Requisição REST (opcional)
     * @return true|\WP_Error
     */
    public function check($request = null)
    {
        $user_id = get_current_user_id();
        $ip = $this->get_client_ip();

        // Verifica limite global do site
        $global_check = $this->check_global_limit();
        if (is_wp_error($global_check)) {
            return $global_check;
        }

        // Usuário autenticado
        if ($user_id > 0) {
            $user_check = $this->check_user_limits($user_id);
            if (is_wp_error($user_check)) {
                return $user_check;
            }
        } else {
            // Não autenticado - verifica por IP
            $ip_check = $this->check_ip_limit($ip);
            if (is_wp_error($ip_check)) {
                return $ip_check;
            }
        }

        return true;
    }

    /**
     * Incrementa contadores após requisição bem-sucedida
     *
     * @return void
     */
    public function increment(): void
    {
        $user_id = get_current_user_id();
        $ip = $this->get_client_ip();

        // Incrementa global
        $this->increment_counter('global', HOUR_IN_SECONDS);

        if ($user_id > 0) {
            // Incrementa por usuário
            $this->increment_counter("user_{$user_id}", MINUTE_IN_SECONDS);
            $this->increment_counter("user_{$user_id}_hour", HOUR_IN_SECONDS);
        } else {
            // Incrementa por IP
            $this->increment_counter("ip_" . md5($ip), MINUTE_IN_SECONDS);
        }
    }

    /**
     * Verifica limite global do site
     *
     * @return true|\WP_Error
     */
    private function check_global_limit()
    {
        $count = (int) get_transient($this->prefix . 'global');
        
        if ($count >= $this->limits['global_hour']) {
            return new \WP_Error(
                'rate_limit_exceeded',
                esc_html__('Limite global do site excedido. Tente novamente mais tarde.', 'ai-content-generator'),
                [
                    'status' => 429,
                    'retry_after' => HOUR_IN_SECONDS,
                    'limit' => $this->limits['global_hour'],
                    'remaining' => 0,
                ]
            );
        }

        return true;
    }

    /**
     * Verifica limites por usuário
     *
     * @param int $user_id ID do usuário
     * @return true|\WP_Error
     */
    private function check_user_limits(int $user_id)
    {
        // Limite por minuto
        $minute_count = (int) get_transient($this->prefix . "user_{$user_id}");
        if ($minute_count >= $this->limits['user_minute']) {
            return new \WP_Error(
                'rate_limit_exceeded',
                esc_html__('Limite de requisições por minuto excedido. Aguarde um momento.', 'ai-content-generator'),
                [
                    'status' => 429,
                    'retry_after' => MINUTE_IN_SECONDS,
                    'limit' => $this->limits['user_minute'],
                    'remaining' => 0,
                    'window' => 'minute',
                ]
            );
        }

        // Limite por hora
        $hour_count = (int) get_transient($this->prefix . "user_{$user_id}_hour");
        if ($hour_count >= $this->limits['user_hour']) {
            return new \WP_Error(
                'rate_limit_exceeded',
                esc_html__('Limite de requisições por hora excedido. Tente novamente mais tarde.', 'ai-content-generator'),
                [
                    'status' => 429,
                    'retry_after' => HOUR_IN_SECONDS,
                    'limit' => $this->limits['user_hour'],
                    'remaining' => 0,
                    'window' => 'hour',
                ]
            );
        }

        return true;
    }

    /**
     * Verifica limite por IP
     *
     * @param string $ip Endereço IP
     * @return true|\WP_Error
     */
    private function check_ip_limit(string $ip)
    {
        $ip_hash = md5($ip);
        $count = (int) get_transient($this->prefix . "ip_{$ip_hash}");

        if ($count >= $this->limits['ip_minute']) {
            return new \WP_Error(
                'rate_limit_exceeded',
                esc_html__('Limite de requisições excedido para este IP. Faça login para continuar.', 'ai-content-generator'),
                [
                    'status' => 429,
                    'retry_after' => MINUTE_IN_SECONDS,
                    'limit' => $this->limits['ip_minute'],
                    'remaining' => 0,
                ]
            );
        }

        return true;
    }

    /**
     * Incrementa um contador de rate limit
     *
     * @param string $key Chave do contador
     * @param int $expiration Tempo de expiração em segundos
     * @return void
     */
    private function increment_counter(string $key, int $expiration): void
    {
        $transient_key = $this->prefix . $key;
        $current = (int) get_transient($transient_key);
        
        if ($current === 0) {
            set_transient($transient_key, 1, $expiration);
        } else {
            // Atualiza o valor mantendo a expiração original quando possível
            set_transient($transient_key, $current + 1, $expiration);
        }
    }

    /**
     * Obtém informações de rate limit para headers
     *
     * @return array
     */
    public function get_limit_info(): array
    {
        $user_id = get_current_user_id();
        $ip = $this->get_client_ip();

        if ($user_id > 0) {
            $count = (int) get_transient($this->prefix . "user_{$user_id}_hour");
            $limit = $this->limits['user_hour'];
        } else {
            $count = (int) get_transient($this->prefix . "ip_" . md5($ip));
            $limit = $this->limits['ip_minute'];
        }

        return [
            'limit' => $limit,
            'remaining' => max(0, $limit - $count),
            'reset' => time() + HOUR_IN_SECONDS,
        ];
    }

    /**
     * Obtém o IP do cliente
     *
     * @return string
     */
    private function get_client_ip(): string
    {
        $ip_keys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', sanitize_text_field(wp_unslash($_SERVER[$key])));
                $ip = trim($ips[0]);
                
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '0.0.0.0';
    }

    /**
     * Reseta contadores de um usuário específico
     *
     * @param int $user_id ID do usuário
     * @return void
     */
    public function reset_user_counters(int $user_id): void
    {
        delete_transient($this->prefix . "user_{$user_id}");
        delete_transient($this->prefix . "user_{$user_id}_hour");
    }

    /**
     * Reseta todos os contadores
     *
     * @return void
     */
    public function reset_all_counters(): void
    {
        global $wpdb;

        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                '_transient_' . $this->prefix . '%'
            )
        );

        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                '_transient_timeout_' . $this->prefix . '%'
            )
        );
    }
}
