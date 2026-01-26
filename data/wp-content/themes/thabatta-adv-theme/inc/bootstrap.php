<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once get_template_directory() . '/inc/theme.php';
require_once get_template_directory() . '/src/Infrastructure/WordPress/Assets.php';
require_once get_template_directory() . '/src/Infrastructure/WordPress/HomepageCache.php';
require_once get_template_directory() . '/src/Infrastructure/WordPress/PostTypes.php';
require_once get_template_directory() . '/src/Infrastructure/WordPress/Repositories/AreaRepository.php';
require_once get_template_directory() . '/src/Infrastructure/WordPress/Repositories/TeamRepository.php';
require_once get_template_directory() . '/src/Infrastructure/WordPress/Repositories/TestimonialRepository.php';
require_once get_template_directory() . '/src/Infrastructure/WordPress/Repositories/PostRepository.php';

$assets = new \ThabattaAdv\Infrastructure\WordPress\Assets();
$assets->register();

$homepage_cache = new \ThabattaAdv\Infrastructure\WordPress\HomepageCache();
$homepage_cache->register();

$post_types = new \ThabattaAdv\Infrastructure\WordPress\PostTypes();
$post_types->register();
