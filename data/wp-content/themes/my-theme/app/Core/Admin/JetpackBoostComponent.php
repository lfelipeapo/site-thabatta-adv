<?php
/**
 * Componente de administração para o Jetpack Boost
 * 
 * Gerencia a integração com o Jetpack Boost do WordPress
 * 
 * @package WPFramework\Core\Admin
 */

namespace WPFramework\Core\Admin;

class JetpackBoostComponent extends BaseAdminComponent
{
    /**
     * Inicializa o componente
     * 
     * @return void
     */
    public function init()
    {
        // Verifica se o Jetpack Boost está ativo
        if (!$this->isJetpackBoostActive()) {
            return;
        }
        
        // Registra hooks e filtros
        add_filter('jetpack_boost_excluded_css_selectors', [$this, 'excludedCssSelectors']);
        add_filter('jetpack_boost_excluded_js_selectors', [$this, 'excludedJsSelectors']);
        add_filter('jetpack_boost_critical_css_exclude_urls', [$this, 'excludeUrlsFromCriticalCss']);
        add_filter('jetpack_boost_ds_excluded_post_types', [$this, 'excludedPostTypesFromDeferJs']);
        add_filter('jetpack_boost_lazy_load_excluded_attributes', [$this, 'excludedAttributesFromLazyLoad']);
        add_filter('jetpack_boost_render_blocking_css_post_types', [$this, 'renderBlockingCssPostTypes']);
        add_filter('jetpack_boost_render_blocking_js_post_types', [$this, 'renderBlockingJsPostTypes']);
        add_action('admin_init', [$this, 'registerBoostSettings']);
    }
    
    /**
     * Verifica se o Jetpack Boost está ativo
     * 
     * @return bool
     */
    private function isJetpackBoostActive()
    {
        return class_exists('Automattic\Jetpack_Boost\Jetpack_Boost');
    }
    
    /**
     * Filtra os seletores CSS excluídos do Critical CSS
     * 
     * @param array $selectors Seletores CSS excluídos
     * @return array
     */
    public function excludedCssSelectors($selectors)
    {
        // Adiciona seletores CSS que devem ser excluídos do Critical CSS
        $selectors[] = '.no-critical-css';
        $selectors[] = '.wp-block-jetpack-slideshow';
        $selectors[] = '.wp-block-gallery';
        
        return $selectors;
    }
    
    /**
     * Filtra os seletores JS excluídos do Defer JS
     * 
     * @param array $selectors Seletores JS excluídos
     * @return array
     */
    public function excludedJsSelectors($selectors)
    {
        // Adiciona seletores JS que devem ser excluídos do Defer JS
        $selectors[] = 'script[data-no-defer]';
        $selectors[] = 'script[data-cfasync="false"]';
        $selectors[] = 'script[data-pagespeed-no-defer]';
        
        return $selectors;
    }
    
    /**
     * Filtra as URLs excluídas do Critical CSS
     * 
     * @param array $urls URLs excluídas
     * @return array
     */
    public function excludeUrlsFromCriticalCss($urls)
    {
        // Adiciona URLs que devem ser excluídas do Critical CSS
        $urls[] = '/checkout/';
        $urls[] = '/cart/';
        $urls[] = '/my-account/';
        
        return $urls;
    }
    
    /**
     * Filtra os tipos de post excluídos do Defer JS
     * 
     * @param array $post_types Tipos de post excluídos
     * @return array
     */
    public function excludedPostTypesFromDeferJs($post_types)
    {
        // Adiciona tipos de post que devem ser excluídos do Defer JS
        $post_types[] = 'product';
        
        return $post_types;
    }
    
    /**
     * Filtra os atributos excluídos do Lazy Load
     * 
     * @param array $attributes Atributos excluídos
     * @return array
     */
    public function excludedAttributesFromLazyLoad($attributes)
    {
        // Adiciona atributos que devem ser excluídos do Lazy Load
        $attributes[] = 'data-no-lazy';
        $attributes[] = 'data-skip-lazy';
        $attributes[] = 'data-src';
        
        return $attributes;
    }
    
    /**
     * Filtra os tipos de post para o CSS de bloqueio de renderização
     * 
     * @param array $post_types Tipos de post
     * @return array
     */
    public function renderBlockingCssPostTypes($post_types)
    {
        // Adiciona tipos de post para o CSS de bloqueio de renderização
        $post_types[] = 'curso';
        
        return $post_types;
    }
    
