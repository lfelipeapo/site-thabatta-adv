<?php
/**
 * Template para o cabeçalho do tema
 * 
 * Este arquivo contém o código HTML para o cabeçalho do tema.
 * 
 * @package WPFramework
 */

// Previne acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>
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
                    'container' => false,
                    'fallback_cb' => false,
                ]);
                ?>
            </nav>
        </div>
    </header>
    
    <main id="main" class="site-main">
