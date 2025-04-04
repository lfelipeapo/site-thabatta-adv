<?php
/**
 * Template part para exibir páginas
 *
 * @package Thabatta_Advocacia
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('page-content'); ?>>
    <header class="entry-header">
        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
    </header><!-- .entry-header -->

    <?php if (has_post_thumbnail()) : ?>
        <div class="featured-image">
            <?php the_post_thumbnail('thabatta-featured'); ?>
        </div>
    <?php endif; ?>

    <div class="entry-content">
        <?php
        the_content();

        wp_link_pages(
            array(
                'before' => '<div class="page-links">' . esc_html__('Páginas:', 'thabatta-adv'),
                'after'  => '</div>',
            )
        );
        ?>
    </div><!-- .entry-content -->

    <?php if (get_edit_post_link()) : ?>
        <footer class="entry-footer">
            <?php
            edit_post_link(
                sprintf(
                    wp_kses(
                        /* translators: %s: Nome do post atual. Visível apenas para usuários que podem editar. */
                        __('Editar <span class="screen-reader-text">%s</span>', 'thabatta-adv'),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    wp_kses_post(get_the_title())
                ),
                '<span class="edit-link">',
                '</span>'
            );
            ?>
        </footer><!-- .entry-footer -->
    <?php endif; ?>
    
    <?php if (function_exists('thabatta_get_related_posts')) : 
        $related_posts = thabatta_get_related_posts();
        
        if (!empty($related_posts)) : ?>
            <div class="related-posts">
                <h3><?php esc_html_e('Páginas Relacionadas', 'thabatta-adv'); ?></h3>
                <div class="grid grid-3">
                    <?php foreach ($related_posts as $related_post) : ?>
                        <div class="card">
                            <?php if (has_post_thumbnail($related_post->ID)) : ?>
                                <div class="card-image">
                                    <a href="<?php echo esc_url(get_permalink($related_post->ID)); ?>">
                                        <?php echo get_the_post_thumbnail($related_post->ID, 'thabatta-card'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="card-content">
                                <h4><a href="<?php echo esc_url(get_permalink($related_post->ID)); ?>"><?php echo esc_html(get_the_title($related_post->ID)); ?></a></h4>
                                <p><?php echo get_the_excerpt($related_post->ID); ?></p>
                                <a href="<?php echo esc_url(get_permalink($related_post->ID)); ?>" class="read-more">
                                    <?php esc_html_e('Leia mais', 'thabatta-adv'); ?> <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif;
    endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
