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
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('thabatta-archive-item'); ?>>
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium'); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <header class="entry-header">
                        <h2 class="entry-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <div class="entry-meta">
                            <?php thabatta_posted_on(); ?>
                        </div>
                    </header>

                    <div class="entry-summary">
                        <?php the_excerpt(); ?>
                    </div>
                </article>
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
        <p><?php esc_html_e('Nenhum conteúdo encontrado.', 'thabatta-adv'); ?></p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
