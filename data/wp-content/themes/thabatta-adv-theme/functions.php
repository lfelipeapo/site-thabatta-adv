<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include custom functions and hooks (mantido como estava)
require get_template_directory() . '/includes/custom-functions.php';
require get_template_directory() . '/includes/hooks.php';

// --- Theme Setup ---
function thabatta_theme_setup()
{
    // Make theme available for translation
    load_theme_textdomain('thabatta', get_template_directory() . '/languages');

    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title.
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages.
    add_theme_support('post-thumbnails');

    // Register navigation menus.
    register_nav_menus(array(
        'primary' => __('Menu Principal', 'thabatta'),
        // 'footer'  => __( 'Menu Rodapé', 'thabatta' ), // Exemplo de outro menu
    )); // É melhor registrar os menus aqui também

    // Switch default core markup for search form, comment form, and comments to output valid HTML5.
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style', // Adicionado para scripts e estilos
        'script', // Adicionado para scripts e estilos
    ));

    // Set up the WordPress core custom background feature.
    add_theme_support('custom-background', apply_filters('thabatta_custom_background_args', array(
        'default-color' => 'ffffff',
        'default-image' => '',
    )));

    // Add theme support for selective refresh for widgets.
    add_theme_support('customize-selective-refresh-widgets');

    // Add support for core custom logo.
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-width'  => true,
        'flex-height' => true,
    ));

    // Add support for Block Styles.
    add_theme_support('wp-block-styles');

    // Add support for full and wide align images.
    add_theme_support('align-wide');

    // Add support for editor styles.
    add_theme_support('editor-styles');

    // Enqueue editor styles.
    add_editor_style('style-editor.css'); // Certifique-se que este arquivo existe

    // Add support for responsive embedded content.
    add_theme_support('responsive-embeds');
}
add_action('after_setup_theme', 'thabatta_theme_setup');

// --- Enqueue Scripts and Styles ---
function thabatta_enqueue_scripts()
{
    wp_enqueue_style('thabatta-style', get_stylesheet_uri(), array(), wp_get_theme()->get('Version'));
    wp_enqueue_script('thabatta-scripts', get_template_directory_uri() . '/js/content.js', array('jquery'), '1.0', true);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'thabatta_enqueue_scripts');

function thabatta_register_sidebars()
{
    register_sidebar(array(
        'name'          => __('Sidebar Principal', 'thabatta'),
        'id'            => 'primary-sidebar',
        'description'   => __('Widgets adicionados aqui aparecerão na sidebar principal.', 'thabatta'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'thabatta_register_sidebars');

function thabatta_register_custom_post_types()
{register_post_type('thabatta_custom_post', array(
        'labels' => array(
            'name'          => __('Custom Posts', 'thabatta'),
            'singular_name' => __('Custom Post', 'thabatta'),
            'add_new_item'  => __('Adicionar Novo Custom Post', 'thabatta'),
            'edit_item'     => __('Editar Custom Post', 'thabatta'),
            'new_item'      => __('Novo Custom Post', 'thabatta'),
            'view_item'     => __('Ver Custom Post', 'thabatta'),
            'search_items'  => __('Buscar Custom Posts', 'thabatta'),
            'not_found'     => __('Nenhum Custom Post encontrado', 'thabatta'),
            'not_found_in_trash' => __('Nenhum Custom Post encontrado na lixeira', 'thabatta'),
        ),
        'public'        => true,
        'has_archive'   => true, 
        'supports'      => array('title', 'editor', 'thumbnail', 'custom-fields', 'excerpt', 'comments'),
        'rewrite'       => array('slug' => 'custom-posts'),
        'menu_icon'     => 'dashicons-admin-post',
        'show_in_rest'  => true,
    ));
}
add_action('init', 'thabatta_register_custom_post_types');
