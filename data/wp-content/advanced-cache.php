<?php
// Boost Cache Plugin - v0.0.3
if ( ! file_exists( '/var/www/html/wp-content/plugins/jetpack-boost/app/modules/optimizations/page-cache/pre-wordpress/class-boost-cache.php' ) ) {
return;
}
require_once( '/var/www/html/wp-content/plugins/jetpack-boost/app/modules/optimizations/page-cache/pre-wordpress/class-boost-cache.php');
$boost_cache = new Automattic\Jetpack_Boost\Modules\Optimizations\Page_Cache\Pre_WordPress\Boost_Cache();
$boost_cache->init_actions();
$boost_cache->serve();
