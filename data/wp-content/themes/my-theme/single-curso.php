<?php
/**
 * Template para exibição de um curso individual
 * 
 * Este arquivo é usado para exibir um curso individual quando single-cursos.php é chamado.
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

<div class="curso-header">
    <div class="container">
        <h1 class="curso-title"><?php the_title(); ?></h1>
        
        <?php if (has_term('', 'categoria_curso')) : ?>
            <div class="curso-categorias">
                <?php
                $categorias = get_the_terms(get_the_ID(), 'categoria_curso');
                foreach ($categorias as $categoria) {
                    echo '<a href="' . esc_url(get_term_link($categoria)) . '" class="categoria-badge">' . esc_html($categoria->name) . '</a>';
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <div class="curso-content">
        <div class="curso-main">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <article id="curso-<?php the_ID(); ?>" <?php post_class('single-curso'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="entry-thumbnail">
                                <?php the_post_thumbnail('large'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>
                        
                        <?php
                        // Obtém os campos ACF
                        $campos = $curso_service->getCursoFields(get_the_ID());
                        
                        // Verifica se existem módulos
                        if (!empty($campos['modulos'])) :
                        ?>
                            <div class="curso-modulos">
                                <h2><?php _e('Módulos do Curso', 'wpframework'); ?></h2>
                                
                                <div class="modulos-accordion">
                                    <?php foreach ($campos['modulos'] as $index => $modulo) : ?>
                                        <div class="modulo-item">
                                            <div class="modulo-header" data-modulo="<?php echo esc_attr($index); ?>">
                                                <span><?php echo esc_html($modulo['titulo']); ?></span>
                                                <span class="modulo-toggle">+</span>
                                            </div>
                                            <div class="modulo-content" id="modulo-<?php echo esc_attr($index); ?>" style="display: none;">
                                                <?php if (!empty($modulo['duracao'])) : ?>
                                                    <p><strong><?php _e('Duração:', 'wpframework'); ?></strong> <?php echo esc_html($modulo['duracao']); ?></p>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($modulo['descricao'])) : ?>
                                                    <div class="modulo-descricao">
                                                        <?php echo wp_kses_post($modulo['descricao']); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endwhile; ?>
            <?php else : ?>
                <p><?php _e('Nenhum curso encontrado.', 'wpframework'); ?></p>
            <?php endif; ?>
            
            <?php
            // Cursos relacionados
            $cursos_relacionados = $curso_service->getCursosRelacionados(get_the_ID(), 3);
            
            if (!empty($cursos_relacionados)) :
            ?>
                <div class="curso-relacionados-section">
                    <h2><?php _e('Cursos Relacionados', 'wpframework'); ?></h2>
                    
                    <div class="posts-grid">
                        <?php foreach ($cursos_relacionados as $curso_relacionado) : ?>
                            <article class="post-card curso-card">
                                <?php if (has_post_thumbnail($curso_relacionado->ID)) : ?>
                                    <div class="post-thumbnail">
                                        <a href="<?php echo esc_url(get_permalink($curso_relacionado->ID)); ?>">
                                            <?php echo get_the_post_thumbnail($curso_relacionado->ID, 'medium'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="post-content">
                                    <header class="entry-header">
                                        <h3 class="entry-title">
                                            <a href="<?php echo esc_url(get_permalink($curso_relacionado->ID)); ?>"><?php echo esc_html($curso_relacionado->post_title); ?></a>
                                        </h3>
                                    </header>
                                    
                                    <div class="entry-summary">
                                        <?php echo wp_trim_words($curso_relacionado->post_excerpt ?: $curso_relacionado->post_content, 20); ?>
                                    </div>
                                    
                                    <?php
                                    // Obtém os campos ACF do curso relacionado
                                    $campos_relacionado = $curso_service->getCursoFields($curso_relacionado->ID);
                                    ?>
                                    
                                    <div class="curso-footer">
                                        <?php if (!empty($campos_relacionado['preco'])) : ?>
                                            <div class="curso-preco">
                                                <?php echo \WPFramework\Core\Helpers\GlobalHelpers::formatMoney($campos_relacionado['preco']); ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <a href="<?php echo esc_url(get_permalink($curso_relacionado->ID)); ?>" class="button"><?php _e('Ver Curso', 'wpframework'); ?></a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="curso-sidebar">
            <div class="curso-info-card">
                <?php
                // Obtém os campos ACF
                $campos = $curso_service->getCursoFields(get_the_ID());
                
                // Preço
                if (!empty($campos['preco'])) :
                ?>
                    <div class="curso-preco">
                        <span class="preco-valor"><?php echo \WPFramework\Core\Helpers\GlobalHelpers::formatMoney($campos['preco']); ?></span>
                    </div>
                <?php endif; ?>
                
                <div class="curso-meta">
                    <?php if (!empty($campos['instrutor'])) : ?>
                        <div class="meta-item">
                            <span class="meta-label"><?php _e('Instrutor:', 'wpframework'); ?></span>
                            <span class="meta-value"><?php echo esc_html($campos['instrutor']); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($campos['duracao'])) : ?>
                        <div class="meta-item">
                            <span class="meta-label"><?php _e('Duração:', 'wpframework'); ?></span>
                            <span class="meta-value"><?php echo esc_html($campos['duracao']); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($campos['nivel'])) : ?>
                        <div class="meta-item">
                            <span class="meta-label"><?php _e('Nível:', 'wpframework'); ?></span>
                            <span class="meta-value">
                                <?php
                                $niveis = [
                                    'iniciante' => __('Iniciante', 'wpframework'),
                                    'intermediario' => __('Intermediário', 'wpframework'),
                                    'avancado' => __('Avançado', 'wpframework'),
                                ];
                                echo isset($niveis[$campos['nivel']]) ? $niveis[$campos['nivel']] : $campos['nivel'];
                                ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($campos['data_inicio'])) : ?>
                        <div class="meta-item">
                            <span class="meta-label"><?php _e('Data de Início:', 'wpframework'); ?></span>
                            <span class="meta-value"><?php echo \WPFramework\Core\Helpers\GlobalHelpers::formatDate($campos['data_inicio']); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($campos['vagas']) && $campos['vagas'] !== '') : ?>
                        <div class="meta-item">
                            <span class="meta-label"><?php _e('Vagas Disponíveis:', 'wpframework'); ?></span>
                            <span class="meta-value"><?php echo esc_html($campos['vagas']); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="curso-acoes">
                    <a href="#" class="button button-large"><?php _e('Inscrever-se', 'wpframework'); ?></a>
                    <a href="#" class="button button-secondary"><?php _e('Adicionar à Lista de Desejos', 'wpframework'); ?></a>
                </div>
            </div>
            
            <?php
            // Cursos relacionados para a sidebar
            $cursos_relacionados_sidebar = $curso_service->getCursosRelacionados(get_the_ID(), 3);
            
            if (!empty($cursos_relacionados_sidebar)) :
            ?>
                <div class="curso-relacionados">
                    <h3><?php _e('Cursos Relacionados', 'wpframework'); ?></h3>
                    
                    <?php foreach ($cursos_relacionados_sidebar as $curso_relacionado) : ?>
                        <div class="curso-relacionado">
                            <div class="curso-relacionado-imagem">
                                <?php if (has_post_thumbnail($curso_relacionado->ID)) : ?>
                                    <a href="<?php echo esc_url(get_permalink($curso_relacionado->ID)); ?>">
                                        <?php echo get_the_post_thumbnail($curso_relacionado->ID, 'thumbnail'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            
                            <div class="curso-relacionado-info">
                                <h4>
                                    <a href="<?php echo esc_url(get_permalink($curso_relacionado->ID)); ?>"><?php echo esc_html($curso_relacionado->post_title); ?></a>
                                </h4>
                                
                                <?php
                                // Obtém os campos ACF do curso relacionado
                                $campos_relacionado = $curso_service->getCursoFields($curso_relacionado->ID);
                                
                                if (!empty($campos_relacionado['preco'])) :
                                ?>
                                    <div class="curso-relacionado-preco">
                                        <?php echo \WPFramework\Core\Helpers\GlobalHelpers::formatMoney($campos_relacionado['preco']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa o accordion dos módulos
        const moduloHeaders = document.querySelectorAll('.modulo-header');
        
        moduloHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const moduloId = this.getAttribute('data-modulo');
                const content = document.getElementById('modulo-' + moduloId);
                const toggle = this.querySelector('.modulo-toggle');
                
                if (content.style.display === 'none') {
                    content.style.display = 'block';
                    toggle.textContent = '-';
                } else {
                    content.style.display = 'none';
                    toggle.textContent = '+';
                }
            });
        });
    });
</script>

<?php get_footer(); ?>
