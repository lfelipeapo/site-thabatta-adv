<?php
/**
 * Classe base para componentes de admin
 * 
 * Fornece a estrutura básica para todos os componentes de admin
 * 
 * @package WPFramework\Core\Admin
 */

namespace WPFramework\Core\Admin;

abstract class BaseAdminComponent
{
    /**
     * Instância única da classe (padrão Singleton)
     * 
     * @var BaseAdminComponent
     */
    protected static $instance = null;
    
    /**
     * Obtém a instância única da classe (padrão Singleton)
     * 
     * @return static
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        
        return static::$instance;
    }
    
    /**
     * Inicializa o componente
     * 
     * @return void
     */
    abstract public function init();
}
