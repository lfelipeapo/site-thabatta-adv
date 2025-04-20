<?php
/**
 * Classe para o Custom Post Type Cursos
 * 
 * Implementa o CPT Cursos com seus campos personalizados
 * 
 * @package WPFramework\Models\PostTypes
 */

namespace WPFramework\Models\PostTypes;

class Curso
{
    /**
     * Nome do post type
     * 
     * @var string
     */
    const POST_TYPE = 'curso';
    
    /**
     * Registra o post type
     */
    public static function register()
    {
        // Registra o Custom Post Type
        register_post_type(self::POST_TYPE, [
            'labels' => [
                'name' => __('Cursos', 'wpframework'),
                'singular_name' => __('Curso', 'wpframework'),
                'add_new' => __('Adicionar Novo', 'wpframework'),
                'add_new_item' => __('Adicionar Novo Curso', 'wpframework'),
                'edit_item' => __('Editar Curso', 'wpframework'),
                'new_item' => __('Novo Curso', 'wpframework'),
                'view_item' => __('Ver Curso', 'wpframework'),
                'search_items' => __('Buscar Cursos', 'wpframework'),
                'not_found' => __('Nenhum curso encontrado', 'wpframework'),
                'not_found_in_trash' => __('Nenhum curso encontrado na lixeira', 'wpframework'),
                'all_items' => __('Todos os Cursos', 'wpframework'),
                'archives' => __('Arquivos de Cursos', 'wpframework'),
                'insert_into_item' => __('Inserir no curso', 'wpframework'),
                'uploaded_to_this_item' => __('Enviado para este curso', 'wpframework'),
                'featured_image' => __('Imagem Destacada', 'wpframework'),
                'set_featured_image' => __('Definir imagem destacada', 'wpframework'),
                'remove_featured_image' => __('Remover imagem destacada', 'wpframework'),
                'use_featured_image' => __('Usar como imagem destacada', 'wpframework'),
                'menu_name' => __('Cursos', 'wpframework'),
            ],
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'cursos'],
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-welcome-learn-more',
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'author'],
            'show_in_rest' => true,
        ]);
        
        // Registra a taxonomia Categoria de Cursos
        register_taxonomy('categoria_curso', [self::POST_TYPE], [
            'labels' => [
                'name' => __('Categorias de Cursos', 'wpframework'),
                'singular_name' => __('Categoria de Curso', 'wpframework'),
                'search_items' => __('Buscar Categorias', 'wpframework'),
                'all_items' => __('Todas as Categorias', 'wpframework'),
                'parent_item' => __('Categoria Pai', 'wpframework'),
                'parent_item_colon' => __('Categoria Pai:', 'wpframework'),
                'edit_item' => __('Editar Categoria', 'wpframework'),
                'update_item' => __('Atualizar Categoria', 'wpframework'),
                'add_new_item' => __('Adicionar Nova Categoria', 'wpframework'),
                'new_item_name' => __('Nome da Nova Categoria', 'wpframework'),
                'menu_name' => __('Categorias', 'wpframework'),
            ],
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'categoria-curso'],
            'show_in_rest' => true,
        ]);
        
        // Registra campos ACF se o plugin estiver ativo
        if (function_exists('acf_add_local_field_group')) {
            self::registerAcfFields();
        }
    }
    
    /**
     * Registra os campos ACF para o post type Curso
     */
    private static function registerAcfFields()
    {
        acf_add_local_field_group([
            'key' => 'group_cursos',
            'title' => 'Informações do Curso',
            'fields' => [
                [
                    'key' => 'field_curso_instrutor',
                    'label' => 'Instrutor',
                    'name' => 'instrutor',
                    'type' => 'text',
                    'instructions' => 'Nome do instrutor do curso',
                    'required' => 1,
                ],
                [
                    'key' => 'field_curso_duracao',
                    'label' => 'Duração',
                    'name' => 'duracao',
                    'type' => 'text',
                    'instructions' => 'Duração do curso (ex: 10 horas, 4 semanas)',
                    'required' => 1,
                ],
                [
                    'key' => 'field_curso_preco',
                    'label' => 'Preço',
                    'name' => 'preco',
                    'type' => 'number',
                    'instructions' => 'Preço do curso em reais',
                    'required' => 1,
                    'min' => 0,
                    'step' => 0.01,
                ],
                [
                    'key' => 'field_curso_nivel',
                    'label' => 'Nível',
                    'name' => 'nivel',
                    'type' => 'select',
                    'instructions' => 'Nível de dificuldade do curso',
                    'required' => 1,
                    'choices' => [
                        'iniciante' => 'Iniciante',
                        'intermediario' => 'Intermediário',
                        'avancado' => 'Avançado',
                    ],
                    'default_value' => 'iniciante',
                ],
                [
                    'key' => 'field_curso_data_inicio',
                    'label' => 'Data de Início',
                    'name' => 'data_inicio',
                    'type' => 'date_picker',
                    'instructions' => 'Data de início do curso',
                    'required' => 0,
                    'display_format' => 'd/m/Y',
                    'return_format' => 'd/m/Y',
                ],
                [
                    'key' => 'field_curso_vagas',
                    'label' => 'Vagas Disponíveis',
                    'name' => 'vagas',
                    'type' => 'number',
                    'instructions' => 'Número de vagas disponíveis',
                    'required' => 0,
                    'min' => 0,
                ],
                [
                    'key' => 'field_curso_modulos',
                    'label' => 'Módulos',
                    'name' => 'modulos',
                    'type' => 'repeater',
                    'instructions' => 'Adicione os módulos do curso',
                    'required' => 0,
                    'min' => 0,
                    'layout' => 'block',
                    'button_label' => 'Adicionar Módulo',
                    'sub_fields' => [
                        [
                            'key' => 'field_modulo_titulo',
                            'label' => 'Título',
                            'name' => 'titulo',
                            'type' => 'text',
                            'required' => 1,
                        ],
                        [
                            'key' => 'field_modulo_descricao',
                            'label' => 'Descrição',
                            'name' => 'descricao',
                            'type' => 'textarea',
                            'required' => 0,
                        ],
                        [
                            'key' => 'field_modulo_duracao',
                            'label' => 'Duração',
                            'name' => 'duracao',
                            'type' => 'text',
                            'required' => 0,
                        ],
                    ],
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => self::POST_TYPE,
                    ],
                ],
            ],
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
            'show_in_rest' => true,
        ]);
    }
}
