<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once get_template_directory() . '/inc/theme.php';
require_once get_template_directory() . '/src/Infrastructure/WordPress/Assets.php';
require_once get_template_directory() . '/src/Infrastructure/WordPress/PostTypes.php';

$assets = new \ThabattaAdv\Infrastructure\WordPress\Assets();
$assets->register();

$post_types = new \ThabattaAdv\Infrastructure\WordPress\PostTypes();
$post_types->register();
