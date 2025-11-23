<?php
/**
 * Template genérico para arquivos (categorias, tags, CPTs sem arquivo específico)
 *
 * @package Thabatta_Advocacia
 */

get_header(); ?>

<main id="primary" class="site-main container">
    <header class="page-header">
        <?php if (is_category() || is_tag() || is_tax()) : ?>
            <h1 class="page-title"><?php single_term_title(); ?></h1>
        <?php elseif (is_author()) : ?>
            <h1 class="page-title"><?php the_author(); ?></h1>
        <?php elseif (is_post_type_archive()) : ?>
            <h1 class="page-title"><?php post_type_archive_title(); ?></h1>
        <?php else : ?>
            <h1 class="page-title"><?php _e('Arquivo', 'thabatta-adv'); ?></h1>
        <?php endif; ?>

        <?php
        if (term_description()) {
            echo '<div class="taxonomy-description">' . term_description() . '</div>';
        }
        ?>
    </header>

    <?php if (have_posts()) : ?>
        <div class="thabatta-archive-grid">
            <?php 
            while (have_posts()) : 
                the_post();
                get_template_part('template-parts/content');
            endwhile; 
            ?>
        </div>

        <?php
        // Paginação
        echo '<div class="pagination">';
        the_posts_pagination(array(
            'mid_size'  => 2,
            'prev_text' => '<i class="fas fa-arrow-left"></i> ' . esc_html__('Anterior', 'thabatta-adv'),
            'next_text' => esc_html__('Próximo', 'thabatta-adv') . ' <i class="fas fa-arrow-right"></i>',
            'screen_reader_text' => ' ',
            'aria_label' => __('Navegação de posts', 'thabatta-adv'),
        ));
        echo '</div>';
        ?>

    <?php else : ?>
        <p><?php esc_html_e('Nenhum conteúdo encontrado.', 'thabatta-adv'); ?></p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
