<?php
/**
 * Arquivo de rotas para o Custom Post Type Cursos
 * 
 * Este arquivo deve ser incluído no arquivo routes.php principal
 * 
 * @package WPFramework
 */

// Previne acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

// Obtém a instância do Router
$router = \WPFramework\Core\Router::init();

// Rotas para cursos
$router->get('/cursos', 'CursosController@index');
$router->get('/cursos/categoria/{categoria}', 'CursosController@categoria');
$router->get('/cursos/{id}', 'CursosController@show');
