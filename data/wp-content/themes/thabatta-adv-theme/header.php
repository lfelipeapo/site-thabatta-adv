<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.png">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header>
    <img src="<?php echo get_template_directory_uri(); ?>/images/logo.png" alt="Logo">
    <nav>
        <?php wp_nav_menu(array('theme_location' => 'main-menu')); ?>
    </nav>
</header>
<?php