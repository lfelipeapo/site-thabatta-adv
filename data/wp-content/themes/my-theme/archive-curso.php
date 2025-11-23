<?php
/**
 * Template para exibição de arquivos de cursos
 * 
 * Este arquivo é usado para exibir listas de cursos quando archive-curso.php é chamado.
 * 
 * @package WPFramework
 */

// Previne acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Obtém o serviço de cursos
$curso_service = \WPFramework\Services\CursoService::getInstance();
?>

<div class="container">
    <div class="content-area">
        <div class="main-content">
            <header class="page-header">
                <h1 class="page-title">
                    <?php
                    if (is_tax('categoria_curso')) {
                        single_term_title(__('Cursos na categoria: ', 'wpframework'));
                    } else {
                        _e('Todos os Cursos', 'wpframework');
                    }
                    ?>
                </h1>
                
                <?php
                // Descrição da taxonomia, se estiver em uma página de taxonomia
                if (is_tax('categoria_curso')) {
                    the_archive_description('<div class="archive-description">', '</div>');
                }
                ?>
                
                <div class="cursos-filtros">
                    <form action="<?php echo esc_url(get_post_type_archive_link('curso')); ?>" method="get" class="filtro-form">
                        <?php
                        // Taxonomias para filtro
                        $categorias = get_terms([
                            'taxonomy' => 'categoria_curso',
                            'hide_empty' => true,
                        ]);
                        
                        if (!empty($categorias) && !is_wp_error($categorias)) :
                        ?>
                            <div class="filtro-grupo">
                                <label for="categoria"><?php _e('Categoria:', 'wpframework'); ?></label>
                                <select name="categoria" id="categoria">
                                    <option value=""><?php _e('Todas as categorias', 'wpframework'); ?></option>
                                    <?php foreach ($categorias as $categoria) : ?>
                                        <option value="<?php echo esc_attr($categoria->slug); ?>" <?php selected(isset($_GET['categoria']) && $_GET['categoria'] === $categoria->slug); ?>>
                                            <?php echo esc_html($categoria->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                        
                        <div class="filtro-grupo">
                            <label for="nivel"><?php _e('Nível:', 'wpframework'); ?></label>
                            <select name="nivel" id="nivel">
                                <option value=""><?php _e('Todos os níveis', 'wpframework'); ?></option>
                                <option value="iniciante" <?php selected(isset($_GET['nivel']) && $_GET['nivel'] === 'iniciante'); ?>><?php _e('Iniciante', 'wpframework'); ?></option>
                                <option value="intermediario" <?php selected(isset($_GET['nivel']) && $_GET['nivel'] === 'intermediario'); ?>><?php _e('Intermediário', 'wpframework'); ?></option>
                                <option value="avancado" <?php selected(isset($_GET['nivel']) && $_GET['nivel'] === 'avancado'); ?>><?php _e('Avançado', 'wpframework'); ?></option>
                            </select>
                        </div>
                        
                        <div class="filtro-grupo">
                            <label for="ordenar"><?php _e('Ordenar por:', 'wpframework'); ?></label>
                            <select name="ordenar" id="ordenar">
                                <option value="titulo" <?php selected(isset($_GET['ordenar']) && $_GET['ordenar'] === 'titulo'); ?>><?php _e('Título', 'wpframework'); ?></option>
                                <option value="data" <?php selected(!isset($_GET['ordenar']) || $_GET['ordenar'] === 'data'); ?>><?php _e('Data', 'wpframework'); ?></option>
                                <option value="preco_asc" <?php selected(isset($_GET['ordenar']) && $_GET['ordenar'] === 'preco_asc'); ?>><?php _e('Preço (menor para maior)', 'wpframework'); ?></option>
                                <option value="preco_desc" <?php selected(isset($_GET['ordenar']) && $_GET['ordenar'] === 'preco_desc'); ?>><?php _e('Preço (maior para menor)', 'wpframework'); ?></option>
                            </select>
                        </div>
                        
                        <div class="filtro-acoes">
                            <button type="submit" class="button"><?php _e('Filtrar', 'wpframework'); ?></button>
                            <a href="<?php echo esc_url(get_post_type_archive_link('curso')); ?>" class="button button-secondary"><?php _e('Limpar', 'wpframework'); ?></a>
                        </div>
                    </form>
                </div>
            </header>
            
            <?php
            // Prepara os argumentos da consulta com base nos filtros
            $args = [
                'post_type' => 'curso',
                'post_status' => 'publish',
                'posts_per_page' => 12,
                'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
            ];
            
            // Filtro por categoria
            if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
                $args['tax_query'] = [
                    [
                        'taxonomy' => 'categoria_curso',
                        'field' => 'slug',
                        'terms' => sanitize_text_field($_GET['categoria']),
                    ],
                ];
            }
            
            // Filtro por nível (campo ACF)
            if (isset($_GET['nivel']) && !empty($_GET['nivel'])) {
                $args['meta_query'][] = [
                    'key' => 'nivel',
                    'value' => sanitize_text_field($_GET['nivel']),
                    'compare' => '=',
                ];
            }
            
            // Ordenação
            if (isset($_GET['ordenar'])) {
                switch ($_GET['ordenar']) {
                    case 'titulo':
                        $args['orderby'] = 'title';
                        $args['order'] = 'ASC';
                        break;
                    case 'preco_asc':
                        $args['meta_key'] = 'preco';
                        $args['orderby'] = 'meta_value_num';
                        $args['order'] = 'ASC';
                        break;
                    case 'preco_desc':
                        $args['meta_key'] = 'preco';
                        $args['orderby'] = 'meta_value_num';
                        $args['order'] = 'DESC';
                        break;
                    case 'data':
                    default:
                        $args['orderby'] = 'date';
                        $args['order'] = 'DESC';
                        break;
                }
            }
            
            // Executa a consulta
            $query = new WP_Query($args);
            
            if ($query->have_posts()) :
            ?>
                <div class="posts-grid">
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <article id="curso-<?php the_ID(); ?>" <?php post_class('post-card curso-card'); ?>>
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="post-content">
                                <header class="entry-header">
                                    <h2 class="entry-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>
                                    
                                    <?php if (has_term('', 'categoria_curso')) : ?>
                                        <div class="entry-meta">
                                            <span class="curso-categorias">
                                                <?php
                                                $categorias = get_the_terms(get_the_ID(), 'categoria_curso');
                                                $cats = [];
                                                foreach ($categorias as $categoria) {
                                                    $cats[] = '<a href="' . esc_url(get_term_link($categoria)) . '">' . esc_html($categoria->name) . '</a>';
                                                }
                                                echo implode(', ', $cats);
                                                ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </header>
                                
                                <div class="entry-summary">
                                    <?php the_excerpt(); ?>
                                </div>
                                
                                <?php
                                // Obtém os campos ACF
                                $campos = $curso_service->getCursoFields(get_the_ID());
                                ?>
                                
                                <div class="curso-meta">
                                    <?php if (!empty($campos['instrutor'])) : ?>
                                        <div>
                                            <strong><?php _e('Instrutor:', 'wpframework'); ?></strong> <?php echo esc_html($campos['instrutor']); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($campos['duracao'])) : ?>
                                        <div>
                                            <strong><?php _e('Duração:', 'wpframework'); ?></strong> <?php echo esc_html($campos['duracao']); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($campos['nivel'])) : ?>
                                        <div>
                                            <strong><?php _e('Nível:', 'wpframework'); ?></strong>
                                            <?php
                                            $niveis = [
                                                'iniciante' => __('Iniciante', 'wpframework'),
                                                'intermediario' => __('Intermediário', 'wpframework'),
                                                'avancado' => __('Avançado', 'wpframework'),
                                            ];
                                            echo isset($niveis[$campos['nivel']]) ? $niveis[$campos['nivel']] : $campos['nivel'];
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="curso-footer">
                                    <?php if (!empty($campos['preco'])) : ?>
                                        <div class="curso-preco">
                                            <?php echo \WPFramework\Core\Helpers\GlobalHelpers::formatMoney($campos['preco']); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <a href="<?php the_permalink(); ?>" class="button"><?php _e('Ver Curso', 'wpframework'); ?></a>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
                
                <div class="pagination">
                    <?php
                    echo paginate_links([
                        'base' => str_replace('999999999', '%#%', esc_url(get_pagenum_link(999999999))),
                        'format' => '?paged=%#%',
                        'current' => max(1, get_query_var('paged')),
                        'total' => $query->max_num_pages,
                        'prev_text' => __('&laquo; Anterior', 'wpframework'),
                        'next_text' => __('Próximo &raquo;', 'wpframework'),
                    ]);
                    ?>
                </div>
                
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <div class="no-results">
                    <p><?php _e('Nenhum curso encontrado.', 'wpframework'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <?php get_sidebar(); ?>
    </div>
</div>

<style>
    .cursos-filtros {
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
    }
    
    .filtro-form {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-end;
    }
    
    .filtro-grupo {
        flex: 1;
        min-width: 200px;
    }
    
    .filtro-grupo label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    
    .filtro-grupo select {
        width: 100%;
        padding: 0.5rem;
        border-radius: 4px;
        border: 1px solid #ced4da;
    }
    
    .filtro-acoes {
        display: flex;
        gap: 0.5rem;
    }
    
    @media (max-width: 768px) {
        .filtro-form {
            flex-direction: column;
        }
        
        .filtro-grupo {
            width: 100%;
        }
    }
</style>

<?php get_footer(); ?>
