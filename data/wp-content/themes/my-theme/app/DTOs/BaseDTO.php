<?php
/**
 * Classe base para Data Transfer Objects
 * 
 * Fornece funcionalidades básicas para todos os DTOs
 * 
 * @package WPFramework\DTOs
 */

namespace WPFramework\DTOs;

abstract class BaseDTO
{
    /**
     * Propriedades do DTO
     * 
     * @var array
     */
    protected $properties = [];
    
    /**
     * Regras de validação para as propriedades
     * 
     * @var array
     */
    protected $rules = [];
    
    /**
     * Construtor
     * 
     * @param array $data Dados iniciais
     */
    public function __construct(array $data = [])
    {
        $this->fill($data);
    }
    
    /**
     * Preenche o DTO com dados
     * 
     * @param array $data Dados para preencher
     * @return BaseDTO
     */
    public function fill(array $data)
    {
        foreach ($data as $key => $value) {
            $this->__set($key, $value);
        }
        
        return $this;
    }
    
    /**
     * Define uma propriedade
     * 
     * @param string $name Nome da propriedade
     * @param mixed $value Valor da propriedade
     */
    public function __set($name, $value)
    {
        $this->properties[$name] = $value;
    }
    
    /**
     * Obtém uma propriedade
     * 
     * @param string $name Nome da propriedade
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->properties[$name]) ? $this->properties[$name] : null;
    }
    
    /**
     * Verifica se uma propriedade existe
     * 
     * @param string $name Nome da propriedade
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->properties[$name]);
    }
    
    /**
     * Remove uma propriedade
     * 
     * @param string $name Nome da propriedade
     */
    public function __unset($name)
    {
        unset($this->properties[$name]);
    }
    
    /**
     * Obtém uma propriedade com valor padrão
     * 
     * @param string $name Nome da propriedade
     * @param mixed $default Valor padrão se não existir
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return isset($this->properties[$name]) ? $this->properties[$name] : $default;
    }
    
    /**
     * Verifica se uma propriedade existe
     * 
     * @param string $name Nome da propriedade
     * @return bool
     */
    public function has($name)
    {
        return isset($this->properties[$name]);
    }
    
    /**
     * Converte o DTO para array
     * 
     * @return array
     */
    public function toArray()
    {
        return $this->properties;
    }
    
    /**
     * Converte o DTO para JSON
     * 
     * @param int $options Opções do json_encode
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->properties, $options);
    }
    
    /**
     * Cria um DTO a partir de um array
     * 
     * @param array $data Dados para o DTO
     * @return static
     * @phpstan-return static
     */
    public static function fromArray(array $data)
    {
        /** @var static */
        return new static($data);
    }
    
    /**
     * Cria um DTO a partir de um objeto
     * 
     * @param object $object Objeto para converter
     * @return static
     * @phpstan-return static
     */
    public static function fromObject($object)
    {
        /** @var static */
        return new static((array) $object);
    }
    
    /**
     * Cria um DTO a partir de JSON
     * 
     * @param string $json String JSON
     * @return static
     * @phpstan-return static
     */
    public static function fromJson($json)
    {
        /** @var static */
        return new static(json_decode($json, true) ?: []);
    }
    
    /**
     * Valida o DTO
     * 
     * @return array Erros de validação (vazio se válido)
     */
    public function validate()
    {
        $errors = [];
        
        foreach ($this->rules as $field => $rule) {
            $rules = explode('|', $rule);
            
            foreach ($rules as $r) {
                // Regra com parâmetro (ex: min:3)
                if (strpos($r, ':') !== false) {
                    list($ruleName, $ruleParam) = explode(':', $r);
                } else {
                    $ruleName = $r;
                    $ruleParam = null;
                }
                
                // Verifica a regra
                switch ($ruleName) {
                    case 'required':
                        if (!isset($this->properties[$field]) || empty($this->properties[$field])) {
                            $errors[$field][] = "O campo {$field} é obrigatório.";
                        }
                        break;
                    
                    case 'email':
                        if (isset($this->properties[$field]) && !empty($this->properties[$field]) && !filter_var($this->properties[$field], FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = "O campo {$field} deve ser um e-mail válido.";
                        }
                        break;
                    
                    case 'min':
                        if (isset($this->properties[$field]) && strlen($this->properties[$field]) < $ruleParam) {
                            $errors[$field][] = "O campo {$field} deve ter no mínimo {$ruleParam} caracteres.";
                        }
                        break;
                    
                    case 'max':
                        if (isset($this->properties[$field]) && strlen($this->properties[$field]) > $ruleParam) {
                            $errors[$field][] = "O campo {$field} deve ter no máximo {$ruleParam} caracteres.";
                        }
                        break;
                    
                    case 'numeric':
                        if (isset($this->properties[$field]) && !is_numeric($this->properties[$field])) {
                            $errors[$field][] = "O campo {$field} deve ser numérico.";
                        }
                        break;
                    
                    case 'url':
                        if (isset($this->properties[$field]) && !empty($this->properties[$field]) && !filter_var($this->properties[$field], FILTER_VALIDATE_URL)) {
                            $errors[$field][] = "O campo {$field} deve ser uma URL válida.";
                        }
                        break;
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Verifica se o DTO é válido
     * 
     * @return bool
     */
    public function isValid()
    {
        return empty($this->validate());
    }
}
