<?php
/**
 * View para listagem de cursos
 * 
 * @package WPFramework
 */

// Previne acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

// Obtém os cursos
$cursos = isset($cursos) ? $cursos : [];
$pagination = isset($pagination) ? $pagination : ['total' => 1, 'current' => 1];
?>

<div class="cursos-header">
    <div class="container">
        <h1><?php _e('Nossos Cursos', 'wpframework'); ?></h1>
        <p><?php _e('Conheça todos os nossos cursos disponíveis', 'wpframework'); ?></p>
    </div>
</div>

<div class="container">
    <div class="cursos-filters">
        <form action="<?php echo esc_url(home_url('/cursos')); ?>" method="get" class="cursos-search-form">
            <input type="text" name="s" placeholder="<?php esc_attr_e('Buscar cursos...', 'wpframework'); ?>" value="<?php echo get_search_query(); ?>">
            <button type="submit"><?php _e('Buscar', 'wpframework'); ?></button>
        </form>
        
        <?php
        // Obtém todas as categorias de cursos
        $categorias = get_terms([
            'taxonomy' => 'categoria_curso',
            'hide_empty' => true,
        ]);
        
        if (!empty($categorias) && !is_wp_error($categorias)):
        ?>
            <div class="cursos-categorias">
                <h3><?php _e('Categorias', 'wpframework'); ?></h3>
                <ul>
                    <?php foreach ($categorias as $categoria): ?>
                        <li>
                            <a href="<?php echo esc_url(get_term_link($categoria)); ?>">
                                <?php echo esc_html($categoria->name); ?>
                                <span class="count">(<?php echo esc_html((string) $categoria->count); ?>)</span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="cursos-grid">
        <?php if (!empty($cursos)): ?>
            <?php foreach ($cursos as $curso): ?>
                <?php
                // Obtém os campos ACF
                $instrutor = get_field('instrutor', $curso->ID);
                $duracao = get_field('duracao', $curso->ID);
                $preco = get_field('preco', $curso->ID);
                $nivel = get_field('nivel', $curso->ID);
                
                // Formata o preço
                $preco_formatado = 'R$ ' . number_format($preco, 2, ',', '.');
                
                // Obtém a imagem destacada
                $imagem = get_the_post_thumbnail_url($curso->ID, 'medium');
                
                // Obtém o link do curso
                $link = get_permalink($curso->ID);
                
                // Obtém o nível formatado
                $niveis = [
                    'iniciante' => __('Iniciante', 'wpframework'),
                    'intermediario' => __('Intermediário', 'wpframework'),
                    'avancado' => __('Avançado', 'wpframework'),
                ];
                
                $nivel_formatado = isset($niveis[$nivel]) ? $niveis[$nivel] : $nivel;
                
                // Prepara o conteúdo do card
                $card_content = '<div class="curso-meta">';
                
                if (!empty($instrutor)) {
                    $card_content .= '<div class="curso-instrutor"><strong>' . __('Instrutor:', 'wpframework') . '</strong> ' . esc_html($instrutor) . '</div>';
                }
                
                if (!empty($duracao)) {
                    $card_content .= '<div class="curso-duracao"><strong>' . __('Duração:', 'wpframework') . '</strong> ' . esc_html($duracao) . '</div>';
                }
                
                if (!empty($nivel)) {
                    $card_content .= '<div class="curso-nivel"><strong>' . __('Nível:', 'wpframework') . '</strong> ' . esc_html($nivel_formatado) . '</div>';
                }
                
                $card_content .= '</div>';
                
                // Prepara o footer do card
                $card_footer = '<div class="curso-preco">' . esc_html($preco_formatado) . '</div>';
                $card_footer .= '<a href="' . esc_url($link) . '" class="button">' . __('Ver Detalhes', 'wpframework') . '</a>';
                ?>
                
                <?php
                // Renderiza o componente de card
                \WPFramework\Views\View::component('card', [
                    'title' => $curso->post_title,
                    'content' => $card_content,
                    'image' => $imagem,
                    'link' => $link,
                    'footer' => $card_footer,
                    'class' => 'curso-card',
                ]);
                ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="cursos-empty">
                <p><?php _e('Nenhum curso encontrado.', 'wpframework'); ?></p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if ($pagination['total'] > 1): ?>
        <div class="pagination">
            <?php
            echo paginate_links([
                'base' => str_replace('999999999', '%#%', esc_url(get_pagenum_link(999999999))),
                'format' => '?paged=%#%',
                'current' => max(1, $pagination['current']),
                'total' => $pagination['total'],
                'prev_text' => '&laquo; ' . __('Anterior', 'wpframework'),
                'next_text' => __('Próximo', 'wpframework') . ' &raquo;',
            ]);
            ?>
        </div>
    <?php endif; ?>
</div>
