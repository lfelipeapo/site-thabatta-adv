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
     * Construtor protegido para padrão Singleton
     */
    protected function __construct()
    {
        $this->init();
    }
    
    /**
     * Obtém a instância única da classe (padrão Singleton)
     * 
     * @return static
     * @phpstan-return static
     */
    public static function getInstance()
    {
        $class = get_called_class();
        if ($class === __CLASS__) {
            throw new \Exception('Não é possível instanciar a classe base abstrata');
        }
        
        if (static::$instance === null) {
            /** @phpstan-ignore-next-line */
            static::$instance = new $class();
        }
        
        /** @phpstan-var static */
        return static::$instance;
    }
    
    /**
     * Inicializa o componente
     * 
     * @return void
     */
    abstract public function init();
}
