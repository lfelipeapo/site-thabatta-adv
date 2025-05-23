<?php
/**
 * Thabatta Advocacia Theme Customizer
 *
 * @package Thabatta_Advocacia
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function thabatta_customize_register($wp_customize) {
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport = 'postMessage';
    $wp_customize->get_setting('header_textcolor')->transport = 'postMessage';

    $wp_customize->selective_refresh->add_partial('blogname', array(
        'selector' => '.site-title a',
        'render_callback' => 'thabatta_customize_partial_blogname',
    ));
    $wp_customize->selective_refresh->add_partial('blogdescription', array(
        'selector' => '.site-description',
        'render_callback' => 'thabatta_customize_partial_blogdescription',
    ));

    // Seção Configurações Gerais
    $wp_customize->add_section('thabatta_general_settings', array(
        'title' => __('Configurações Gerais', 'thabatta-adv'),
        'priority' => 20,
    ));
    
    // Nota: Logo e favicon estão disponíveis na seção "Identidade do Site" nativa do WordPress
    
    // Telefone
    $wp_customize->add_setting('general_phone', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('general_phone', array(
        'label' => __('Telefone', 'thabatta-adv'),
        'description' => __('Número de telefone principal para contato.', 'thabatta-adv'),
        'section' => 'thabatta_general_settings',
        'type' => 'text',
    ));
    
    // E-mail
    $wp_customize->add_setting('general_email', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('general_email', array(
        'label' => __('E-mail', 'thabatta-adv'),
        'description' => __('E-mail principal para contato.', 'thabatta-adv'),
        'section' => 'thabatta_general_settings',
        'type' => 'email',
    ));
    
    // Google Analytics
    $wp_customize->add_setting('general_google_analytics', array(
        'default' => '',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('general_google_analytics', array(
        'label' => __('Google Analytics', 'thabatta-adv'),
        'description' => __('Cole o código de acompanhamento do Google Analytics aqui.', 'thabatta-adv'),
        'section' => 'thabatta_general_settings',
        'type' => 'textarea',
    ));

    // Preloader
    $wp_customize->add_setting('general_enable_preloader', array(
        'default' => true,
        'sanitize_callback' => 'thabatta_sanitize_checkbox',
    ));
    $wp_customize->add_control('general_enable_preloader', array(
        'label' => __('Ativar preloader', 'thabatta-adv'),
        'description' => __('Exibe uma animação de carregamento enquanto a página está sendo carregada.', 'thabatta-adv'),
        'section' => 'thabatta_general_settings',
        'type' => 'checkbox',
    ));

    // Botão Voltar ao Topo
    $wp_customize->add_setting('general_enable_back_to_top', array(
        'default' => true,
        'sanitize_callback' => 'thabatta_sanitize_checkbox',
    ));
    $wp_customize->add_control('general_enable_back_to_top', array(
        'label' => __('Ativar botão voltar ao topo', 'thabatta-adv'),
        'description' => __('Exibe um botão para voltar ao topo da página quando o usuário rolar para baixo.', 'thabatta-adv'),
        'section' => 'thabatta_general_settings',
        'type' => 'checkbox',
    ));

    // Seção de cores do tema
    $wp_customize->add_section('thabatta_colors', array(
        'title' => __('Cores', 'thabatta-adv'),
        'priority' => 30,
    ));

    // Cor primária
    $wp_customize->add_setting('primary_color', array(
        'default' => '#8B0000',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_color', array(
        'label' => __('Cor Primária (Bordô)', 'thabatta-adv'),
        'section' => 'thabatta_colors',
        'setting' => 'primary_color',
    )));

    // Cor secundária
    $wp_customize->add_setting('secondary_color', array(
        'default' => '#D4AF37',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'secondary_color', array(
        'label' => __('Cor Secundária (Dourado)', 'thabatta-adv'),
        'section' => 'thabatta_colors',
        'setting' => 'secondary_color',
    )));

    // Cor de texto
    $wp_customize->add_setting('text_color', array(
        'default' => '#333333',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'text_color', array(
        'label' => __('Cor do Texto Principal', 'thabatta-adv'),
        'section' => 'thabatta_colors',
        'setting' => 'text_color',
    )));

    // Cor de destaque
    $wp_customize->add_setting('accent_color', array(
        'default' => '#4A0404',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'accent_color', array(
        'label' => __('Cor de Destaque (Vermelho Sangue)', 'thabatta-adv'),
        'section' => 'thabatta_colors',
        'setting' => 'accent_color',
    )));

    // Cor de fundo
    $wp_customize->add_setting('background_color', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'background_color', array(
        'label' => __('Cor de Fundo Principal', 'thabatta-adv'),
        'section' => 'thabatta_colors',
        'setting' => 'background_color',
    )));

    // Seção de layout
    $wp_customize->add_section('thabatta_layout', array(
        'title' => __('Layout', 'thabatta-adv'),
        'priority' => 40,
    ));

    // Layout da sidebar
    $wp_customize->add_setting('sidebar_position', array(
        'default' => 'right',
        'sanitize_callback' => 'thabatta_sanitize_select',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('sidebar_position', array(
        'label' => __('Posição da Sidebar', 'thabatta-adv'),
        'section' => 'thabatta_layout',
        'type' => 'select',
        'choices' => array(
            'right' => __('Direita', 'thabatta-adv'),
            'left' => __('Esquerda', 'thabatta-adv'),
            'none' => __('Sem Sidebar', 'thabatta-adv'),
        ),
    ));

    // Container width
    $wp_customize->add_setting('container_width', array(
        'default' => '1200',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('container_width', array(
        'label' => __('Largura do Container (px)', 'thabatta-adv'),
        'section' => 'thabatta_layout',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 960,
            'max' => 1600,
            'step' => 10,
        ),
    ));

    // Largura do Conteúdo
    $wp_customize->add_setting('content_width', array(
        'default' => '1170',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('content_width', array(
        'label' => __('Largura Máxima do Conteúdo (px)', 'thabatta-adv'),
        'section' => 'thabatta_layout',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 600,
            'max' => 1600,
            'step' => 10,
        ),
    ));

    // Opção para ativar/desativar animações
    $wp_customize->add_setting('enable_animations', array(
        'default' => '1',
        'sanitize_callback' => 'thabatta_sanitize_checkbox',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('enable_animations', array(
        'label' => __('Ativar Animações de Scroll?', 'thabatta-adv'),
        'section' => 'thabatta_layout',
        'type' => 'checkbox',
    ));

    // Seção de tipografia
    $wp_customize->add_section('thabatta_typography', array(
        'title' => __('Tipografia', 'thabatta-adv'),
        'priority' => 50,
    ));

    // Fonte do corpo
    $wp_customize->add_setting('body_font', array(
        'default' => 'Roboto',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('body_font', array(
        'label' => __('Fonte do Corpo', 'thabatta-adv'),
        'section' => 'thabatta_typography',
        'type' => 'select',
        'choices' => array(
            'Roboto' => 'Roboto',
            'Open Sans' => 'Open Sans',
            'Lato' => 'Lato',
            'Montserrat' => 'Montserrat',
            'Raleway' => 'Raleway',
            'Playfair Display' => 'Playfair Display',
            'Merriweather' => 'Merriweather',
        ),
    ));

    // Fonte dos títulos
    $wp_customize->add_setting('heading_font', array(
        'default' => 'Playfair Display',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('heading_font', array(
        'label' => __('Fonte dos Títulos', 'thabatta-adv'),
        'section' => 'thabatta_typography',
        'type' => 'select',
        'choices' => array(
            'Roboto' => 'Roboto',
            'Open Sans' => 'Open Sans',
            'Lato' => 'Lato',
            'Montserrat' => 'Montserrat',
            'Raleway' => 'Raleway',
            'Playfair Display' => 'Playfair Display',
            'Merriweather' => 'Merriweather',
        ),
    ));

    // Tamanho da fonte base
    $wp_customize->add_setting('base_font_size', array(
        'default' => '16',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('base_font_size', array(
        'label' => __('Tamanho da Fonte Base (px)', 'thabatta-adv'),
        'section' => 'thabatta_typography',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 12,
            'max' => 24,
            'step' => 1,
        ),
    ));

    // Seção de rodapé
    $wp_customize->add_section('thabatta_footer', array(
        'title' => __('Rodapé', 'thabatta-adv'),
        'priority' => 60,
    ));

    // Texto do copyright
    $wp_customize->add_setting('copyright_text', array(
        'default' => '© ' . date('Y') . ' Thabatta Apolinário Advocacia. Todos os direitos reservados.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('copyright_text', array(
        'label' => __('Texto de Copyright', 'thabatta-adv'),
        'section' => 'thabatta_footer',
        'type' => 'text',
    ));

    // Mostrar créditos do tema
    $wp_customize->add_setting('show_credits', array(
        'default' => true,
        'sanitize_callback' => 'thabatta_sanitize_checkbox',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('show_credits', array(
        'label' => __('Mostrar Créditos do Tema', 'thabatta-adv'),
        'section' => 'thabatta_footer',
        'type' => 'checkbox',
    ));
    
    // Horário de Atendimento
    $wp_customize->add_setting('footer_horario_atendimento', array(
        'default' => "Segunda - Sexta: 9:00 - 18:00\nSábado: Com agendamento\nDomingo: Fechado",
        'sanitize_callback' => 'sanitize_textarea_field',
    ));

    $wp_customize->add_control('footer_horario_atendimento', array(
        'label' => __('Horário de Atendimento', 'thabatta-adv'),
        'description' => __('Informe os horários de atendimento que serão exibidos no rodapé do site.', 'thabatta-adv'),
        'section' => 'thabatta_footer',
        'type' => 'textarea',
    ));

    // Seção Hero
    $wp_customize->add_section('thabatta_hero_section', array(
        'title'    => __('Seção Hero (Banner)', 'thabatta-adv'),
        'priority' => 25,
    ));

    // Título da Seção Hero
    $wp_customize->add_setting('hero_title', array(
        'default'           => 'Thabatta Apolinário Advocacia',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('hero_title', array(
        'label'    => __('Título', 'thabatta-adv'),
        'section'  => 'thabatta_hero_section',
        'type'     => 'text',
    ));
    
    // Descrição da Seção Hero
    $wp_customize->add_setting('hero_description', array(
        'default'           => 'Advocacia especializada em Direito Civil, Empresarial e Trabalhista. Atendimento personalizado e soluções jurídicas eficientes.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('hero_description', array(
        'label'    => __('Descrição', 'thabatta-adv'),
        'section'  => 'thabatta_hero_section',
        'type'     => 'textarea',
    ));

    // Imagem de fundo da Seção Hero
    $wp_customize->add_setting('hero_background_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'hero_background_image', array(
        'label'    => __('Imagem de Fundo', 'thabatta-adv'),
        'section'  => 'thabatta_hero_section',
    )));
    
    // Texto do botão Hero
    $wp_customize->add_setting('hero_button_text', array(
        'default'           => 'Fale Conosco',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('hero_button_text', array(
        'label'    => __('Texto do Botão', 'thabatta-adv'),
        'section'  => 'thabatta_hero_section',
        'type'     => 'text',
    ));
    
    // URL do botão Hero
    $wp_customize->add_setting('hero_button_url', array(
        'default'           => '/contato',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('hero_button_url', array(
        'label'    => __('URL do Botão', 'thabatta-adv'),
        'section'  => 'thabatta_hero_section',
        'type'     => 'url',
    ));

    // Seção Serviços
    $wp_customize->add_section('thabatta_services_section', array(
        'title'    => __('Seção Serviços', 'thabatta-adv'),
        'priority' => 28,
    ));

    // Título da Seção Serviços
    $wp_customize->add_setting('services_title', array(
        'default'           => 'Áreas de Atuação',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('services_title', array(
        'label'    => __('Título', 'thabatta-adv'),
        'section'  => 'thabatta_services_section',
        'type'     => 'text',
    ));
    
    // Descrição da Seção Serviços
    $wp_customize->add_setting('services_description', array(
        'default'           => 'Conheça as principais áreas em que atuamos com excelência e compromisso.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('services_description', array(
        'label'    => __('Descrição', 'thabatta-adv'),
        'section'  => 'thabatta_services_section',
        'type'     => 'textarea',
    ));

    // Número de serviços para mostrar
    $wp_customize->add_setting('services_count', array(
        'default'           => 6,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('services_count', array(
        'label'    => __('Quantidade de serviços para exibir', 'thabatta-adv'),
        'section'  => 'thabatta_services_section',
        'type'     => 'number',
        'input_attrs' => array(
            'min' => 3,
            'max' => 12,
            'step' => 1,
        ),
    ));

    // Seção Depoimentos
    $wp_customize->add_section('thabatta_testimonials_section', array(
        'title'    => __('Seção Depoimentos', 'thabatta-adv'),
        'priority' => 35,
    ));

    // Título da Seção Depoimentos
    $wp_customize->add_setting('testimonials_title', array(
        'default'           => 'Depoimentos de Clientes',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('testimonials_title', array(
        'label'    => __('Título', 'thabatta-adv'),
        'section'  => 'thabatta_testimonials_section',
        'type'     => 'text',
    ));
    
    // Descrição da Seção Depoimentos
    $wp_customize->add_setting('testimonials_description', array(
        'default'           => 'Veja o que nossos clientes dizem sobre nossos serviços e atendimento.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('testimonials_description', array(
        'label'    => __('Descrição', 'thabatta-adv'),
        'section'  => 'thabatta_testimonials_section',
        'type'     => 'textarea',
    ));

    // Número de depoimentos para mostrar
    $wp_customize->add_setting('testimonials_count', array(
        'default'           => 5,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('testimonials_count', array(
        'label'    => __('Quantidade de depoimentos para exibir', 'thabatta-adv'),
        'section'  => 'thabatta_testimonials_section',
        'type'     => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 10,
            'step' => 1,
        ),
    ));

    // Seção CTA
    $wp_customize->add_section('thabatta_cta_section', array(
        'title'    => __('Seção CTA (Chamada para Ação)', 'thabatta-adv'),
        'priority' => 40,
    ));

    // Título da Seção CTA
    $wp_customize->add_setting('cta_title', array(
        'default'           => 'Precisando de Orientação Jurídica?',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('cta_title', array(
        'label'    => __('Título', 'thabatta-adv'),
        'section'  => 'thabatta_cta_section',
        'type'     => 'text',
    ));
    
    // Descrição da Seção CTA
    $wp_customize->add_setting('cta_description', array(
        'default'           => 'Entre em contato conosco para uma consulta inicial. Nossos advogados estão prontos para ajudar você.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('cta_description', array(
        'label'    => __('Descrição', 'thabatta-adv'),
        'section'  => 'thabatta_cta_section',
        'type'     => 'textarea',
    ));
    
    // Texto do botão CTA
    $wp_customize->add_setting('cta_button_text', array(
        'default'           => 'Agende uma Consulta',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('cta_button_text', array(
        'label'    => __('Texto do Botão', 'thabatta-adv'),
        'section'  => 'thabatta_cta_section',
        'type'     => 'text',
    ));
    
    // URL do botão CTA
    $wp_customize->add_setting('cta_button_url', array(
        'default'           => '/contato',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('cta_button_url', array(
        'label'    => __('URL do Botão', 'thabatta-adv'),
        'section'  => 'thabatta_cta_section',
        'type'     => 'url',
    ));

    // Seção Equipe
    $wp_customize->add_section('thabatta_team_section', array(
        'title'    => __('Seção Equipe', 'thabatta-adv'),
        'priority' => 42,
    ));

    // Título da Seção Equipe
    $wp_customize->add_setting('team_title', array(
        'default'           => 'Nossa Equipe',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('team_title', array(
        'label'    => __('Título', 'thabatta-adv'),
        'section'  => 'thabatta_team_section',
        'type'     => 'text',
    ));
    
    // Descrição da Seção Equipe
    $wp_customize->add_setting('team_description', array(
        'default'           => 'Conheça os profissionais dedicados a cuidar do seu caso com excelência.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('team_description', array(
        'label'    => __('Descrição', 'thabatta-adv'),
        'section'  => 'thabatta_team_section',
        'type'     => 'textarea',
    ));
    
    // Número de membros para mostrar
    $wp_customize->add_setting('team_count', array(
        'default'           => 4,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('team_count', array(
        'label'    => __('Quantidade de membros para exibir', 'thabatta-adv'),
        'section'  => 'thabatta_team_section',
        'type'     => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 8,
            'step' => 1,
        ),
    ));

    // Seção Blog
    $wp_customize->add_section('thabatta_blog_section', array(
        'title'    => __('Seção Blog', 'thabatta-adv'),
        'priority' => 45,
    ));

    // Título da Seção Blog
    $wp_customize->add_setting('blog_title', array(
        'default'           => 'Últimas do Blog',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('blog_title', array(
        'label'    => __('Título', 'thabatta-adv'),
        'section'  => 'thabatta_blog_section',
        'type'     => 'text',
    ));
    
    // Descrição da Seção Blog
    $wp_customize->add_setting('blog_description', array(
        'default'           => 'Fique atualizado com nosso conteúdo jurídico e dicas relevantes.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('blog_description', array(
        'label'    => __('Descrição', 'thabatta-adv'),
        'section'  => 'thabatta_blog_section',
        'type'     => 'textarea',
    ));
    
    // Número de posts para mostrar
    $wp_customize->add_setting('blog_count', array(
        'default'           => 3,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('blog_count', array(
        'label'    => __('Quantidade de posts para exibir', 'thabatta-adv'),
        'section'  => 'thabatta_blog_section',
        'type'     => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 6,
            'step' => 1,
        ),
    ));
    
    // Mostrar botão "Ver todos"
    $wp_customize->add_setting('blog_show_all_button', array(
        'default'           => true,
        'sanitize_callback' => 'thabatta_sanitize_checkbox',
    ));
    $wp_customize->add_control('blog_show_all_button', array(
        'label'    => __('Mostrar botão "Ver todos os posts"', 'thabatta-adv'),
        'section'  => 'thabatta_blog_section',
        'type'     => 'checkbox',
    ));

    // Seção Sobre
    $wp_customize->add_section('thabatta_about_section', array(
        'title'    => __('Seção Sobre', 'thabatta-adv'),
        'priority' => 30,
    ));

    // Título da Seção Sobre
    $wp_customize->add_setting('about_title', array(
        'default'           => 'Sobre Nosso Escritório',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('about_title', array(
        'label'    => __('Título', 'thabatta-adv'),
        'section'  => 'thabatta_about_section',
        'type'     => 'text',
    ));
    
    // Subtítulo da Seção Sobre
    $wp_customize->add_setting('about_subtitle', array(
        'default'           => 'Nossa História e Compromisso',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('about_subtitle', array(
        'label'    => __('Subtítulo', 'thabatta-adv'),
        'section'  => 'thabatta_about_section',
        'type'     => 'text',
    ));

    // Descrição da Seção Sobre
    $wp_customize->add_setting('about_description', array(
        'default'           => 'Somos um escritório de advocacia comprometido com a excelência e resultados. Nossa equipe é formada por profissionais experientes, dedicados a oferecer soluções jurídicas personalizadas para cada cliente. Defendemos seus direitos com ética, competência e determinação.',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('about_description', array(
        'label'    => __('Descrição', 'thabatta-adv'),
        'section'  => 'thabatta_about_section',
        'type'     => 'textarea',
    ));

    // Imagem da Seção Sobre
    $wp_customize->add_setting('about_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'about_image', array(
        'label'    => __('Imagem', 'thabatta-adv'),
        'section'  => 'thabatta_about_section',
    )));
    
    // Título do Overlay da Imagem
    $wp_customize->add_setting('about_overlay_title', array(
        'default'           => 'Nossa Missão',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('about_overlay_title', array(
        'label'    => __('Título do Overlay da Imagem', 'thabatta-adv'),
        'section'  => 'thabatta_about_section',
        'type'     => 'text',
    ));
    
    // Texto do Overlay da Imagem
    $wp_customize->add_setting('about_overlay_text', array(
        'default'           => 'Justiça e excelência em cada caso.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('about_overlay_text', array(
        'label'    => __('Texto do Overlay da Imagem', 'thabatta-adv'),
        'section'  => 'thabatta_about_section',
        'type'     => 'text',
    ));

    // Característica 1
    $wp_customize->add_setting('about_feature_1', array(
        'default'           => 'Atendimento Personalizado',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('about_feature_1', array(
        'label'    => __('Característica 1', 'thabatta-adv'),
        'section'  => 'thabatta_about_section',
        'type'     => 'text',
    ));

    // Característica 2
    $wp_customize->add_setting('about_feature_2', array(
        'default'           => 'Profissionais Qualificados',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('about_feature_2', array(
        'label'    => __('Característica 2', 'thabatta-adv'),
        'section'  => 'thabatta_about_section',
        'type'     => 'text',
    ));

    // Característica 3
    $wp_customize->add_setting('about_feature_3', array(
        'default'           => 'Soluções Eficientes',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('about_feature_3', array(
        'label'    => __('Característica 3', 'thabatta-adv'),
        'section'  => 'thabatta_about_section',
        'type'     => 'text',
    ));
    
    // Característica 4
    $wp_customize->add_setting('about_feature_4', array(
        'default'           => 'Compromisso com a Justiça',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('about_feature_4', array(
        'label'    => __('Característica 4', 'thabatta-adv'),
        'section'  => 'thabatta_about_section',
        'type'     => 'text',
    ));
    
    // Texto do botão
    $wp_customize->add_setting('about_button_text', array(
        'default'           => 'Conheça Nossa História',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('about_button_text', array(
        'label'    => __('Texto do Botão', 'thabatta-adv'),
        'section'  => 'thabatta_about_section',
        'type'     => 'text',
    ));
    
    // Seção Redes Sociais
    $wp_customize->add_section('thabatta_social_networks', array(
        'title'    => __('Redes Sociais', 'thabatta-adv'),
        'priority' => 95,
    ));

    // URL do Facebook
    $wp_customize->add_setting('social_facebook_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('social_facebook_url', array(
        'label'    => __('URL do Facebook', 'thabatta-adv'),
        'section'  => 'thabatta_social_networks',
        'type'     => 'url',
    ));

    // URL do Instagram
    $wp_customize->add_setting('social_instagram_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('social_instagram_url', array(
        'label'    => __('URL do Instagram', 'thabatta-adv'),
        'section'  => 'thabatta_social_networks',
        'type'     => 'url',
    ));

    // URL do LinkedIn
    $wp_customize->add_setting('social_linkedin_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('social_linkedin_url', array(
        'label'    => __('URL do LinkedIn', 'thabatta-adv'),
        'section'  => 'thabatta_social_networks',
        'type'     => 'url',
    ));

    // URL do Twitter
    $wp_customize->add_setting('social_twitter_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('social_twitter_url', array(
        'label'    => __('URL do Twitter', 'thabatta-adv'),
        'section'  => 'thabatta_social_networks',
        'type'     => 'url',
    ));

    // URL do YouTube
    $wp_customize->add_setting('social_youtube_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('social_youtube_url', array(
        'label'    => __('URL do YouTube', 'thabatta-adv'),
        'section'  => 'thabatta_social_networks',
        'type'     => 'url',
    ));

    // Número do WhatsApp
    $wp_customize->add_setting('social_whatsapp_number', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('social_whatsapp_number', array(
        'label'    => __('Número do WhatsApp', 'thabatta-adv'),
        'description' => __('Formato: 5511999999999 (código do país + DDD + número)', 'thabatta-adv'),
        'section'  => 'thabatta_social_networks',
        'type'     => 'text',
    ));

    // Habilitar compartilhamento social
    $wp_customize->add_setting('social_enable_sharing', array(
        'default'           => true,
        'sanitize_callback' => 'thabatta_sanitize_checkbox',
    ));
    $wp_customize->add_control('social_enable_sharing', array(
        'label'    => __('Ativar botões de compartilhamento nos posts', 'thabatta-adv'),
        'section'  => 'thabatta_social_networks',
        'type'     => 'checkbox',
    ));

    // Seção SEO
    $wp_customize->add_section('thabatta_seo_settings', array(
        'title'    => __('SEO', 'thabatta-adv'),
        'priority' => 96,
    ));

    // Meta Descrição Padrão
    $wp_customize->add_setting('seo_default_meta_description', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('seo_default_meta_description', array(
        'label'    => __('Meta Descrição Padrão', 'thabatta-adv'),
        'description' => __('Descrição padrão para páginas sem descrição personalizada.', 'thabatta-adv'),
        'section'  => 'thabatta_seo_settings',
        'type'     => 'textarea',
    ));

    // Meta Palavras-chave Padrão
    $wp_customize->add_setting('seo_default_meta_keywords', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('seo_default_meta_keywords', array(
        'label'    => __('Meta Palavras-chave Padrão', 'thabatta-adv'),
        'description' => __('Palavras-chave padrão separadas por vírgula.', 'thabatta-adv'),
        'section'  => 'thabatta_seo_settings',
        'type'     => 'text',
    ));

    // Ativar Marcação de Esquema
    $wp_customize->add_setting('seo_enable_schema_markup', array(
        'default'           => true,
        'sanitize_callback' => 'thabatta_sanitize_checkbox',
    ));
    $wp_customize->add_control('seo_enable_schema_markup', array(
        'label'    => __('Ativar Marcação de Esquema', 'thabatta-adv'),
        'description' => __('Adiciona dados estruturados para melhor visibilidade nos motores de busca.', 'thabatta-adv'),
        'section'  => 'thabatta_seo_settings',
        'type'     => 'checkbox',
    ));

    // Ativar Breadcrumbs
    $wp_customize->add_setting('seo_enable_breadcrumbs', array(
        'default'           => true,
        'sanitize_callback' => 'thabatta_sanitize_checkbox',
    ));
    $wp_customize->add_control('seo_enable_breadcrumbs', array(
        'label'    => __('Ativar Breadcrumbs', 'thabatta-adv'),
        'description' => __('Exibe navegação hierárquica nas páginas e posts.', 'thabatta-adv'),
        'section'  => 'thabatta_seo_settings',
        'type'     => 'checkbox',
    ));

    // Ativar Open Graph
    $wp_customize->add_setting('seo_enable_open_graph', array(
        'default'           => true,
        'sanitize_callback' => 'thabatta_sanitize_checkbox',
    ));
    $wp_customize->add_control('seo_enable_open_graph', array(
        'label'    => __('Ativar Open Graph', 'thabatta-adv'),
        'description' => __('Adiciona metadados para melhor compartilhamento em redes sociais.', 'thabatta-adv'),
        'section'  => 'thabatta_seo_settings',
        'type'     => 'checkbox',
    ));

    // Ativar Twitter Cards
    $wp_customize->add_setting('seo_enable_twitter_cards', array(
        'default'           => true,
        'sanitize_callback' => 'thabatta_sanitize_checkbox',
    ));
    $wp_customize->add_control('seo_enable_twitter_cards', array(
        'label'    => __('Ativar Twitter Cards', 'thabatta-adv'),
        'description' => __('Adiciona metadados para melhor exibição no Twitter.', 'thabatta-adv'),
        'section'  => 'thabatta_seo_settings',
        'type'     => 'checkbox',
    ));
}
add_action('customize_register', 'thabatta_customize_register');

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function thabatta_customize_partial_blogname() {
    bloginfo('name');
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function thabatta_customize_partial_blogdescription() {
    bloginfo('description');
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function thabatta_customize_preview_js() {
    wp_enqueue_script('thabatta-customizer', get_template_directory_uri() . '/assets/js/customizer.js', array('customize-preview'), THABATTA_VERSION, true);
}
add_action('customize_preview_init', 'thabatta_customize_preview_js');

/**
 * Sanitize select field
 *
 * @param string $input The input from the setting
 * @param object $setting The selected setting
 * @return string The sanitized input
 */
