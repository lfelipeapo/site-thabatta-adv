<?php
/**
 * Integração com o Jetpack para cache e otimização
 *
 * @package Thabatta_Advocacia
 */

if (!defined('ABSPATH')) {
    exit; // Saída direta se acessado diretamente
}

/**
 * Classe para gerenciar a integração com o Jetpack
 */
class Thabatta_Jetpack_Integration
{
    /**
     * Inicializa a classe
     */
    public function __construct()
    {
        // Adicionar menu de administração
        add_action('admin_menu', array($this, 'add_admin_menu'));

        // Adicionar configurações
        add_action('admin_init', array($this, 'register_settings'));

        // Adicionar ações AJAX
        add_action('wp_ajax_thabatta_clear_cache', array($this, 'ajax_clear_cache'));
        add_action('wp_ajax_thabatta_toggle_cache', array($this, 'ajax_toggle_cache'));

        // Adicionar scripts e estilos de administração
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        // Adicionar metabox para configurações de cache por página
        add_action('add_meta_boxes', array($this, 'add_cache_meta_box'));
        add_action('save_post', array($this, 'save_cache_meta_box'));
    }

    /**
     * Adicionar menu de administração
     */
    public function add_admin_menu()
    {
        add_menu_page(
            __('Otimização', 'thabatta-adv'),
            __('Otimização', 'thabatta-adv'),
            'manage_options',
            'thabatta-optimization',
            array($this, 'render_admin_page'),
            'dashicons-performance',
            80
        );
    }

    /**
     * Registrar configurações
     */
    public function register_settings()
    {
        register_setting('thabatta_jetpack_options', 'thabatta_jetpack_cache_enabled');
        register_setting('thabatta_jetpack_options', 'thabatta_jetpack_cache_expiry');
        register_setting('thabatta_jetpack_options', 'thabatta_jetpack_lazy_images');
        register_setting('thabatta_jetpack_options', 'thabatta_jetpack_cdn');
        register_setting('thabatta_jetpack_options', 'thabatta_jetpack_optimize_css');
        register_setting('thabatta_jetpack_options', 'thabatta_jetpack_optimize_js');
    }

