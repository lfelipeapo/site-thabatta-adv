<?php
/**
 * Classe base para todos os controllers
 * 
 * Versão refatorada para melhor integração com o fluxo do WordPress
 * 
 * @package WPFramework\Controllers
 */

namespace WPFramework\Controllers;

use WPFramework\Core\Router;

abstract class BaseController
{
    /**
     * Renderiza uma view
     * 
     * @param string $view Nome da view
     * @param array $data Dados para a view
     * @return string Conteúdo HTML renderizado
     */
    protected function view($view, $data = [])
    {
        // Sanitiza os dados
        $data = $this->sanitizeData($data);
        
        // Adiciona o nonce para segurança em formulários
        $data['_wpnonce_field'] = Router::init()->getNonceField();
        
        // Extrai os dados para a view
        extract($data);
        
        // Inicia o buffer de saída
        ob_start();
        
        // Caminho completo para a view
        $view_path = get_template_directory() . '/app/Views/' . $view . '.php';
        
        // Verifica se a view existe
        if (file_exists($view_path)) {
            // Inclui a view
            include $view_path;
        } else {
            // View não encontrada
            echo '<p>' . sprintf(__('View não encontrada: %s', 'wpframework'), esc_html($view)) . '</p>';
        }
        
        // Retorna o conteúdo do buffer
        return ob_get_clean();
    }
    
