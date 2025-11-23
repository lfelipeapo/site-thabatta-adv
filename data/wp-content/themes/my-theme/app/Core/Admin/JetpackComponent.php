<?php
/**
 * Componente de administração para o Jetpack
 * 
 * Gerencia a integração com o Jetpack do WordPress
 * 
 * @package WPFramework\Core\Admin
 */

namespace WPFramework\Core\Admin;

class JetpackComponent extends BaseAdminComponent
{
    /**
     * Inicializa o componente
     * 
     * @return void
     */
    public function init()
    {
        // Verifica se o Jetpack está ativo
        if (!$this->isJetpackActive()) {
            return;
        }
        
        // Registra hooks e filtros
        add_filter('jetpack_development_mode', [$this, 'jetpackDevelopmentMode']);
        add_action('jetpack_modules_loaded', [$this, 'jetpackModulesLoaded']);
        add_filter('jetpack_active_modules', [$this, 'jetpackActiveModules']);
        add_filter('jetpack_sharing_services', [$this, 'jetpackSharingServices']);
        add_filter('jetpack_relatedposts_filter_options', [$this, 'jetpackRelatedPostsOptions']);
        add_filter('jetpack_contact_form_button_markup', [$this, 'jetpackContactFormButtonMarkup']);
        add_filter('jetpack_lazy_images_skip_image_with_attributes', [$this, 'jetpackLazyImagesSkipImageWithAttributes'], 10, 2);
        add_filter('jetpack_photon_skip_for_url', [$this, 'jetpackPhotonSkipForUrl'], 10, 2);
        add_filter('jetpack_sitemap_post_types', [$this, 'jetpackSitemapPostTypes']);
        add_filter('jetpack_sitemap_taxonomies', [$this, 'jetpackSitemapTaxonomies']);
    }
    
    /**
     * Verifica se o Jetpack está ativo
     * 
     * @return bool
     */
    private function isJetpackActive()
    {
        return class_exists('Jetpack') && \Jetpack::is_active();
    }
    
    /**
     * Define o modo de desenvolvimento do Jetpack
     * 
     * @param bool $development_mode Modo de desenvolvimento atual
     * @return bool
     */
    public function jetpackDevelopmentMode($development_mode)
    {
        // Ativa o modo de desenvolvimento em ambientes de desenvolvimento
        if (defined('WP_ENVIRONMENT_TYPE') && WP_ENVIRONMENT_TYPE === 'development') {
            return true;
        }
        
        return $development_mode;
    }
    
    /**
     * Executa ações quando os módulos do Jetpack são carregados
     * 
     * @return void
     */
    public function jetpackModulesLoaded()
    {
        // Ações a serem executadas quando os módulos do Jetpack são carregados
    }
    
    /**
     * Filtra os módulos ativos do Jetpack
     * 
     * @param array $modules Módulos ativos
     * @return array
     */
    public function jetpackActiveModules($modules)
    {
        // Módulos recomendados para ativar
        $recommended_modules = [
            'carousel',
            'contact-form',
            'lazy-images',
            'photon',
            'related-posts',
            'responsive-videos',
            'search',
            'seo-tools',
            'sharing',
            'shortcodes',
            'sitemaps',
            'tiled-gallery',
            'widget-visibility',
            'widgets',
            'wpforms',
        ];
        
        // Módulos para desativar
        $disabled_modules = [
            'comments', // Desativa comentários do Jetpack se estiver usando comentários nativos
            'subscriptions', // Desativa assinaturas se estiver usando outra solução
        ];
        
        // Adiciona módulos recomendados se não estiverem ativos
        foreach ($recommended_modules as $module) {
            if (!in_array($module, $modules) && \Jetpack::is_module_available($module)) {
                $modules[] = $module;
            }
        }
        
        // Remove módulos desativados
        foreach ($disabled_modules as $module) {
            $key = array_search($module, $modules);
            if ($key !== false) {
                unset($modules[$key]);
            }
        }
        
        return array_values($modules);
    }
    
    /**
     * Filtra os serviços de compartilhamento do Jetpack
     * 
     * @param array $services Serviços de compartilhamento
     * @return array
     */
    public function jetpackSharingServices($services)
    {
        // Personaliza os serviços de compartilhamento
        // Por exemplo, adiciona ou remove serviços
        
        return $services;
    }
    
    /**
     * Filtra as opções de posts relacionados do Jetpack
     * 
     * @param array $options Opções de posts relacionados
     * @return array
     */
    public function jetpackRelatedPostsOptions($options)
    {
        // Personaliza as opções de posts relacionados
        $options['headline'] = __('Artigos Relacionados', 'wpframework');
        $options['size'] = 3;
        $options['show_thumbnails'] = true;
        $options['show_date'] = true;
        $options['show_context'] = true;
        
        return $options;
    }
    
    /**
     * Filtra o markup do botão do formulário de contato do Jetpack
     * 
     * @param string $button_html HTML do botão
     * @return string
     */
    public function jetpackContactFormButtonMarkup($button_html)
    {
        // Personaliza o markup do botão do formulário de contato
        // Por exemplo, adiciona classes ou atributos
        
        return str_replace('class="pushbutton-wide"', 'class="pushbutton-wide button"', $button_html);
    }
    
    /**
     * Filtra as imagens que devem ser ignoradas pelo lazy loading do Jetpack
     * 
     * @param bool $skip Pular a imagem
     * @param array $attributes Atributos da imagem
     * @return bool
     */
    public function jetpackLazyImagesSkipImageWithAttributes($skip, $attributes)
    {
        // Ignora imagens com a classe 'no-lazy'
        if (isset($attributes['class']) && strpos($attributes['class'], 'no-lazy') !== false) {
            return true;
        }
        
        // Ignora imagens de logos
        if (isset($attributes['class']) && strpos($attributes['class'], 'logo') !== false) {
            return true;
        }
        
        return $skip;
    }
    
    /**
     * Filtra as URLs que devem ser ignoradas pelo Photon do Jetpack
     * 
     * @param bool $skip Pular a URL
     * @param string $image_url URL da imagem
     * @return bool
     */
    public function jetpackPhotonSkipForUrl($skip, $image_url)
    {
        // Ignora imagens de logos
        if (strpos($image_url, 'logo') !== false) {
            return true;
        }
        
        return $skip;
    }
    
    /**
     * Filtra os tipos de post para o sitemap do Jetpack
     * 
     * @param array $post_types Tipos de post
     * @return array
     */
    public function jetpackSitemapPostTypes($post_types)
    {
        // Adiciona o tipo de post 'curso' ao sitemap
        $post_types[] = 'curso';
        
        return $post_types;
    }
    
    /**
     * Filtra as taxonomias para o sitemap do Jetpack
     * 
     * @param array $taxonomies Taxonomias
     * @return array
     */
    public function jetpackSitemapTaxonomies($taxonomies)
    {
        // Adiciona a taxonomia 'categoria_curso' ao sitemap
        $taxonomies[] = 'categoria_curso';
        
        return $taxonomies;
    }
}
