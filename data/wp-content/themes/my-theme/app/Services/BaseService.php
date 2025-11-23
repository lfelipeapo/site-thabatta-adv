<?php
/**
 * Classe de serviço base
 * 
 * Fornece a estrutura básica para todos os serviços
 * 
 * @package WPFramework\Services
 */

namespace WPFramework\Services;

abstract class BaseService
{
    /**
     * Instância única da classe (padrão Singleton)
     * 
     * @var BaseService
     */
    protected static $instance = null;
    
    /**
     * Obtém a instância única da classe (padrão Singleton)
     * 
     * @return static
     * @phpstan-return static
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            /** @phpstan-ignore-next-line */
            static::$instance = new static();
        }
        
        /** @phpstan-var static */
        return static::$instance;
    }
    
    /**
     * Inicializa o serviço
     * 
     * @return void
     */
    abstract public function init();
}
