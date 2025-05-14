<?php
/**
 * View para exibição de um curso específico
 * 
 * @package WPFramework
 */

// Previne acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

// Obtém o curso e seus campos
$curso = isset($curso) ? $curso : null;
$campos = isset($campos) ? $campos : [];

// Verifica se o curso existe
if (!$curso) {
    return;
}

// Extrai os campos
$instrutor = isset($campos['instrutor']) ? $campos['instrutor'] : '';
$duracao = isset($campos['duracao']) ? $campos['duracao'] : '';
$preco = isset($campos['preco']) ? $campos['preco'] : 0;
$nivel = isset($campos['nivel']) ? $campos['nivel'] : '';
$data_inicio = isset($campos['data_inicio']) ? $campos['data_inicio'] : '';
$vagas = isset($campos['vagas']) ? $campos['vagas'] : '';
$modulos = isset($campos['modulos']) ? $campos['modulos'] : [];

// Formata o preço
$preco_formatado = 'R$ ' . number_format($preco, 2, ',', '.');

// Obtém o nível formatado
$niveis = [
    'iniciante' => __('Iniciante', 'wpframework'),
    'intermediario' => __('Intermediário', 'wpframework'),
    'avancado' => __('Avançado', 'wpframework'),
];

$nivel_formatado = isset($niveis[$nivel]) ? $niveis[$nivel] : $nivel;

// Obtém as categorias
$categorias = get_the_terms($curso->ID, 'categoria_curso');
?>

