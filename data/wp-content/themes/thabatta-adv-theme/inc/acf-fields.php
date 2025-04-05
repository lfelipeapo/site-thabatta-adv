<?php

/**
 * Registrar campos ACF para o tema Thabatta Advocacia
 *
 * @package Thabatta_Advocacia
 */

if (!defined('ABSPATH')) {
    exit; // Saída direta se acessado diretamente
}

/**
 * Registrar campos ACF
 */
function thabatta_register_acf_fields()
{
    if (function_exists('acf_add_local_field_group')) {
        // Grupo de campos para opções do tema
        acf_add_local_field_group(array(
            'key' => 'group_theme_options',
            'title' => 'Opções do Tema',
            'fields' => array(
                array(
                    'key' => 'field_contact_info',
                    'label' => 'Informações de Contato',
                    'name' => '',
                    'type' => 'tab',
                    'placement' => 'top',
                ),
                array(
                    'key' => 'field_telefone',
                    'label' => 'Telefone',
                    'name' => 'telefone',
                    'type' => 'text',
                    'instructions' => 'Insira o número de telefone principal do escritório.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_whatsapp',
                    'label' => 'WhatsApp',
                    'name' => 'whatsapp',
                    'type' => 'text',
                    'instructions' => 'Insira o número de WhatsApp do escritório (com código do país).',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_whatsapp_message',
                    'label' => 'Mensagem padrão do WhatsApp',
                    'name' => 'whatsapp_message',
                    'type' => 'text',
                    'instructions' => 'Insira a mensagem padrão que será enviada pelo WhatsApp.',
                    'required' => 0,
                    'default_value' => 'Olá, gostaria de mais informações sobre os serviços de advocacia.',
                ),
                array(
                    'key' => 'field_email',
                    'label' => 'E-mail',
                    'name' => 'email',
                    'type' => 'email',
                    'instructions' => 'Insira o e-mail principal do escritório.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_endereco',
                    'label' => 'Endereço',
                    'name' => 'endereco',
                    'type' => 'text',
                    'instructions' => 'Insira o endereço do escritório (rua, número, complemento).',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_cidade_estado_cep',
                    'label' => 'Cidade, Estado e CEP',
                    'name' => 'cidade_estado_cep',
                    'type' => 'text',
                    'instructions' => 'Insira a cidade, estado e CEP do escritório.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_horario_funcionamento',
                    'label' => 'Horário de Funcionamento',
                    'name' => 'horario_funcionamento',
                    'type' => 'text',
                    'instructions' => 'Insira o horário de funcionamento do escritório.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_social_media',
                    'label' => 'Redes Sociais',
                    'name' => '',
                    'type' => 'tab',
                    'placement' => 'top',
                ),
                array(
                    'key' => 'field_redes_sociais',
                    'label' => 'Redes Sociais',
                    'name' => 'redes_sociais',
                    'type' => 'repeater',
                    'instructions' => 'Adicione as redes sociais do escritório.',
                    'required' => 0,
                    'min' => 0,
                    'max' => 10,
                    'layout' => 'table',
                    'button_label' => 'Adicionar Rede Social',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_rede_social',
                            'label' => 'Rede Social',
                            'name' => 'rede_social',
                            'type' => 'select',
                            'instructions' => '',
                            'required' => 1,
                            'choices' => array(
                                'facebook' => 'Facebook',
                                'instagram' => 'Instagram',
                                'twitter' => 'Twitter',
                                'linkedin' => 'LinkedIn',
                                'youtube' => 'YouTube',
                                'whatsapp' => 'WhatsApp',
                                'telegram' => 'Telegram',
                            ),
                            'default_value' => 'facebook',
                        ),
                        array(
                            'key' => 'field_url',
                            'label' => 'URL',
                            'name' => 'url',
                            'type' => 'url',
                            'instructions' => '',
                            'required' => 1,
                        ),
                    ),
                ),
                array(
                    'key' => 'field_seo',
                    'label' => 'SEO',
                    'name' => '',
                    'type' => 'tab',
                    'placement' => 'top',
                ),
                array(
                    'key' => 'field_google_analytics',
                    'label' => 'Google Analytics',
                    'name' => 'google_analytics',
                    'type' => 'text',
                    'instructions' => 'Insira o código de acompanhamento do Google Analytics (ex: UA-XXXXXXXX-X ou G-XXXXXXXXXX).',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_google_tag_manager',
                    'label' => 'Google Tag Manager',
                    'name' => 'google_tag_manager',
                    'type' => 'text',
                    'instructions' => 'Insira o ID do Google Tag Manager (ex: GTM-XXXXXX).',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_meta_description',
                    'label' => 'Meta Description Padrão',
                    'name' => 'meta_description',
                    'type' => 'textarea',
                    'instructions' => 'Insira a meta description padrão para o site.',
                    'required' => 0,
                    'maxlength' => 160,
                    'rows' => 3,
                ),
                array(
                    'key' => 'field_scripts',
                    'label' => 'Scripts',
                    'name' => '',
                    'type' => 'tab',
                    'placement' => 'top',
                ),
                array(
                    'key' => 'field_header_scripts',
                    'label' => 'Scripts do Cabeçalho',
                    'name' => 'header_scripts',
                    'type' => 'textarea',
                    'instructions' => 'Insira scripts que devem ser carregados no cabeçalho do site.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_footer_scripts',
                    'label' => 'Scripts do Rodapé',
                    'name' => 'footer_scripts',
                    'type' => 'textarea',
                    'instructions' => 'Insira scripts que devem ser carregados no rodapé do site.',
                    'required' => 0,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'theme-general-settings',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));

        // Grupo de campos para a página inicial
        acf_add_local_field_group(array(
            'key' => 'group_home_page',
            'title' => 'Configurações da Página Inicial',
            'fields' => array(
                array(
                    'key' => 'field_hero_section',
                    'label' => 'Seção Hero',
                    'name' => '',
                    'type' => 'tab',
                    'placement' => 'top',
                ),
                array(
                    'key' => 'field_hero_title',
                    'label' => 'Título',
                    'name' => 'hero_title',
                    'type' => 'text',
                    'instructions' => 'Insira o título principal da seção hero.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_hero_subtitle',
                    'label' => 'Subtítulo',
                    'name' => 'hero_subtitle',
                    'type' => 'text',
                    'instructions' => 'Insira o subtítulo da seção hero.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_hero_description',
                    'label' => 'Descrição',
                    'name' => 'hero_description',
                    'type' => 'textarea',
                    'instructions' => 'Insira uma breve descrição para a seção hero.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_hero_button_text',
                    'label' => 'Texto do Botão',
                    'name' => 'hero_button_text',
                    'type' => 'text',
                    'instructions' => 'Insira o texto do botão da seção hero.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_hero_button_url',
                    'label' => 'URL do Botão',
                    'name' => 'hero_button_url',
                    'type' => 'url',
                    'instructions' => 'Insira a URL para onde o botão deve direcionar.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_hero_background_image',
                    'label' => 'Imagem de Fundo',
                    'name' => 'hero_background_image',
                    'type' => 'image',
                    'instructions' => 'Selecione uma imagem de fundo para a seção hero.',
                    'required' => 0,
                    'return_format' => 'url',
                    'preview_size' => 'medium',
                    'library' => 'all',
                    'min_width' => '',
                    'min_height' => '',
                    'min_size' => '',
                    'max_width' => '',
                    'max_height' => '',
                    'max_size' => '',
                    'mime_types' => '',
                ),
                array(
                    'key' => 'field_about_section',
                    'label' => 'Seção Sobre',
                    'name' => '',
                    'type' => 'tab',
                    'placement' => 'top',
                ),
                array(
                    'key' => 'field_about_title',
                    'label' => 'Título',
                    'name' => 'about_title',
                    'type' => 'text',
                    'instructions' => 'Insira o título da seção sobre.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_about_content',
                    'label' => 'Conteúdo',
                    'name' => 'about_content',
                    'type' => 'wysiwyg',
                    'instructions' => 'Insira o conteúdo da seção sobre.',
                    'required' => 0,
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                ),
                array(
                    'key' => 'field_about_image',
                    'label' => 'Imagem',
                    'name' => 'about_image',
                    'type' => 'image',
                    'instructions' => 'Selecione uma imagem para a seção sobre.',
                    'required' => 0,
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ),
                array(
                    'key' => 'field_services_section',
                    'label' => 'Seção Serviços',
                    'name' => '',
                    'type' => 'tab',
                    'placement' => 'top',
                ),
                array(
                    'key' => 'field_services_title',
                    'label' => 'Título',
                    'name' => 'services_title',
                    'type' => 'text',
                    'instructions' => 'Insira o título da seção de serviços.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_services_description',
                    'label' => 'Descrição',
                    'name' => 'services_description',
                    'type' => 'textarea',
                    'instructions' => 'Insira uma breve descrição para a seção de serviços.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_services',
                    'label' => 'Serviços',
                    'name' => 'services',
                    'type' => 'repeater',
                    'instructions' => 'Adicione os serviços que serão exibidos na página inicial.',
                    'required' => 0,
                    'min' => 0,
                    'max' => 6,
                    'layout' => 'block',
                    'button_label' => 'Adicionar Serviço',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_service_icon',
                            'label' => 'Ícone',
                            'name' => 'icon',
                            'type' => 'text',
                            'instructions' => 'Insira o nome do ícone (FontAwesome).',
                            'required' => 0,
                        ),
                        array(
                            'key' => 'field_service_title',
                            'label' => 'Título',
                            'name' => 'title',
                            'type' => 'text',
                            'instructions' => 'Insira o título do serviço.',
                            'required' => 1,
                        ),
                        array(
                            'key' => 'field_service_description',
                            'label' => 'Descrição',
                            'name' => 'description',
                            'type' => 'textarea',
                            'instructions' => 'Insira uma breve descrição do serviço.',
                            'required' => 0,
                        ),
                        array(
                            'key' => 'field_service_link',
                            'label' => 'Link',
                            'name' => 'link',
                            'type' => 'url',
                            'instructions' => 'Insira o link para a página do serviço.',
                            'required' => 0,
                        ),
                    ),
                ),
                array(
                    'key' => 'field_team_section',
                    'label' => 'Seção Equipe',
                    'name' => '',
                    'type' => 'tab',
                    'placement' => 'top',
                ),
                array(
                    'key' => 'field_team_title',
                    'label' => 'Título',
                    'name' => 'team_title',
                    'type' => 'text',
                    'instructions' => 'Insira o título da seção de equipe.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_team_description',
                    'label' => 'Descrição',
                    'name' => 'team_description',
                    'type' => 'textarea',
                    'instructions' => 'Insira uma breve descrição para a seção de equipe.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_team_members',
                    'label' => 'Membros da Equipe',
                    'name' => 'team_members',
                    'type' => 'relationship',
                    'instructions' => 'Selecione os membros da equipe que serão exibidos na página inicial.',
                    'required' => 0,
                    'post_type' => array(
                        0 => 'equipe',
                    ),
                    'taxonomy' => '',
                    'filters' => array(
                        0 => 'search',
                    ),
                    'elements' => '',
                    'min' => '',
                    'max' => '',
                    'return_format' => 'object',
                ),
                array(
                    'key' => 'field_testimonials_section',
                    'label' => 'Seção Depoimentos',
                    'name' => '',
                    'type' => 'tab',
                    'placement' => 'top',
                ),
                array(
                    'key' => 'field_testimonials_title',
                    'label' => 'Título',
                    'name' => 'testimonials_title',
                    'type' => 'text',
                    'instructions' => 'Insira o título da seção de depoimentos.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_testimonials_description',
                    'label' => 'Descrição',
                    'name' => 'testimonials_description',
                    'type' => 'textarea',
                    'instructions' => 'Insira uma breve descrição para a seção de depoimentos.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_testimonials',
                    'label' => 'Depoimentos',
                    'name' => 'testimonials',
                    'type' => 'repeater',
                    'instructions' => 'Adicione os depoimentos que serão exibidos na página inicial.',
                    'required' => 0,
                    'min' => 0,
                    'max' => 10,
                    'layout' => 'block',
                    'button_label' => 'Adicionar Depoimento',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_testimonial_name',
                            'label' => 'Nome',
                            'name' => 'name',
                            'type' => 'text',
                            'instructions' => 'Insira o nome da pessoa que deu o depoimento.',
                            'required' => 1,
                        ),
                        array(
                            'key' => 'field_testimonial_position',
                            'label' => 'Cargo/Posição',
                            'name' => 'position',
                            'type' => 'text',
                            'instructions' => 'Insira o cargo ou posição da pessoa.',
                            'required' => 0,
                        ),
                        array(
                            'key' => 'field_testimonial_content',
                            'label' => 'Depoimento',
                            'name' => 'content',
                            'type' => 'textarea',
                            'instructions' => 'Insira o conteúdo do depoimento.',
                            'required' => 1,
                        ),
                        array(
                            'key' => 'field_testimonial_image',
                            'label' => 'Foto',
                            'name' => 'image',
                            'type' => 'image',
                            'instructions' => 'Selecione uma foto da pessoa (opcional).',
                            'required' => 0,
                            'return_format' => 'array',
                            'preview_size' => 'thumbnail',
                            'library' => 'all',
                        ),
                        array(
                            'key' => 'field_testimonial_rating',
                            'label' => 'Avaliação',
                            'name' => 'rating',
                            'type' => 'number',
                            'instructions' => 'Insira a avaliação (de 1 a 5).',
                            'required' => 0,
                            'min' => 1,
                            'max' => 5,
                            'step' => 1,
                            'default_value' => 5,
                        ),
                    ),
                ),
                array(
                    'key' => 'field_cta_section',
                    'label' => 'Seção CTA',
                    'name' => '',
                    'type' => 'tab',
                    'placement' => 'top',
                ),
                array(
                    'key' => 'field_cta_title',
                    'label' => 'Título',
                    'name' => 'cta_title',
                    'type' => 'text',
                    'instructions' => 'Insira o título da seção de chamada para ação.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_cta_description',
                    'label' => 'Descrição',
                    'name' => 'cta_description',
                    'type' => 'textarea',
                    'instructions' => 'Insira uma breve descrição para a seção de chamada para ação.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_cta_button_text',
                    'label' => 'Texto do Botão',
                    'name' => 'cta_button_text',
                    'type' => 'text',
                    'instructions' => 'Insira o texto do botão da seção de chamada para ação.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_cta_button_url',
                    'label' => 'URL do Botão',
                    'name' => 'cta_button_url',
                    'type' => 'url',
                    'instructions' => 'Insira a URL para onde o botão deve direcionar.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_cta_background',
                    'label' => 'Imagem de Fundo',
                    'name' => 'cta_background',
                    'type' => 'image',
                    'instructions' => 'Selecione uma imagem de fundo para a seção de chamada para ação.',
                    'required' => 0,
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ),
                array(
                    'key' => 'field_blog_section',
                    'label' => 'Seção Blog',
                    'name' => '',
                    'type' => 'tab',
                    'placement' => 'top',
                ),
                array(
                    'key' => 'field_blog_title',
                    'label' => 'Título',
                    'name' => 'blog_title',
                    'type' => 'text',
                    'instructions' => 'Insira o título da seção de blog.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_blog_description',
                    'label' => 'Descrição',
                    'name' => 'blog_description',
                    'type' => 'textarea',
                    'instructions' => 'Insira uma breve descrição para a seção de blog.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_blog_count',
                    'label' => 'Número de Posts',
                    'name' => 'blog_count',
                    'type' => 'number',
                    'instructions' => 'Insira o número de posts a serem exibidos.',
                    'required' => 0,
                    'default_value' => 3,
                    'min' => 1,
                    'max' => 9,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_blog_category',
                    'label' => 'Categoria',
                    'name' => 'blog_category',
                    'type' => 'taxonomy',
                    'instructions' => 'Selecione a categoria de posts a serem exibidos (opcional).',
                    'required' => 0,
                    'taxonomy' => 'category',
                    'field_type' => 'select',
                    'allow_null' => 1,
                    'add_term' => 0,
                    'save_terms' => 0,
                    'load_terms' => 0,
                    'return_format' => 'id',
                    'multiple' => 0,
                ),
                array(
                    'key' => 'field_blog_button_text',
                    'label' => 'Texto do Botão',
                    'name' => 'blog_button_text',
                    'type' => 'text',
                    'instructions' => 'Insira o texto do botão da seção de blog.',
                    'required' => 0,
                    'default_value' => 'Ver Todos os Posts',
                ),
                array(
                    'key' => 'field_blog_button_url',
                    'label' => 'URL do Botão',
                    'name' => 'blog_button_url',
                    'type' => 'url',
                    'instructions' => 'Insira a URL para onde o botão deve direcionar.',
                    'required' => 0,
                    'default_value' => '/blog/',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'page_template',
                        'operator' => '==',
                        'value' => 'templates/template-home.php',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => array(
                0 => 'the_content',
            ),
            'active' => true,
            'description' => '',
        ));

        // Grupo de campos para áreas de atuação
        acf_add_local_field_group(array(
            'key' => 'group_area_atuacao',
            'title' => 'Informações da Área de Atuação',
            'fields' => array(
                array(
                    'key' => 'field_area_icon',
                    'label' => 'Ícone',
                    'name' => 'area_icon',
                    'type' => 'text',
                    'instructions' => 'Insira o nome do ícone (FontAwesome) para esta área de atuação.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_area_resumo',
                    'label' => 'Resumo',
                    'name' => 'area_resumo',
                    'type' => 'textarea',
                    'instructions' => 'Insira um breve resumo sobre esta área de atuação.',
                    'required' => 0,
                    'maxlength' => 300,
                    'rows' => 3,
                ),
                array(
                    'key' => 'field_area_destaque',
                    'label' => 'Destacar na Página Inicial',
                    'name' => 'area_destaque',
                    'type' => 'true_false',
                    'instructions' => 'Marque para destacar esta área de atuação na página inicial.',
                    'required' => 0,
                    'ui' => 1,
                ),
                array(
                    'key' => 'field_area_ordem',
                    'label' => 'Ordem de Exibição',
                    'name' => 'area_ordem',
                    'type' => 'number',
                    'instructions' => 'Insira um número para definir a ordem de exibição (menor = primeiro).',
                    'required' => 0,
                    'default_value' => 10,
                    'min' => 1,
                    'max' => 100,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_area_imagem_destaque',
                    'label' => 'Imagem de Destaque',
                    'name' => 'area_imagem_destaque',
                    'type' => 'image',
                    'instructions' => 'Selecione uma imagem de destaque para esta área de atuação.',
                    'required' => 0,
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ),
                array(
                    'key' => 'field_area_servicos',
                    'label' => 'Serviços Oferecidos',
                    'name' => 'area_servicos',
                    'type' => 'repeater',
                    'instructions' => 'Adicione os serviços oferecidos nesta área de atuação.',
                    'required' => 0,
                    'min' => 0,
                    'max' => 20,
                    'layout' => 'table',
                    'button_label' => 'Adicionar Serviço',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_area_servico_nome',
                            'label' => 'Nome do Serviço',
                            'name' => 'nome',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 1,
                        ),
                        array(
                            'key' => 'field_area_servico_descricao',
                            'label' => 'Descrição',
                            'name' => 'descricao',
                            'type' => 'textarea',
                            'instructions' => '',
                            'required' => 0,
                            'rows' => 3,
                        ),
                    ),
                ),
                array(
                    'key' => 'field_area_faq',
                    'label' => 'Perguntas Frequentes',
                    'name' => 'area_faq',
                    'type' => 'repeater',
                    'instructions' => 'Adicione perguntas frequentes sobre esta área de atuação.',
                    'required' => 0,
                    'min' => 0,
                    'max' => 15,
                    'layout' => 'block',
                    'button_label' => 'Adicionar Pergunta',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_area_faq_pergunta',
                            'label' => 'Pergunta',
                            'name' => 'pergunta',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 1,
                        ),
                        array(
                            'key' => 'field_area_faq_resposta',
                            'label' => 'Resposta',
                            'name' => 'resposta',
                            'type' => 'wysiwyg',
                            'instructions' => '',
                            'required' => 1,
                            'tabs' => 'all',
                            'toolbar' => 'basic',
                            'media_upload' => 0,
                        ),
                    ),
                ),
                array(
                    'key' => 'field_area_cta',
                    'label' => 'Chamada para Ação',
                    'name' => 'area_cta',
                    'type' => 'group',
                    'instructions' => 'Configure a chamada para ação desta área de atuação.',
                    'required' => 0,
                    'layout' => 'block',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_area_cta_titulo',
                            'label' => 'Título',
                            'name' => 'titulo',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 0,
                        ),
                        array(
                            'key' => 'field_area_cta_texto',
                            'label' => 'Texto',
                            'name' => 'texto',
                            'type' => 'textarea',
                            'instructions' => '',
                            'required' => 0,
                        ),
                        array(
                            'key' => 'field_area_cta_botao_texto',
                            'label' => 'Texto do Botão',
                            'name' => 'botao_texto',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 0,
                            'default_value' => 'Entre em Contato',
                        ),
                        array(
                            'key' => 'field_area_cta_botao_url',
                            'label' => 'URL do Botão',
                            'name' => 'botao_url',
                            'type' => 'url',
                            'instructions' => '',
                            'required' => 0,
                            'default_value' => '/contato/',
                        ),
                    ),
                ),
                array(
                    'key' => 'field_area_seo',
                    'label' => 'SEO',
                    'name' => 'area_seo',
                    'type' => 'group',
                    'instructions' => 'Configure as informações de SEO para esta área de atuação.',
                    'required' => 0,
                    'layout' => 'block',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_area_seo_title',
                            'label' => 'Meta Title',
                            'name' => 'meta_title',
                            'type' => 'text',
                            'instructions' => 'Insira o título para SEO (máximo 60 caracteres).',
                            'required' => 0,
                            'maxlength' => 60,
                        ),
                        array(
                            'key' => 'field_area_seo_description',
                            'label' => 'Meta Description',
                            'name' => 'meta_description',
                            'type' => 'textarea',
                            'instructions' => 'Insira a descrição para SEO (máximo 160 caracteres).',
                            'required' => 0,
                            'maxlength' => 160,
                            'rows' => 3,
                        ),
                        array(
                            'key' => 'field_area_seo_keywords',
                            'label' => 'Meta Keywords',
                            'name' => 'meta_keywords',
                            'type' => 'text',
                            'instructions' => 'Insira palavras-chave separadas por vírgula.',
                            'required' => 0,
                        ),
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'area_atuacao',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));

        // Grupo de campos para equipe
        acf_add_local_field_group(array(
            'key' => 'group_equipe',
            'title' => 'Informações do Membro da Equipe',
            'fields' => array(
                array(
                    'key' => 'field_equipe_cargo',
                    'label' => 'Cargo',
                    'name' => 'equipe_cargo',
                    'type' => 'text',
                    'instructions' => 'Insira o cargo do membro da equipe.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_equipe_oab',
                    'label' => 'Número OAB',
                    'name' => 'equipe_oab',
                    'type' => 'text',
                    'instructions' => 'Insira o número da OAB do advogado.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_equipe_email',
                    'label' => 'E-mail',
                    'name' => 'equipe_email',
                    'type' => 'email',
                    'instructions' => 'Insira o e-mail do membro da equipe.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_equipe_telefone',
                    'label' => 'Telefone',
                    'name' => 'equipe_telefone',
                    'type' => 'text',
                    'instructions' => 'Insira o telefone do membro da equipe.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_equipe_resumo',
                    'label' => 'Resumo',
                    'name' => 'equipe_resumo',
                    'type' => 'textarea',
                    'instructions' => 'Insira um breve resumo sobre o membro da equipe.',
                    'required' => 0,
                    'maxlength' => 300,
                    'rows' => 3,
                ),
                array(
                    'key' => 'field_equipe_destaque',
                    'label' => 'Destacar na Página Inicial',
                    'name' => 'equipe_destaque',
                    'type' => 'true_false',
                    'instructions' => 'Marque para destacar este membro da equipe na página inicial.',
                    'required' => 0,
                    'ui' => 1,
                ),
                array(
                    'key' => 'field_equipe_ordem',
                    'label' => 'Ordem de Exibição',
                    'name' => 'equipe_ordem',
                    'type' => 'number',
                    'instructions' => 'Insira um número para definir a ordem de exibição (menor = primeiro).',
                    'required' => 0,
                    'default_value' => 10,
                    'min' => 1,
                    'max' => 100,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_equipe_redes_sociais',
                    'label' => 'Redes Sociais',
                    'name' => 'equipe_redes_sociais',
                    'type' => 'repeater',
                    'instructions' => 'Adicione as redes sociais do membro da equipe.',
                    'required' => 0,
                    'min' => 0,
                    'max' => 5,
                    'layout' => 'table',
                    'button_label' => 'Adicionar Rede Social',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_equipe_rede_social',
                            'label' => 'Rede Social',
                            'name' => 'rede_social',
                            'type' => 'select',
                            'instructions' => '',
                            'required' => 1,
                            'choices' => array(
                                'facebook' => 'Facebook',
                                'instagram' => 'Instagram',
                                'twitter' => 'Twitter',
                                'linkedin' => 'LinkedIn',
                                'youtube' => 'YouTube',
                            ),
                            'default_value' => 'linkedin',
                        ),
                        array(
                            'key' => 'field_equipe_rede_social_url',
                            'label' => 'URL',
                            'name' => 'url',
                            'type' => 'url',
                            'instructions' => '',
                            'required' => 1,
                        ),
                    ),
                ),
                array(
                    'key' => 'field_equipe_formacao',
                    'label' => 'Formação Acadêmica',
                    'name' => 'equipe_formacao',
                    'type' => 'repeater',
                    'instructions' => 'Adicione a formação acadêmica do membro da equipe.',
                    'required' => 0,
                    'min' => 0,
                    'max' => 10,
                    'layout' => 'table',
                    'button_label' => 'Adicionar Formação',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_equipe_formacao_curso',
                            'label' => 'Curso',
                            'name' => 'curso',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 1,
                        ),
                        array(
                            'key' => 'field_equipe_formacao_instituicao',
                            'label' => 'Instituição',
                            'name' => 'instituicao',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 1,
                        ),
                        array(
                            'key' => 'field_equipe_formacao_ano',
                            'label' => 'Ano de Conclusão',
                            'name' => 'ano',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 0,
                        ),
                    ),
                ),
                array(
                    'key' => 'field_equipe_areas_atuacao',
                    'label' => 'Áreas de Atuação',
                    'name' => 'equipe_areas_atuacao',
                    'type' => 'relationship',
                    'instructions' => 'Selecione as áreas de atuação deste membro da equipe.',
                    'required' => 0,
                    'post_type' => array(
                        0 => 'area_atuacao',
                    ),
                    'taxonomy' => '',
                    'filters' => array(
                        0 => 'search',
                    ),
                    'elements' => '',
                    'min' => '',
                    'max' => '',
                    'return_format' => 'object',
                ),
                array(
                    'key' => 'field_equipe_seo',
                    'label' => 'SEO',
                    'name' => 'equipe_seo',
                    'type' => 'group',
                    'instructions' => 'Configure as informações de SEO para este membro da equipe.',
                    'required' => 0,
                    'layout' => 'block',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_equipe_seo_title',
                            'label' => 'Meta Title',
                            'name' => 'meta_title',
                            'type' => 'text',
                            'instructions' => 'Insira o título para SEO (máximo 60 caracteres).',
                            'required' => 0,
                            'maxlength' => 60,
                        ),
                        array(
                            'key' => 'field_equipe_seo_description',
                            'label' => 'Meta Description',
                            'name' => 'meta_description',
                            'type' => 'textarea',
                            'instructions' => 'Insira a descrição para SEO (máximo 160 caracteres).',
                            'required' => 0,
                            'maxlength' => 160,
                            'rows' => 3,
                        ),
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'equipe',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));

        // Grupo de campos para posts relacionados
        acf_add_local_field_group(array(
            'key' => 'group_posts_relacionados',
            'title' => 'Posts Relacionados',
            'fields' => array(
                array(
                    'key' => 'field_posts_relacionados',
                    'label' => 'Posts Relacionados',
                    'name' => 'posts_relacionados',
                    'type' => 'relationship',
                    'instructions' => 'Selecione posts relacionados a este conteúdo.',
                    'required' => 0,
                    'post_type' => array(
                        0 => 'post',
                        1 => 'page',
                        2 => 'area_atuacao',
                    ),
                    'taxonomy' => '',
                    'filters' => array(
                        0 => 'search',
                        1 => 'post_type',
                        2 => 'taxonomy',
                    ),
                    'elements' => '',
                    'min' => '',
                    'max' => 6,
                    'return_format' => 'object',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'post',
                    ),
                ),
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'page',
                    ),
                ),
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'area_atuacao',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'side',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));

        // Grupo de campos para SEO
        acf_add_local_field_group(array(
            'key' => 'group_seo',
            'title' => 'SEO',
            'fields' => array(
                array(
                    'key' => 'field_seo_title',
                    'label' => 'Meta Title',
                    'name' => 'seo_title',
                    'type' => 'text',
                    'instructions' => 'Insira o título para SEO (máximo 60 caracteres).',
                    'required' => 0,
                    'maxlength' => 60,
                ),
                array(
                    'key' => 'field_seo_description',
                    'label' => 'Meta Description',
                    'name' => 'seo_description',
                    'type' => 'textarea',
                    'instructions' => 'Insira a descrição para SEO (máximo 160 caracteres).',
                    'required' => 0,
                    'maxlength' => 160,
                    'rows' => 3,
                ),
                array(
                    'key' => 'field_seo_keywords',
                    'label' => 'Meta Keywords',
                    'name' => 'seo_keywords',
                    'type' => 'text',
                    'instructions' => 'Insira palavras-chave separadas por vírgula.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_seo_og_image',
                    'label' => 'Imagem para Redes Sociais',
                    'name' => 'seo_og_image',
                    'type' => 'image',
                    'instructions' => 'Selecione uma imagem para compartilhamento em redes sociais (1200x630px recomendado).',
                    'required' => 0,
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                    'min_width' => '',
                    'min_height' => '',
                    'min_size' => '',
                    'max_width' => '',
                    'max_height' => '',
                    'max_size' => '',
                    'mime_types' => '',
                ),
                array(
                    'key' => 'field_seo_no_index',
                    'label' => 'Não Indexar',
                    'name' => 'seo_no_index',
                    'type' => 'true_false',
                    'instructions' => 'Marque para impedir que esta página seja indexada pelos motores de busca.',
                    'required' => 0,
                    'ui' => 1,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'post',
                    ),
                ),
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'page',
                    ),
                ),
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'area_atuacao',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'side',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));

        // Grupo de campos para página de contato
        acf_add_local_field_group(array(
            'key' => 'group_contato',
            'title' => 'Configurações da Página de Contato',
            'fields' => array(
                array(
                    'key' => 'field_contato_titulo',
                    'label' => 'Título',
                    'name' => 'contato_titulo',
                    'type' => 'text',
                    'instructions' => 'Insira o título da página de contato.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_contato_descricao',
                    'label' => 'Descrição',
                    'name' => 'contato_descricao',
                    'type' => 'textarea',
                    'instructions' => 'Insira uma breve descrição para a página de contato.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_contato_shortcode',
                    'label' => 'Shortcode do Formulário',
                    'name' => 'contato_shortcode',
                    'type' => 'text',
                    'instructions' => 'Insira o shortcode do formulário de contato (ex: [contact-form-7 id="123" title="Formulário de Contato"]).',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_contato_mapa',
                    'label' => 'Mapa',
                    'name' => 'contato_mapa',
                    'type' => 'google_map',
                    'instructions' => 'Selecione a localização do escritório no mapa.',
                    'required' => 0,
                    'center_lat' => '-23.550520',
                    'center_lng' => '-46.633308',
                    'zoom' => 16,
                    'height' => 400,
                ),
                array(
                    'key' => 'field_contato_iframe',
                    'label' => 'Iframe do Mapa',
                    'name' => 'contato_iframe',
                    'type' => 'textarea',
                    'instructions' => 'Alternativamente, insira o código iframe do Google Maps.',
                    'required' => 0,
                ),
                array(
                    'key' => 'field_contato_info',
                    'label' => 'Informações de Contato',
                    'name' => 'contato_info',
                    'type' => 'group',
                    'instructions' => 'Configure as informações de contato exibidas na página.',
                    'required' => 0,
                    'layout' => 'block',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_contato_info_titulo',
                            'label' => 'Título',
                            'name' => 'titulo',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 0,
                            'default_value' => 'Entre em Contato',
                        ),
                        array(
                            'key' => 'field_contato_info_mostrar_telefone',
                            'label' => 'Mostrar Telefone',
                            'name' => 'mostrar_telefone',
                            'type' => 'true_false',
                            'instructions' => '',
                            'required' => 0,
                            'ui' => 1,
                            'default_value' => 1,
                        ),
                        array(
                            'key' => 'field_contato_info_mostrar_whatsapp',
                            'label' => 'Mostrar WhatsApp',
                            'name' => 'mostrar_whatsapp',
                            'type' => 'true_false',
                            'instructions' => '',
                            'required' => 0,
                            'ui' => 1,
                            'default_value' => 1,
                        ),
                        array(
                            'key' => 'field_contato_info_mostrar_email',
                            'label' => 'Mostrar E-mail',
                            'name' => 'mostrar_email',
                            'type' => 'true_false',
                            'instructions' => '',
                            'required' => 0,
                            'ui' => 1,
                            'default_value' => 1,
                        ),
                        array(
                            'key' => 'field_contato_info_mostrar_endereco',
                            'label' => 'Mostrar Endereço',
                            'name' => 'mostrar_endereco',
                            'type' => 'true_false',
                            'instructions' => '',
                            'required' => 0,
                            'ui' => 1,
                            'default_value' => 1,
                        ),
                        array(
                            'key' => 'field_contato_info_mostrar_horario',
                            'label' => 'Mostrar Horário de Funcionamento',
                            'name' => 'mostrar_horario',
                            'type' => 'true_false',
                            'instructions' => '',
                            'required' => 0,
                            'ui' => 1,
                            'default_value' => 1,
                        ),
                        array(
                            'key' => 'field_contato_info_mostrar_redes_sociais',
                            'label' => 'Mostrar Redes Sociais',
                            'name' => 'mostrar_redes_sociais',
                            'type' => 'true_false',
                            'instructions' => '',
                            'required' => 0,
                            'ui' => 1,
                            'default_value' => 1,
                        ),
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'page_template',
                        'operator' => '==',
                        'value' => 'templates/template-contato.php',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => array(
                0 => 'the_content',
            ),
            'active' => true,
            'description' => '',
        ));
    }
}
add_action('acf/init', 'thabatta_register_acf_fields');

