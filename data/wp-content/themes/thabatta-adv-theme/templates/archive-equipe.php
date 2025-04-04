<?php
/**
 * Template para o arquivo de listagem do CPT Equipe
 *
 * @package Thabatta_Advocacia
 */

get_header(); ?>

<main id="primary" class="site-main container">
    <header class="page-header">
        <h1 class="page-title"><?php post_type_archive_title(); ?></h1>
    </header>

    <?php if (have_posts()) : ?>
        <div class="thabatta-team-grid">
            <?php while (have_posts()) : the_post(); ?>
                <?php
                if (function_exists('thabatta_featured_team_member')) {
                    thabatta_featured_team_member(get_the_ID());
                } else {
                    get_template_part('template-parts/content', 'equipe'); // fallback se quiser criar um card separado
                }
                ?>
            <?php endwhile; ?>
        </div>

        <?php
        // Paginação
        the_posts_pagination(array(
            'mid_size'  => 2,
            'prev_text' => __('« Anterior', 'thabatta-adv'),
            'next_text' => __('Próximo »', 'thabatta-adv'),
        ));
?>

    <?php else : ?>
        <p><?php esc_html_e('Nenhum membro da equipe encontrado.', 'thabatta-adv'); ?></p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
