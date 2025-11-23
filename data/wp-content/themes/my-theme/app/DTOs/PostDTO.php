<?php
/**
 * Classe PostDTO
 * 
 * DTO para representar dados de posts
 * 
 * @package WPFramework\DTOs
 */

namespace WPFramework\DTOs;

class PostDTO extends BaseDTO
{
    /**
     * Regras de validação para as propriedades
     * 
     * @var array
     */
    protected $rules = [
        'title' => 'required|min:3',
        'content' => 'required',
        'excerpt' => 'max:300',
        'status' => 'required',
        'author' => 'numeric',
    ];
    
    /**
     * Cria um DTO a partir de um objeto WP_Post
     * 
     * @param \WP_Post $post Objeto post do WordPress
     * @return PostDTO
     * @phpstan-return static
     */
    public static function fromWpPost(\WP_Post $post)
    {
        /** @phpstan-ignore-next-line */
        return new static([
            'id' => $post->ID,
            'title' => $post->post_title,
            'content' => $post->post_content,
            'excerpt' => $post->post_excerpt,
            'status' => $post->post_status,
            'author' => $post->post_author,
            'date' => $post->post_date,
            'modified' => $post->post_modified,
            'slug' => $post->post_name,
            'type' => $post->post_type,
            'permalink' => get_permalink($post->ID),
            'thumbnail' => get_the_post_thumbnail_url($post->ID, 'full'),
            'categories' => get_the_category($post->ID),
            'tags' => get_the_tags($post->ID),
        ]);
    }
    
    /**
     * Converte o DTO para um array compatível com wp_insert_post
     * 
     * @return array
     */
    public function toWpPostArray()
    {
        $data = [
            'post_title' => $this->get('title', ''),
            'post_content' => $this->get('content', ''),
            'post_status' => $this->get('status', 'publish'),
            'post_type' => $this->get('type', 'post'),
        ];
        
        // Adiciona campos opcionais se estiverem definidos
        if ($this->has('id')) {
            $data['ID'] = $this->get('id');
        }
        
        if ($this->has('excerpt')) {
            $data['post_excerpt'] = $this->get('excerpt');
        }
        
        if ($this->has('author')) {
            $data['post_author'] = $this->get('author');
        }
        
        if ($this->has('slug')) {
            $data['post_name'] = $this->get('slug');
        }
        
        return $data;
    }
}