/**
 * Obter valor de campo ACF com fallback
 */
function thabatta_get_field($field_name, $post_id = false, $default = '')
{
    if (function_exists('get_field')) {
        $value = get_field($field_name, $post_id);
        return $value !== null && $value !== '' ? $value : $default;
    }
    return $default;
}

/**
 * Obter valor de campo ACF de opções com fallback
 */
function thabatta_get_option($field_name, $default = '')
{
    if (function_exists('get_field')) {
        $value = get_field($field_name, 'option');
        return $value !== null && $value !== '' ? $value : $default;
    }
    return $default;
}

/**
 * Obter informações de contato
 */
function thabatta_get_contact_info()
{
    $contact_info = array(
        'telefone' => thabatta_get_option('telefone', ''),
        'whatsapp' => thabatta_get_option('whatsapp', ''),
        'whatsapp_message' => thabatta_get_option('whatsapp_message', 'Olá, gostaria de mais informações sobre os serviços de advocacia.'),
        'email' => thabatta_get_option('email', ''),
        'endereco' => thabatta_get_option('endereco', ''),
        'cidade_estado_cep' => thabatta_get_option('cidade_estado_cep', ''),
        'horario_funcionamento' => thabatta_get_option('horario_funcionamento', ''),
    );

    return $contact_info;
}

