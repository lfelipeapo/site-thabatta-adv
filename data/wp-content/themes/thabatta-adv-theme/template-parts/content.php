<?php
/**
 * Template part para exibir posts
 *
 * @package Thabatta_Advocacia
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="card-image">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('thabatta-card'); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="card-content">
        <header class="entry-header">
            <?php
            if (is_singular()) :
                the_title('<h1 class="entry-title">', '</h1>');
            else :
                the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
            endif;

            if ('post' === get_post_type()) :
            ?>
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

        <div class="entry-content">
            <?php
            if (is_singular()) :
                the_content();
            else :
                the_excerpt();
            ?>
                <a href="<?php the_permalink(); ?>" class="read-more">
                    <?php esc_html_e('Leia mais', 'thabatta-adv'); ?> <i class="fas fa-arrow-right"></i>
                </a>
            <?php
            endif;
            ?>
        </div><!-- .entry-content -->

        <?php if (is_singular() && 'post' === get_post_type()) : ?>
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
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo $share_url; ?>&text=<?php echo $share_title; ?>" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $share_url; ?>" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="https://api.whatsapp.com/send?text=<?php echo $share_title . '%20' . $share_url; ?>" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </footer><!-- .entry-footer -->
        <?php endif; ?>
    </div><!-- .card-content -->
</article><!-- #post-<?php the_ID(); ?> -->
