<?php
/**
 * Parser para respostas da API
 *
 * @package AICG\API
 * @since   1.0.0
 */

namespace AICG\API;

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ResponseParser
 *
 * Valida e processa respostas JSON da API Groq
 *
 * @package AICG\API
 * @since   1.0.0
 */
class ResponseParser
{
    /**
     * Schema esperado da resposta
     *
     * @var array
     */
    private array $schema = [
        'post' => [
            'title' => ['type' => 'string', 'required' => true, 'max_length' => 100],
            'content' => ['type' => 'string', 'required' => true],
            'excerpt' => ['type' => 'string', 'required' => false, 'max_length' => 300],
            'status' => ['type' => 'string', 'required' => false, 'enum' => ['draft', 'future']],
            'date' => ['type' => 'string', 'required' => false],
        ],
        'media' => [
            'image_url' => ['type' => 'string', 'required' => false],
            'image_alt' => ['type' => 'string', 'required' => false, 'max_length' => 125],
        ],
        'seo' => [
            'meta_title' => ['type' => 'string', 'required' => false, 'max_length' => 60],
            'meta_description' => ['type' => 'string', 'required' => false, 'max_length' => 160],
            'focus_keyword' => ['type' => 'string', 'required' => false],
            'keywords' => ['type' => 'array', 'required' => false],
        ],
    ];

    /**
     * Parse e valida resposta da API
     *
     * @param string $content Conteúdo JSON da resposta
     * @return array|\WP_Error
     */
    public function parse(string $content)
    {
        // Limpa possíveis envoltórios markdown
        $content = $this->clean_markdown($content);

        // Decode JSON
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new \WP_Error(
                'json_parse_error',
                sprintf(
                    /* translators: %s: JSON error message */
                    esc_html__('Erro ao parsear JSON: %s', 'ai-content-generator'),
                    json_last_error_msg()
                )
            );
        }

        // Valida schema
        $validation = $this->validate_schema($data);
        
        if (is_wp_error($validation)) {
            return $validation;
        }