/**
 * Obter redes sociais
 */
function thabatta_get_social_media()
{
    $redes_sociais = thabatta_get_option('redes_sociais', array());

    if (!is_array($redes_sociais) || empty($redes_sociais)) {
        return array();
    }

    return $redes_sociais;
}

/**
 * Obter URL do WhatsApp
 */
function thabatta_get_whatsapp_url()
{
    $whatsapp = thabatta_get_option('whatsapp', '');
    $message = thabatta_get_option('whatsapp_message', 'Olá, gostaria de mais informações sobre os serviços de advocacia.');

    if (empty($whatsapp)) {
        return '';
    }

    // Remover caracteres não numéricos
    $whatsapp = preg_replace('/[^0-9]/', '', $whatsapp);

    // Adicionar código do país se não estiver presente
    if (substr($whatsapp, 0, 2) !== '55') {
        $whatsapp = '55' . $whatsapp;
    }

    return 'https://wa.me/' . $whatsapp . '?text=' . urlencode($message);
}

/**
 * Obter meta tags SEO para um post
 */
function thabatta_get_seo_meta_tags($post_id = false)
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $meta_tags = array(
        'title' => '',
        'description' => '',
        'keywords' => '',
        'og_image' => '',
        'no_index' => false,
    );

    // Obter valores dos campos ACF
    $meta_tags['title'] = thabatta_get_field('seo_title', $post_id, '');
    $meta_tags['description'] = thabatta_get_field('seo_description', $post_id, '');
    $meta_tags['keywords'] = thabatta_get_field('seo_keywords', $post_id, '');
    $meta_tags['no_index'] = thabatta_get_field('seo_no_index', $post_id, false);

    $og_image = thabatta_get_field('seo_og_image', $post_id);
    if (is_array($og_image) && isset($og_image['url'])) {
        $meta_tags['og_image'] = $og_image['url'];
    }

    // Usar valores padrão se os campos estiverem vazios
    if (empty($meta_tags['title'])) {
        $meta_tags['title'] = get_the_title($post_id);
    }

    if (empty($meta_tags['description'])) {
        // Tentar obter do resumo do post
        $post = get_post($post_id);
        if ($post && !empty($post->post_excerpt)) {
            $meta_tags['description'] = wp_strip_all_tags($post->post_excerpt);
        } elseif ($post && !empty($post->post_content)) {
            $meta_tags['description'] = wp_trim_words(wp_strip_all_tags($post->post_content), 30, '...');
        } else {
            $meta_tags['description'] = thabatta_get_option('meta_description', '');
        }
    }

    if (empty($meta_tags['og_image'])) {
        // Tentar obter da imagem destacada
        if (has_post_thumbnail($post_id)) {
            $meta_tags['og_image'] = get_the_post_thumbnail_url($post_id, 'large');
        }
    }

    return $meta_tags;
}

