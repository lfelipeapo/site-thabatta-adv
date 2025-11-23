<?php
/**
 * Controller para o Custom Post Type Cursos
 * 
 * @package WPFramework\Controllers
 */

namespace WPFramework\Controllers;

class CursosController extends BaseController
{
    /**
     * Construtor
     */
    public function __construct()
    {
        // BaseController não tem construtor, então não precisa chamar parent
    }

    /**
     * Lista todos os cursos
     * 
     * @return void
     */
    public function index()
    {
        // Obtém os cursos
        $args = [
            'post_type' => 'curso',
            'post_status' => 'publish',
            'posts_per_page' => 10,
            'paged' => get_query_var('paged') ? get_query_var('paged') : 1
        ];
        
        $query = new \WP_Query($args);
        $cursos = $query->posts;
        
        // Renderiza a view
        $this->view('cursos.index', [
            'title' => 'Cursos',
            'cursos' => $cursos,
            'pagination' => [
                'total' => $query->max_num_pages,
                'current' => get_query_var('paged') ? get_query_var('paged') : 1
            ]
        ]);
    }

    /**
     * Exibe um curso específico
     * 
     * @param int $id ID do curso
     * @return void
     */
    public function show($id)
    {
        // Obtém o curso
        $curso = get_post($id);
        
        // Verifica se o curso existe
        if (!$curso || $curso->post_type !== 'curso') {
            $this->notFound();
            return;
        }
        
        // Obtém os campos ACF
        $campos = [];
        
        if (function_exists('get_fields')) {
            $campos = get_fields($id);
        }
        
        // Renderiza a view
        $this->view('cursos.show', [
            'title' => $curso->post_title,
            'curso' => $curso,
            'campos' => $campos
        ]);
    }

    /**
     * Exibe cursos por categoria
     * 
     * @param string $categoria Slug da categoria
     * @return void
     */
    public function categoria($categoria)
    {
        // Obtém a categoria
        $term = get_term_by('slug', $categoria, 'categoria_curso');
        
        // Verifica se a categoria existe
        if (!$term) {
            $this->notFound();
            return;
        }
        
        // Obtém os cursos da categoria
        $args = [
            'post_type' => 'curso',
            'post_status' => 'publish',
            'posts_per_page' => 10,
            'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
            'tax_query' => [
                [
                    'taxonomy' => 'categoria_curso',
                    'field' => 'slug',
                    'terms' => $categoria
                ]
            ]
        ];
        
        $query = new \WP_Query($args);
        $cursos = $query->posts;
        
        // Renderiza a view
        $this->view('cursos.categoria', [
            'title' => 'Cursos - ' . $term->name,
            'categoria' => $term,
            'cursos' => $cursos,
            'pagination' => [
                'total' => $query->max_num_pages,
                'current' => get_query_var('paged') ? get_query_var('paged') : 1
            ]
        ]);
    }

    /**
     * Página 404 para cursos não encontrados
     * 
     * @return void
     */
    private function notFound()
    {
        // Define o status HTTP 404
        status_header(404);
        
        // Renderiza a view
        $this->view('errors.404', [
            'title' => 'Curso não encontrado'
        ]);
    }
}
