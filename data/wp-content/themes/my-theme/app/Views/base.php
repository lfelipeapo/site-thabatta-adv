<?php
/**
 * Arquivo base para todas as views
 * 
 * Este arquivo serve como template base para todas as views do tema
 * 
 * @package WPFramework
 */

// Previne acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

// Obtém o conteúdo da view
$content = isset($content) ? $content : '';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo isset($title) ? esc_html($title) . ' - ' . get_bloginfo('name') : get_bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    
    <header class="site-header">
        <div class="container">
            <div class="site-branding">
                <?php if (has_custom_logo()): ?>
                    <?php the_custom_logo(); ?>
                <?php else: ?>
                    <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></h1>
                    <p class="site-description"><?php bloginfo('description'); ?></p>
                <?php endif; ?>
            </div>
            
            <nav class="main-navigation">
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'menu_id' => 'primary-menu',
                    'container' => '',
                    'fallback_cb' => false,
                ]);
                ?>
            </nav>
        </div>
    </header>
    
    <main id="main" class="site-main">
        <?php echo $content; ?>
    </main>
    
    <footer class="site-footer">
        <div class="container">
            <div class="footer-widgets">
                <div class="footer-widget">
                    <h3><?php _e('Sobre Nós', 'wpframework'); ?></h3>
                    <p><?php bloginfo('description'); ?></p>
                </div>
                
                <div class="footer-widget">
                    <h3><?php _e('Links Rápidos', 'wpframework'); ?></h3>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'footer',
                        'menu_id' => 'footer-menu',
                        'container' => '',
                        'fallback_cb' => false,
                    ]);
                    ?>
                </div>
                
                <div class="footer-widget">
                    <h3><?php _e('Contato', 'wpframework'); ?></h3>
                    <p><?php _e('Entre em contato conosco', 'wpframework'); ?></p>
                    <a href="<?php echo esc_url(home_url('/contato')); ?>" class="button"><?php _e('Contato', 'wpframework'); ?></a>
                </div>
            </div>
            
            <div class="site-info">
                <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('Todos os direitos reservados.', 'wpframework'); ?></p>
            </div>
        </div>
    </footer>
    
    <?php wp_footer(); ?>
</body>
</html>
