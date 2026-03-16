<?php
/**
 * Autoloader PSR-4 para o plugin
 *
 * @package AICG\Core
 * @since   1.0.0
 */

namespace AICG\Core;

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Autoloader
 *
 * Implementa autoloading PSR-4 para todas as classes do plugin
 *
 * @package AICG\Core
 * @since   1.0.0
 */
class Autoloader
{
    /**
     * Namespace prefix do plugin
     *
     * @var string
     */
    private static string $prefix = 'AICG\\';

    /**
     * Comprimento do prefixo
     *
     * @var int
     */
    private static int $prefix_length = 5;

    /**
     * Diretório base para as classes
     *
     * @var string
     */
    private static string $base_dir;

    /**
     * Registra o autoloader
     *
     * @return void
     */
    public static function register(): void
    {
        self::$base_dir = AICG_PLUGIN_DIR . 'includes/';
        
        spl_autoload_register([self::class, 'autoload']);
    }

    /**
     * Carrega a classe solicitada
     *
     * @param string $class Nome completo da classe
     * @return void
     */
    private static function autoload(string $class): void
    {
        // Verifica se a classe pertence ao nosso namespace
        if (strncmp(self::$prefix, $class, self::$prefix_length) !== 0) {
            return;
        }

        // Remove o prefixo
        $relative_class = substr($class, self::$prefix_length);

        // Converte namespace para path de arquivo
        $file = self::$base_dir . str_replace('\\', '/', $relative_class) . '.php';

        // Se o arquivo existir, carrega
        if (file_exists($file)) {
            require_once $file;
        }
    }
}
