<?php
/**
 * Serviço para gerenciamento de cursos
 * 
 * Fornece funcionalidades para manipulação de cursos
 * 
 * @package WPFramework\Services
 */

namespace WPFramework\Services;

class CursoService extends BaseService
{
    /**
     * Inicializa o serviço
     * 
     * @return void
     */
    public function init()
    {
        // Registra hooks e filtros
        add_action('init', [$this, 'registerPostType']);
        add_action('init', [$this, 'registerTaxonomies']);
        add_action('acf/init', [$this, 'registerFields']);
    }
    
    /**
     * Registra o Custom Post Type Curso
     * 
     * @return void
     */
    public function registerPostType()
    {
        $labels = [
            'name'               => __('Cursos', 'wpframework'),
            'singular_name'      => __('Curso', 'wpframework'),
            'menu_name'          => __('Cursos', 'wpframework'),
            'name_admin_bar'     => __('Curso', 'wpframework'),
            'add_new'            => __('Adicionar Novo', 'wpframework'),
            'add_new_item'       => __('Adicionar Novo Curso', 'wpframework'),
            'new_item'           => __('Novo Curso', 'wpframework'),
            'edit_item'          => __('Editar Curso', 'wpframework'),
            'view_item'          => __('Ver Curso', 'wpframework'),
            'all_items'          => __('Todos os Cursos', 'wpframework'),
            'search_items'       => __('Buscar Cursos', 'wpframework'),
            'parent_item_colon'  => __('Cursos Pai:', 'wpframework'),
            'not_found'          => __('Nenhum curso encontrado.', 'wpframework'),
            'not_found_in_trash' => __('Nenhum curso encontrado na lixeira.', 'wpframework')
        ];
        
        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'cursos'],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-welcome-learn-more',
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
            'show_in_rest'       => true,
        ];
        
        register_post_type('curso', $args);
    }
    
    /**
     * Registra as taxonomias para o Custom Post Type Curso
     * 
     * @return void
     */
    public function registerTaxonomies()
    {
        // Taxonomia para categorias de cursos
        $labels = [
            'name'              => __('Categorias de Cursos', 'wpframework'),
            'singular_name'     => __('Categoria de Curso', 'wpframework'),
            'search_items'      => __('Buscar Categorias', 'wpframework'),
            'all_items'         => __('Todas as Categorias', 'wpframework'),
            'parent_item'       => __('Categoria Pai', 'wpframework'),
            'parent_item_colon' => __('Categoria Pai:', 'wpframework'),
            'edit_item'         => __('Editar Categoria', 'wpframework'),
            'update_item'       => __('Atualizar Categoria', 'wpframework'),
            'add_new_item'      => __('Adicionar Nova Categoria', 'wpframework'),
            'new_item_name'     => __('Nova Categoria', 'wpframework'),
            'menu_name'         => __('Categorias', 'wpframework'),
        ];
        
        $args = [
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'categoria-curso'],
            'show_in_rest'      => true,
        ];
        
        register_taxonomy('categoria_curso', ['curso'], $args);
    }
    
    /**
     * Registra os campos ACF para o Custom Post Type Curso
     * 
     * @return void
     */
    public function registerFields()
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }
        
        acf_add_local_field_group([
            'key' => 'group_curso',
            'title' => 'Informações do Curso',
            'fields' => [
                [
                    'key' => 'field_instrutor',
                    'label' => 'Instrutor',
                    'name' => 'instrutor',
                    'type' => 'text',
                    'required' => 1,
                ],
                [
                    'key' => 'field_duracao',
                    'label' => 'Duração',
                    'name' => 'duracao',
                    'type' => 'text',
                    'required' => 1,
                ],
                [
                    'key' => 'field_preco',
                    'label' => 'Preço',
                    'name' => 'preco',
                    'type' => 'number',
                    'required' => 1,
                    'min' => 0,
                    'step' => 0.01,
                ],
                [
                    'key' => 'field_nivel',
                    'label' => 'Nível',
                    'name' => 'nivel',
                    'type' => 'select',
                    'required' => 1,
                    'choices' => [
                        'iniciante' => 'Iniciante',
                        'intermediario' => 'Intermediário',
                        'avancado' => 'Avançado',
                    ],
                    'default_value' => 'iniciante',
                ],
                [
                    'key' => 'field_data_inicio',
                    'label' => 'Data de Início',
                    'name' => 'data_inicio',
                    'type' => 'date_picker',
                    'required' => 0,
                ],
                [
                    'key' => 'field_vagas',
                    'label' => 'Vagas Disponíveis',
                    'name' => 'vagas',
                    'type' => 'number',
                    'required' => 0,
                    'min' => 0,
                ],
                [
                    'key' => 'field_modulos',
                    'label' => 'Módulos',
                    'name' => 'modulos',
                    'type' => 'repeater',
                    'required' => 0,
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
                            'key' => 'field_modulo_duracao',
                            'label' => 'Duração',
                            'name' => 'duracao',
                            'type' => 'text',
                            'required' => 0,
                        ],
                        [
                            'key' => 'field_modulo_descricao',
                            'label' => 'Descrição',
                            'name' => 'descricao',
                            'type' => 'textarea',
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
                        'value' => 'curso',
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
    
    /**
     * Obtém todos os cursos
     * 
     * @param array $args Argumentos para a consulta
     * @return array
     */
    public function getCursos($args = [])
    {
        $default_args = [
            'post_type' => 'curso',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ];
        
        $args = wp_parse_args($args, $default_args);
        
        $query = new \WP_Query($args);
        
        return $query->posts;
    }
    
    /**
     * Obtém um curso específico
     * 
     * @param int $id ID do curso
     * @return \WP_Post|null
     */
    public function getCurso($id)
    {
        return get_post($id);
    }
    
    /**
     * Obtém os campos ACF de um curso
     * 
     * @param int $id ID do curso
     * @return array
     */
    public function getCursoFields($id)
    {
        if (!function_exists('get_fields')) {
            return [];
        }
        
        return get_fields($id) ?: [];
    }
    
    /**
     * Obtém cursos relacionados a um curso específico
     * 
     * @param int $id ID do curso
     * @param int $limit Limite de cursos
     * @return array
     */
    public function getCursosRelacionados($id, $limit = 3)
    {
        // Obtém as categorias do curso
        $categorias = wp_get_post_terms($id, 'categoria_curso', ['fields' => 'ids']);
        
        if (empty($categorias) || is_wp_error($categorias)) {
            return [];
        }
        
        $args = [
            'post_type' => 'curso',
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'post__not_in' => [$id],
            'tax_query' => [
                [
                    'taxonomy' => 'categoria_curso',
                    'field' => 'term_id',
                    'terms' => $categorias,
                ],
            ],
        ];
        
        $query = new \WP_Query($args);
        
        return $query->posts;
    }
}
