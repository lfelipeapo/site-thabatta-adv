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

                    // Navegação de posts anterior/próximo
                    the_post_navigation(
                        array(
                            'prev_text' => '<span class="nav-subtitle"><i class="fas fa-arrow-left"></i> ' . esc_html__('Anterior:', 'thabatta-adv') . '</span> <span class="nav-title">%title</span>',
                            'next_text' => '<span class="nav-subtitle">' . esc_html__('Próximo:', 'thabatta-adv') . ' <i class="fas fa-arrow-right"></i></span> <span class="nav-title">%title</span>',
                        )
                    );

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
