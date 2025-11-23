<?php
/**
 * Classe para API de Cursos
 * 
 * Implementa endpoints REST para o Custom Post Type Cursos
 * 
 * @package WPFramework\Api
 */

namespace WPFramework\Api;

use WPFramework\Controllers\ApiController;
use WPFramework\DTOs\PostDTO;

class CursosController extends ApiController
{
    /**
     * Registra os endpoints da API
     */
    public static function register()
    {
        // Obtém a instância do ApiManager
        $api = \WPFramework\Core\ApiManager::init();
        
        // Define o namespace base da API
        $namespace = $api->getNamespace();
        
        // Registra os endpoints
        add_action('rest_api_init', function() use ($namespace) {
            // Endpoint para listar cursos
            register_rest_route($namespace, '/cursos', [
                'methods' => 'GET',
                'callback' => [self::class, 'index'],
                'permission_callback' => function() {
                    return true; // Acesso público
                },
            ]);
            
            // Endpoint para obter um curso específico
            register_rest_route($namespace, '/cursos/(?P<id>\d+)', [
                'methods' => 'GET',
                'callback' => [self::class, 'show'],
                'permission_callback' => function() {
                    return true; // Acesso público
                },
            ]);
            
            // Endpoint para criar um curso
            register_rest_route($namespace, '/cursos', [
                'methods' => 'POST',
                'callback' => [self::class, 'store'],
                'permission_callback' => function() {
                    return current_user_can('edit_posts');
                },
            ]);
            
            // Endpoint para atualizar um curso
            register_rest_route($namespace, '/cursos/(?P<id>\d+)', [
                'methods' => 'PUT',
                'callback' => [self::class, 'update'],
                'permission_callback' => function() {
                    return current_user_can('edit_posts');
                },
            ]);
            
            // Endpoint para excluir um curso
            register_rest_route($namespace, '/cursos/(?P<id>\d+)', [
                'methods' => 'DELETE',
                'callback' => [self::class, 'destroy'],
                'permission_callback' => function() {
                    return current_user_can('delete_posts');
                },
            ]);
        });
    }
    
