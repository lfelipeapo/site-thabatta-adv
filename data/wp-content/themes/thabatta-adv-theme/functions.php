<?php
if (!defined('ABSPATH')) exit;

// Carregar scripts e estilos
function thabatta_enqueue_scripts() {
    wp_enqueue_style('thabatta-style', get_stylesheet_uri());
    wp_enqueue_script('thabatta-scripts', get_template_directory_uri() . '/js/content.js', array(), '1.0', true);
}
add_action('wp_enqueue_scripts', 'thabatta_enqueue_scripts');

// Suporte ao tema
function thabatta_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
}
add_action('after_setup_theme', 'thabatta_theme_setup');