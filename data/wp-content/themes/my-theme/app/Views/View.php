<?php
/**
 * Classe base para views
 * 
 * Fornece funcionalidades para renderização de views
 * 
 * @package WPFramework\Views
 */

namespace WPFramework\Views;

class View
{
    /**
     * Renderiza uma view
     * 
     * @param string $view Nome da view
     * @param array $data Dados para a view
     * @param bool $return Retornar o conteúdo em vez de exibi-lo
     * @return string|void
     */
    public static function render($view, $data = [], $return = false)
    {
        // Extrai os dados para que fiquem disponíveis como variáveis na view
        extract($data);
        
        // Determina o caminho da view
        $viewPath = self::getViewPath($view);
        
        // Verifica se a view existe
        if (!file_exists($viewPath)) {
            throw new \Exception("View não encontrada: {$view}");
        }
        
        // Inicia o buffer de saída
        ob_start();
        
        // Inclui a view
        include $viewPath;
        
        // Obtém o conteúdo do buffer
        $content = ob_get_clean();
        
        // Retorna ou exibe o conteúdo
        if ($return) {
            return $content;
        }
        
        echo $content;
    }
    
    /**
     * Obtém o caminho completo para uma view
     * 
     * @param string $view Nome da view
     * @return string
     */
    protected static function getViewPath($view)
    {
        // Substitui pontos por barras no nome da view
        $view = str_replace('.', '/', $view);
        
        // Adiciona a extensão .php se não estiver presente
        if (substr($view, -4) !== '.php') {
            $view .= '.php';
        }
        
        // Retorna o caminho completo
        return WPFRAMEWORK_APP_DIR . '/Views/' . $view;
    }
    
    /**
     * Renderiza um componente
     * 
     * @param string $component Nome do componente
     * @param array $data Dados para o componente
     * @param bool $return Retornar o conteúdo em vez de exibi-lo
     * @return string|void
     */
    public static function component($component, $data = [], $return = false)
    {
        return self::render('components.' . $component, $data, $return);
    }
    
    /**
     * Renderiza um partial
     * 
     * @param string $partial Nome do partial
     * @param array $data Dados para o partial
     * @param bool $return Retornar o conteúdo em vez de exibi-lo
     * @return string|void
     */
    public static function partial($partial, $data = [], $return = false)
    {
        return self::render('partials.' . $partial, $data, $return);
    }
    
    /**
     * Renderiza um layout
     * 
     * @param string $layout Nome do layout
     * @param array $data Dados para o layout
     * @param bool $return Retornar o conteúdo em vez de exibi-lo
     * @return string|void
     */
    public static function layout($layout, $data = [], $return = false)
    {
        return self::render('layouts.' . $layout, $data, $return);
    }
    
    /**
     * Escapa HTML para saída segura
     * 
     * @param string $value Valor a ser escapado
     * @return string
     */
    public static function escape($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Formata uma data
     * 
     * @param string $date Data a ser formatada
     * @param string $format Formato da data
     * @return string
     */
    public static function formatDate($date, $format = 'd/m/Y H:i')
    {
        return date_i18n($format, strtotime($date));
    }
    
    /**
     * Formata um valor monetário
     * 
     * @param float $value Valor a ser formatado
     * @param string $currency Símbolo da moeda
     * @return string
     */
    public static function formatMoney($value, $currency = 'R$')
    {
        return $currency . ' ' . number_format($value, 2, ',', '.');
    }
    
    /**
     * Trunca um texto
     * 
     * @param string $text Texto a ser truncado
     * @param int $length Comprimento máximo
     * @param string $suffix Sufixo para indicar truncamento
     * @return string
     */
    public static function truncate($text, $length = 100, $suffix = '...')
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . $suffix;
    }
}
