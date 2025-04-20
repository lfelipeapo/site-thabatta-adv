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
     */
    public static function fromWpPost(\WP_Post $post)
    {
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
            'post_title' => $this->title,
            'post_content' => $this->content,
            'post_status' => $this->status ?? 'publish',
            'post_type' => $this->type ?? 'post',
        ];
        
        // Adiciona campos opcionais se estiverem definidos
        if (isset($this->id)) {
            $data['ID'] = $this->id;
        }
        
        if (isset($this->excerpt)) {
            $data['post_excerpt'] = $this->excerpt;
        }
        
        if (isset($this->author)) {
            $data['post_author'] = $this->author;
        }
        
        if (isset($this->slug)) {
            $data['post_name'] = $this->slug;
        }
        
        return $data;
    }
}
