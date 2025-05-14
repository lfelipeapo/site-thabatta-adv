<?php
/**
 * O cabeçalho para o tema Thabatta Advocacia
 *
 * Exibe toda a seção <head> e abre o elemento <body>
 *
 * @package Thabatta_Advocacia
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Pular para o conteúdo', 'thabatta-adv'); ?></a>

    <header id="masthead" class="site-header">
        <div class="container">
            <div class="site-branding">
                <?php
                if (has_custom_logo()) :
                    the_custom_logo();
                else :
                ?>
                    <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
                    <?php
                    $thabatta_description = get_bloginfo('description', 'display');
                    if ($thabatta_description || is_customize_preview()) :
                    ?>
                        <p class="site-description"><?php echo $thabatta_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div><!-- .site-branding -->

            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>

            <nav id="site-navigation" class="main-navigation">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'container'      => '',
                    )
                );
                ?>
            </nav><!-- #site-navigation -->
            
            <div class="header-actions">
                <?php if (function_exists('get_field') && get_field('exibir_botao_contato', 'option')) : ?>
                    <div class="header-contact">
                        <a href="<?php echo esc_url(get_field('link_botao_contato', 'option')); ?>" class="btn btn-primary">
                            <?php echo esc_html(get_field('texto_botao_contato', 'option')); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <div class="header-buttons">
                    <a href="<?php echo esc_url(get_theme_mod('header_contact_link', '#')); ?>" class="btn-contact">
                        <i class="fas fa-phone-alt"></i>
                        <span><?php echo esc_html(get_theme_mod('header_contact_text', __('Contato', 'thabatta-adv'))); ?></span>
                    </a>
                    <button type="button" class="open-consultation-form btn-consultation">
                        <i class="fas fa-calendar-alt"></i>
                        <span><?php esc_html_e('Consulta', 'thabatta-adv'); ?></span>
                    </button>
                </div>
            </div>
        </div>
    </header><!-- #masthead -->
