<?php
/**
 * Template para exibir páginas
 *
 * Este é o template que exibe todas as páginas por padrão.
 * Por favor, note que esta é a hierarquia de template do WordPress.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Thabatta_Advocacia
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <?php
        while (have_posts()) :
            the_post();

            get_template_part('template-parts/content', 'page');

            // Se comentários estão abertos ou temos pelo menos um comentário, carregue o template de comentários.
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;

        endwhile; // Fim do loop.
        ?>
    </div><!-- .container -->
</main><!-- #main -->

<?php
get_footer();
