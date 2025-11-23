<?php
/**
 * Classe PostModel
 * 
 * Model para interagir com posts do WordPress
 * 
 * @package WPFramework\Models
 */

namespace WPFramework\Models;

class PostModel
{
    /**
     * Obtém um post pelo ID
     * 
     * @param int $id ID do post
     * @return \WP_Post|null
     */
    public function find($id)
    {
        return get_post($id);
    }
    
    /**
     * Obtém todos os posts
     * 
     * @param array $args Argumentos para WP_Query
     * @return array
     */
    public function all($args = [])
    {
        $default_args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ];
        
        $args = array_merge($default_args, $args);
        $query = new \WP_Query($args);
        
        return $query->posts;
    }
    
    /**
     * Cria um novo post
     * 
     * @param array $data Dados do post
     * @return int|\WP_Error ID do post criado ou objeto de erro
     */
    public function create($data)
    {
        // Valores padrão
        $defaults = [
            'post_type' => 'post',
            'post_status' => 'publish',
        ];
        
        $data = array_merge($defaults, $data);
        
        return wp_insert_post($data, true);
    }
    
    /**
     * Atualiza um post
     * 
     * @param int $id ID do post
     * @param array $data Dados do post
     * @return int|\WP_Error ID do post atualizado ou objeto de erro
     */
    public function update($id, $data)
    {
        $data['ID'] = $id;
        
        return wp_update_post($data, true);
    }
    
    /**
     * Exclui um post
     * 
     * @param int $id ID do post
     * @param bool $force Forçar exclusão (não enviar para lixeira)
     * @return bool
     */
    public function delete($id, $force = false)
    {
        return wp_delete_post($id, $force);
    }
    
    /**
     * Obtém posts com paginação
     * 
     * @param int $page Número da página
     * @param int $perPage Posts por página
     * @param array $args Argumentos adicionais para WP_Query
     * @return array
     */
    public function paginate($page = 1, $perPage = 10, $args = [])
    {
        $default_args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $perPage,
            'paged' => $page,
        ];
        
        $args = array_merge($default_args, $args);
        $query = new \WP_Query($args);
        
        return [
            'items' => $query->posts,
            'total' => $query->found_posts,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($query->found_posts / $perPage),
            'has_more' => $query->max_num_pages > $page,
        ];
    }
    
    /**
     * Obtém posts por taxonomia
     * 
     * @param string $taxonomy Nome da taxonomia
     * @param int|string $term ID ou slug do termo
     * @param array $args Argumentos adicionais para WP_Query
     * @return array
     */
    public function getByTaxonomy($taxonomy, $term, $args = [])
    {
        $default_args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'tax_query' => [
                [
                    'taxonomy' => $taxonomy,
                    'field' => is_numeric($term) ? 'term_id' : 'slug',
                    'terms' => $term,
                ],
            ],
        ];
        
        $args = array_merge($default_args, $args);
        $query = new \WP_Query($args);
        
        return $query->posts;
    }
    
    /**
     * Obtém posts por meta
     * 
     * @param string $key Chave do meta
     * @param mixed $value Valor do meta
     * @param array $args Argumentos adicionais para WP_Query
     * @return array
     */
    public function getByMeta($key, $value, $args = [])
    {
        $default_args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => $key,
                    'value' => $value,
                ],
            ],
        ];
        
        $args = array_merge($default_args, $args);
        $query = new \WP_Query($args);
        
        return $query->posts;
    }
    
    /**
     * Obtém posts por autor
     * 
     * @param int $authorId ID do autor
     * @param array $args Argumentos adicionais para WP_Query
     * @return array
     */
    public function getByAuthor($authorId, $args = [])
    {
        $default_args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'author' => $authorId,
        ];
        
        $args = array_merge($default_args, $args);
        $query = new \WP_Query($args);
        
        return $query->posts;
    }
    
    /**
     * Pesquisa posts
     * 
     * @param string $search Termo de pesquisa
     * @param array $args Argumentos adicionais para WP_Query
     * @return array
     */
    public function search($search, $args = [])
    {
        $default_args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            's' => $search,
        ];
        
        $args = array_merge($default_args, $args);
        $query = new \WP_Query($args);
        
        return $query->posts;
    }
    
    /**
     * Obtém campos ACF de um post
     * 
     * @param int $postId ID do post
     * @param bool $formatted Retornar valores formatados
     * @return array
     */
    public function getAcfFields($postId, $formatted = true)
    {
        if (!function_exists('get_fields')) {
            return [];
        }
        
        return get_fields($postId, $formatted);
    }
    
    /**
     * Obtém um campo ACF específico de um post
     * 
     * @param string $fieldName Nome do campo
     * @param int $postId ID do post
     * @param bool $formatted Retornar valor formatado
     * @return mixed
     */
    public function getAcfField($fieldName, $postId, $formatted = true)
    {
        if (!function_exists('get_field')) {
            return null;
        }
        
        return get_field($fieldName, $postId, $formatted);
    }
    
    /**
     * Atualiza um campo ACF de um post
     * 
     * @param string $fieldName Nome do campo
     * @param mixed $value Valor do campo
     * @param int $postId ID do post
     * @return bool
     */
    public function updateAcfField($fieldName, $value, $postId)
    {
        if (!function_exists('update_field')) {
            return false;
        }
        
        return update_field($fieldName, $value, $postId);
    }
}
