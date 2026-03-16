<?php
/**
 * Integração com plugins de SEO
 *
 * @package AICG\SEO
 * @since   1.0.0
 */

namespace AICG\SEO;

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class SEOIntegration
 *
 * Detecta e integra com plugins de SEO populares
 *
 * @package AICG\SEO
 * @since   1.0.0
 */
class SEOIntegration
{
    /**
     * Plugin SEO detectado
     *
     * @var string|null
     */
    private ?string $active_plugin = null;

    /**
     * Mapeamento de campos por plugin
     *
     * @var array
     */
    private array $field_mapping = [
        'yoast' => [
            'meta_title' => '_yoast_wpseo_title',
            'meta_description' => '_yoast_wpseo_metadesc',
            'focus_keyword' => '_yoast_wpseo_focuskw',
            'keywords' => '_yoast_wpseo_keywordsynonyms',
            'canonical_url' => '_yoast_wpseo_canonical',
        ],
        'rankmath' => [
            'meta_title' => 'rank_math_title',
            'meta_description' => 'rank_math_description',
            'focus_keyword' => 'rank_math_focus_keyword',
            'keywords' => 'rank_math_focus_keywords',
            'canonical_url' => 'rank_math_canonical_url',
        ],
        'seopress' => [
            'meta_title' => '_seopress_titles_title',
            'meta_description' => '_seopress_titles_desc',
            'focus_keyword' => '_seopress_analysis_target_kw',
            'keywords' => null, // SEOPress não tem keywords secundárias nativamente
            'canonical_url' => '_seopress_robots_canonical',
        ],
        'aioseo' => [
            'meta_title' => '_aioseo_title',
            'meta_description' => '_aioseo_description',
            'focus_keyword' => '_aioseo_keywords',
            'keywords' => null,
            'canonical_url' => '_aioseo_canonical_url',
        ],
        'seo_framework' => [
            'meta_title' => '_genesis_title',
            'meta_description' => '_genesis_description',
            'focus_keyword' => null,
            'keywords' => null,
            'canonical_url' => '_genesis_canonical_uri',
        ],
    ];

    /**
     * Construtor
     */
    public function __construct()
    {
        $this->detect_seo_plugin();
    }

    /**
     * Detecta qual plugin SEO está ativo
     *
     * @return void
     */
    private function detect_seo_plugin(): void
    {
        // Yoast SEO
        if (defined('WPSEO_VERSION') || class_exists('Yoast\WP\SEO\Main')) {
            $this->active_plugin = 'yoast';
            return;
        }

        // Rank Math
        if (class_exists('RankMath') || function_exists('rank_math')) {
            $this->active_plugin = 'rankmath';
            return;
        }

        // SEOPress
        if (defined('SEOPRESS_VERSION') || function_exists('seopress_init')) {
            $this->active_plugin = 'seopress';
            return;
        }

        // All in One SEO
        if (class_exists('AIOSEO\Plugin\AIOSEO')) {
            $this->active_plugin = 'aioseo';
            return;
        }

        // The SEO Framework
        if (defined('THE_SEO_FRAMEWORK_VERSION')) {
            $this->active_plugin = 'seo_framework';
            return;
        }
    }

    /**
     * Atualiza metadados SEO de um post
     *
     * @param int $post_id ID do post
     * @param array $seo_data Dados SEO
     * @return void
     */
    public function update_seo_meta(int $post_id, array $seo_data): void
    {
        // Usa método específico do plugin detectado
        if ($this->active_plugin && method_exists($this, "update_{$this->active_plugin}_meta")) {
            call_user_func([$this, "update_{$this->active_plugin}_meta"], $post_id, $seo_data);
        }

        // Sempre salva em fallback para portabilidade
        $this->save_fallback_meta($post_id, $seo_data);
    }

    /**
     * Atualiza metadados para Yoast SEO
     *
     * @param int $post_id ID do post
     * @param array $seo_data Dados SEO
     * @return void
     */
    private function update_yoast_meta(int $post_id, array $seo_data): void
    {
        $mapping = $this->field_mapping['yoast'];

        if (!empty($seo_data['meta_title'])) {
            update_post_meta($post_id, $mapping['meta_title'], sanitize_text_field($seo_data['meta_title']));
        }

        if (!empty($seo_data['meta_description'])) {
            update_post_meta($post_id, $mapping['meta_description'], sanitize_textarea_field($seo_data['meta_description']));
        }

        if (!empty($seo_data['focus_keyword'])) {
            update_post_meta($post_id, $mapping['focus_keyword'], sanitize_text_field($seo_data['focus_keyword']));
        }

        if (!empty($seo_data['keywords']) && is_array($seo_data['keywords'])) {
            update_post_meta($post_id, $mapping['keywords'], sanitize_text_field(implode(',', $seo_data['keywords'])));
        }
    }

