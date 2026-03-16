<?php
/**
 * Criador de posts/pages
 *
 * @package AICG\Content
 * @since   1.0.0
 */

namespace AICG\Content;

use AICG\Media\ImageHandler;
use AICG\SEO\SEOIntegration;

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class PostCreator
 *
 * Gerencia a criação de posts e páginas a partir de conteúdo gerado
 *
 * @package AICG\Content
 * @since   1.0.0
 */
class PostCreator
{
    /**
     * Handler de imagens
     *
     * @var ImageHandler|null
     */
    private ?ImageHandler $image_handler = null;

    /**
     * Integração com SEO
     *
     * @var SEOIntegration|null
     */
    private ?SEOIntegration $seo_integration = null;

    /**
     * Construtor
     */
    public function __construct()
    {
        $this->image_handler = new ImageHandler();
        $this->seo_integration = new SEOIntegration();
    }

    /**
     * Cria um novo post/page a partir dos dados gerados
     *
     * @param array $data Dados da resposta da IA
     * @param array $options Opções adicionais
     * @return array|\WP_Error
     */
    public function create(array $data, array $options = [])
    {
        // Valida dados obrigatórios
        if (empty($data['post']['title']) || empty($data['post']['content'])) {
            return new \WP_Error(
                'missing_required_data',
                esc_html__('Dados obrigatórios ausentes para criação do post.', 'ai-content-generator')
            );
        }

        // Determina tipo de conteúdo
        $content_type = $options['content_type'] ?? 'post';
        $schedule_date = $options['schedule_date'] ?? null;

        // Prepara dados do post
        $post_data = $this->prepare_post_data($data, $content_type, $schedule_date, $options);

        // Insere post
        $post_id = wp_insert_post($post_data, true);

        if (is_wp_error($post_id)) {
            return $post_id;
        }

        // Processa imagem destacada
        $featured_image_id = null;
        if (!empty($data['media']['image_url']) && get_option('aicg_include_images', true)) {
            $featured_image_id = $this->image_handler->process_featured_image(
                $data['media']['image_url'],
                $post_id,
                $data['media']['image_alt'] ?? ''
            );
        }

        // Aplica metadados SEO
        if (!empty($data['seo'])) {
            $this->seo_integration->update_seo_meta($post_id, $data['seo']);
        }

        // Registra metadados de rastreamento
        $this->save_generation_meta($post_id, $data, $options);

        // Retorna dados do post criado
        return [
            'post_id' => $post_id,
            'post_type' => $content_type,
            'status' => $post_data['post_status'],
            'title' => $post_data['post_title'],
            'edit_link' => get_edit_post_link($post_id, 'raw'),
            'preview_link' => get_preview_post_link($post_id),
            'featured_image_id' => $featured_image_id,
            'view_link' => $post_data['post_status'] === 'publish' ? get_permalink($post_id) : null,
        ];
    }

    /**
     * Prepara array de dados para wp_insert_post
     *
     * @param array $data Dados da IA
     * @param string $content_type Tipo de conteúdo
     * @param string|null $schedule_date Data de agendamento
     * @param array $options Opções adicionais
     * @return array
     */
    private function prepare_post_data(array $data, string $content_type, ?string $schedule_date, array $options): array
    {
        $post_status = 'draft';
        $post_date = null;
        $post_date_gmt = null;

        // Determina status baseado na data de agendamento
        if (!empty($schedule_date)) {
            $scheduled_timestamp = strtotime($schedule_date);
            $now = current_time('timestamp');

            // Só agenda se a data for futura (com margem de 1 minuto)
            if ($scheduled_timestamp > ($now + 60)) {
                $post_status = 'future';
                $post_date = gmdate('Y-m-d H:i:s', $scheduled_timestamp);
                $post_date_gmt = get_gmt_from_date($post_date);
            }
        }

        // Usa data do conteúdo gerado se disponível e status for future
        if ($post_status === 'future' && !empty($data['post']['date'])) {
            $parsed_date = strtotime($data['post']['date']);
            if ($parsed_date !== false) {
                $post_date = gmdate('Y-m-d H:i:s', $parsed_date);
                $post_date_gmt = get_gmt_from_date($post_date);
            }
        }

        // Prepara tags se disponíveis
        $tags_input = [];
        if (!empty($data['seo']['keywords']) && is_array($data['seo']['keywords'])) {
            $tags_input = array_map('sanitize_text_field', $data['seo']['keywords']);
        }

        // Prepara categorias
        $post_category = [];
        if (!empty($options['category']) && is_array($options['category'])) {
            $post_category = array_map('intval', $options['category']);
        }

        // Monta array de post
        $post_data = [
            // Campos obrigatórios
            'post_title' => sanitize_text_field($data['post']['title']),
            'post_content' => wp_kses_post($data['post']['content']),
            'post_status' => $post_status,
            'post_type' => $content_type,

            // Campos de data
            'post_date' => $post_date,
            'post_date_gmt' => $post_date_gmt,

            // Campos de autor
            'post_author' => $options['author_id'] ?? get_current_user_id(),

            // Campos opcionais
            'post_excerpt' => !empty($data['post']['excerpt']) 
                ? sanitize_text_field($data['post']['excerpt']) 
                : '',
            'post_name' => !empty($data['post']['slug']) 
                ? sanitize_title($data['post']['slug']) 
                : sanitize_title($data['post']['title']),

            // Taxonomias
            'post_category' => $post_category,
            'tags_input' => $tags_input,

            // Configurações de interação
            'comment_status' => get_default_comment_status($content_type),
            'ping_status' => get_option('default_ping_status'),

            // Metadados
            'meta_input' => [
                '_aicg_generated' => '1',
                '_aicg_generation_time' => current_time('mysql'),
                '_aicg_model_used' => $data['metadata']['model'] ?? 'unknown',
                '_aicg_tokens_input' => $data['metadata']['tokens_input'] ?? 0,
                '_aicg_tokens_output' => $data['metadata']['tokens_output'] ?? 0,
            ],
        ];

        // Remove campos nulos
        return array_filter($post_data, function ($value) {
            return $value !== null;
        });
    }

