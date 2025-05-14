<?php
/**
 * Componente de administração para o Classic Editor
 * 
 * Gerencia a integração com o Classic Editor do WordPress
 * 
 * @package WPFramework\Core\Admin
 */

namespace WPFramework\Core\Admin;

class ClassicEditorComponent extends BaseAdminComponent
{
    /**
     * Inicializa o componente
     * 
     * @return void
     */
    public function init()
    {
        // Registra hooks e filtros
        add_action('admin_init', [$this, 'registerEditorSettings']);
        add_filter('mce_buttons', [$this, 'registerButtons']);
        add_filter('mce_external_plugins', [$this, 'registerPlugins']);
        add_filter('tiny_mce_before_init', [$this, 'beforeInit']);
        add_filter('content_save_pre', [$this, 'contentSavePre']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
    }
    
    /**
     * Registra configurações do editor
     * 
     * @return void
     */
    public function registerEditorSettings()
    {
        // Adiciona suporte para editor de estilo
        add_editor_style('public/css/editor-style.css');
        
        // Adiciona suporte para miniaturas de posts
        add_theme_support('post-thumbnails');
        
        // Adiciona suporte para formatos de posts
        add_theme_support('post-formats', [
            'aside',
            'gallery',
            'link',
            'image',
            'quote',
            'status',
            'video',
            'audio',
            'chat',
        ]);
    }
    
    /**
     * Registra botões adicionais para o editor
     * 
     * @param array $buttons Botões existentes
     * @return array
     */
    public function registerButtons($buttons)
    {
        // Adiciona botões personalizados
        $buttons[] = 'styleselect';
        $buttons[] = 'table';
        
        return $buttons;
    }
    
    /**
     * Registra plugins adicionais para o editor
     * 
     * @param array $plugins Plugins existentes
     * @return array
     */
    public function registerPlugins($plugins)
    {
        // Adiciona plugins personalizados
        $plugins['table'] = get_template_directory_uri() . '/public/js/tinymce/plugins/table/plugin.min.js';
        $plugins['wpframework'] = get_template_directory_uri() . '/public/js/tinymce/plugins/wpframework/plugin.js';
        
        return $plugins;
    }
    
    /**
     * Configura o editor antes da inicialização
     * 
     * @param array $settings Configurações do editor
     * @return array
     */
    public function beforeInit($settings)
    {
        // Adiciona estilos personalizados ao editor
        $style_formats = [
            [
                'title' => 'Botões',
                'items' => [
                    [
                        'title' => 'Botão Primário',
                        'inline' => 'span',
                        'classes' => 'button',
                        'wrapper' => true,
                    ],
                    [
                        'title' => 'Botão Secundário',
                        'inline' => 'span',
                        'classes' => 'button button-secondary',
                        'wrapper' => true,
                    ],
                    [
                        'title' => 'Botão Grande',
                        'inline' => 'span',
                        'classes' => 'button button-large',
                        'wrapper' => true,
                    ],
                ],
            ],
            [
                'title' => 'Caixas',
                'items' => [
                    [
                        'title' => 'Caixa de Informação',
                        'block' => 'div',
                        'classes' => 'info-box',
                        'wrapper' => true,
                    ],
                    [
                        'title' => 'Caixa de Alerta',
                        'block' => 'div',
                        'classes' => 'alert-box',
                        'wrapper' => true,
                    ],
                    [
                        'title' => 'Caixa de Sucesso',
                        'block' => 'div',
                        'classes' => 'success-box',
                        'wrapper' => true,
                    ],
                ],
            ],
            [
                'title' => 'Texto',
                'items' => [
                    [
                        'title' => 'Destaque',
                        'inline' => 'span',
                        'classes' => 'highlight',
                    ],
                    [
                        'title' => 'Texto Grande',
                        'inline' => 'span',
                        'classes' => 'large-text',
                    ],
                    [
                        'title' => 'Texto Pequeno',
                        'inline' => 'span',
                        'classes' => 'small-text',
                    ],
                ],
            ],
        ];
        
        $settings['style_formats'] = json_encode($style_formats);
        
        // Configura o editor para usar classes em vez de estilos inline
        $settings['convert_fonts_to_spans'] = true;
        
        // Configura o editor para permitir elementos HTML5
        $settings['extended_valid_elements'] = 'article[*],section[*],aside[*],figure[*],figcaption[*],footer[*],header[*],nav[*],dialog[*],popover[*],template[*],slot[*],custom-element[*]';
        
        // Configura o editor para permitir atributos personalizados
        $settings['custom_elements'] = 'article,section,aside,figure,figcaption,footer,header,nav,dialog,popover,template,slot,custom-element';
        
        return $settings;
    }
    
    /**
     * Processa o conteúdo antes de salvar
     * 
     * @param string $content Conteúdo a ser salvo
     * @return string
     */
    public function contentSavePre($content)
    {
        // Processa o conteúdo antes de salvar
        // Por exemplo, pode-se adicionar classes, remover atributos indesejados, etc.
        
        return $content;
    }
    
    /**
     * Carrega scripts e estilos para o editor
     * 
     * @param string $hook Hook atual
     * @return void
     */
    public function enqueueScripts($hook)
    {
        // Verifica se estamos na página de edição de post ou página
        if (!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }
        
        // Carrega scripts e estilos para o editor
        wp_enqueue_script(
            'wpframework-editor',
            get_template_directory_uri() . '/public/js/editor.js',
            ['jquery'],
            '1.0.0',
            true
        );
        
        wp_enqueue_style(
            'wpframework-editor-style',
            get_template_directory_uri() . '/public/css/editor-style.css',
            [],
            '1.0.0'
        );
        
        // Passa variáveis para o script
        wp_localize_script('wpframework-editor', 'wpframeworkEditor', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wpframework-editor'),
        ]);
    }
}
