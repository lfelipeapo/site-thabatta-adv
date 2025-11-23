<?php
/**
 * Classe base para todos os models
 * 
 * Fornece métodos comuns e funcionalidades para todos os models
 * 
 * @package WPFramework\Models
 */

namespace WPFramework\Models;

abstract class BaseModel
{
    /**
     * Nome da tabela no banco de dados
     * 
     * @var string
     */
    protected $table;
    
    /**
     * Chave primária da tabela
     * 
     * @var string
     */
    protected $primaryKey = 'id';
    
    /**
     * Indica se o model usa timestamps
     * 
     * @var bool
     */
    protected $timestamps = true;
    
    /**
     * Campos permitidos para atribuição em massa
     * 
     * @var array
     */
    protected $fillable = [];
    
    /**
     * Campos protegidos contra atribuição em massa
     * 
     * @var array
     */
    protected $guarded = ['id'];
    
    /**
     * Instância do wpdb
     * 
     * @var \wpdb
     */
    protected $db;
    
    /**
     * Construtor
     */
    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        
        // Define o nome da tabela se não estiver definido
        if (empty($this->table)) {
            $this->table = $wpdb->prefix . strtolower(basename(str_replace('\\', '/', get_class($this))));
        }
    }
    
    /**
     * Encontra um registro pelo ID
     * 
     * @param int $id ID do registro
     * @return object|null
     */
    public function find($id)
    {
        $query = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = %d", $id);
        $result = $this->db->get_row($query);
        
        return $result;
    }
    
    /**
     * Obtém todos os registros
     * 
     * @param string $orderBy Campo para ordenação
     * @param string $order Direção da ordenação (ASC ou DESC)
     * @return array
     */
    public function all($orderBy = null, $order = 'ASC')
    {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy} {$order}";
        }
        
        return $this->db->get_results($sql);
    }
    
    /**
     * Cria um novo registro
     * 
     * @param array $data Dados do registro
     * @return int|false ID do registro criado ou false em caso de erro
     */
    public function create($data)
    {
        // Filtra os dados de acordo com os campos permitidos
        $data = $this->filterData($data);
        
        // Adiciona timestamps se necessário
        if ($this->timestamps) {
            $data['created_at'] = current_time('mysql');
            $data['updated_at'] = current_time('mysql');
        }
        
        // Insere o registro
        $result = $this->db->insert($this->table, $data);
        
        if ($result) {
            return $this->db->insert_id;
        }
        
        return false;
    }
    
    /**
     * Atualiza um registro
     * 
     * @param int $id ID do registro
     * @param array $data Dados do registro
     * @return bool
     */
    public function update($id, $data)
    {
        // Filtra os dados de acordo com os campos permitidos
        $data = $this->filterData($data);
        
        // Adiciona timestamp de atualização se necessário
        if ($this->timestamps) {
            $data['updated_at'] = current_time('mysql');
        }
        
        // Atualiza o registro
        $result = $this->db->update(
            $this->table,
            $data,
            [$this->primaryKey => $id]
        );
        
        return $result !== false;
    }
    
    /**
     * Exclui um registro
     * 
     * @param int $id ID do registro
     * @return bool
     */
    public function delete($id)
    {
        $result = $this->db->delete(
            $this->table,
            [$this->primaryKey => $id]
        );
        
        return $result !== false;
    }
    
    /**
     * Filtra os dados de acordo com os campos permitidos
     * 
     * @param array $data Dados a serem filtrados
     * @return array
     */
    protected function filterData($data)
    {
        $filtered = [];
        
        // Se houver campos permitidos definidos
        if (!empty($this->fillable)) {
            foreach ($this->fillable as $field) {
                if (isset($data[$field])) {
                    $filtered[$field] = $data[$field];
                }
            }
        } else {
            // Caso contrário, usa todos os campos exceto os protegidos
            foreach ($data as $field => $value) {
                if (!in_array($field, $this->guarded)) {
                    $filtered[$field] = $value;
                }
            }
        }
        
        return $filtered;
    }
    
    /**
     * Executa uma consulta personalizada
     * 
     * @param string $sql Consulta SQL
     * @param array $args Argumentos para a consulta
     * @return array|object|null
     */
    public function query($sql, $args = [])
    {
        if (!empty($args)) {
            $sql = $this->db->prepare($sql, $args);
        }
        
        return $this->db->get_results($sql);
    }
    
    /**
     * Executa uma consulta personalizada e retorna um único resultado
     * 
     * @param string $sql Consulta SQL
     * @param array $args Argumentos para a consulta
     * @return object|null
     */
    public function queryOne($sql, $args = [])
    {
        if (!empty($args)) {
            $sql = $this->db->prepare($sql, $args);
        }
        
        return $this->db->get_row($sql);
    }
    
    /**
     * Obtém o total de registros
     * 
     * @param string $where Condição WHERE (opcional)
     * @param array $args Argumentos para a condição WHERE
     * @return int
     */
    public function count($where = '', $args = [])
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        
        if (!empty($where)) {
            $sql .= " WHERE {$where}";
        }
        
        if (!empty($args)) {
            $sql = $this->db->prepare($sql, $args);
        }
        
        return (int) $this->db->get_var($sql);
    }
    
    /**
     * Obtém registros com paginação
     * 
     * @param int $page Número da página
     * @param int $perPage Registros por página
     * @param string $orderBy Campo para ordenação
     * @param string $order Direção da ordenação (ASC ou DESC)
     * @return array
     */
    public function paginate($page = 1, $perPage = 10, $orderBy = null, $order = 'ASC')
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table}";
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy} {$order}";
        }
        
        $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        
        $items = $this->db->get_results($sql);
        $total = $this->count();
        
        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Obtém registros com base em condições
     * 
     * @param array $conditions Condições para a consulta
     * @param string $operator Operador para as condições (AND ou OR)
     * @return array
     */
    public function where($conditions, $operator = 'AND')
    {
        $sql = "SELECT * FROM {$this->table} WHERE ";
        $where = [];
        $values = [];
        
        foreach ($conditions as $field => $value) {
            $where[] = "{$field} = %s";
            $values[] = $value;
        }
        
        $sql .= implode(" {$operator} ", $where);
        
        $query = $this->db->prepare($sql, $values);
        
        return $this->db->get_results($query);
    }
    
    /**
     * Obtém o primeiro registro com base em condições
     * 
     * @param array $conditions Condições para a consulta
     * @param string $operator Operador para as condições (AND ou OR)
     * @return object|null
     */
    public function firstWhere($conditions, $operator = 'AND')
    {
        $results = $this->where($conditions, $operator);
        
        return !empty($results) ? $results[0] : null;
    }
}
