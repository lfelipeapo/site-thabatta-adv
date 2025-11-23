<?php
/**
 * Template para exibir posts individuais
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Thabatta_Advocacia
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="content-area">
            <div class="main-content">
                <?php
                while (have_posts()) :
                    the_post();

                    get_template_part('template-parts/content', 'single');

                    // Se comentários estão abertos ou temos pelo menos um comentário, carregue o template de comentários.
                    if (comments_open() || get_comments_number()) :
                        comments_template();
                    endif;

                endwhile; // Fim do loop.
                ?>
            </div><!-- .main-content -->

            <?php get_sidebar(); ?>
        </div><!-- .content-area -->
    </div><!-- .container -->
</main><!-- #main -->

<?php
get_footer();
