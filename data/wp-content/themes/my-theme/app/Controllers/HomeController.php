<?php
/**
 * Classe HomeController
 * 
 * Controller para a página inicial e páginas estáticas
 * 
 * @package WPFramework\Controllers
 */

namespace WPFramework\Controllers;

class HomeController extends BaseController
{
    /**
     * Construtor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Página inicial
     * 
     * @return void
     */
    public function index()
    {
        // Obtém os posts mais recentes
        $args = [
            'post_type' => 'post',
            'posts_per_page' => 5,
            'post_status' => 'publish'
        ];
        
        $query = new \WP_Query($args);
        $posts = $query->posts;
        
        // Renderiza a view
        $this->view('home.index', [
            'title' => 'Página Inicial',
            'posts' => $posts
        ]);
    }

    /**
     * Página sobre
     * 
     * @return void
     */
    public function about()
    {
        // Renderiza a view
        $this->view('home.about', [
            'title' => 'Sobre Nós'
        ]);
    }

    /**
     * Página de contato
     * 
     * @return void
     */
    public function contact()
    {
        $errors = [];
        $success = false;
        
        // Processa o formulário de contato
        if ($this->isPost()) {
            // Obtém os dados do formulário
            $name = $this->input('name');
            $email = $this->input('email');
            $message = $this->input('message');
            
            // Valida os dados
            $errors = $this->validate([
                'name' => $name,
                'email' => $email,
                'message' => $message
            ], [
                'name' => 'required|min:3',
                'email' => 'required|email',
                'message' => 'required|min:10'
            ]);
            
            // Se não houver erros, envia o e-mail
            if (empty($errors)) {
                $to = get_option('admin_email');
                $subject = 'Mensagem de contato de ' . $name;
                $body = "Nome: {$name}\n\nE-mail: {$email}\n\nMensagem: {$message}";
                $headers = ['Content-Type: text/plain; charset=UTF-8'];
                
                $sent = wp_mail($to, $subject, $body, $headers);
                
                if ($sent) {
                    $success = true;
                } else {
                    $errors['global'] = ['Não foi possível enviar a mensagem. Tente novamente mais tarde.'];
                }
            }
        }
        
        // Renderiza a view
        $this->view('home.contact', [
            'title' => 'Contato',
            'errors' => $errors,
            'success' => $success
        ]);
    }

    /**
     * Página 404
     * 
     * @return void
     */
    public function notFound()
    {
        // Define o status HTTP 404
        status_header(404);
        
        // Renderiza a view
        $this->view('errors.404', [
            'title' => 'Página não encontrada'
        ]);
    }
}