/**
 * Obter áreas de atuação em destaque
 */
function thabatta_get_featured_areas($count = 6)
{
    $args = array(
        'post_type' => 'area_atuacao',
        'posts_per_page' => $count,
        'meta_key' => 'area_destaque',
        'meta_value' => '1',
        'orderby' => array(
            'meta_value_num' => 'ASC',
            'title' => 'ASC',
        ),
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => 'area_ordem',
                'compare' => 'EXISTS',
            ),
            array(
                'key' => 'area_ordem',
                'compare' => 'NOT EXISTS',
            ),
        ),
    );

    $query = new WP_Query($args);

    return $query->posts;
}

/**
 * Obter membros da equipe em destaque
 */
function thabatta_get_featured_team_members($count = 4)
{
    $args = array(
        'post_type' => 'equipe',
        'posts_per_page' => $count,
        'meta_key' => 'equipe_destaque',
        'meta_value' => '1',
        'orderby' => array(
            'meta_value_num' => 'ASC',
            'title' => 'ASC',
        ),
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => 'equipe_ordem',
                'compare' => 'EXISTS',
            ),
            array(
                'key' => 'equipe_ordem',
                'compare' => 'NOT EXISTS',
            ),
        ),
    );

    $query = new WP_Query($args);

    return $query->posts;
}

/**
 * Obter posts recentes
 */
function thabatta_get_recent_posts($count = 3, $category_id = null)
{
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $count,
        'orderby' => 'date',
        'order' => 'DESC',
    );

    if ($category_id) {
        $args['cat'] = $category_id;
    }

    $query = new WP_Query($args);

    return $query->posts;
}