        // Sanitiza dados
        return $this->sanitize_data($data);
    }

    /**
     * Remove envoltórios markdown do JSON
     *
     * @param string $content Conteúdo possivelmente envolvido
     * @return string
     */
    private function clean_markdown(string $content): string
    {
        // Remove blocos de código markdown
        $patterns = [
            '/^```(?:json)?\s*/m',  // Abertura ``` ou ```json
            '/```\s*$/m',           // Fechamento ```
        ];

        foreach ($patterns as $pattern) {
            $content = preg_replace($pattern, '', $content);
        }

        return trim($content);
    }

    /**
     * Valida dados contra o schema
     *
     * @param array $data Dados a validar
     * @return true|\WP_Error
     */
    private function validate_schema(array $data)
    {
        // Verifica campos obrigatórios do nível post
        if (!isset($data['post'])) {
            return new \WP_Error(
                'missing_post_section',
                esc_html__('Resposta inválida: seção "post" não encontrada.', 'ai-content-generator')
            );
        }

        $post = $data['post'];

        // Valida campos obrigatórios
        foreach ($this->schema['post'] as $field => $config) {
            if ($config['required'] && !isset($post[$field])) {
                return new \WP_Error(
                    'missing_required_field',
                    sprintf(
                        /* translators: %s: Field name */
                        esc_html__('Campo obrigatório ausente: post.%s', 'ai-content-generator'),
                        $field
                    )
                );
            }

            if (isset($post[$field])) {
                // Valida tipo
                if (!$this->validate_type($post[$field], $config['type'])) {
                    return new \WP_Error(
                        'invalid_field_type',
                        sprintf(
                            /* translators: 1: Field name, 2: Expected type */
                            esc_html__('Tipo inválido para post.%1$s. Esperado: %2$s', 'ai-content-generator'),
                            $field,
                            $config['type']
                        )
                    );
                }

                // Valida comprimento máximo
                if (isset($config['max_length']) && strlen($post[$field]) > $config['max_length']) {
                    // Trunca em vez de erro
                    $post[$field] = substr($post[$field], 0, $config['max_length']);
                }

                // Valida enum
                if (isset($config['enum']) && !in_array($post[$field], $config['enum'], true)) {
                    return new \WP_Error(
                        'invalid_enum_value',
                        sprintf(
                            /* translators: 1: Field name, 2: Allowed values */
                            esc_html__('Valor inválido para post.%1$s. Valores permitidos: %2$s', 'ai-content-generator'),
                            $field,
                            implode(', ', $config['enum'])
                        )
                    );
                }
            }
        }

        return true;
    }

    /**
     * Valida tipo de dado
     *
     * @param mixed $value Valor a validar
     * @param string $type Tipo esperado
     * @return bool
     */
    private function validate_type($value, string $type): bool
    {
        return match ($type) {
            'string' => is_string($value),
            'array' => is_array($value),
            'integer' => is_int($value),
            'boolean' => is_bool($value),
            default => true,
        };
    }

    /**
     * Sanitiza dados recebidos
     *
     * @param array $data Dados a sanitizar
     * @return array
     */
    private function sanitize_data(array $data): array
    {
        $sanitized = [];

        // Sanitiza post
        if (isset($data['post'])) {
            $sanitized['post'] = [
                'title' => sanitize_text_field($data['post']['title']),
                'content' => wp_kses_post($data['post']['content']),
                'excerpt' => isset($data['post']['excerpt']) 
                    ? sanitize_text_field($data['post']['excerpt']) 
                    : '',
                'status' => isset($data['post']['status']) 
                    ? sanitize_key($data['post']['status']) 
                    : 'draft',
                'date' => isset($data['post']['date']) 
                    ? sanitize_text_field($data['post']['date']) 
                    : null,
            ];
        }

        // Sanitiza media
        $sanitized['media'] = [
            'image_url' => isset($data['media']['image_url']) 
                ? esc_url_raw($data['media']['image_url']) 
                : '',
            'image_alt' => isset($data['media']['image_alt']) 
                ? sanitize_text_field($data['media']['image_alt']) 
                : '',
        ];

        // Sanitiza SEO
        $sanitized['seo'] = [
            'meta_title' => isset($data['seo']['meta_title']) 
                ? sanitize_text_field($data['seo']['meta_title']) 
                : '',
            'meta_description' => isset($data['seo']['meta_description']) 
                ? sanitize_text_field($data['seo']['meta_description']) 
                : '',
            'focus_keyword' => isset($data['seo']['focus_keyword']) 
                ? sanitize_text_field($data['seo']['focus_keyword']) 
                : '',
            'keywords' => isset($data['seo']['keywords']) && is_array($data['seo']['keywords'])
                ? array_map('sanitize_text_field', $data['seo']['keywords'])
                : [],
        ];

        return $sanitized;
    }

    /**
     * Extrai JSON de uma string possivelmente misturada
     *
     * @param string $content Conteúdo misto
     * @return string
     */
    public function extract_json(string $content): string
    {
        // Tenta encontrar JSON entre chaves
        if (preg_match('/\{.*\}/s', $content, $matches)) {
            return $matches[0];
        }

        return $content;
    }

    /**
     * Valida HTML do conteúdo
     *
     * @param string $html HTML a validar
     * @return true|\WP_Error
     */
    public function validate_html(string $html)
    {
        // Tags permitidas no conteúdo
        $allowed_tags = wp_kses_allowed_html('post');

        // Verifica tags não permitidas
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        
        // Adiciona encoding
        $wrapped = '<?xml encoding="UTF-8"><div>' . $html . '</div>';
        $dom->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        libxml_clear_errors();

        // Lista de tags proibidas
        $forbidden_tags = ['script', 'style', 'iframe', 'object', 'embed', 'form'];
        
        foreach ($forbidden_tags as $tag) {
            $elements = $dom->getElementsByTagName($tag);
            if ($elements->length > 0) {
                return new \WP_Error(
                    'forbidden_html_tag',
                    sprintf(
                        /* translators: %s: HTML tag name */
                        esc_html__('Tag HTML não permitida encontrada: <%s>', 'ai-content-generator'),
                        $tag
                    )
                );
            }
        }

        return true;
    }
}