<div class="curso-header">
    <div class="container">
        <div class="curso-header-content">
            <h1 class="curso-title"><?php echo esc_html($curso->post_title); ?></h1>
            
            <?php if (!empty($categorias) && !is_wp_error($categorias)): ?>
                <div class="curso-categorias">
                    <?php foreach ($categorias as $categoria): ?>
                        <a href="<?php echo esc_url(get_term_link($categoria)); ?>" class="categoria-badge">
                            <?php echo esc_html($categoria->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container">
    <div class="curso-content">
        <div class="curso-main">
            <?php if (has_post_thumbnail($curso->ID)): ?>
                <div class="curso-imagem">
                    <?php echo get_the_post_thumbnail($curso->ID, 'large'); ?>
                </div>
            <?php endif; ?>
            
            <div class="curso-descricao">
                <h2><?php _e('Descrição do Curso', 'wpframework'); ?></h2>
                <?php echo apply_filters('the_content', $curso->post_content); ?>
            </div>
            
            <?php if (!empty($modulos)): ?>
                <div class="curso-modulos">
                    <h2><?php _e('Módulos do Curso', 'wpframework'); ?></h2>
                    
                    <div class="modulos-accordion">
                        <?php foreach ($modulos as $index => $modulo): ?>
                            <details class="modulo-item" <?php echo $index === 0 ? 'open' : ''; ?>>
                                <summary class="modulo-header">
                                    <span class="modulo-titulo"><?php echo esc_html($modulo['titulo']); ?></span>
                                    <?php if (!empty($modulo['duracao'])): ?>
                                        <span class="modulo-duracao"><?php echo esc_html($modulo['duracao']); ?></span>
                                    <?php endif; ?>
                                </summary>
                                
                                <div class="modulo-content">
                                    <?php if (!empty($modulo['descricao'])): ?>
                                        <p><?php echo nl2br(esc_html($modulo['descricao'])); ?></p>
                                    <?php endif; ?>
                                </div>
                            </details>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="curso-sidebar">
            <div class="curso-info-card">
                <div class="curso-preco">
                    <span class="preco-valor"><?php echo esc_html($preco_formatado); ?></span>
                </div>
                
                <div class="curso-meta">
                    <?php if (!empty($instrutor)): ?>
                        <div class="meta-item">
                            <span class="meta-label"><?php _e('Instrutor:', 'wpframework'); ?></span>
                            <span class="meta-value"><?php echo esc_html($instrutor); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($duracao)): ?>
                        <div class="meta-item">
                            <span class="meta-label"><?php _e('Duração:', 'wpframework'); ?></span>
                            <span class="meta-value"><?php echo esc_html($duracao); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($nivel)): ?>
                        <div class="meta-item">
                            <span class="meta-label"><?php _e('Nível:', 'wpframework'); ?></span>
                            <span class="meta-value"><?php echo esc_html($nivel_formatado); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($data_inicio)): ?>
                        <div class="meta-item">
                            <span class="meta-label"><?php _e('Data de Início:', 'wpframework'); ?></span>
                            <span class="meta-value"><?php echo esc_html($data_inicio); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($vagas)): ?>
                        <div class="meta-item">
                            <span class="meta-label"><?php _e('Vagas Disponíveis:', 'wpframework'); ?></span>
                            <span class="meta-value"><?php echo esc_html($vagas); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="curso-acoes">
                    <?php
                    // Renderiza o componente de modal para inscrição
                    \WPFramework\Views\View::component('modal', [
                        'id' => 'modal-inscricao-' . $curso->ID,
                        'title' => __('Inscrição no Curso', 'wpframework'),
                        'content' => '
                            <form class="inscricao-form" id="form-inscricao-' . $curso->ID . '">
                                <div class="form-group">
                                    <label for="nome">' . __('Nome Completo', 'wpframework') . '</label>
                                    <input type="text" id="nome" name="nome" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">' . __('E-mail', 'wpframework') . '</label>
                                    <input type="email" id="email" name="email" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="telefone">' . __('Telefone', 'wpframework') . '</label>
                                    <input type="tel" id="telefone" name="telefone" required>
                                </div>
                                
                                <input type="hidden" name="curso_id" value="' . $curso->ID . '">
                                <input type="hidden" name="curso_titulo" value="' . esc_attr($curso->post_title) . '">
                                
                                <div class="form-actions">
                                    <button type="submit" class="button button-primary">' . __('Confirmar Inscrição', 'wpframework') . '</button>
                                </div>
                            </form>
                        ',
                        'trigger_text' => __('Inscrever-se', 'wpframework'),
                        'trigger_class' => 'button button-primary button-large',
                        'size' => 'medium'
                    ]);
                    ?>
                    
                    <?php
                    // Renderiza o componente de popover para compartilhamento
                    \WPFramework\Views\View::component('popover', [
                        'id' => 'popover-compartilhar-' . $curso->ID,
                        'title' => __('Compartilhar', 'wpframework'),
                        'content' => '
                            <div class="compartilhar-links">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=' . urlencode(get_permalink($curso->ID)) . '" target="_blank" class="compartilhar-link facebook">
                                    Facebook
                                </a>
                                
                                <a href="https://twitter.com/intent/tweet?url=' . urlencode(get_permalink($curso->ID)) . '&text=' . urlencode($curso->post_title) . '" target="_blank" class="compartilhar-link twitter">
                                    Twitter
                                </a>
                                
                                <a href="https://wa.me/?text=' . urlencode($curso->post_title . ' - ' . get_permalink($curso->ID)) . '" target="_blank" class="compartilhar-link whatsapp">
                                    WhatsApp
                                </a>
                                
                                <a href="mailto:?subject=' . urlencode($curso->post_title) . '&body=' . urlencode(get_permalink($curso->ID)) . '" class="compartilhar-link email">
                                    E-mail
                                </a>
                            </div>
                        ',
                        'trigger_text' => __('Compartilhar', 'wpframework'),
                        'trigger_class' => 'button button-secondary',
                        'position' => 'top'
                    ]);
                    ?>
                </div>
            </div>
            
            <div class="curso-relacionados">
                <h3><?php _e('Cursos Relacionados', 'wpframework'); ?></h3>
                
                <?php
                // Obtém cursos relacionados
                $args = [
                    'post_type' => 'curso',
                    'post_status' => 'publish',
                    'posts_per_page' => 3,
                    'post__not_in' => [$curso->ID],
                ];
                
                // Se tiver categorias, busca cursos da mesma categoria
                if (!empty($categorias) && !is_wp_error($categorias)) {
                    $categoria_ids = wp_list_pluck($categorias, 'term_id');
                    
                    $args['tax_query'] = [
                        [
                            'taxonomy' => 'categoria_curso',
                            'field' => 'term_id',
                            'terms' => $categoria_ids
                        ]
                    ];
                }
                
                $cursos_relacionados = new \WP_Query($args);
                
                if ($cursos_relacionados->have_posts()):
                    while ($cursos_relacionados->have_posts()):
                        $cursos_relacionados->the_post();
                        
                        // Obtém o preço
                        $preco_relacionado = get_field('preco', get_the_ID());
                        $preco_formatado_relacionado = 'R$ ' . number_format($preco_relacionado, 2, ',', '.');
                ?>
                    <div class="curso-relacionado">
                        <?php if (has_post_thumbnail()): ?>
                            <a href="<?php the_permalink(); ?>" class="curso-relacionado-imagem">
                                <?php the_post_thumbnail('thumbnail'); ?>
                            </a>
                        <?php endif; ?>
                        
                        <div class="curso-relacionado-info">
                            <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                            <div class="curso-relacionado-preco"><?php echo esc_html($preco_formatado_relacionado); ?></div>
                        </div>
                    </div>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else:
                ?>
                    <p><?php _e('Nenhum curso relacionado encontrado.', 'wpframework'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Script para o formulário de inscrição
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-inscricao-<?php echo $curso->ID; ?>');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Aqui você pode implementar o envio do formulário via AJAX
            // ou redirecionar para uma página de pagamento
            
            alert('Inscrição realizada com sucesso! Em breve entraremos em contato.');
            
            // Fecha o modal
            const modal = document.getElementById('modal-inscricao-<?php echo $curso->ID; ?>');
            if (modal) {
                const closeButton = modal.querySelector('.close-modal');
                if (closeButton) {
                    closeButton.click();
                }
            }
        });
    }
});
</script>
