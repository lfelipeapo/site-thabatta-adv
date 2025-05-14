<?php
/**
 * Componente de administração para o Customizer
 * 
 * Gerencia a integração com o Customizer do WordPress
 * 
 * @package WPFramework\Core\Admin
 */

namespace WPFramework\Core\Admin;

class CustomizerComponent extends BaseAdminComponent
{
    /**
     * Inicializa o componente
     * 
     * @return void
     */
    public function init()
    {
        // Registra hooks e filtros
        add_action('customize_register', [$this, 'registerCustomizerSettings']);
        add_action('customize_preview_init', [$this, 'customizePreviewInit']);
    }
    
    /**
     * Registra as configurações do Customizer
     * 
     * @param \WP_Customize_Manager $wp_customize Objeto do Customizer
     * @return void
     */
    public function registerCustomizerSettings($wp_customize)
    {
        // Seção de Configurações Gerais
        $wp_customize->add_section('wpframework_general_settings', [
            'title'    => __('Configurações Gerais', 'wpframework'),
            'priority' => 30,
        ]);
        
        // Configuração para o logotipo
        $wp_customize->add_setting('wpframework_logo', [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ]);
        
        $wp_customize->add_control(new \WP_Customize_Image_Control($wp_customize, 'wpframework_logo', [
            'label'    => __('Logotipo', 'wpframework'),
            'section'  => 'wpframework_general_settings',
            'settings' => 'wpframework_logo',
        ]));
        
        // Configuração para cores primárias
        $wp_customize->add_setting('wpframework_primary_color', [
            'default'           => '#0066cc',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ]);
        
        $wp_customize->add_control(new \WP_Customize_Color_Control($wp_customize, 'wpframework_primary_color', [
            'label'    => __('Cor Primária', 'wpframework'),
            'section'  => 'wpframework_general_settings',
            'settings' => 'wpframework_primary_color',
        ]));
        
        // Configuração para cores secundárias
        $wp_customize->add_setting('wpframework_secondary_color', [
            'default'           => '#6c757d',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ]);
        
        $wp_customize->add_control(new \WP_Customize_Color_Control($wp_customize, 'wpframework_secondary_color', [
            'label'    => __('Cor Secundária', 'wpframework'),
            'section'  => 'wpframework_general_settings',
            'settings' => 'wpframework_secondary_color',
        ]));
        
        // Configuração para informações de contato
        $wp_customize->add_setting('wpframework_contact_email', [
            'default'           => '',
            'sanitize_callback' => 'sanitize_email',
            'transport'         => 'refresh',
        ]);
        
        $wp_customize->add_control('wpframework_contact_email', [
            'label'    => __('E-mail de Contato', 'wpframework'),
            'section'  => 'wpframework_general_settings',
            'settings' => 'wpframework_contact_email',
            'type'     => 'email',
        ]);
        
        $wp_customize->add_setting('wpframework_contact_phone', [
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ]);
        
        $wp_customize->add_control('wpframework_contact_phone', [
            'label'    => __('Telefone de Contato', 'wpframework'),
            'section'  => 'wpframework_general_settings',
            'settings' => 'wpframework_contact_phone',
            'type'     => 'text',
        ]);
        
        // Seção de Redes Sociais
        $wp_customize->add_section('wpframework_social_media', [
            'title'    => __('Redes Sociais', 'wpframework'),
            'priority' => 40,
        ]);
        
        // Configuração para Facebook
        $wp_customize->add_setting('wpframework_facebook_url', [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ]);
        
        $wp_customize->add_control('wpframework_facebook_url', [
            'label'    => __('URL do Facebook', 'wpframework'),
            'section'  => 'wpframework_social_media',
            'settings' => 'wpframework_facebook_url',
            'type'     => 'url',
        ]);
        
        // Configuração para Instagram
        $wp_customize->add_setting('wpframework_instagram_url', [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ]);
        
        $wp_customize->add_control('wpframework_instagram_url', [
            'label'    => __('URL do Instagram', 'wpframework'),
            'section'  => 'wpframework_social_media',
            'settings' => 'wpframework_instagram_url',
            'type'     => 'url',
        ]);
        
        // Configuração para Twitter
        $wp_customize->add_setting('wpframework_twitter_url', [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ]);
        
        $wp_customize->add_control('wpframework_twitter_url', [
            'label'    => __('URL do Twitter', 'wpframework'),
            'section'  => 'wpframework_social_media',
            'settings' => 'wpframework_twitter_url',
            'type'     => 'url',
        ]);
        
        // Configuração para LinkedIn
        $wp_customize->add_setting('wpframework_linkedin_url', [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ]);
        
        $wp_customize->add_control('wpframework_linkedin_url', [
            'label'    => __('URL do LinkedIn', 'wpframework'),
            'section'  => 'wpframework_social_media',
            'settings' => 'wpframework_linkedin_url',
            'type'     => 'url',
        ]);
        
        // Configuração para YouTube
        $wp_customize->add_setting('wpframework_youtube_url', [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ]);
        
        $wp_customize->add_control('wpframework_youtube_url', [
            'label'    => __('URL do YouTube', 'wpframework'),
            'section'  => 'wpframework_social_media',
            'settings' => 'wpframework_youtube_url',
            'type'     => 'url',
        ]);
        
        // Seção de Layout
        $wp_customize->add_section('wpframework_layout', [
            'title'    => __('Layout', 'wpframework'),
            'priority' => 50,
        ]);
        
        // Configuração para layout da página inicial
        $wp_customize->add_setting('wpframework_homepage_layout', [
            'default'           => 'default',
            'sanitize_callback' => [$this, 'sanitizeSelect'],
            'transport'         => 'refresh',
        ]);
        
        $wp_customize->add_control('wpframework_homepage_layout', [
            'label'    => __('Layout da Página Inicial', 'wpframework'),
            'section'  => 'wpframework_layout',
            'settings' => 'wpframework_homepage_layout',
            'type'     => 'select',
            'choices'  => [
                'default'  => __('Padrão', 'wpframework'),
                'grid'     => __('Grade', 'wpframework'),
                'masonry'  => __('Masonry', 'wpframework'),
                'featured' => __('Destaque', 'wpframework'),
            ],
        ]);
        
        // Configuração para layout de posts
        $wp_customize->add_setting('wpframework_post_layout', [
            'default'           => 'default',
            'sanitize_callback' => [$this, 'sanitizeSelect'],
            'transport'         => 'refresh',
        ]);
        
        $wp_customize->add_control('wpframework_post_layout', [
            'label'    => __('Layout de Posts', 'wpframework'),
            'section'  => 'wpframework_layout',
            'settings' => 'wpframework_post_layout',
            'type'     => 'select',
            'choices'  => [
                'default'      => __('Padrão', 'wpframework'),
                'full-width'   => __('Largura Total', 'wpframework'),
                'no-sidebar'   => __('Sem Barra Lateral', 'wpframework'),
                'left-sidebar' => __('Barra Lateral à Esquerda', 'wpframework'),
            ],
        ]);
        
        // Configuração para layout de páginas
        $wp_customize->add_setting('wpframework_page_layout', [
            'default'           => 'default',
            'sanitize_callback' => [$this, 'sanitizeSelect'],
            'transport'         => 'refresh',
        ]);
        
        $wp_customize->add_control('wpframework_page_layout', [
            'label'    => __('Layout de Páginas', 'wpframework'),
            'section'  => 'wpframework_layout',
            'settings' => 'wpframework_page_layout',
            'type'     => 'select',
            'choices'  => [
                'default'      => __('Padrão', 'wpframework'),
                'full-width'   => __('Largura Total', 'wpframework'),
                'no-sidebar'   => __('Sem Barra Lateral', 'wpframework'),
                'left-sidebar' => __('Barra Lateral à Esquerda', 'wpframework'),
            ],
        ]);
        
        // Configuração para layout de arquivos
        $wp_customize->add_setting('wpframework_archive_layout', [
            'default'           => 'default',
            'sanitize_callback' => [$this, 'sanitizeSelect'],
            'transport'         => 'refresh',
        ]);
        
        $wp_customize->add_control('wpframework_archive_layout', [
            'label'    => __('Layout de Arquivos', 'wpframework'),
            'section'  => 'wpframework_layout',
            'settings' => 'wpframework_archive_layout',
            'type'     => 'select',
            'choices'  => [
                'default'      => __('Padrão', 'wpframework'),
                'grid'         => __('Grade', 'wpframework'),
                'masonry'      => __('Masonry', 'wpframework'),
                'list'         => __('Lista', 'wpframework'),
                'full-width'   => __('Largura Total', 'wpframework'),
                'no-sidebar'   => __('Sem Barra Lateral', 'wpframework'),
                'left-sidebar' => __('Barra Lateral à Esquerda', 'wpframework'),
            ],
        ]);
        
        // Seção de Rodapé
        $wp_customize->add_section('wpframework_footer', [
            'title'    => __('Rodapé', 'wpframework'),
            'priority' => 60,
        ]);
        
        // Configuração para texto do rodapé
        $wp_customize->add_setting('wpframework_footer_text', [
            'default'           => '&copy; ' . date('Y') . ' ' . get_bloginfo('name') . '. ' . __('Todos os direitos reservados.', 'wpframework'),
            'sanitize_callback' => 'wp_kses_post',
            'transport'         => 'refresh',
        ]);
        
        $wp_customize->add_control('wpframework_footer_text', [
            'label'    => __('Texto do Rodapé', 'wpframework'),
            'section'  => 'wpframework_footer',
            'settings' => 'wpframework_footer_text',
            'type'     => 'textarea',
        ]);
        
        // Configuração para exibir widgets no rodapé
        $wp_customize->add_setting('wpframework_footer_widgets', [
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ]);
        
        $wp_customize->add_control('wpframework_footer_widgets', [
            'label'    => __('Exibir Widgets no Rodapé', 'wpframework'),
            'section'  => 'wpframework_footer',
            'settings' => 'wpframework_footer_widgets',
            'type'     => 'checkbox',
        ]);
        
        // Configuração para número de colunas de widgets no rodapé
        $wp_customize->add_setting('wpframework_footer_widgets_columns', [
            'default'           => 3,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ]);
        
        $wp_customize->add_control('wpframework_footer_widgets_columns', [
            'label'    => __('Número de Colunas de Widgets no Rodapé', 'wpframework'),
            'section'  => 'wpframework_footer',
            'settings' => 'wpframework_footer_widgets_columns',
            'type'     => 'number',
            'input_attrs' => [
                'min'  => 1,
                'max'  => 4,
                'step' => 1,
            ],
        ]);
    }
    
    /**
     * Inicializa o preview do Customizer
     * 
     * @return void
     */
    public function customizePreviewInit()
    {
        wp_enqueue_script(
            'wpframework-customizer-preview',
            get_template_directory_uri() . '/public/js/customizer-preview.js',
            ['jquery', 'customize-preview'],
            '1.0.0',
            true
        );
    }
    
    /**
     * Sanitiza um valor de select
     * 
     * @param string $input Valor a ser sanitizado
     * @param \WP_Customize_Setting $setting Objeto de configuração
     * @return string
     */
    public function sanitizeSelect($input, $setting)
    {
        // Obtém as opções válidas
        $choices = $setting->manager->get_control($setting->id)->choices;
        
        // Retorna o valor se for válido ou o valor padrão
        return (array_key_exists($input, $choices) ? $input : $setting->default);
    }
    
    /**
     * Obtém uma configuração do Customizer
     * 
     * @param string $setting Nome da configuração
     * @param mixed $default Valor padrão
     * @return mixed
     */
    public static function getOption($setting, $default = '')
    {
        return get_theme_mod($setting, $default);
    }
}