    /**
     * Atualiza metadados para Rank Math
     *
     * @param int $post_id ID do post
     * @param array $seo_data Dados SEO
     * @return void
     */
    private function update_rankmath_meta(int $post_id, array $seo_data): void
    {
        if (!empty($seo_data['meta_title'])) {
            update_post_meta($post_id, 'rank_math_title', sanitize_text_field($seo_data['meta_title']));
        }

        if (!empty($seo_data['meta_description'])) {
            update_post_meta($post_id, 'rank_math_description', sanitize_textarea_field($seo_data['meta_description']));
        }

        if (!empty($seo_data['focus_keyword'])) {
            update_post_meta($post_id, 'rank_math_focus_keyword', sanitize_text_field($seo_data['focus_keyword']));
        }

        if (!empty($seo_data['keywords']) && is_array($seo_data['keywords'])) {
            // Rank Math armazena keywords como array serializado
            update_post_meta($post_id, 'rank_math_focus_keywords', $seo_data['keywords']);
        }
    }

    /**
     * Atualiza metadados para SEOPress
     *
     * @param int $post_id ID do post
     * @param array $seo_data Dados SEO
     * @return void
     */
    private function update_seopress_meta(int $post_id, array $seo_data): void
    {
        if (!empty($seo_data['meta_title'])) {
            update_post_meta($post_id, '_seopress_titles_title', sanitize_text_field($seo_data['meta_title']));
        }

        if (!empty($seo_data['meta_description'])) {
            update_post_meta($post_id, '_seopress_titles_desc', sanitize_textarea_field($seo_data['meta_description']));
        }

        if (!empty($seo_data['focus_keyword'])) {
            update_post_meta($post_id, '_seopress_analysis_target_kw', sanitize_text_field($seo_data['focus_keyword']));
        }
    }

    /**
     * Atualiza metadados para All in One SEO
     *
     * @param int $post_id ID do post
     * @param array $seo_data Dados SEO
     * @return void
     */
    private function update_aioseo_meta(int $post_id, array $seo_data): void
    {
        if (!empty($seo_data['meta_title'])) {
            update_post_meta($post_id, '_aioseo_title', sanitize_text_field($seo_data['meta_title']));
        }

        if (!empty($seo_data['meta_description'])) {
            update_post_meta($post_id, '_aioseo_description', sanitize_textarea_field($seo_data['meta_description']));
        }

        if (!empty($seo_data['focus_keyword'])) {
            update_post_meta($post_id, '_aioseo_keywords', sanitize_text_field($seo_data['focus_keyword']));
        }
    }

    /**
     * Atualiza metadados para The SEO Framework
     *
     * @param int $post_id ID do post
     * @param array $seo_data Dados SEO
     * @return void
     */
    private function update_seo_framework_meta(int $post_id, array $seo_data): void
    {
        if (!empty($seo_data['meta_title'])) {
            update_post_meta($post_id, '_genesis_title', sanitize_text_field($seo_data['meta_title']));
        }

        if (!empty($seo_data['meta_description'])) {
            update_post_meta($post_id, '_genesis_description', sanitize_textarea_field($seo_data['meta_description']));
        }
    }

    /**
     * Salva metadados de fallback
     *
     * @param int $post_id ID do post
     * @param array $seo_data Dados SEO
     * @return void
     */
    private function save_fallback_meta(int $post_id, array $seo_data): void
    {
        if (!empty($seo_data['meta_title'])) {
            update_post_meta($post_id, '_aicg_seo_title', sanitize_text_field($seo_data['meta_title']));
        }

        if (!empty($seo_data['meta_description'])) {
            update_post_meta($post_id, '_aicg_seo_description', sanitize_textarea_field($seo_data['meta_description']));
        }

        if (!empty($seo_data['focus_keyword'])) {
            update_post_meta($post_id, '_aicg_seo_focus_keyword', sanitize_text_field($seo_data['focus_keyword']));
        }

        if (!empty($seo_data['keywords']) && is_array($seo_data['keywords'])) {
            update_post_meta(
                $post_id,
                '_aicg_seo_keywords',
                implode(',', array_map('sanitize_text_field', $seo_data['keywords']))
            );
        }

        if (!empty($seo_data['canonical_url'])) {
            update_post_meta($post_id, '_aicg_seo_canonical', esc_url_raw($seo_data['canonical_url']));
        }
    }

    /**
     * Obtém o plugin SEO ativo
     *
     * @return string|null
     */
    public function get_active_plugin(): ?string
    {
        return $this->active_plugin;
    }

    /**
     * Verifica se há algum plugin SEO ativo
     *
     * @return bool
     */
    public function has_seo_plugin(): bool
    {
        return $this->active_plugin !== null;
    }

    /**
     * Obtém metadados SEO de um post
     *
     * @param int $post_id ID do post
     * @return array
     */
    public function get_seo_meta(int $post_id): array
    {
        $meta = [];

        // Tenta obter do plugin ativo primeiro
        if ($this->active_plugin) {
            $mapping = $this->field_mapping[$this->active_plugin];

            foreach (['meta_title', 'meta_description', 'focus_keyword'] as $field) {
                if (!empty($mapping[$field])) {
                    $meta[$field] = get_post_meta($post_id, $mapping[$field], true);
                }
            }
        }

        // Fallback para nossos campos
        if (empty($meta['meta_title'])) {
            $meta['meta_title'] = get_post_meta($post_id, '_aicg_seo_title', true);
        }

        if (empty($meta['meta_description'])) {
            $meta['meta_description'] = get_post_meta($post_id, '_aicg_seo_description', true);
        }

        if (empty($meta['focus_keyword'])) {
            $meta['focus_keyword'] = get_post_meta($post_id, '_aicg_seo_focus_keyword', true);
        }

        return $meta;
    }
}
