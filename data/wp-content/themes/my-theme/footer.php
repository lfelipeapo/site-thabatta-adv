<?php
/**
 * Template para o rodapé do tema
 * 
 * Este arquivo contém o código HTML para o rodapé do tema.
 * 
 * @package WPFramework
 */

// Previne acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}
?>
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
                        'container' => false,
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
