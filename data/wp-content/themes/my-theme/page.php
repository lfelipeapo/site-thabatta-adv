<?php
/**
 * Template para exibição de páginas
 * 
 * Este arquivo é usado para exibir páginas quando page.php é chamado.
 * 
 * @package WPFramework
 */

// Previne acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="container">
    <div class="content-area">
        <div class="main-content">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <article id="page-<?php the_ID(); ?>" <?php post_class('single-page'); ?>>
                        <header class="entry-header">
                            <h1 class="entry-title"><?php the_title(); ?></h1>
                        </header>
                        
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="entry-thumbnail">
                                <?php the_post_thumbnail('large'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="entry-content">
                            <?php the_content(); ?>
                            
                            <?php
                            wp_link_pages([
                                'before' => '<div class="page-links">' . __('Páginas:', 'wpframework'),
                                'after'  => '</div>',
                            ]);
                            ?>
                        </div>
                        
                        <?php if (get_edit_post_link()) : ?>
                            <footer class="entry-footer">
                                <?php
                                edit_post_link(
                                    sprintf(
                                        /* translators: %s: Name of current post */
                                        __('Editar %s', 'wpframework'),
                                        the_title('<span class="screen-reader-text">"', '"</span>', false)
                                    ),
                                    '<span class="edit-link">',
                                    '</span>'
                                );
                                ?>
                            </footer>
                        <?php endif; ?>
                    </article>
                    
                    <?php
                    // Se os comentários estão abertos ou temos pelo menos um comentário, carrega o template de comentários
                    if (comments_open() || get_comments_number()) :
                        comments_template();
                    endif;
                    ?>
                <?php endwhile; ?>
            <?php else : ?>
                <p><?php _e('Nenhuma página encontrada.', 'wpframework'); ?></p>
            <?php endif; ?>
        </div>
        
        <?php get_sidebar(); ?>
    </div>
</div>

<?php get_footer(); ?>
