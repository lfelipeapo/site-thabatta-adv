<?php
/**
 * Template para a barra lateral
 *
 * @package Thabatta_Advocacia
 */

if (!is_active_sidebar('sidebar-1')) {
    return;
}
?>

<aside id="secondary" class="widget-area sidebar">
    <?php dynamic_sidebar('sidebar-1'); ?>
    
    <?php
    // Exibir posts relacionados se existirem
    if (is_singular() && function_exists('thabatta_get_related_posts')) {
        $related_posts = thabatta_get_related_posts();
        
        if (!empty($related_posts)) :
    ?>
        <section class="widget widget-related-posts">
            <h2 class="widget-title"><?php esc_html_e('Posts Relacionados', 'thabatta-adv'); ?></h2>
            <ul>
                <?php foreach ($related_posts as $related_post) : ?>
                    <li>
                        <a href="<?php echo esc_url(get_permalink($related_post->ID)); ?>">
                            <?php echo esc_html(get_the_title($related_post->ID)); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php
        endif;
    }
    ?>
    
    <?php
    // Exibir categorias relacionadas se for um post
    if (is_singular('post')) {
        $categories = get_the_category();
        
        if (!empty($categories)) :
    ?>
        <section class="widget widget-categories">
            <h2 class="widget-title"><?php esc_html_e('Categorias Relacionadas', 'thabatta-adv'); ?></h2>
            <ul>
                <?php foreach ($categories as $category) : ?>
                    <li>
                        <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>">
                            <?php echo esc_html($category->name); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php
        endif;
    }
    ?>
    
    <?php
    // Exibir redes sociais
    if (function_exists('get_field')) :
        $facebook = get_field('facebook', 'option');
        $instagram = get_field('instagram', 'option');
        $linkedin = get_field('linkedin', 'option');
        $twitter = get_field('twitter', 'option');
        $youtube = get_field('youtube', 'option');
        
        if ($facebook || $instagram || $linkedin || $twitter || $youtube) :
    ?>
        <section class="widget widget-social">
            <h2 class="widget-title"><?php esc_html_e('Redes Sociais', 'thabatta-adv'); ?></h2>
            <div class="social-links">
                <?php if ($facebook) : ?>
                    <a href="<?php echo esc_url($facebook); ?>" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                <?php endif; ?>
                
                <?php if ($instagram) : ?>
                    <a href="<?php echo esc_url($instagram); ?>" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-instagram"></i>
                    </a>
                <?php endif; ?>
                
                <?php if ($linkedin) : ?>
                    <a href="<?php echo esc_url($linkedin); ?>" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                <?php endif; ?>
                
                <?php if ($twitter) : ?>
                    <a href="<?php echo esc_url($twitter); ?>" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-twitter"></i>
                    </a>
                <?php endif; ?>
                
                <?php if ($youtube) : ?>
                    <a href="<?php echo esc_url($youtube); ?>" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-youtube"></i>
                    </a>
                <?php endif; ?>
            </div>
        </section>
    <?php
        endif;
    endif;
    ?>
</aside><!-- #secondary -->
