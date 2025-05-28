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

<aside id="secondary" class="widget-area sidebar-area">
    <!-- Busca -->
    <div class="sidebar-widget widget_search">
        <?php get_search_form(); ?>
    </div>

    <!-- CTA -->
    <div class="sidebar-widget sidebar-cta">
        <div class="cta-title">Precisa de ajuda?</div>
        <a href="/contato" class="cta-btn">Fale Conosco</a>
    </div>

    <!-- Posts Relacionados -->
    <?php
    if (function_exists('thabatta_get_related_posts')) {
        $related_posts = thabatta_get_related_posts(get_the_ID(), 3);
        if ($related_posts) {
            echo '<div class="sidebar-widget">';
            echo '<div class="widget-title">Posts Relacionados</div>';
            echo '<div class="related-posts-sidebar">';
            foreach ($related_posts as $post) {
                $thumb = get_the_post_thumbnail_url($post->ID, 'thumbnail');
                $cat = get_the_category($post->ID);
                echo '<div class="sidebar-post-card related-post-sidebar-item">';
                if ($thumb) {
                    echo '<img class="sidebar-thumb" src="' . esc_url($thumb) . '" alt="' . esc_attr(get_the_title($post->ID)) . '">';
                }
                echo '<div class="sidebar-info">';
                echo '<div class="sidebar-title">' . esc_html(get_the_title($post->ID)) . '</div>';
                if (!empty($cat)) {
                    echo '<div class="sidebar-category">' . esc_html($cat[0]->name) . '</div>';
                }
                echo '<div class="sidebar-excerpt">' . wp_trim_words($post->post_content, 10) . '</div>';
                echo '<a href="' . get_permalink($post->ID) . '" class="sidebar-readmore">Leia mais</a>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
            echo '</div>';
        }
    }
    ?>

    <!-- Posts Recentes -->
    <div class="sidebar-widget">
        <div class="widget-title">Posts Recentes</div>
        <div class="recent-posts-sidebar">
            <?php
            $recent_posts = wp_get_recent_posts(array('numberposts' => 4, 'post_status' => 'publish'));
            foreach ($recent_posts as $post) :
                $thumb = get_the_post_thumbnail_url($post['ID'], 'thumbnail');
                $cat = get_the_category($post['ID']);
                ?>
                <div class="sidebar-post-card recent-post-sidebar-item">
                    <?php if ($thumb) : ?>
                        <img class="sidebar-thumb" src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($post['post_title']); ?>">
                    <?php endif; ?>
                    <div class="sidebar-info">
                        <div class="sidebar-title"><?php echo esc_html($post['post_title']); ?></div>
                        <?php if (!empty($cat)) : ?>
                            <div class="sidebar-category"><?php echo esc_html($cat[0]->name); ?></div>
                        <?php endif; ?>
                        <div class="sidebar-excerpt"><?php echo wp_trim_words($post['post_content'], 10); ?></div>
                        <a href="<?php echo get_permalink($post['ID']); ?>" class="sidebar-readmore">Leia mais</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Categorias em cards -->
    <div class="sidebar-widget">
        <div class="widget-title">Categorias</div>
        <div class="categories-sidebar-list">
            <?php
            $categories = get_categories(array('orderby' => 'name', 'order' => 'ASC'));
            foreach ($categories as $category) : ?>
                <div class="category-sidebar-item">
                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>">
                        <?php echo esc_html($category->name); ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Social -->
    <?php
    $facebook_url = get_theme_mod('social_facebook_url');
    $instagram_url = get_theme_mod('social_instagram_url');
    $linkedin_url = get_theme_mod('social_linkedin_url');
    $twitter_url = get_theme_mod('social_twitter_url');
    $youtube_url = get_theme_mod('social_youtube_url');
    $whatsapp_number = get_theme_mod('social_whatsapp_number');
    if ($facebook_url || $instagram_url || $linkedin_url || $twitter_url || $youtube_url || $whatsapp_number) :
    ?>
        <div class="sidebar-widget">
            <div class="widget-title">Redes Sociais</div>
            <div class="sidebar-social-links">
                <?php if ($facebook_url) : ?>
                    <a href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a>
                <?php endif; ?>
                <?php if ($instagram_url) : ?>
                    <a href="<?php echo esc_url($instagram_url); ?>" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>
                <?php endif; ?>
                <?php if ($linkedin_url) : ?>
                    <a href="<?php echo esc_url($linkedin_url); ?>" target="_blank" rel="noopener noreferrer"><i class="fab fa-linkedin-in"></i></a>
                <?php endif; ?>
                <?php if ($twitter_url) : ?>
                    <a href="<?php echo esc_url($twitter_url); ?>" target="_blank" rel="noopener noreferrer"><i class="fab fa-twitter"></i></a>
                <?php endif; ?>
                <?php if ($youtube_url) : ?>
                    <a href="<?php echo esc_url($youtube_url); ?>" target="_blank" rel="noopener noreferrer"><i class="fab fa-youtube"></i></a>
                <?php endif; ?>
                <?php if ($whatsapp_number) : ?>
                    <a href="https://wa.me/<?php echo esc_attr($whatsapp_number); ?>" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i></a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</aside>
