<?php
/**
 * Arquivo de teste para validar a implementação da arquitetura MVC
 * 
 * Este arquivo executa testes básicos para garantir que os componentes
 * principais da arquitetura estão funcionando corretamente.
 * 
 * @package WPFramework
 */

// Previne acesso direto ao arquivo
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

// Define constantes para simulação do ambiente WordPress
define('WPFRAMEWORK_VERSION', '1.0.0');
define('WPFRAMEWORK_DIR', __DIR__);
define('WPFRAMEWORK_APP_DIR', WPFRAMEWORK_DIR . '/app');

// Função de autoload para testes
spl_autoload_register(function($className) {
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
});

// Classe de teste
class WPFrameworkTest
{
    /**
     * Executa todos os testes
     */
    public function run()
    {
        echo "Iniciando testes da arquitetura WPFramework MVC...\n\n";
        
        $this->testCore();
        $this->testControllers();
        $this->testModels();
        $this->testDTOs();
        $this->testViews();
        $this->testMiddleware();
        $this->testAPI();
        $this->testCursosCPT();
        
        echo "\nTodos os testes concluídos!\n";
    }
    
    /**
     * Testa os componentes do Core
     */
    private function testCore()
    {
        echo "Testando componentes do Core...\n";
        
        // Verifica se as classes principais existem
        $this->assertClassExists('WPFramework\\Core\\Bootstrap');
        $this->assertClassExists('WPFramework\\Core\\Router');
        $this->assertClassExists('WPFramework\\Core\\ApiManager');
        $this->assertClassExists('WPFramework\\Core\\SessionManager');
        $this->assertClassExists('WPFramework\\Core\\AssetManager');
        $this->assertClassExists('WPFramework\\Core\\AdminManager');
        
        echo "✓ Componentes do Core verificados com sucesso!\n";
    }
    
    /**
     * Testa os Controllers
     */
    private function testControllers()
    {
        echo "Testando Controllers...\n";
        
        // Verifica se as classes de controller existem
        $this->assertClassExists('WPFramework\\Controllers\\BaseController');
        $this->assertClassExists('WPFramework\\Controllers\\HomeController');
        $this->assertClassExists('WPFramework\\Controllers\\ApiController');
        $this->assertClassExists('WPFramework\\Controllers\\CursosController');
        
        echo "✓ Controllers verificados com sucesso!\n";
    }
    
    /**
     * Testa os Models
     */
    private function testModels()
    {
        echo "Testando Models...\n";
        
        // Verifica se as classes de model existem
        $this->assertClassExists('WPFramework\\Models\\BaseModel');
        $this->assertClassExists('WPFramework\\Models\\PostModel');
        $this->assertClassExists('WPFramework\\Models\\PostTypes\\Curso');
        
        echo "✓ Models verificados com sucesso!\n";
    }
    
    /**
     * Testa os DTOs
     */
    private function testDTOs()
    {
        echo "Testando DTOs...\n";
        
        // Verifica se as classes de DTO existem
        $this->assertClassExists('WPFramework\\DTOs\\BaseDTO');
        $this->assertClassExists('WPFramework\\DTOs\\PostDTO');
        
        echo "✓ DTOs verificados com sucesso!\n";
    }
    
    /**
     * Testa as Views
     */
    private function testViews()
    {
        echo "Testando Views...\n";
        
        // Verifica se a classe de View existe
        $this->assertClassExists('WPFramework\\Views\\View');
        
        // Verifica se os arquivos de view existem
        $this->assertFileExists(WPFRAMEWORK_APP_DIR . '/Views/base.php');
        $this->assertFileExists(WPFRAMEWORK_APP_DIR . '/Views/layouts/default.php');
        $this->assertFileExists(WPFRAMEWORK_APP_DIR . '/Views/home/index.php');
        $this->assertFileExists(WPFRAMEWORK_APP_DIR . '/Views/components/card.php');
        $this->assertFileExists(WPFRAMEWORK_APP_DIR . '/Views/components/modal.php');
        $this->assertFileExists(WPFRAMEWORK_APP_DIR . '/Views/components/popover.php');
        
        echo "✓ Views verificadas com sucesso!\n";
    }
    
    /**
     * Testa os Middleware
     */
    private function testMiddleware()
    {
        echo "Testando Middleware...\n";
        
        // Verifica se as classes de middleware existem
        $this->assertClassExists('WPFramework\\Middleware\\BaseMiddleware');
        $this->assertClassExists('WPFramework\\Middleware\\ApiAuth');
        $this->assertClassExists('WPFramework\\Middleware\\ApiCors');
        $this->assertClassExists('WPFramework\\Middleware\\SecurityHeaders');
        
        echo "✓ Middleware verificados com sucesso!\n";
    }
    
    /**
     * Testa a API
     */
    private function testAPI()
    {
        echo "Testando API...\n";
        
        // Verifica se as classes de API existem
        $this->assertClassExists('WPFramework\\Api\\CursosController');
        
        // Verifica se o arquivo de rotas da API existe
        $this->assertFileExists(WPFRAMEWORK_APP_DIR . '/api.php');
        
        echo "✓ API verificada com sucesso!\n";
    }
    
    /**
     * Testa o Custom Post Type Cursos
     */
    private function testCursosCPT()
    {
        echo "Testando Custom Post Type Cursos...\n";
        
        // Verifica se os arquivos relacionados ao CPT Cursos existem
        $this->assertFileExists(WPFRAMEWORK_APP_DIR . '/Models/PostTypes/Curso.php');
        $this->assertFileExists(WPFRAMEWORK_APP_DIR . '/Controllers/CursosController.php');
        $this->assertFileExists(WPFRAMEWORK_APP_DIR . '/Api/CursosController.php');
        $this->assertFileExists(WPFRAMEWORK_APP_DIR . '/Views/cursos/index.php');
        $this->assertFileExists(WPFRAMEWORK_APP_DIR . '/Views/cursos/show.php');
        $this->assertFileExists(WPFRAMEWORK_APP_DIR . '/routes-cursos.php');
        
        echo "✓ Custom Post Type Cursos verificado com sucesso!\n";
    }
    
    /**
     * Verifica se uma classe existe
     * 
     * @param string $className Nome da classe
     */
    private function assertClassExists($className)
    {
        if (!class_exists($className)) {
            echo "❌ ERRO: Classe {$className} não encontrada!\n";
            exit(1);
        }
    }
    
    /**
     * Verifica se um arquivo existe
     * 
     * @param string $filePath Caminho do arquivo
     */
    private function assertFileExists($filePath)
    {
        if (!file_exists($filePath)) {
            echo "❌ ERRO: Arquivo {$filePath} não encontrado!\n";
            exit(1);
        }
    }
}

// Executa os testes
$tester = new WPFrameworkTest();
$tester->run();