function thabatta_sanitize_select($input, $setting) {
    // Get list of choices from the control associated with the setting
    $choices = $setting->manager->get_control($setting->id)->choices;
    
    // If the input is a valid key, return it; otherwise, return the default
    return (array_key_exists($input, $choices) ? $input : $setting->default);
}

/**
 * Sanitize checkbox field
 *
 * @param string $checked Whether the checkbox is checked ('1' or '0')
 * @return string '1' if checked, '0' otherwise
 */
function thabatta_sanitize_checkbox($checked) {
    // Ensure it's either '1' or '0'. Handle boolean true explicitly.
    return ($checked == '1' || $checked === true) ? '1' : '0';
}

/**
 * Generate CSS for the theme customizer options
 */
function thabatta_customizer_css() {
    ?>
    <style type="text/css">
        :root {
            --primary-color: <?php echo esc_attr(get_theme_mod('primary_color', '#8b0000')); ?>;
            --primary-color-hover: <?php echo esc_attr(thabatta_adjust_brightness(get_theme_mod('primary_color', '#8b0000'), -20)); ?>;
            --secondary-color: <?php echo esc_attr(get_theme_mod('secondary_color', '#d4af37')); ?>;
            --secondary-color-hover: <?php echo esc_attr(thabatta_adjust_brightness(get_theme_mod('secondary_color', '#d4af37'), -20)); ?>;
            --text-color: <?php echo esc_attr(get_theme_mod('text_color', '#333333')); ?>;
            --background-color: <?php echo esc_attr(get_theme_mod('background_color', '#ffffff')); ?>;
            --body-font: <?php echo esc_attr(get_theme_mod('body_font', 'Roboto')); ?>, sans-serif;
            --heading-font: <?php echo esc_attr(get_theme_mod('heading_font', 'Playfair Display')); ?>, serif;
            --base-font-size: <?php echo esc_attr(get_theme_mod('base_font_size', '16')); ?>px;
            --container-width: <?php echo esc_attr(get_theme_mod('container_width', '1200')); ?>px;
        }
        
        body {
            font-family: var(--body-font);
            font-size: var(--base-font-size);
            color: var(--text-color);
            background-color: var(--background-color);
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--heading-font);
            color: var(--primary-color);
        }
        
        a {
            color: var(--primary-color);
        }
        
        a:hover, a:focus {
            color: var(--primary-color-hover);
        }
        
        .btn-primary, .wp-block-button__link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover, .wp-block-button__link:hover {
            background-color: var(--primary-color-hover);
            border-color: var(--primary-color-hover);
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-secondary:hover {
            background-color: var(--secondary-color-hover);
            border-color: var(--secondary-color-hover);
        }
        
        .container {
            max-width: var(--container-width);
        }
        
        /* Sidebar positioning */
        <?php if (get_theme_mod('sidebar_position', 'right') === 'left') : ?>
        @media (min-width: 992px) {
            .content-area {
                order: 2;
            }
            .widget-area {
                order: 1;
            }
        }
        <?php elseif (get_theme_mod('sidebar_position', 'right') === 'none') : ?>
        @media (min-width: 992px) {
            .content-area {
                flex: 0 0 100%;
                max-width: 100%;
            }
            .widget-area {
                display: none;
            }
        }
        <?php endif; ?>
    </style>
    <?php
}
add_action('wp_head', 'thabatta_customizer_css');

