<?php
/**
 * Cliente para API Groq
 *
 * @package AICG\API
 * @since   1.0.0
 */

namespace AICG\API;

use AICG\Security\Encryption;
use AICG\Core\Logger;

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class GroqClient
 *
 * Wrapper para comunicação com a API Groq via Neuron PHP
 *
 * @package AICG\API
 * @since   1.0.0
 */
class GroqClient
{
    /**
     * URL base da API Groq
     *
     * @var string
     */
    private string $base_url = 'https://api.groq.com/openai/v1/';

    /**
     * Chave API descriptografada
     *
     * @var string|null
     */
    private ?string $api_key = null;

    /**
     * Timeout padrão para requisições
     *
     * @var int
     */
    private int $timeout = 60;

    /**
     * Número máximo de retries
     *
     * @var int
     */
    private int $max_retries = 3;

    /**
     * Logger para operações
     *
     * @var Logger|null
     */
    private ?Logger $logger = null;

    /**
     * Construtor
     */
    public function __construct()
    {
        $this->load_api_key();
        $this->logger = new Logger();
    }

    /**
     * Carrega e descriptografa a chave API
     *
     * @return void
     */
    private function load_api_key(): void
    {
        $encrypted_key = get_option('aicg_api_key_encrypted', '');
        
        if (!empty($encrypted_key)) {
            $encryption = new Encryption();
            $this->api_key = $encryption->decrypt($encrypted_key);
        }
    }

    /**
     * Gera conteúdo baseado em prompt
     *
     * @param string $prompt Prompt do usuário
     * @param array $options Opções adicionais
     * @return array|\WP_Error
     */
    public function generate_content(string $prompt, array $options = [])
    {
        // Verifica autenticação
        if (empty($this->api_key)) {
            return new \WP_Error(
                'api_key_missing',
                esc_html__('Chave API não configurada. Verifique as configurações do plugin.', 'ai-content-generator')
            );
        }

        // Configura modelo
        $model = $options['model'] ?? get_option('aicg_default_model', 'llama-3.3-70b-versatile');
        $tone = $options['tone'] ?? get_option('aicg_default_tone', 'professional');
        $length = $this->resolve_length($options);

        // Constrói mensagens
        $messages = $this->build_messages($prompt, $tone, $length, $options);

        // Prepara payload
        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? 0.7,
            'max_tokens' => $options['max_tokens'] ?? $this->resolve_max_tokens($options, $length),
            'response_format' => ['type' => 'json_object'],
        ];

        // Faz requisição com retry
        $response = $this->make_request_with_retry('chat/completions', $payload);

        if (is_wp_error($response)) {
            $this->logger->error('API request failed', [
                'error' => $response->get_error_message(),
                'code' => $response->get_error_code(),
            ]);
            return $response;
        }

