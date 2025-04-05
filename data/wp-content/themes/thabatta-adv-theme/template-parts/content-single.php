<?php
/**
 * Template part para exibir posts individuais
 *
 * @package Thabatta_Advocacia
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>
    <header class="entry-header">
        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>

        <?php if ('post' === get_post_type()) : ?>
            <div class="entry-meta">
                <span class="posted-on">
                    <i class="far fa-calendar-alt"></i>
                    <?php echo get_the_date(); ?>
                </span>
                <span class="byline">
                    <i class="far fa-user"></i>
                    <?php the_author(); ?>
                </span>
                <?php if (has_category()) : ?>
                    <span class="cat-links">
                        <i class="far fa-folder-open"></i>
                        <?php the_category(', '); ?>
                    </span>
                <?php endif; ?>
            </div><!-- .entry-meta -->
        <?php endif; ?>
    </header><!-- .entry-header -->

    <?php if (has_post_thumbnail()) : ?>
        <div class="featured-image">
            <?php the_post_thumbnail('thabatta-featured'); ?>
        </div>
    <?php endif; ?>

    <div class="entry-content">
        <?php
        the_content(
            sprintf(
                wp_kses(
                    /* translators: %s: Nome do post. Usado apenas em posts de leitura contínua. */
                    __('Continue lendo<span class="screen-reader-text"> "%s"</span>', 'thabatta-adv'),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                wp_kses_post(get_the_title())
            )
        );

        wp_link_pages(
            array(
                'before' => '<div class="page-links">' . esc_html__('Páginas:', 'thabatta-adv'),
                'after'  => '</div>',
            )
        );
        ?>
    </div><!-- .entry-content -->

    <footer class="entry-footer">
        <?php if (has_tag()) : ?>
            <div class="tags-links">
                <i class="fas fa-tags"></i>
                <?php the_tags('', ', '); ?>
            </div>
        <?php endif; ?>
        
        <?php
        // Compartilhamento em redes sociais
        $share_url = urlencode(get_permalink());
        $share_title = urlencode(get_the_title());
        ?>
        <div class="share-links">
            <span><?php esc_html_e('Compartilhar:', 'thabatta-adv'); ?></span>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Compartilhar no Facebook', 'thabatta-adv'); ?>">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="https://twitter.com/intent/tweet?url=<?php echo $share_url; ?>&text=<?php echo $share_title; ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Compartilhar no Twitter', 'thabatta-adv'); ?>">
                <i class="fab fa-twitter"></i>
            </a>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $share_url; ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Compartilhar no LinkedIn', 'thabatta-adv'); ?>">
                <i class="fab fa-linkedin-in"></i>
            </a>
            <a href="https://api.whatsapp.com/send?text=<?php echo $share_title . '%20' . $share_url; ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Compartilhar no WhatsApp', 'thabatta-adv'); ?>">
                <i class="fab fa-whatsapp"></i>
            </a>
        </div>
    </footer><!-- .entry-footer -->
    
    <?php // Exibe posts relacionados
    if (function_exists('thabatta_get_related_posts')) {
        thabatta_get_related_posts(get_the_ID());
    }
    ?>

    <?php thabatta_post_navigation(); ?>
</article><!-- #post-<?php the_ID(); ?> -->