/**
 * Adjust color brightness
 *
 * @param string $hex Hex color code
 * @param int $steps Steps to adjust brightness (negative for darker, positive for lighter)
 * @return string Adjusted hex color
 */
function thabatta_adjust_brightness($hex, $steps) {
    // Remove # if present
    $hex = ltrim($hex, '#');
    
    // Convert to RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Adjust brightness
    $r = max(0, min(255, $r + $steps));
    $g = max(0, min(255, $g + $steps));
    $b = max(0, min(255, $b + $steps));
    
    // Convert back to hex
    return sprintf('#%02x%02x%02x', $r, $g, $b);
}

/**
 * Generate customizer JavaScript
 */
function thabatta_generate_customizer_js() {
    // Caminho para o arquivo de script
    $script_file = get_template_directory() . '/assets/js/customizer.js';
    
    // Verificar se o diretório existe
    $script_dir = dirname($script_file);
    if (!file_exists($script_dir)) {
        wp_mkdir_p($script_dir);
    }
    
    // Conteúdo do script
    $script_content = <<<'EOT'
/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

(function($) {
    // Site title and description.
    wp.customize('blogname', function(value) {
        value.bind(function(to) {
            $('.site-title a').text(to);
        });
    });
    
    wp.customize('blogdescription', function(value) {
        value.bind(function(to) {
            $('.site-description').text(to);
        });
    });
    
    // Header text color.
    wp.customize('header_textcolor', function(value) {
        value.bind(function(to) {
            if ('blank' === to) {
                $('.site-title, .site-description').css({
                    'clip': 'rect(1px, 1px, 1px, 1px)',
                    'position': 'absolute'
                });
            } else {
                $('.site-title, .site-description').css({
                    'clip': 'auto',
                    'position': 'relative'
                });
                $('.site-title a, .site-description').css({
                    'color': to
                });
            }
        });
    });
    
    // Primary color
    wp.customize('primary_color', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--primary-color', to);
            
            // Calculate hover color (20% darker)
            const darker = adjustBrightness(to, -20);
            document.documentElement.style.setProperty('--primary-color-hover', darker);
        });
    });
    
    // Secondary color
    wp.customize('secondary_color', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--secondary-color', to);
            
            // Calculate hover color (20% darker)
            const darker = adjustBrightness(to, -20);
            document.documentElement.style.setProperty('--secondary-color-hover', darker);
        });
    });
    
    // Text color
    wp.customize('text_color', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--text-color', to);
        });
    });
    
    // Background color
    wp.customize('background_color', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--background-color', to);
        });
    });
    
    // Body font
    wp.customize('body_font', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--body-font', to + ', sans-serif');
        });
    });
    
    // Heading font
    wp.customize('heading_font', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--heading-font', to + ', serif');
        });
    });
    
    // Base font size
    wp.customize('base_font_size', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--base-font-size', to + 'px');
        });
    });
    
    // Container width
    wp.customize('container_width', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--container-width', to + 'px');
        });
    });
    
    // Copyright text
    wp.customize('copyright_text', function(value) {
        value.bind(function(to) {
            $('.site-info .copyright').text(to);
        });
    });
    
    // Show credits
    wp.customize('show_credits', function(value) {
        value.bind(function(to) {
            if (to) {
                $('.site-info .credits').show();
            } else {
                $('.site-info .credits').hide();
            }
        });
    });
    
    /**
     * Helper function to adjust brightness of a hex color
     */
    function adjustBrightness(hex, steps) {
        // Remove # if present
        hex = hex.replace(/^#/, '');
        
        // Convert to RGB
        let r = parseInt(hex.substring(0, 2), 16);
        let g = parseInt(hex.substring(2, 4), 16);
        let b = parseInt(hex.substring(4, 6), 16);
        
        // Adjust brightness
        r = Math.max(0, Math.min(255, r + steps));
        g = Math.max(0, Math.min(255, g + steps));
        b = Math.max(0, Math.min(255, b + steps));
        
        // Convert back to hex
        return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
    }
})(jQuery);
EOT;
    
    // Escrever o arquivo
    file_put_contents($script_file, $script_content);
}

