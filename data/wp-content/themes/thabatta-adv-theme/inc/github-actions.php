<?php
/**
 * GitHub Actions Workflow para deploy no Infinity Free
 *
 * @package Thabatta_Advocacia
 */

if (!defined('ABSPATH')) {
    exit; // Saída direta se acessado diretamente
}

/**
 * Classe para gerenciar o GitHub Actions Workflow
 */
class Thabatta_GitHub_Actions {
    /**
     * Inicializa a classe
     */
    public function __construct() {
        // Adicionar página de administração
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Registrar configurações
        add_action('admin_init', array($this, 'register_settings'));
        
        // Adicionar ação AJAX para gerar arquivo de workflow
        add_action('wp_ajax_thabatta_generate_workflow', array($this, 'generate_workflow_file'));
    }
    
    /**
     * Adicionar página de administração
     */
    public function add_admin_menu() {
        add_submenu_page(
            'options-general.php',
            __('GitHub Actions Workflow', 'thabatta-adv'),
            __('GitHub Actions', 'thabatta-adv'),
            'manage_options',
            'thabatta-github-actions',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Registrar configurações
     */
    public function register_settings() {
        register_setting('thabatta_github_actions', 'thabatta_github_repo');
        register_setting('thabatta_github_actions', 'thabatta_github_branch');
        register_setting('thabatta_github_actions', 'thabatta_infinity_free_username');
        register_setting('thabatta_github_actions', 'thabatta_infinity_free_password');
        register_setting('thabatta_github_actions', 'thabatta_infinity_free_hostname');
    }
    
    /**
     * Renderizar página de administração
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('GitHub Actions Workflow', 'thabatta-adv'); ?></h1>
            
            <p><?php echo esc_html__('Configure o GitHub Actions Workflow para deploy automático no Infinity Free.', 'thabatta-adv'); ?></p>
            
            <form method="post" action="options.php">
                <?php settings_fields('thabatta_github_actions'); ?>
                <?php do_settings_sections('thabatta_github_actions'); ?>
                
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html__('Repositório GitHub', 'thabatta-adv'); ?></th>
                        <td>
                            <input type="text" name="thabatta_github_repo" value="<?php echo esc_attr(get_option('thabatta_github_repo')); ?>" class="regular-text" placeholder="username/repository" />
                            <p class="description"><?php echo esc_html__('Nome do repositório no formato username/repository', 'thabatta-adv'); ?></p>
                        </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html__('Branch', 'thabatta-adv'); ?></th>
                        <td>
                            <input type="text" name="thabatta_github_branch" value="<?php echo esc_attr(get_option('thabatta_github_branch', 'main')); ?>" class="regular-text" placeholder="main" />
                            <p class="description"><?php echo esc_html__('Branch que será usada para deploy (geralmente main ou master)', 'thabatta-adv'); ?></p>
                        </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html__('Usuário Infinity Free', 'thabatta-adv'); ?></th>
                        <td>
                            <input type="text" name="thabatta_infinity_free_username" value="<?php echo esc_attr(get_option('thabatta_infinity_free_username')); ?>" class="regular-text" />
                            <p class="description"><?php echo esc_html__('Nome de usuário FTP do Infinity Free', 'thabatta-adv'); ?></p>
                        </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html__('Senha Infinity Free', 'thabatta-adv'); ?></th>
                        <td>
                            <input type="password" name="thabatta_infinity_free_password" value="<?php echo esc_attr(get_option('thabatta_infinity_free_password')); ?>" class="regular-text" />
                            <p class="description"><?php echo esc_html__('Senha FTP do Infinity Free (será armazenada como segredo no GitHub)', 'thabatta-adv'); ?></p>
                        </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html__('Hostname Infinity Free', 'thabatta-adv'); ?></th>
                        <td>
                            <input type="text" name="thabatta_infinity_free_hostname" value="<?php echo esc_attr(get_option('thabatta_infinity_free_hostname')); ?>" class="regular-text" placeholder="ftpupload.net" />
                            <p class="description"><?php echo esc_html__('Hostname FTP do Infinity Free (geralmente ftpupload.net)', 'thabatta-adv'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <hr>
            
            <h2><?php echo esc_html__('Gerar Arquivo de Workflow', 'thabatta-adv'); ?></h2>
            
            <p><?php echo esc_html__('Clique no botão abaixo para gerar o arquivo de workflow do GitHub Actions. Você precisará adicionar este arquivo ao seu repositório no caminho .github/workflows/deploy.yml.', 'thabatta-adv'); ?></p>
            
            <button id="generate-workflow" class="button button-primary"><?php echo esc_html__('Gerar Arquivo de Workflow', 'thabatta-adv'); ?></button>
            
            <div id="workflow-result" style="display: none; margin-top: 20px;">
                <h3><?php echo esc_html__('Arquivo de Workflow', 'thabatta-adv'); ?></h3>
                <p><?php echo esc_html__('Copie o conteúdo abaixo e salve-o no seu repositório como .github/workflows/deploy.yml', 'thabatta-adv'); ?></p>
                <textarea id="workflow-content" style="width: 100%; height: 400px; font-family: monospace;"></textarea>
                <button id="copy-workflow" class="button" style="margin-top: 10px;"><?php echo esc_html__('Copiar para Área de Transferência', 'thabatta-adv'); ?></button>
            </div>
            
            <script>
            jQuery(document).ready(function($) {
                $('#generate-workflow').on('click', function() {
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'thabatta_generate_workflow',
                            nonce: '<?php echo wp_create_nonce('thabatta_generate_workflow'); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#workflow-content').val(response.data.workflow);
                                $('#workflow-result').show();
                            } else {
                                alert(response.data.message);
                            }
                        },
                        error: function() {
                            alert('<?php echo esc_js(__('Ocorreu um erro ao gerar o arquivo de workflow.', 'thabatta-adv')); ?>');
                        }
                    });
                });
                
                $('#copy-workflow').on('click', function() {
                    $('#workflow-content').select();
                    document.execCommand('copy');
                    alert('<?php echo esc_js(__('Conteúdo copiado para a área de transferência!', 'thabatta-adv')); ?>');
                });
            });
            </script>
        </div>
        <?php
    }
    
    /**
     * Gerar arquivo de workflow
     */
    public function generate_workflow_file() {
        // Verificar nonce
        check_ajax_referer('thabatta_generate_workflow', 'nonce');
        
        // Verificar permissões
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array(
                'message' => __('Você não tem permissão para realizar esta ação.', 'thabatta-adv')
            ));
        }
        
        // Obter configurações
        $repo = get_option('thabatta_github_repo');
        $branch = get_option('thabatta_github_branch', 'main');
        $username = get_option('thabatta_infinity_free_username');
        $hostname = get_option('thabatta_infinity_free_hostname', 'ftpupload.net');
        
        // Verificar se todas as configurações necessárias foram preenchidas
        if (empty($repo) || empty($branch) || empty($username) || empty($hostname)) {
            wp_send_json_error(array(
                'message' => __('Preencha todas as configurações antes de gerar o arquivo de workflow.', 'thabatta-adv')
            ));
        }
        
        // Gerar conteúdo do arquivo de workflow
        $workflow = <<<YAML
name: Deploy to Infinity Free

on:
  push:
    branches: [ $branch ]
  workflow_dispatch:

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '7.4'
          extensions: mbstring, intl, gd, xml, zip
          tools: composer:v2
      
      - name: Install dependencies
        run: |
          composer install --no-dev --optimize-autoloader
          npm ci
      
      - name: Build assets
        run: |
          npm run build
      
      - name: Deploy to Infinity Free
        uses: SamKirkland/FTP-Deploy-Action@4.3.0
        with:
          server: $hostname
          username: $username
          password: \${{ secrets.INFINITY_FREE_PASSWORD }}
          local-dir: ./
          server-dir: /htdocs/
          exclude: |
            **/.git*
            **/.git*/**
            **/node_modules/**
            **/vendor/**
            **/tests/**
            **/docs/**
            **/.github/**
            **/.vscode/**
            **/.idea/**
            **/package.json
            **/package-lock.json
            **/composer.json
            **/composer.lock
            **/phpunit.xml
            **/README.md
            **/LICENSE
            **/.editorconfig
            **/.gitignore
            **/.travis.yml
            **/gulpfile.js
            **/webpack.config.js
            **/src/**
YAML;
        
        // Enviar resposta
        wp_send_json_success(array(
            'workflow' => $workflow
        ));
    }
    
    /**
     * Gerar arquivo de workflow para o diretório .github/workflows
     */
    public static function generate_workflow_file_for_directory() {
        // Obter configurações
        $repo = get_option('thabatta_github_repo');
        $branch = get_option('thabatta_github_branch', 'main');
        $username = get_option('thabatta_infinity_free_username');
        $hostname = get_option('thabatta_infinity_free_hostname', 'ftpupload.net');
        
        // Verificar se todas as configurações necessárias foram preenchidas
        if (empty($repo) || empty($branch) || empty($username) || empty($hostname)) {
            return false;
        }
        
        // Gerar conteúdo do arquivo de workflow
        $workflow = <<<YAML
name: Deploy to Infinity Free

on:
  push:
    branches: [ $branch ]
  workflow_dispatch:

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '7.4'
          extensions: mbstring, intl, gd, xml, zip
          tools: composer:v2
      
      - name: Install dependencies
        run: |
          composer install --no-dev --optimize-autoloader
          npm ci
      
      - name: Build assets
        run: |
          npm run build
      
      - name: Deploy to Infinity Free
        uses: SamKirkland/FTP-Deploy-Action@4.3.0
        with:
          server: $hostname
          username: $username
          password: \${{ secrets.INFINITY_FREE_PASSWORD }}
          local-dir: ./
          server-dir: /htdocs/
          exclude: |
            **/.git*
            **/.git*/**
            **/node_modules/**
            **/vendor/**
            **/tests/**
            **/docs/**
            **/.github/**
            **/.vscode/**
            **/.idea/**
            **/package.json
            **/package-lock.json
            **/composer.json
            **/composer.lock
            **/phpunit.xml
            **/README.md
            **/LICENSE
            **/.editorconfig
            **/.gitignore
            **/.travis.yml
            **/gulpfile.js
            **/webpack.config.js
            **/src/**
YAML;
        
        // Criar diretório .github/workflows se não existir
        $github_dir = get_template_directory() . '/.github';
        $workflows_dir = $github_dir . '/workflows';
        
        if (!file_exists($github_dir)) {
            mkdir($github_dir, 0755, true);
        }
        
        if (!file_exists($workflows_dir)) {
            mkdir($workflows_dir, 0755, true);
        }
        
        // Salvar arquivo de workflow
        $workflow_file = $workflows_dir . '/deploy.yml';
        file_put_contents($workflow_file, $workflow);
        
        return true;
    }
}

// Inicializar classe de GitHub Actions
new Thabatta_GitHub_Actions();

// Adicionar função para gerar arquivo de workflow
function thabatta_generate_github_workflow() {
    return Thabatta_GitHub_Actions::generate_workflow_file_for_directory();
}
