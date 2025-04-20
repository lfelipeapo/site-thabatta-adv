<?php
/**
 * Arquivo de definição de rotas
 * 
 * Este arquivo define todas as rotas personalizadas da aplicação
 * 
 * @package WPFramework
 */

// Previne acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

// Obtém a instância do Router
$router = \WPFramework\Core\Router::init();

// Middleware global
$router->middleware([
    '\\WPFramework\\Middleware\\SecurityHeaders',
]);

// Rotas da página inicial
$router->get('/', 'HomeController@index');
$router->get('/sobre', 'HomeController@about');
$router->get('/contato', 'HomeController@contact');
$router->post('/contato', 'HomeController@contact');

// Rota 404 (fallback)
$router->any('*', 'HomeController@notFound');
