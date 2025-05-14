<?php
/**
 * View da página inicial
 * 
 * @package WPFramework
 */

// Previne acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

// Obtém os posts
$posts = isset($posts) ? $posts : [];
?>

<div class="home-banner">
    <div class="container">
        <h2><?php _e('Bem-vindo ao WPFramework', 'wpframework'); ?></h2>
        <p><?php _e('Uma estrutura MVC completa para WordPress utilizando o paradigma orientado a objetos.', 'wpframework'); ?></p>
    </div>
</div>

<div class="container">
    <div class="latest-posts">
        <h2><?php _e('Últimas Publicações', 'wpframework'); ?></h2>
        
        <?php if (!empty($posts)): ?>
            <div class="posts-grid">
                <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <?php if (has_post_thumbnail($post->ID)): ?>
                            <div class="post-thumbnail">
                                <a href="<?php echo get_permalink($post->ID); ?>">
                                    <?php echo get_the_post_thumbnail($post->ID, 'medium'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="post-content">
                            <h3 class="post-title">
                                <a href="<?php echo get_permalink($post->ID); ?>">
                                    <?php echo get_the_title($post->ID); ?>
                                </a>
                            </h3>
                            
                            <div class="post-meta">
                                <span class="post-date"><?php echo get_the_date('', $post->ID); ?></span>
                                <span class="post-author"><?php _e('por', 'wpframework'); ?> <?php echo get_the_author_meta('display_name', $post->post_author); ?></span>
                            </div>
                            
                            <div class="post-excerpt">
                                <?php echo get_the_excerpt($post->ID); ?>
                            </div>
                            
                            <a href="<?php echo get_permalink($post->ID); ?>" class="read-more">
                                <?php _e('Leia mais', 'wpframework'); ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p><?php _e('Nenhuma publicação encontrada.', 'wpframework'); ?></p>
        <?php endif; ?>
    </div>
</div>
