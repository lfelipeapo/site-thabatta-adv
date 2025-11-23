<?php
/**
 * Template para exibição de arquivos
 * 
 * Este arquivo é usado para exibir listas de posts, categorias, tags, etc.
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
            <header class="page-header">
                <?php
                the_archive_title('<h1 class="page-title">', '</h1>');
                the_archive_description('<div class="archive-description">', '</div>');
                ?>
            </header>
            
            <?php if (have_posts()) : ?>
                <div class="posts-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="post-content">
                                <header class="entry-header">
                                    <h2 class="entry-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>
                                    
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
                                    </div>
                                </header>
                                
                                <div class="entry-summary">
                                    <?php the_excerpt(); ?>
                                </div>
                                
                                <footer class="entry-footer">
                                    <a href="<?php the_permalink(); ?>" class="read-more">
                                        <?php _e('Leia mais', 'wpframework'); ?>
                                    </a>
                                </footer>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
                
                <div class="pagination">
                    <?php
                    the_posts_pagination([
                        'mid_size' => 2,
                        'prev_text' => __('Anterior', 'wpframework'),
                        'next_text' => __('Próximo', 'wpframework'),
                    ]);
                    ?>
                </div>
            <?php else : ?>
                <p><?php _e('Nenhum post encontrado.', 'wpframework'); ?></p>
            <?php endif; ?>
        </div>
        
        <?php get_sidebar(); ?>
    </div>
</div>

<?php get_footer(); ?>