    /**
     * Filtra os tipos de post para o JS de bloqueio de renderização
     * 
     * @param array $post_types Tipos de post
     * @return array
     */
    public function renderBlockingJsPostTypes($post_types)
    {
        // Adiciona tipos de post para o JS de bloqueio de renderização
        $post_types[] = 'curso';
        
        return $post_types;
    }
    
    /**
     * Registra configurações para o Jetpack Boost
     * 
     * @return void
     */
    public function registerBoostSettings()
    {
        // Registra configurações para o Jetpack Boost
        // Isso pode ser usado para adicionar opções personalizadas ao Jetpack Boost
        
        // Exemplo: registrar uma seção de configurações
        add_settings_section(
            'wpframework_jetpack_boost_settings',
            __('Configurações do WPFramework para Jetpack Boost', 'wpframework'),
            [$this, 'renderSettingsSection'],
            'jetpack-boost'
        );
        
        // Exemplo: registrar um campo de configuração
        add_settings_field(
            'wpframework_jetpack_boost_enable_critical_css',
            __('Ativar Critical CSS', 'wpframework'),
            [$this, 'renderEnableCriticalCssField'],
            'jetpack-boost',
            'wpframework_jetpack_boost_settings'
        );
        
        // Exemplo: registrar um campo de configuração
        add_settings_field(
            'wpframework_jetpack_boost_enable_defer_js',
            __('Ativar Defer JS', 'wpframework'),
            [$this, 'renderEnableDeferJsField'],
            'jetpack-boost',
            'wpframework_jetpack_boost_settings'
        );
        
        // Exemplo: registrar um campo de configuração
        add_settings_field(
            'wpframework_jetpack_boost_enable_lazy_load',
            __('Ativar Lazy Load', 'wpframework'),
            [$this, 'renderEnableLazyLoadField'],
            'jetpack-boost',
            'wpframework_jetpack_boost_settings'
        );
    }
    
    /**
     * Renderiza a seção de configurações
     * 
     * @return void
     */
    public function renderSettingsSection()
    {
        echo '<p>' . __('Configurações personalizadas do WPFramework para o Jetpack Boost.', 'wpframework') . '</p>';
    }
    
    /**
     * Renderiza o campo de ativar Critical CSS
     * 
     * @return void
     */
    public function renderEnableCriticalCssField()
    {
        $option = get_option('wpframework_jetpack_boost_enable_critical_css', true);
        echo '<input type="checkbox" name="wpframework_jetpack_boost_enable_critical_css" value="1" ' . checked(1, $option, false) . ' />';
        echo '<p class="description">' . __('Ativa o Critical CSS para melhorar o desempenho de carregamento da página.', 'wpframework') . '</p>';
    }
    
    /**
     * Renderiza o campo de ativar Defer JS
     * 
     * @return void
     */
    public function renderEnableDeferJsField()
    {
        $option = get_option('wpframework_jetpack_boost_enable_defer_js', true);
        echo '<input type="checkbox" name="wpframework_jetpack_boost_enable_defer_js" value="1" ' . checked(1, $option, false) . ' />';
        echo '<p class="description">' . __('Ativa o Defer JS para melhorar o desempenho de carregamento da página.', 'wpframework') . '</p>';
    }
    
    /**
     * Renderiza o campo de ativar Lazy Load
     * 
     * @return void
     */
    public function renderEnableLazyLoadField()
    {
        $option = get_option('wpframework_jetpack_boost_enable_lazy_load', true);
        echo '<input type="checkbox" name="wpframework_jetpack_boost_enable_lazy_load" value="1" ' . checked(1, $option, false) . ' />';
        echo '<p class="description">' . __('Ativa o Lazy Load para melhorar o desempenho de carregamento da página.', 'wpframework') . '</p>';
    }
    
    /**
     * Obtém as configurações do Jetpack Boost
     * 
     * @return array
     */
    public static function getBoostSettings()
    {
        return [
            'enable_critical_css' => get_option('wpframework_jetpack_boost_enable_critical_css', true),
            'enable_defer_js' => get_option('wpframework_jetpack_boost_enable_defer_js', true),
            'enable_lazy_load' => get_option('wpframework_jetpack_boost_enable_lazy_load', true),
        ];
    }
}