    /**
     * Sanitiza os dados para a view
     * 
     * @param array $data Dados para sanitizar
     * @return array Dados sanitizados
     */
    private function sanitizeData($data)
    {
        // Sanitiza os dados recursivamente
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitizeData($value);
            } elseif (is_string($value)) {
                // Não sanitiza aqui, pois o escape deve ser feito na saída
                $data[$key] = $value;
            }
        }
        
        return $data;
    }
    
    /**
     * Redireciona para uma URL
     * 
     * @param string $url URL para redirecionamento
     * @param int $status Código de status HTTP
     * @return void
     */
    protected function redirect($url, $status = 302)
    {
        wp_redirect(esc_url_raw($url), $status);
        exit;
    }
    
    /**
     * Retorna uma resposta JSON
     * 
     * @param mixed $data Dados para a resposta
     * @param int $status Código de status HTTP
     * @return void
     */
    protected function json($data, $status = 200)
    {
        // Define o código de status
        status_header($status);
        
        // Define o cabeçalho
        header('Content-Type: application/json; charset=UTF-8');
        
        // Codifica os dados
        echo wp_json_encode($data);
        
        // Finaliza a execução
        exit;
    }
    
    /**
     * Verifica se a requisição é AJAX
     * 
     * @return bool
     */
    protected function isAjax()
    {
        return defined('DOING_AJAX') && DOING_AJAX;
    }
    
    /**
     * Verifica se a requisição é via POST
     * 
     * @return bool
     */
    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Verifica se a requisição é via GET
     * 
     * @return bool
     */
    protected function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    /**
     * Obtém um valor de $_GET
     * 
     * @param string $key Chave do parâmetro
     * @param mixed $default Valor padrão se não existir
     * @return mixed
     */
    protected function getQuery($key, $default = null)
    {
        return isset($_GET[$key]) ? $this->sanitizeInput($_GET[$key]) : $default;
    }
    
    /**
     * Obtém um valor de $_POST
     * 
     * @param string $key Chave do parâmetro
     * @param mixed $default Valor padrão se não existir
     * @return mixed
     */
    protected function getPost($key, $default = null)
    {
        return isset($_POST[$key]) ? $this->sanitizeInput($_POST[$key]) : $default;
    }
    
    /**
     * Obtém um valor de $_REQUEST
     * 
     * @param string $key Chave do parâmetro
     * @param mixed $default Valor padrão se não existir
     * @return mixed
     */
    protected function getRequest($key, $default = null)
    {
        return isset($_REQUEST[$key]) ? $this->sanitizeInput($_REQUEST[$key]) : $default;
    }
    
    /**
     * Sanitiza uma entrada
     * 
     * @param mixed $input Entrada a ser sanitizada
     * @return mixed
     */
    protected function sanitizeInput($input)
    {
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = $this->sanitizeInput($value);
            }
            return $input;
        }
        
        return sanitize_text_field($input);
    }
    
    /**
     * Verifica se o usuário está logado
     * 
     * @return bool
     */
    protected function isUserLoggedIn()
    {
        return is_user_logged_in();
    }
    
    /**
     * Verifica se o usuário tem uma capacidade
     * 
     * @param string $capability Capacidade a ser verificada
     * @return bool
     */
    protected function userCan($capability)
    {
        return current_user_can($capability);
    }
    
    /**
     * Obtém o usuário atual
     * 
     * @return \WP_User|false
     */
    protected function getCurrentUser()
    {
        return wp_get_current_user();
    }
    
    /**
     * Verifica o nonce
     * 
     * @param string $action Ação do nonce
     * @param string $nonce_field Nome do campo do nonce
     * @return bool
     */
    protected function verifyNonce($action = 'wpframework_action', $nonce_field = '_wpnonce')
    {
        $nonce = isset($_REQUEST[$nonce_field]) ? $_REQUEST[$nonce_field] : '';
        return wp_verify_nonce($nonce, $action);
    }
    
    /**
     * Verifica o nonce e morre se for inválido
     * 
     * @param string $action Ação do nonce
     * @param string $nonce_field Nome do campo do nonce
     * @return void
     */
    protected function checkNonce($action = 'wpframework_action', $nonce_field = '_wpnonce')
    {
        if (!$this->verifyNonce($action, $nonce_field)) {
            wp_die(
                __('Erro de segurança. Por favor, tente novamente.', 'wpframework'),
                __('Erro de segurança', 'wpframework'),
                [
                    'response' => 403,
                    'back_link' => true,
                ]
            );
        }
    }
    
    /**
     * Obtém um valor de $_POST ou $_GET (alias para getRequest)
     * 
     * @param string $key Chave do parâmetro
     * @param mixed $default Valor padrão se não existir
     * @return mixed
     */
    protected function input($key, $default = null)
    {
        return $this->getRequest($key, $default);
    }
    
    /**
     * Valida dados com regras
     * 
     * @param array $data Dados para validar
     * @param array $rules Regras de validação
     * @return array Array de erros (vazio se válido)
     */
    protected function validate($data, $rules)
    {
        $errors = [];
        
        foreach ($rules as $field => $ruleString) {
            $rulesArray = explode('|', $ruleString);
            $value = $data[$field] ?? null;
            
            foreach ($rulesArray as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleValue = $ruleParts[1] ?? null;
                
                switch ($ruleName) {
                    case 'required':
                        if (empty($value)) {
                            $errors[$field][] = sprintf(__('O campo %s é obrigatório.', 'wpframework'), $field);
                        }
                        break;
                    case 'email':
                        if (!empty($value) && !is_email($value)) {
                            $errors[$field][] = sprintf(__('O campo %s deve ser um e-mail válido.', 'wpframework'), $field);
                        }
                        break;
                    case 'min':
                        if (!empty($value) && strlen($value) < (int)$ruleValue) {
                            $errors[$field][] = sprintf(__('O campo %s deve ter no mínimo %d caracteres.', 'wpframework'), $field, $ruleValue);
                        }
                        break;
                    case 'max':
                        if (!empty($value) && strlen($value) > (int)$ruleValue) {
                            $errors[$field][] = sprintf(__('O campo %s deve ter no máximo %d caracteres.', 'wpframework'), $field, $ruleValue);
                        }
                        break;
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Sanitiza um valor (alias para sanitizeInput)
     * 
     * @param mixed $value Valor para sanitizar
     * @return mixed
     */
    protected function sanitize($value)
    {
        return $this->sanitizeInput($value);
    }
}
