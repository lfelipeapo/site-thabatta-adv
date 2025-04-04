<?php
/**
 * O arquivo de template principal
 *
 * Este é o template mais genérico do WordPress.
 * Ele é usado para exibir uma página quando nada mais específico corresponde a uma consulta.
 * Por exemplo, ele coloca juntos o cabeçalho, o conteúdo e o rodapé da página inicial quando nenhum home.php existe.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Thabatta_Advocacia
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <?php if (is_home() && !is_front_page()) : ?>
            <header class="page-header">
                <h1 class="page-title"><?php single_post_title(); ?></h1>
            </header>
        <?php endif; ?>

        <div class="content-area">
            <div class="main-content">
                <?php
                if (have_posts()) :

                    /* Iniciar o Loop */
                    while (have_posts()) :
                        the_post();

                        /*
                         * Incluir o template de conteúdo parcial para o formato de post.
                         * Se você quiser sobrescrever isso em um tema filho, então inclua um arquivo
                         * chamado content-___.php (onde ___ é o formato de Post) e isso será usado.
                         */
                        get_template_part('template-parts/content', get_post_type());

                    endwhile;

                    the_posts_navigation(array(
                        'prev_text' => '<i class="fas fa-arrow-left"></i> ' . esc_html__('Posts Anteriores', 'thabatta-adv'),
                        'next_text' => esc_html__('Posts Recentes', 'thabatta-adv') . ' <i class="fas fa-arrow-right"></i>',
                    ));

                else :

                    get_template_part('template-parts/content', 'none');

                endif;
                ?>
            </div><!-- .main-content -->

            <?php get_sidebar(); ?>
        </div><!-- .content-area -->
    </div><!-- .container -->
</main><!-- #main -->

<?php
get_footer();
