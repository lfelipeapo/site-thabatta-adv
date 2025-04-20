<?php
/**
 * Classe Autoloader para carregamento manual de classes
 * 
 * Implementa um autoloader PSR-4 para o namespace WPFramework
 * quando o Composer não está disponível.
 * 
 * @package WPFramework\Core
 */

namespace WPFramework\Core;

class Autoloader
{
    /**
     * Registra o autoloader
     */
    public function __construct()
    {
        spl_autoload_register([$this, 'loadClass']);
    }

    /**
     * Carrega uma classe baseada no namespace PSR-4
     * 
     * @param string $className Nome completo da classe com namespace
     * @return void
     */
    public function loadClass($className)
    {
        // Verifica se a classe pertence ao namespace WPFramework
        if (strpos($className, 'WPFramework\\') !== 0) {
            return;
        }

        // Remove o namespace base
        $className = str_replace('WPFramework\\', '', $className);
        
        // Converte namespace para caminho de diretório
        $filePath = WPFRAMEWORK_APP_DIR . '/' . str_replace('\\', '/', $className) . '.php';
        
        // Carrega o arquivo se existir
        if (file_exists($filePath)) {
            require_once $filePath;
        }
    }
}