    /**
     * Salva metadados de rastreamento
     *
     * @param int $post_id ID do post
     * @param array $data Dados da geração
     * @param array $options Opções usadas
     * @return void
     */
    private function save_generation_meta(int $post_id, array $data, array $options): void
    {
        // Prompt original
        if (!empty($options['original_prompt'])) {
            update_post_meta($post_id, '_aicg_prompt_original', sanitize_textarea_field($options['original_prompt']));
        }

        // Dados de processamento
        update_post_meta($post_id, '_aicg_generation_duration', $data['metadata']['generation_duration'] ?? 0);
        
        // Custo estimado (simplificado)
        $input_tokens = $data['metadata']['tokens_input'] ?? 0;
        $output_tokens = $data['metadata']['tokens_output'] ?? 0;
        // Preço aproximado: $0.0001 por 1K tokens (pode variar por modelo)
        $cost_estimate = ($input_tokens + $output_tokens) * 0.0000001;
        update_post_meta($post_id, '_aicg_cost_estimate', round($cost_estimate, 6));

        // Status de revisão inicial
        wp_set_object_terms($post_id, 'pending_review', 'ai_review_status', false);
    }

    /**
     * Atualiza um post existente
     *
     * @param int $post_id ID do post
     * @param array $data Novos dados
     * @param array $options Opções
     * @return array|\WP_Error
     */
    public function update(int $post_id, array $data, array $options = [])
    {
        $post = get_post($post_id);

        if (!$post) {
            return new \WP_Error(
                'post_not_found',
                esc_html__('Post não encontrado.', 'ai-content-generator')
            );
        }

        // Verifica permissões
        if (!current_user_can('edit_post', $post_id)) {
            return new \WP_Error(
                'permission_denied',
                esc_html__('Sem permissão para editar este post.', 'ai-content-generator')
            );
        }

        $update_data = [
            'ID' => $post_id,
        ];

        if (!empty($data['post']['title'])) {
            $update_data['post_title'] = sanitize_text_field($data['post']['title']);
        }

        if (!empty($data['post']['content'])) {
            $update_data['post_content'] = wp_kses_post($data['post']['content']);
        }

        if (!empty($data['post']['excerpt'])) {
            $update_data['post_excerpt'] = sanitize_text_field($data['post']['excerpt']);
        }

        $result = wp_update_post($update_data, true);

        if (is_wp_error($result)) {
            return $result;
        }

        // Atualiza SEO
        if (!empty($data['seo'])) {
            $this->seo_integration->update_seo_meta($post_id, $data['seo']);
        }

        return [
            'post_id' => $post_id,
            'edit_link' => get_edit_post_link($post_id, 'raw'),
            'preview_link' => get_preview_post_link($post_id),
        ];
    }

    /**
     * Verifica se um post foi gerado por IA
     *
     * @param int $post_id ID do post
     * @return bool
     */
    public static function is_ai_generated(int $post_id): bool
    {
        return (bool) get_post_meta($post_id, '_aicg_generated', true);
    }

    /**
     * Obtém estatísticas de posts gerados
     *
     * @return array
     */
    public static function get_statistics(): array
    {
        global $wpdb;

        $total = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->postmeta} 
             WHERE meta_key = '_aicg_generated' AND meta_value = '1'"
        );

        $by_status = $wpdb->get_results(
            "SELECT p.post_status, COUNT(*) as count 
             FROM {$wpdb->posts} p 
             INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
             WHERE pm.meta_key = '_aicg_generated' AND pm.meta_value = '1'
             AND p.post_status IN ('publish', 'draft', 'future', 'pending')
             GROUP BY p.post_status",
            OBJECT_K
        );

        $this_month = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->postmeta} pm 
                 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                 WHERE pm.meta_key = '_aicg_generated' AND pm.meta_value = '1'
                 AND p.post_date >= %s",
                date('Y-m-01')
            )
        );

        return [
            'total' => (int) $total,
            'by_status' => $by_status,
            'this_month' => (int) $this_month,
        ];
    }
}
