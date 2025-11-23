<?php
/**
 * Classe ApiController
 * 
 * Controller base para endpoints da API REST
 * 
 * @package WPFramework\Controllers
 */

namespace WPFramework\Controllers;

class ApiController extends BaseController
{
    /**
     * Construtor
     */
    public function __construct()
    {
        // BaseController não tem construtor, então não precisa chamar parent
    }

    /**
     * Retorna uma resposta de sucesso
     * 
     * @param mixed $data Dados para a resposta
     * @param int $status Código de status HTTP
     * @return \WP_REST_Response
     */
    protected function success($data, $status = 200)
    {
        return new \WP_REST_Response([
            'success' => true,
            'data' => $data
        ], $status);
    }

    /**
     * Retorna uma resposta de erro
     * 
     * @param string $message Mensagem de erro
     * @param int $status Código de status HTTP
     * @param array $errors Erros adicionais
     * @return \WP_REST_Response
     */
    protected function error($message, $status = 400, $errors = [])
    {
        return new \WP_REST_Response([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    /**
     * Valida os parâmetros da requisição
     * 
     * @param \WP_REST_Request $request Objeto de requisição
     * @param array $rules Regras de validação
     * @return array|bool Erros de validação ou true se válido
     */
    protected function validateRequest($request, $rules)
    {
        $params = $request->get_params();
        $errors = $this->validate($params, $rules);
        
        if (empty($errors)) {
            return true;
        }
        
        return $errors;
    }

    /**
     * Sanitiza os parâmetros da requisição
     * 
     * @param \WP_REST_Request $request Objeto de requisição
     * @return array Parâmetros sanitizados
     */
    protected function sanitizeRequest($request)
    {
        $params = $request->get_params();
        $sanitized = [];
        
        foreach ($params as $key => $value) {
            $sanitized[$key] = $this->sanitize($value);
        }
        
        return $sanitized;
    }
}