    /**
     * Lista todos os cursos
     * 
     * @param \WP_REST_Request $request Requisição
     * @return \WP_REST_Response
     */
    public static function index($request)
    {
        // Obtém os parâmetros da requisição
        $params = $request->get_params();
        
        // Define os argumentos para a consulta
        $args = [
            'post_type' => 'curso',
            'post_status' => 'publish',
            'posts_per_page' => isset($params['per_page']) ? intval($params['per_page']) : 10,
            'paged' => isset($params['page']) ? intval($params['page']) : 1,
        ];
        
        // Adiciona ordenação se especificada
        if (isset($params['orderby'])) {
            $args['orderby'] = $params['orderby'];
        }
        
        if (isset($params['order'])) {
            $args['order'] = $params['order'];
        }
        
        // Adiciona filtro por categoria se especificado
        if (isset($params['categoria'])) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'categoria_curso',
                    'field' => 'slug',
                    'terms' => $params['categoria'],
                ],
            ];
        }
        
        // Executa a consulta
        $query = new \WP_Query($args);
        
        // Prepara os dados para a resposta
        $cursos = [];
        
        foreach ($query->posts as $post) {
            $cursos[] = self::formatCursoData($post);
        }
        
        // Prepara a resposta com metadados de paginação
        $response = [
            'cursos' => $cursos,
            'total' => $query->found_posts,
            'total_pages' => $query->max_num_pages,
            'current_page' => isset($params['page']) ? intval($params['page']) : 1,
        ];
        
        return new \WP_REST_Response($response, 200);
    }
    
    /**
     * Obtém um curso específico
     * 
     * @param \WP_REST_Request $request Requisição
     * @return \WP_REST_Response|\WP_Error
     */
    public static function show($request)
    {
        // Obtém o ID do curso
        $id = $request->get_param('id');
        
        // Obtém o post
        $post = get_post($id);
        
        // Verifica se o post existe e é do tipo curso
        if (!$post || $post->post_type !== 'curso') {
            return new \WP_Error(
                'rest_post_not_found',
                __('Curso não encontrado.', 'wpframework'),
                ['status' => 404]
            );
        }
        
        // Formata os dados do curso
        $curso = self::formatCursoData($post);
        
        return new \WP_REST_Response($curso, 200);
    }
    
    /**
     * Cria um novo curso
     * 
     * @param \WP_REST_Request $request Requisição
     * @return \WP_REST_Response|\WP_Error
     */
    public static function store($request)
    {
        // Obtém os parâmetros da requisição
        $params = $request->get_params();
        
        // Valida os parâmetros obrigatórios
        $required = ['title', 'content', 'instrutor', 'duracao', 'preco', 'nivel'];
        
        foreach ($required as $field) {
            if (!isset($params[$field]) || empty($params[$field])) {
                return new \WP_Error(
                    'rest_missing_param',
                    sprintf(__('Parâmetro obrigatório não informado: %s', 'wpframework'), $field),
                    ['status' => 400]
                );
            }
        }
        
        // Prepara os dados do post
        $post_data = [
            'post_title' => sanitize_text_field($params['title']),
            'post_content' => wp_kses_post($params['content']),
            'post_excerpt' => isset($params['excerpt']) ? sanitize_text_field($params['excerpt']) : '',
            'post_status' => 'publish',
            'post_type' => 'curso',
        ];
        
        // Insere o post
        $post_id = wp_insert_post($post_data, true);
        
        // Verifica se houve erro na inserção
        if (is_wp_error($post_id)) {
            return $post_id;
        }
        
        // Adiciona categoria se especificada
        if (isset($params['categoria']) && !empty($params['categoria'])) {
            wp_set_object_terms($post_id, $params['categoria'], 'categoria_curso');
        }
        
        // Adiciona campos ACF
        if (function_exists('update_field')) {
            update_field('instrutor', sanitize_text_field($params['instrutor']), $post_id);
            update_field('duracao', sanitize_text_field($params['duracao']), $post_id);
            update_field('preco', floatval($params['preco']), $post_id);
            update_field('nivel', sanitize_text_field($params['nivel']), $post_id);
            
            // Campos opcionais
            if (isset($params['data_inicio']) && !empty($params['data_inicio'])) {
                update_field('data_inicio', sanitize_text_field($params['data_inicio']), $post_id);
            }
            
            if (isset($params['vagas']) && !empty($params['vagas'])) {
                update_field('vagas', intval($params['vagas']), $post_id);
            }
            
            // Módulos
            if (isset($params['modulos']) && is_array($params['modulos']) && !empty($params['modulos'])) {
                update_field('modulos', $params['modulos'], $post_id);
            }
        }
        
        // Obtém o post atualizado
        $post = get_post($post_id);
        
        // Formata os dados do curso
        $curso = self::formatCursoData($post);
        
        return new \WP_REST_Response($curso, 201);
    }
    
    /**
     * Atualiza um curso existente
     * 
     * @param \WP_REST_Request $request Requisição
     * @return \WP_REST_Response|\WP_Error
     */
    public static function update($request)
    {
        // Obtém o ID do curso
        $id = $request->get_param('id');
        
        // Obtém o post
        $post = get_post($id);
        
        // Verifica se o post existe e é do tipo curso
        if (!$post || $post->post_type !== 'curso') {
            return new \WP_Error(
                'rest_post_not_found',
                __('Curso não encontrado.', 'wpframework'),
                ['status' => 404]
            );
        }
        
        // Obtém os parâmetros da requisição
        $params = $request->get_params();
        
        // Prepara os dados do post
        $post_data = [
            'ID' => $id,
        ];
        
        // Atualiza os campos do post se fornecidos
        if (isset($params['title'])) {
            $post_data['post_title'] = sanitize_text_field($params['title']);
        }
        
        if (isset($params['content'])) {
            $post_data['post_content'] = wp_kses_post($params['content']);
        }
        
        if (isset($params['excerpt'])) {
            $post_data['post_excerpt'] = sanitize_text_field($params['excerpt']);
        }
        
        // Atualiza o post
        $post_id = wp_update_post($post_data, true);
        
        // Verifica se houve erro na atualização
        if (is_wp_error($post_id)) {
            return $post_id;
        }
        
        // Atualiza categoria se especificada
        if (isset($params['categoria']) && !empty($params['categoria'])) {
            wp_set_object_terms($post_id, $params['categoria'], 'categoria_curso');
        }
        
        // Atualiza campos ACF
        if (function_exists('update_field')) {
            // Atualiza os campos ACF se fornecidos
            if (isset($params['instrutor'])) {
                update_field('instrutor', sanitize_text_field($params['instrutor']), $post_id);
            }
            
            if (isset($params['duracao'])) {
                update_field('duracao', sanitize_text_field($params['duracao']), $post_id);
            }
            
            if (isset($params['preco'])) {
                update_field('preco', floatval($params['preco']), $post_id);
            }
            
            if (isset($params['nivel'])) {
                update_field('nivel', sanitize_text_field($params['nivel']), $post_id);
            }
            
            if (isset($params['data_inicio'])) {
                update_field('data_inicio', sanitize_text_field($params['data_inicio']), $post_id);
            }
            
            if (isset($params['vagas'])) {
                update_field('vagas', intval($params['vagas']), $post_id);
            }
            
            if (isset($params['modulos']) && is_array($params['modulos'])) {
                update_field('modulos', $params['modulos'], $post_id);
            }
        }
        
        // Obtém o post atualizado
        $post = get_post($post_id);
        
        // Formata os dados do curso
        $curso = self::formatCursoData($post);
        
        return new \WP_REST_Response($curso, 200);
    }
    
    /**
     * Exclui um curso
     * 
     * @param \WP_REST_Request $request Requisição
     * @return \WP_REST_Response|\WP_Error
     */
    public static function destroy($request)
    {
        // Obtém o ID do curso
        $id = $request->get_param('id');
        
        // Obtém o post
        $post = get_post($id);
        
        // Verifica se o post existe e é do tipo curso
        if (!$post || $post->post_type !== 'curso') {
            return new \WP_Error(
                'rest_post_not_found',
                __('Curso não encontrado.', 'wpframework'),
                ['status' => 404]
            );
        }
        
        // Exclui o post
        $result = wp_delete_post($id, true);
        
        // Verifica se houve erro na exclusão
        if (!$result) {
            return new \WP_Error(
                'rest_cannot_delete',
                __('Não foi possível excluir o curso.', 'wpframework'),
                ['status' => 500]
            );
        }
        
        return new \WP_REST_Response([
            'message' => __('Curso excluído com sucesso.', 'wpframework'),
            'id' => $id
        ], 200);
    }
    
    /**
     * Formata os dados de um curso para a resposta da API
     * 
     * @param \WP_Post $post Post do curso
     * @return array
     */
    private static function formatCursoData($post)
    {
        // Obtém os campos ACF
        $acf_fields = [];
        
        if (function_exists('get_fields')) {
            $acf_fields = get_fields($post->ID) ?: [];
        }
        
        // Obtém as categorias
        $categorias = [];
        $terms = get_the_terms($post->ID, 'categoria_curso');
        
        if ($terms && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $categorias[] = [
                    'id' => $term->term_id,
                    'name' => $term->name,
                    'slug' => $term->slug,
                ];
            }
        }
        
        // Formata os dados do curso
        $curso = [
            'id' => $post->ID,
            'title' => $post->post_title,
            'content' => $post->post_content,
            'excerpt' => $post->post_excerpt,
            'date' => $post->post_date,
            'modified' => $post->post_modified,
            'slug' => $post->post_name,
            'author' => [
                'id' => (int) $post->post_author,
                'name' => get_the_author_meta('display_name', (int) $post->post_author),
            ],
            'featured_image' => get_the_post_thumbnail_url($post->ID, 'full'),
            'categorias' => $categorias,
            'link' => get_permalink($post->ID),
        ];
        
        // Adiciona os campos ACF
        if (!empty($acf_fields)) {
            $curso = array_merge($curso, $acf_fields);
        }
        
        return $curso;
    }
}
