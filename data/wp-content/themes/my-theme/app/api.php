<?php
/**
 * Arquivo de definição de rotas da API
 * 
 * Este arquivo define todas as rotas da API REST personalizadas
 * 
 * @package WPFramework
 */

// Previne acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

// Obtém a instância do ApiManager
$api = \WPFramework\Core\ApiManager::init();

// Define o namespace base da API
$api->setNamespace('wpframework/v1');

// Middleware global
$api->middleware([
    '\\WPFramework\\Middleware\\ApiAuth',
    '\\WPFramework\\Middleware\\ApiCors',
]);

// Rotas da API
$api->get('info', function($request) {
    return [
        'name' => 'WPFramework API',
        'version' => WPFRAMEWORK_VERSION,
        'wordpress' => get_bloginfo('version'),
        'description' => 'API REST do WPFramework',
    ];
});

// Exemplo de rota com controller
$api->get('posts', 'PostsController@index');
$api->get('posts/(?P<id>\d+)', 'PostsController@show');
$api->post('posts', 'PostsController@store', [
    'permission_callback' => function() {
        return current_user_can('edit_posts');
    }
]);
$api->put('posts/(?P<id>\d+)', 'PostsController@update', [
    'permission_callback' => function() {
        return current_user_can('edit_posts');
    }
]);
$api->delete('posts/(?P<id>\d+)', 'PostsController@destroy', [
    'permission_callback' => function() {
        return current_user_can('edit_posts');
    }
]);