    /**
     * Renderizar página de administração
     */
    public function render_admin_page()
    {
        // Verificar se o Jetpack está ativo
        if (!class_exists('Jetpack')) {
            $this->render_jetpack_not_active_notice();
            return;
        }

        // Verificar se o módulo Jetpack Boost está ativo
        $boost_active = Jetpack::is_module_active('boost');

        // Obter configurações
        $cache_enabled = get_option('thabatta_jetpack_cache_enabled', false);
        $cache_expiry = get_option('thabatta_jetpack_cache_expiry', 3600);
        $lazy_images = get_option('thabatta_jetpack_lazy_images', true);
        $cdn_enabled = get_option('thabatta_jetpack_cdn', false);
        $optimize_css = get_option('thabatta_jetpack_optimize_css', true);
        $optimize_js = get_option('thabatta_jetpack_optimize_js', true);

        // Renderizar página
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Otimização do Site', 'thabatta-adv'); ?></h1>
            
            <div class="notice notice-info">
                <p><?php esc_html_e('Esta página permite gerenciar as configurações de otimização do site usando recursos do Jetpack.', 'thabatta-adv'); ?></p>
            </div>
            
            <?php if (!$boost_active) : ?>
                <div class="notice notice-warning">
                    <p><?php esc_html_e('O módulo Jetpack Boost não está ativo. Algumas funcionalidades podem não estar disponíveis.', 'thabatta-adv'); ?></p>
                    <p><a href="<?php echo esc_url(admin_url('admin.php?page=jetpack_modules')); ?>" class="button"><?php esc_html_e('Ativar Módulos do Jetpack', 'thabatta-adv'); ?></a></p>
                </div>
            <?php endif; ?>
            
            <div class="thabatta-admin-columns">
                <div class="thabatta-admin-column">
                    <div class="thabatta-admin-card">
                        <h2><?php esc_html_e('Gerenciamento de Cache', 'thabatta-adv'); ?></h2>
                        
                        <form method="post" action="options.php">
                            <?php settings_fields('thabatta_jetpack_options'); ?>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><?php esc_html_e('Ativar Cache', 'thabatta-adv'); ?></th>
                                    <td>
                                        <label for="thabatta_jetpack_cache_enabled">
                                            <input type="checkbox" name="thabatta_jetpack_cache_enabled" id="thabatta_jetpack_cache_enabled" value="1" <?php checked($cache_enabled, true); ?>>
                                            <?php esc_html_e('Ativar cache de página para melhorar o desempenho', 'thabatta-adv'); ?>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e('Tempo de Expiração do Cache', 'thabatta-adv'); ?></th>
                                    <td>
                                        <select name="thabatta_jetpack_cache_expiry" id="thabatta_jetpack_cache_expiry">
                                            <option value="3600" <?php selected($cache_expiry, 3600); ?>><?php esc_html_e('1 hora', 'thabatta-adv'); ?></option>
                                            <option value="21600" <?php selected($cache_expiry, 21600); ?>><?php esc_html_e('6 horas', 'thabatta-adv'); ?></option>
                                            <option value="43200" <?php selected($cache_expiry, 43200); ?>><?php esc_html_e('12 horas', 'thabatta-adv'); ?></option>
                                            <option value="86400" <?php selected($cache_expiry, 86400); ?>><?php esc_html_e('1 dia', 'thabatta-adv'); ?></option>
                                            <option value="604800" <?php selected($cache_expiry, 604800); ?>><?php esc_html_e('1 semana', 'thabatta-adv'); ?></option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                            
                            <p class="submit">
                                <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Salvar Configurações', 'thabatta-adv'); ?>">
                                <button type="button" id="thabatta-clear-cache" class="button"><?php esc_html_e('Limpar Cache Agora', 'thabatta-adv'); ?></button>
                            </p>
                        </form>
                    </div>
                    
                    <div class="thabatta-admin-card">
                        <h2><?php esc_html_e('Otimização de Imagens', 'thabatta-adv'); ?></h2>
                        
                        <form method="post" action="options.php">
                            <?php settings_fields('thabatta_jetpack_options'); ?>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><?php esc_html_e('Lazy Loading de Imagens', 'thabatta-adv'); ?></th>
                                    <td>
                                        <label for="thabatta_jetpack_lazy_images">
                                            <input type="checkbox" name="thabatta_jetpack_lazy_images" id="thabatta_jetpack_lazy_images" value="1" <?php checked($lazy_images, true); ?>>
                                            <?php esc_html_e('Ativar carregamento preguiçoso de imagens', 'thabatta-adv'); ?>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e('Rede de Distribuição de Conteúdo (CDN)', 'thabatta-adv'); ?></th>
                                    <td>
                                        <label for="thabatta_jetpack_cdn">
                                            <input type="checkbox" name="thabatta_jetpack_cdn" id="thabatta_jetpack_cdn" value="1" <?php checked($cdn_enabled, true); ?>>
                                            <?php esc_html_e('Usar CDN do Jetpack para imagens', 'thabatta-adv'); ?>
                                        </label>
                                    </td>
                                </tr>
                            </table>
                            
                            <p class="submit">
                                <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Salvar Configurações', 'thabatta-adv'); ?>">
                            </p>
                        </form>
                    </div>
                </div>
                
                <div class="thabatta-admin-column">
                    <div class="thabatta-admin-card">
                        <h2><?php esc_html_e('Otimização de Recursos', 'thabatta-adv'); ?></h2>
                        
                        <form method="post" action="options.php">
                            <?php settings_fields('thabatta_jetpack_options'); ?>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><?php esc_html_e('Otimização de CSS', 'thabatta-adv'); ?></th>
                                    <td>
                                        <label for="thabatta_jetpack_optimize_css">
                                            <input type="checkbox" name="thabatta_jetpack_optimize_css" id="thabatta_jetpack_optimize_css" value="1" <?php checked($optimize_css, true); ?>>
                                            <?php esc_html_e('Minificar e combinar arquivos CSS', 'thabatta-adv'); ?>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e('Otimização de JavaScript', 'thabatta-adv'); ?></th>
                                    <td>
                                        <label for="thabatta_jetpack_optimize_js">
                                            <input type="checkbox" name="thabatta_jetpack_optimize_js" id="thabatta_jetpack_optimize_js" value="1" <?php checked($optimize_js, true); ?>>
                                            <?php esc_html_e('Minificar e combinar arquivos JavaScript', 'thabatta-adv'); ?>
                                        </label>
                                    </td>
                                </tr>
                            </table>
                            
                            <p class="submit">
                                <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Salvar Configurações', 'thabatta-adv'); ?>">
                            </p>
                        </form>
                    </div>
                    
                    <div class="thabatta-admin-card">
                        <h2><?php esc_html_e('Status do Sistema', 'thabatta-adv'); ?></h2>
                        
                        <table class="widefat">
                            <tbody>
                                <tr>
                                    <td><?php esc_html_e('Status do Cache:', 'thabatta-adv'); ?></td>
                                    <td>
                                        <?php if ($cache_enabled) : ?>
                                            <span class="thabatta-status-active"><?php esc_html_e('Ativo', 'thabatta-adv'); ?></span>
                                        <?php else : ?>
                                            <span class="thabatta-status-inactive"><?php esc_html_e('Inativo', 'thabatta-adv'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php esc_html_e('Tempo de Expiração:', 'thabatta-adv'); ?></td>
                                    <td>
                                        <?php
                                        switch ($cache_expiry) {
                                            case 3600:
                                                esc_html_e('1 hora', 'thabatta-adv');
                                                break;
                                            case 21600:
                                                esc_html_e('6 horas', 'thabatta-adv');
                                                break;
                                            case 43200:
                                                esc_html_e('12 horas', 'thabatta-adv');
                                                break;
                                            case 86400:
                                                esc_html_e('1 dia', 'thabatta-adv');
                                                break;
                                            case 604800:
                                                esc_html_e('1 semana', 'thabatta-adv');
                                                break;
                                            default:
                                                echo esc_html(human_time_diff(0, $cache_expiry));
                                        }
        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php esc_html_e('Lazy Loading de Imagens:', 'thabatta-adv'); ?></td>
                                    <td>
                                        <?php if ($lazy_images) : ?>
                                            <span class="thabatta-status-active"><?php esc_html_e('Ativo', 'thabatta-adv'); ?></span>
                                        <?php else : ?>
                                            <span class="thabatta-status-inactive"><?php esc_html_e('Inativo', 'thabatta-adv'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php esc_html_e('CDN:', 'thabatta-adv'); ?></td>
                                    <td>
                                        <?php if ($cdn_enabled) : ?>
                                            <span class="thabatta-status-active"><?php esc_html_e('Ativo', 'thabatta-adv'); ?></span>
                                        <?php else : ?>
                                            <span class="thabatta-status-inactive"><?php esc_html_e('Inativo', 'thabatta-adv'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php esc_html_e('Otimização de CSS:', 'thabatta-adv'); ?></td>
                                    <td>
                                        <?php if ($optimize_css) : ?>
                                            <span class="thabatta-status-active"><?php esc_html_e('Ativo', 'thabatta-adv'); ?></span>
                                        <?php else : ?>
                                            <span class="thabatta-status-inactive"><?php esc_html_e('Inativo', 'thabatta-adv'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php esc_html_e('Otimização de JavaScript:', 'thabatta-adv'); ?></td>
                                    <td>
                                        <?php if ($optimize_js) : ?>
                                            <span class="thabatta-status-active"><?php esc_html_e('Ativo', 'thabatta-adv'); ?></span>
                                        <?php else : ?>
                                            <span class="thabatta-status-inactive"><?php esc_html_e('Inativo', 'thabatta-adv'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="thabatta-cache-stats">
                            <h3><?php esc_html_e('Estatísticas de Cache', 'thabatta-adv'); ?></h3>
                            <?php
                            // Obter estatísticas de cache
                            $cache_hits = get_option('thabatta_cache_hits', 0);
        $cache_misses = get_option('thabatta_cache_misses', 0);
        $total_requests = $cache_hits + $cache_misses;
        $hit_ratio = $total_requests > 0 ? round(($cache_hits / $total_requests) * 100, 2) : 0;
        ?>
                            <div class="thabatta-cache-stats-grid">
                                <div class="thabatta-cache-stat">
                                    <span class="thabatta-cache-stat-number"><?php echo esc_html($cache_hits); ?></span>
                                    <span class="thabatta-cache-stat-label"><?php esc_html_e('Cache Hits', 'thabatta-adv'); ?></span>
                                </div>
                                <div class="thabatta-cache-stat">
                                    <span class="thabatta-cache-stat-number"><?php echo esc_html($cache_misses); ?></span>
                                    <span class="thabatta-cache-stat-label"><?php esc_html_e('Cache Misses', 'thabatta-adv'); ?></span>
                                </div>
                                <div class="thabatta-cache-stat">
                                    <span class="thabatta-cache-stat-number"><?php echo esc_html($hit_ratio); ?>%</span>
                                    <span class="thabatta-cache-stat-label"><?php esc_html_e('Hit Ratio', 'thabatta-adv'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Renderizar aviso de Jetpack não ativo
     */
    public function render_jetpack_not_active_notice()
    {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Otimização do Site', 'thabatta-adv'); ?></h1>
            
            <div class="notice notice-error">
                <p><?php esc_html_e('O plugin Jetpack não está ativo. Por favor, instale e ative o Jetpack para usar os recursos de otimização.', 'thabatta-adv'); ?></p>
                <p>
                    <a href="<?php echo esc_url(admin_url('plugin-install.php?s=jetpack&tab=search&type=term')); ?>" class="button button-primary">
                        <?php esc_html_e('Instalar Jetpack', 'thabatta-adv'); ?>
                    </a>
                </p>
            </div>
        </div>
        <?php
    }

    /**
     * Adicionar scripts e estilos de administração
     */
    public function enqueue_admin_scripts($hook)
    {
        // Verificar se estamos na página de otimização
        if ($hook === 'toplevel_page_thabatta-optimization') {
            // Adicionar estilos
            wp_enqueue_style('thabatta-admin-style', get_template_directory_uri() . '/assets/css/admin.css', array(), '1.0.0');

            // Adicionar scripts
            wp_enqueue_script('thabatta-admin-script', get_template_directory_uri() . '/assets/js/admin.js', array('jquery'), '1.0.0', true);

            // Passar variáveis para o script
            wp_localize_script('thabatta-admin-script', 'thabattaAdmin', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('thabatta_admin_nonce'),
                'clearCacheText' => __('Limpar Cache', 'thabatta-adv'),
                'clearingCacheText' => __('Limpando...', 'thabatta-adv'),
                'cacheClearedText' => __('Cache Limpo!', 'thabatta-adv'),
                'errorText' => __('Erro ao processar a solicitação.', 'thabatta-adv')
            ));
        }
    }

    /**
     * Adicionar metabox para configurações de cache por página
     */
    public function add_cache_meta_box()
    {
        add_meta_box(
            'thabatta_cache_settings',
            __('Configurações de Cache', 'thabatta-adv'),
            array($this, 'render_cache_meta_box'),
            array('post', 'page', 'area_atuacao'),
            'side',
            'default'
        );
    }

    /**
     * Renderizar metabox de configurações de cache
     */
    public function render_cache_meta_box($post)
    {
        wp_nonce_field('thabatta_cache_nonce', 'thabatta_cache_nonce');

        // Obter configurações de cache para este post
        $exclude_from_cache = get_post_meta($post->ID, '_thabatta_exclude_from_cache', true);
        $custom_cache_expiry = get_post_meta($post->ID, '_thabatta_custom_cache_expiry', true);

        ?>
        <p>
            <label for="thabatta_exclude_from_cache">
                <input type="checkbox" name="thabatta_exclude_from_cache" id="thabatta_exclude_from_cache" value="1" <?php checked($exclude_from_cache, '1'); ?>>
                <?php esc_html_e('Excluir esta página do cache', 'thabatta-adv'); ?>
            </label>
        </p>
        
        <p>
            <label for="thabatta_custom_cache_expiry"><?php esc_html_e('Tempo de expiração personalizado:', 'thabatta-adv'); ?></label><br>
            <select name="thabatta_custom_cache_expiry" id="thabatta_custom_cache_expiry">
                <option value="" <?php selected($custom_cache_expiry, ''); ?>><?php esc_html_e('Usar configuração global', 'thabatta-adv'); ?></option>
                <option value="3600" <?php selected($custom_cache_expiry, '3600'); ?>><?php esc_html_e('1 hora', 'thabatta-adv'); ?></option>
                <option value="21600" <?php selected($custom_cache_expiry, '21600'); ?>><?php esc_html_e('6 horas', 'thabatta-adv'); ?></option>
                <option value="43200" <?php selected($custom_cache_expiry, '43200'); ?>><?php esc_html_e('12 horas', 'thabatta-adv'); ?></option>
                <option value="86400" <?php selected($custom_cache_expiry, '86400'); ?>><?php esc_html_e('1 dia', 'thabatta-adv'); ?></option>
                <option value="604800" <?php selected($custom_cache_expiry, '604800'); ?>><?php esc_html_e('1 semana', 'thabatta-adv'); ?></option>
            </select>
        </p>
        
        <p>
            <button type="button" id="thabatta-clear-page-cache" class="button" data-post-id="<?php echo esc_attr($post->ID); ?>">
                <?php esc_html_e('Limpar Cache Desta Página', 'thabatta-adv'); ?>
            </button>
        </p>
        <?php
    }

    /**
     * Salvar dados da metabox de cache
     */
    public function save_cache_meta_box($post_id)
    {
        // Verificar se é um salvamento automático
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Verificar nonce
        if (!isset($_POST['thabatta_cache_nonce']) || !wp_verify_nonce($_POST['thabatta_cache_nonce'], 'thabatta_cache_nonce')) {
            return;
        }

        // Verificar permissões
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Salvar configurações de cache
        $exclude_from_cache = isset($_POST['thabatta_exclude_from_cache']) ? '1' : '';
        update_post_meta($post_id, '_thabatta_exclude_from_cache', $exclude_from_cache);

        if (isset($_POST['thabatta_custom_cache_expiry'])) {
            update_post_meta($post_id, '_thabatta_custom_cache_expiry', sanitize_text_field($_POST['thabatta_custom_cache_expiry']));
        }

        // Limpar cache para este post
        $this->clear_cache_for_post($post_id);
    }

    /**
     * Limpar cache para um post específico
     */
    public function clear_cache_for_post($post_id)
    {
        // Verificar se o Jetpack está ativo
        if (!class_exists('Jetpack')) {
            return;
        }

        // Obter URL do post
        $post_url = get_permalink($post_id);

        // Limpar cache para esta URL
        if (function_exists('jetpack_page_cache_invalidate_url')) {
            jetpack_page_cache_invalidate_url($post_url);
        }

        // Registrar a limpeza
        $this->log_cache_clear('post', $post_id);
    }

    /**
     * Limpar todo o cache
     */
    public function clear_all_cache()
    {
        // Verificar se o Jetpack está ativo
        if (!class_exists('Jetpack')) {
            return false;
        }

        // Limpar todo o cache
        if (function_exists('jetpack_page_cache_invalidate_all')) {
            jetpack_page_cache_invalidate_all();

            // Registrar a limpeza
            $this->log_cache_clear('all');

            return true;
        }

        return false;
    }

    /**
     * Registrar limpeza de cache
     */
    public function log_cache_clear($type, $post_id = null)
    {
        $log = get_option('thabatta_cache_clear_log', array());

        $entry = array(
            'time' => current_time('timestamp'),
            'type' => $type
        );

        if ($post_id) {
            $entry['post_id'] = $post_id;
            $entry['post_title'] = get_the_title($post_id);
        }

        // Adicionar entrada ao log
        array_unshift($log, $entry);

        // Limitar o log a 50 entradas
        if (count($log) > 50) {
            $log = array_slice($log, 0, 50);
        }

        update_option('thabatta_cache_clear_log', $log);
    }

    /**
     * Manipular solicitação AJAX para limpar cache
     */
    public function ajax_clear_cache()
    {
        // Verificar nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'thabatta_admin_nonce')) {
            wp_send_json_error(array('message' => __('Erro de segurança. Por favor, recarregue a página.', 'thabatta-adv')));
        }

        // Verificar permissões
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Você não tem permissão para realizar esta ação.', 'thabatta-adv')));
        }

        // Verificar tipo de limpeza
        $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'all';

        if ($type === 'post' && isset($_POST['post_id'])) {
            // Limpar cache para um post específico
            $post_id = intval($_POST['post_id']);
            $this->clear_cache_for_post($post_id);
            wp_send_json_success(array('message' => __('Cache limpo para esta página.', 'thabatta-adv')));
        } else {
            // Limpar todo o cache
            $result = $this->clear_all_cache();

            if ($result) {
                wp_send_json_success(array('message' => __('Cache limpo com sucesso.', 'thabatta-adv')));
            } else {
                wp_send_json_error(array('message' => __('Erro ao limpar o cache. Verifique se o Jetpack está ativo.', 'thabatta-adv')));
            }
        }
    }

    /**
     * Manipular solicitação AJAX para ativar/desativar cache
     */
    public function ajax_toggle_cache()
    {
        // Verificar nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'thabatta_admin_nonce')) {
            wp_send_json_error(array('message' => __('Erro de segurança. Por favor, recarregue a página.', 'thabatta-adv')));
        }

        // Verificar permissões
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Você não tem permissão para realizar esta ação.', 'thabatta-adv')));
        }

        // Obter estado atual
        $current_state = get_option('thabatta_jetpack_cache_enabled', false);

        // Alternar estado
        $new_state = !$current_state;
        update_option('thabatta_jetpack_cache_enabled', $new_state);

        // Responder com novo estado
        wp_send_json_success(array(
            'enabled' => $new_state,
            'message' => $new_state ? __('Cache ativado com sucesso.', 'thabatta-adv') : __('Cache desativado com sucesso.', 'thabatta-adv')
        ));
    }

    /**
     * Aplicar configurações de cache
     */
    public function apply_cache_settings()
    {
        // Verificar se o Jetpack está ativo
        if (!class_exists('Jetpack')) {
            return;
        }

        // Obter configurações
        $cache_enabled = get_option('thabatta_jetpack_cache_enabled', false);
        $lazy_images = get_option('thabatta_jetpack_lazy_images', true);
        $cdn_enabled = get_option('thabatta_jetpack_cdn', false);
        $optimize_css = get_option('thabatta_jetpack_optimize_css', true);
        $optimize_js = get_option('thabatta_jetpack_optimize_js', true);

        // Aplicar configurações
        if ($cache_enabled) {
            add_filter('jetpack_page_cache_enabled', '__return_true');
        } else {
            add_filter('jetpack_page_cache_enabled', '__return_false');
        }

        if ($lazy_images) {
            add_filter('jetpack_lazy_images_enabled', '__return_true');
        } else {
            add_filter('jetpack_lazy_images_enabled', '__return_false');
        }

        if ($cdn_enabled) {
            add_filter('jetpack_photon_enabled', '__return_true');
        } else {
            add_filter('jetpack_photon_enabled', '__return_false');
        }

        // Aplicar otimização de CSS e JS
        if ($optimize_css) {
            add_filter('jetpack_boost_optimize_css', '__return_true');
        } else {
            add_filter('jetpack_boost_optimize_css', '__return_false');
        }

        if ($optimize_js) {
            add_filter('jetpack_boost_optimize_js', '__return_true');
        } else {
            add_filter('jetpack_boost_optimize_js', '__return_false');
        }

        // Aplicar configurações de cache por página
        add_filter('jetpack_page_cache_should_cache_request', array($this, 'filter_cache_request'), 10, 1);
    }

    /**
     * Filtrar solicitações de cache
     */
    public function filter_cache_request($should_cache)
    {
        // Não cachear páginas de administração ou login
        if (is_admin() || is_user_logged_in()) {
            return false;
        }

        // Verificar se estamos em um post ou página
        if (is_singular()) {
            global $post;

            // Verificar se este post deve ser excluído do cache
            $exclude_from_cache = get_post_meta($post->ID, '_thabatta_exclude_from_cache', true);
            if ($exclude_from_cache === '1') {
                return false;
            }
        }

        return $should_cache;
    }

    /**
     * Obter tempo de expiração do cache para a página atual
     */
    public function get_cache_expiry_for_current_page()
    {
        // Obter tempo de expiração global
        $global_expiry = get_option('thabatta_jetpack_cache_expiry', 3600);

        // Verificar se estamos em um post ou página
        if (is_singular()) {
            global $post;

            // Verificar se este post tem um tempo de expiração personalizado
            $custom_expiry = get_post_meta($post->ID, '_thabatta_custom_cache_expiry', true);
            if (!empty($custom_expiry)) {
                return intval($custom_expiry);
            }
        }

        return $global_expiry;
    }

    /**
     * Registrar hit de cache
     */
    public function register_cache_hit()
    {
        $hits = get_option('thabatta_cache_hits', 0);
        update_option('thabatta_cache_hits', $hits + 1);
    }

    /**
     * Registrar miss de cache
     */
    public function register_cache_miss()
    {
        $misses = get_option('thabatta_cache_misses', 0);
        update_option('thabatta_cache_misses', $misses + 1);
    }
}

// Inicializar a classe
$thabatta_jetpack_integration = new Thabatta_Jetpack_Integration();

// Aplicar configurações de cache
add_action('init', array($thabatta_jetpack_integration, 'apply_cache_settings'));

// Registrar hits e misses de cache
add_action('jetpack_page_cache_hit', array($thabatta_jetpack_integration, 'register_cache_hit'));
add_action('jetpack_page_cache_miss', array($thabatta_jetpack_integration, 'register_cache_miss'));

