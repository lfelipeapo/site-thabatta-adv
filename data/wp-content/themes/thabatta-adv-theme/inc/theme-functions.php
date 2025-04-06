/**
 * Funções de compatibilidade para configurações legadas
 * Ajuda na transição do sistema antigo para o customizer
 */

/**
 * Obtém uma opção do tema, primeiro verificando no customizer e depois na opção legada
 * @param string $option_name Nome da opção
 * @param mixed $default Valor padrão caso a opção não exista
 * @return mixed
 */
function thabatta_get_theme_option($option_name, $default = false) {
    // Mapeamento de opções antigas para novas
    $options_map = array(
        // Configurações gerais
        'thabatta_google_analytics' => 'general_google_analytics',
        'thabatta_enable_preloader' => 'general_enable_preloader',
        'thabatta_enable_back_to_top' => 'general_enable_back_to_top',
        'thabatta_phone' => 'general_phone',
        'thabatta_email' => 'general_email',
        
        // Redes sociais
        'thabatta_facebook_url' => 'social_facebook_url',
        'thabatta_instagram_url' => 'social_instagram_url',
        'thabatta_linkedin_url' => 'social_linkedin_url',
        'thabatta_twitter_url' => 'social_twitter_url',
        'thabatta_youtube_url' => 'social_youtube_url',
        'thabatta_whatsapp_number' => 'social_whatsapp_number',
        'thabatta_enable_social_sharing' => 'social_enable_sharing',
        
        // SEO
        'thabatta_default_meta_description' => 'seo_default_meta_description',
        'thabatta_default_meta_keywords' => 'seo_default_meta_keywords',
        'thabatta_enable_schema_markup' => 'seo_enable_schema_markup',
        'thabatta_enable_breadcrumbs' => 'seo_enable_breadcrumbs',
        'thabatta_enable_open_graph' => 'seo_enable_open_graph',
        'thabatta_enable_twitter_cards' => 'seo_enable_twitter_cards',
        
        // Cores e tipografia
        'thabatta_primary_color' => 'primary_color',
        'thabatta_secondary_color' => 'secondary_color',
        'thabatta_accent_color' => 'accent_color',
        'thabatta_text_color' => 'text_color',
        'thabatta_heading_font' => 'heading_font',
        'thabatta_body_font' => 'body_font',
        
        // Rodapé
        'horario_atendimento' => 'footer_horario_atendimento',
    );
    
    // Verificar se a opção tem um mapeamento para o customizer
    if (isset($options_map[$option_name])) {
        $theme_mod_value = get_theme_mod($options_map[$option_name]);
        
        // Se a opção existe no customizer, retorna ela
        if ($theme_mod_value !== false) {
            return $theme_mod_value;
        }
    }
    
    // Caso contrário, obtenha a opção antiga
    return get_option($option_name, $default);
}

/**
 * Função para substituir o get_option para opções do tema
 * Esta função é usada por plugins de filtro que interceptam chamadas para get_option
 */
function thabatta_get_option_filter($value, $option, $default) {
    // Lista de opções do tema que queremos interceptar
    $theme_options = array(
        'thabatta_google_analytics',
        'thabatta_enable_preloader',
        'thabatta_enable_back_to_top',
        'thabatta_phone',
        'thabatta_email',
        'thabatta_facebook_url',
        'thabatta_instagram_url',
        'thabatta_linkedin_url',
        'thabatta_twitter_url',
        'thabatta_youtube_url',
        'thabatta_whatsapp_number',
        'thabatta_enable_social_sharing',
        'thabatta_default_meta_description',
        'thabatta_default_meta_keywords',
        'thabatta_enable_schema_markup',
        'thabatta_enable_breadcrumbs',
        'thabatta_enable_open_graph',
        'thabatta_enable_twitter_cards',
        'thabatta_primary_color',
        'thabatta_secondary_color',
        'thabatta_accent_color',
        'thabatta_text_color',
        'thabatta_heading_font',
        'thabatta_body_font',
    );
    
    // Se não for uma opção do tema, retorne o valor normalmente
    if (!in_array($option, $theme_options)) {
        return $value;
    }
    
    // Use nossa função personalizada para obter o valor
    return thabatta_get_theme_option($option, $default);
}

// Adicionar o filtro apenas se não estivermos no customizer
if (!is_customize_preview()) {
    add_filter('pre_option', 'thabatta_get_option_filter', 10, 3);
} 