/**
 * Transfere as configurações do painel Tema Thabatta para o customizer
 */
function thabatta_migrate_theme_options_to_customizer() {
    // Configurações Gerais
    thabatta_migrate_option('thabatta_google_analytics', 'general_google_analytics');
    thabatta_migrate_option('thabatta_enable_preloader', 'general_enable_preloader');
    thabatta_migrate_option('thabatta_enable_back_to_top', 'general_enable_back_to_top');
    thabatta_migrate_option('thabatta_phone', 'general_phone');
    thabatta_migrate_option('thabatta_email', 'general_email');
    
    // Cores e Tipografia
    thabatta_migrate_option('thabatta_primary_color', 'primary_color');
    thabatta_migrate_option('thabatta_secondary_color', 'secondary_color');
    thabatta_migrate_option('thabatta_accent_color', 'accent_color');
    thabatta_migrate_option('thabatta_text_color', 'text_color');
    thabatta_migrate_option('thabatta_heading_font', 'heading_font');
    thabatta_migrate_option('thabatta_body_font', 'body_font');
    
    // Redes Sociais
    thabatta_migrate_option('thabatta_facebook_url', 'social_facebook_url');
    thabatta_migrate_option('thabatta_instagram_url', 'social_instagram_url');
    thabatta_migrate_option('thabatta_linkedin_url', 'social_linkedin_url');
    thabatta_migrate_option('thabatta_twitter_url', 'social_twitter_url');
    thabatta_migrate_option('thabatta_youtube_url', 'social_youtube_url');
    thabatta_migrate_option('thabatta_whatsapp_number', 'social_whatsapp_number');
    thabatta_migrate_option('thabatta_enable_social_sharing', 'social_enable_sharing');
    
    // SEO
    thabatta_migrate_option('thabatta_default_meta_description', 'seo_default_meta_description');
    thabatta_migrate_option('thabatta_default_meta_keywords', 'seo_default_meta_keywords');
    thabatta_migrate_option('thabatta_enable_schema_markup', 'seo_enable_schema_markup');
    thabatta_migrate_option('thabatta_enable_breadcrumbs', 'seo_enable_breadcrumbs');
    thabatta_migrate_option('thabatta_enable_open_graph', 'seo_enable_open_graph');
    thabatta_migrate_option('thabatta_enable_twitter_cards', 'seo_enable_twitter_cards');
    
    // Rodapé e ACF Options
    if (function_exists('get_field')) {
        $horario_atendimento = get_field('horario_atendimento', 'option');
        if ($horario_atendimento) {
            set_theme_mod('footer_horario_atendimento', $horario_atendimento);
        }
    }
    
    // Definir uma flag para indicar que a migração foi realizada
    update_option('thabatta_options_migrated', true);
}

/**
 * Função auxiliar para migrar uma opção para o customizer
 */
function thabatta_migrate_option($old_option, $new_theme_mod) {
    $value = get_option($old_option);
    if ($value !== false) {
        set_theme_mod($new_theme_mod, $value);
    }
}

// Executar a migração apenas uma vez
function thabatta_maybe_migrate_options() {
    if (!get_option('thabatta_options_migrated', false)) {
        thabatta_migrate_theme_options_to_customizer();
    }
}
add_action('after_setup_theme', 'thabatta_maybe_migrate_options');