        // Processa resposta
        return $this->process_response($response);
    }

    /**
     * Constrói mensagens para o modelo
     *
     * @param string $prompt Prompt do usuário
     * @param string $tone Tom de voz
     * @param string $length Comprimento desejado
     * @param array $options Opções adicionais
     * @return array
     */
    private function build_messages(string $prompt, string $tone, string $length, array $options): array
    {
        $site_name = get_bloginfo('name');
        $site_description = get_bloginfo('description');
        $schema_example = wp_json_encode([
            'post' => [
                'title' => 'Titulo otimizado (ate 100 caracteres)',
                'content' => 'Conteudo em HTML valido',
                'excerpt' => 'Resumo de ate 300 caracteres',
            ],
            'media' => [
                'image_url' => 'URL da imagem sugerida (opcional)',
                'image_alt' => 'Texto alternativo da imagem',
            ],
            'seo' => [
                'meta_title' => 'Titulo SEO (ate 60 caracteres)',
                'meta_description' => 'Meta description (150-160 caracteres)',
                'focus_keyword' => 'Palavra-chave principal',
                'keywords' => ['palavra1', 'palavra2', 'palavra3'],
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // System prompt
        $system_content = sprintf(
            "Você é um assistente de redação especializado. " .
            "Site: %s. Descrição: %s. " .
            "Tom de voz: %s. " .
            "Comprimento: %s. " .
            "Gere conteúdo em português do Brasil. " .
            "Responda APENAS em JSON válido conforme schema especificado.",
            $site_name,
            $site_description,
            $this->get_tone_description($tone),
            $this->get_length_description($length)
        );

        // User prompt com estrutura esperada
        $user_content = sprintf(
            "Crie %s sobre: %s\n\n" .
            "Estrutura de resposta JSON obrigatória:\n" .
            "%s",
            $options['content_type'] === 'page' ? 'uma página' : 'um artigo de blog',
            $prompt,
            $schema_example
        );

        return [
            [
                'role' => 'system',
                'content' => $system_content,
            ],
            [
                'role' => 'user',
                'content' => $user_content,
            ],
        ];
    }

    /**
     * Obtém descrição do tom de voz
     *
     * @param string $tone Identificador do tom
     * @return string
     */
    private function get_tone_description(string $tone): string
    {
        $tones = [
            'professional' => 'profissional, formal e objetivo',
            'casual' => 'casual, conversacional e amigável',
            'technical' => 'técnico, especializado e detalhado',
            'persuasive' => 'persuasivo, focado em conversão e vendas',
            'narrative' => 'narrativo, com storytelling envolvente',
        ];

        return $tones[$tone] ?? $tones['professional'];
    }

    /**
     * Obtém descrição do comprimento
     *
     * @param string $length Identificador do comprimento
     * @return string
     */
    private function get_length_description(string $length): string
    {
        $lengths = [
            'short' => '300-500 palavras (artigo curto)',
            'medium' => '800-1200 palavras (artigo médio)',
            'long' => '1500-2500 palavras (artigo longo/detallhado)',
        ];

        return $lengths[$length] ?? $lengths['medium'];
    }

    /**
     * Resolve o identificador de comprimento a partir das opcoes recebidas.
     *
     * @param array $options Opções enviadas pelo cliente
     * @return string
     */
    private function resolve_length(array $options): string
    {
        if (!empty($options['length']) && in_array($options['length'], ['short', 'medium', 'long'], true)) {
            return $options['length'];
        }

        if (!empty($options['target_length'])) {
            return match (true) {
                $options['target_length'] <= 600 => 'short',
                $options['target_length'] >= 1500 => 'long',
                default => 'medium',
            };
        }

        return get_option('aicg_default_length', 'medium');
    }

    /**
     * Resolve o limite de tokens conforme o comprimento solicitado.
     *
     * @param array $options Opções enviadas pelo cliente
     * @param string $length Comprimento normalizado
     * @return int
     */
    private function resolve_max_tokens(array $options, string $length): int
    {
        if (!empty($options['target_length'])) {
            return max(600, min((int) ceil(((int) $options['target_length']) * 1.8), 4000));
        }

        return match ($length) {
            'short' => 1200,
            'long' => 3200,
            default => 2200,
        };
    }

    /**
     * Faz requisição com retry e backoff exponencial
     *
     * @param string $endpoint Endpoint da API
     * @param array $payload Dados da requisição
     * @return array|\WP_Error
     */
    private function make_request_with_retry(string $endpoint, array $payload)
    {
        $attempt = 0;
        $last_error = null;

        while ($attempt < $this->max_retries) {
            $attempt++;

            $response = $this->make_request($endpoint, $payload);

            if (!is_wp_error($response)) {
                return $response;
            }

            $last_error = $response;
            $error_code = $response->get_error_code();
            $error_data = $response->get_error_data();

            // Não faz retry em erros de autenticação
            if (in_array($error_code, ['auth_failed', 'invalid_api_key'], true)) {
                return $response;
            }

            // Verifica se deve fazer retry baseado no status HTTP
            $http_code = $error_data['status'] ?? 0;
            
            // Retry em erros de servidor, rate limit ou timeout
            if (!in_array($http_code, [500, 502, 503, 504, 429], true) && $http_code > 0) {
                return $response;
            }

            // Calcula delay com backoff exponencial e jitter
            if ($attempt < $this->max_retries) {
                $delay = pow(2, $attempt - 1) * 1000000; // microsegundos
                $jitter = random_int(0, $delay * 0.2); // ±20% jitter
                usleep($delay + $jitter);
            }
        }

        return $last_error;
    }

    /**
     * Faz requisição HTTP para a API
     *
     * @param string $endpoint Endpoint da API
     * @param array $payload Dados da requisição
     * @return array|\WP_Error
     */
    private function make_request(string $endpoint, array $payload)
    {
        $url = $this->base_url . $endpoint;
        $request_id = wp_generate_uuid4();

        $args = [
            'method' => 'POST',
            'timeout' => $this->timeout,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json',
                'X-Request-ID' => $request_id,
            ],
            'body' => wp_json_encode($payload),
            'sslverify' => true,
        ];

        $start_time = microtime(true);
        $response = wp_remote_post($url, $args);
        $duration = round((microtime(true) - $start_time) * 1000);

        // Log da requisição
        $this->logger->log_request([
            'request_id' => $request_id,
            'method' => 'POST',
            'endpoint' => $endpoint,
            'duration_ms' => $duration,
            'status_code' => is_wp_error($response) ? 0 : wp_remote_retrieve_response_code($response),
        ]);

        if (is_wp_error($response)) {
            return new \WP_Error(
                'http_error',
                $response->get_error_message(),
                ['status' => 0]
            );
        }

        $http_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);

        // Trata códigos de erro HTTP
        if ($http_code !== 200) {
            $error_data = json_decode($body, true);
            $error_message = $error_data['error']['message'] ?? 'Erro desconhecido';

            $error_codes = [
                401 => 'auth_failed',
                403 => 'forbidden',
                429 => 'rate_limited',
                500 => 'server_error',
                502 => 'bad_gateway',
                503 => 'service_unavailable',
                504 => 'gateway_timeout',
            ];

            return new \WP_Error(
                $error_codes[$http_code] ?? 'api_error',
                $error_message,
                [
                    'status' => $http_code,
                    'body' => $body,
                ]
            );
        }

        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new \WP_Error(
                'json_parse_error',
                esc_html__('Erro ao processar resposta da API.', 'ai-content-generator'),
                ['status' => $http_code]
            );
        }

        return $data;
    }

    /**
     * Processa resposta da API
     *
     * @param array $response Resposta bruta da API
     * @return array|\WP_Error
     */
    private function process_response(array $response)
    {
        // Extrai conteúdo da resposta
        $content = $response['choices'][0]['message']['content'] ?? '';

        if (empty($content)) {
            return new \WP_Error(
                'empty_response',
                esc_html__('Resposta vazia da API.', 'ai-content-generator')
            );
        }

        // Parse JSON da resposta
        $parser = new ResponseParser();
        $parsed = $parser->parse($content);

        if (is_wp_error($parsed)) {
            return $parsed;
        }

        // Adiciona metadados da geração
        $parsed['metadata'] = [
            'model' => $response['model'] ?? 'unknown',
            'tokens_input' => $response['usage']['prompt_tokens'] ?? 0,
            'tokens_output' => $response['usage']['completion_tokens'] ?? 0,
            'tokens_total' => $response['usage']['total_tokens'] ?? 0,
        ];

        return $parsed;
    }

    /**
     * Valida conexão com a API
     *
     * @return bool|\WP_Error
     */
    public function validate_connection()
    {
        if (empty($this->api_key)) {
            return new \WP_Error(
                'api_key_missing',
                esc_html__('Chave API não configurada.', 'ai-content-generator')
            );
        }

        $models = $this->get_available_models();

        if (is_wp_error($models)) {
            return $models;
        }

        return true;
    }

    /**
     * Obtém lista de modelos disponíveis
     *
     * @return array|\WP_Error
     */
    public function get_available_models()
    {
        if (empty($this->api_key)) {
            return new \WP_Error(
                'api_key_missing',
                esc_html__('Chave API não configurada.', 'ai-content-generator')
            );
        }

        $url = $this->base_url . 'models';
        
        $args = [
            'method' => 'GET',
            'timeout' => 30,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json',
            ],
            'sslverify' => true,
        ];

        $response = wp_remote_get($url, $args);

        if (is_wp_error($response)) {
            return new \WP_Error(
                'http_error',
                $response->get_error_message()
            );
        }

        $http_code = wp_remote_retrieve_response_code($response);
        
        if ($http_code !== 200) {
            return new \WP_Error(
                'api_error',
                esc_html__('Erro ao obter modelos.', 'ai-content-generator'),
                ['status' => $http_code]
            );
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new \WP_Error(
                'json_parse_error',
                esc_html__('Erro ao processar resposta.', 'ai-content-generator')
            );
        }

        return $data['data'] ?? [];
    }

    /**
     * Obtém estatísticas de uso
     *
     * @return array
     */
    public function get_usage_stats(): array
    {
        // Groq não tem endpoint específico de uso, retorna estatísticas locais
        global $wpdb;
        
        $table_logs = $wpdb->prefix . 'aicg_logs';
        
        $stats = $wpdb->get_row(
            "SELECT 
                COUNT(*) as total_requests,
                SUM(tokens_input) as total_input,
                SUM(tokens_output) as total_output,
                AVG(duration_ms) as avg_duration
            FROM {$table_logs}
            WHERE component = 'GroqClient'
            AND timestamp >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            AND level = 'INFO'"
        );

        return [
            'total_requests' => (int) ($stats->total_requests ?? 0),
            'total_tokens_input' => (int) ($stats->total_input ?? 0),
            'total_tokens_output' => (int) ($stats->total_output ?? 0),
            'average_duration_ms' => round($stats->avg_duration ?? 0, 2),
        ];
    }
}
