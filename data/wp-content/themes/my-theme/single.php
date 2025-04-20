<?php
/**
 * Template para exibição de posts individuais
 * 
 * Este arquivo é usado para exibir posts individuais quando single.php é chamado.
 * 
 * @package WPFramework
 */

// Previne acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="container">
    <div class="content-area">
        <div class="main-content">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>
                        <header class="entry-header">
                            <h1 class="entry-title"><?php the_title(); ?></h1>
                            
                            <div class="entry-meta">
                                <span class="posted-on">
                                    <?php _e('Publicado em', 'wpframework'); ?> 
                                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                        <?php echo get_the_date(); ?>
                                    </time>
                                </span>
                                
                                <span class="byline">
                                    <?php _e('por', 'wpframework'); ?> 
                                    <span class="author vcard">
                                        <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                                            <?php echo get_the_author(); ?>
                                        </a>
                                    </span>
                                </span>
                                
                                <?php if (has_category()) : ?>
                                    <span class="cat-links">
                                        <?php _e('em', 'wpframework'); ?> 
                                        <?php the_category(', '); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </header>
                        
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="entry-thumbnail">
                                <?php the_post_thumbnail('large'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="entry-content">
                            <?php the_content(); ?>
                            
                            <?php
                            wp_link_pages([
                                'before' => '<div class="page-links">' . __('Páginas:', 'wpframework'),
                                'after'  => '</div>',
                            ]);
                            ?>
                        </div>
                        
                        <footer class="entry-footer">
                            <?php if (has_tag()) : ?>
                                <div class="entry-tags">
                                    <?php the_tags('<span class="tags-title">' . __('Tags:', 'wpframework') . '</span> ', ', ', ''); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="entry-share">
                                <h4><?php _e('Compartilhar', 'wpframework'); ?></h4>
                                <div class="share-buttons">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" class="share-facebook"><?php _e('Facebook', 'wpframework'); ?></a>
                                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" class="share-twitter"><?php _e('Twitter', 'wpframework'); ?></a>
                                    <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' - ' . get_permalink()); ?>" target="_blank" class="share-whatsapp"><?php _e('WhatsApp', 'wpframework'); ?></a>
                                </div>
                            </div>
                        </footer>
                    </article>
                    
                    <?php
                    // Se os comentários estão abertos ou temos pelo menos um comentário, carrega o template de comentários
                    if (comments_open() || get_comments_number()) :
                        comments_template();
                    endif;
                    ?>
                    
                    <nav class="navigation post-navigation">
                        <h2 class="screen-reader-text"><?php _e('Navegação de Posts', 'wpframework'); ?></h2>
                        <div class="nav-links">
                            <?php
                            previous_post_link('<div class="nav-previous">%link</div>', '<span class="nav-title">' . __('Post Anterior', 'wpframework') . '</span> %title');
                            next_post_link('<div class="nav-next">%link</div>', '<span class="nav-title">' . __('Próximo Post', 'wpframework') . '</span> %title');
                            ?>
                        </div>
                    </nav>
                <?php endwhile; ?>
            <?php else : ?>
                <p><?php _e('Nenhum post encontrado.', 'wpframework'); ?></p>
            <?php endif; ?>
        </div>
        
        <?php get_sidebar(); ?>
    </div>
</div>

<?php get_footer(); ?>
