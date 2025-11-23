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
 * Adicionar página de configurações de cache
 */
function thabatta_add_cache_settings_page()
{
    add_submenu_page(
        'options-general.php',
        __('Configurações de Cache', 'thabatta-adv'),
        __('Cache', 'thabatta-adv'),
        'manage_options',
        'thabatta-cache-settings',
        'thabatta_render_cache_settings_page'
    );
}
add_action('admin_menu', 'thabatta_add_cache_settings_page');

/**
 * Registrar configurações de cache
 */
function thabatta_register_cache_settings()
{
    register_setting('thabatta_cache_settings', 'thabatta_enable_cache');
    register_setting('thabatta_cache_settings', 'thabatta_cache_expiry');
    register_setting('thabatta_cache_settings', 'thabatta_enable_browser_cache');
    register_setting('thabatta_cache_settings', 'thabatta_enable_gzip');

    add_settings_section(
        'thabatta_cache_section',
        __('Configurações de Cache', 'thabatta-adv'),
        'thabatta_cache_section_callback',
        'thabatta-cache-settings'
    );

    add_settings_field(
        'thabatta_enable_cache',
        __('Habilitar Cache', 'thabatta-adv'),
        'thabatta_enable_cache_callback',
        'thabatta-cache-settings',
        'thabatta_cache_section'
    );

    add_settings_field(
        'thabatta_cache_expiry',
        __('Tempo de Expiração do Cache', 'thabatta-adv'),
        'thabatta_cache_expiry_callback',
        'thabatta-cache-settings',
        'thabatta_cache_section'
    );

    add_settings_field(
        'thabatta_enable_browser_cache',
        __('Cache do Navegador', 'thabatta-adv'),
        'thabatta_enable_browser_cache_callback',
        'thabatta-cache-settings',
        'thabatta_cache_section'
    );

    add_settings_field(
        'thabatta_enable_gzip',
        __('Compressão GZIP', 'thabatta-adv'),
        'thabatta_enable_gzip_callback',
        'thabatta-cache-settings',
        'thabatta_cache_section'
    );
}
add_action('admin_init', 'thabatta_register_cache_settings');

/**
 * Callback para a seção de cache
 */
function thabatta_cache_section_callback()
{
    echo '<p>' . esc_html__('Configure as opções de cache para melhorar o desempenho do site.', 'thabatta-adv') . '</p>';
}

/**
 * Callback para o campo de habilitar cache
 */
function thabatta_enable_cache_callback()
{
    $value = get_option('thabatta_enable_cache', 1);
    echo '<input type="checkbox" id="thabatta_enable_cache" name="thabatta_enable_cache" value="1" ' . checked(1, $value, false) . ' />';
    echo '<p class="description">' . esc_html__('Habilita o cache de páginas para melhorar o desempenho.', 'thabatta-adv') . '</p>';
}

/**
 * Callback para o campo de tempo de expiração do cache
 */
function thabatta_cache_expiry_callback()
{
    $value = get_option('thabatta_cache_expiry', 3600);
    ?>
    <select id="thabatta_cache_expiry" name="thabatta_cache_expiry">
        <option value="1800" <?php selected(1800, $value); ?>><?php esc_html_e('30 minutos', 'thabatta-adv'); ?></option>
        <option value="3600" <?php selected(3600, $value); ?>><?php esc_html_e('1 hora', 'thabatta-adv'); ?></option>
        <option value="7200" <?php selected(7200, $value); ?>><?php esc_html_e('2 horas', 'thabatta-adv'); ?></option>
        <option value="21600" <?php selected(21600, $value); ?>><?php esc_html_e('6 horas', 'thabatta-adv'); ?></option>
        <option value="43200" <?php selected(43200, $value); ?>><?php esc_html_e('12 horas', 'thabatta-adv'); ?></option>
        <option value="86400" <?php selected(86400, $value); ?>><?php esc_html_e('1 dia', 'thabatta-adv'); ?></option>
        <option value="604800" <?php selected(604800, $value); ?>><?php esc_html_e('1 semana', 'thabatta-adv'); ?></option>
    </select>
    <p class="description"><?php esc_html_e('Tempo que o cache será mantido antes de ser renovado.', 'thabatta-adv'); ?></p>
    <?php
}

/**
 * Callback para o campo de cache do navegador
 */
function thabatta_enable_browser_cache_callback()
{
    $value = get_option('thabatta_enable_browser_cache', 1);
    echo '<input type="checkbox" id="thabatta_enable_browser_cache" name="thabatta_enable_browser_cache" value="1" ' . checked(1, $value, false) . ' />';
    echo '<p class="description">' . esc_html__('Habilita o cache do navegador para recursos estáticos (CSS, JS, imagens).', 'thabatta-adv') . '</p>';
}

/**
 * Callback para o campo de compressão GZIP
 */
function thabatta_enable_gzip_callback()
{
    $value = get_option('thabatta_enable_gzip', 1);
    echo '<input type="checkbox" id="thabatta_enable_gzip" name="thabatta_enable_gzip" value="1" ' . checked(1, $value, false) . ' />';
    echo '<p class="description">' . esc_html__('Habilita a compressão GZIP para reduzir o tamanho dos arquivos transferidos.', 'thabatta-adv') . '</p>';
}

/**
 * Limpar todo o cache
 */
function thabatta_ajax_clear_all_cache()
{
    // Verificar nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'thabatta_cache_nonce')) {
        wp_send_json_error(__('Erro de segurança. Por favor, atualize a página e tente novamente.', 'thabatta-adv'));
    }

    // Verificar permissões
    if (!current_user_can('manage_options')) {
        wp_send_json_error(__('Você não tem permissão para realizar esta ação.', 'thabatta-adv'));
    }

    // Limpar cache do Jetpack
    if (class_exists('Jetpack') && function_exists('jetpack_page_cache_invalidate_all')) {
        jetpack_page_cache_invalidate_all();
    }

    // Limpar cache do WordPress
    global $wp_object_cache;
    if (is_object($wp_object_cache) && method_exists($wp_object_cache, 'flush')) {
        $wp_object_cache->flush();
    }

    // Limpar cache de transientes
    delete_expired_transients(true);

    wp_send_json_success(__('Cache limpo com sucesso!', 'thabatta-adv'));
}
add_action('wp_ajax_thabatta_clear_all_cache', 'thabatta_ajax_clear_all_cache');

/**
 * Limpar cache de páginas
 */
function thabatta_ajax_clear_page_cache()
{
    // Verificar nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'thabatta_cache_nonce')) {
        wp_send_json_error(__('Erro de segurança. Por favor, atualize a página e tente novamente.', 'thabatta-adv'));
    }

    // Verificar permissões
    if (!current_user_can('manage_options')) {
        wp_send_json_error(__('Você não tem permissão para realizar esta ação.', 'thabatta-adv'));
    }

    // Limpar cache de páginas do Jetpack
    if (class_exists('Jetpack') && function_exists('jetpack_page_cache_invalidate_all')) {
        jetpack_page_cache_invalidate_all();
    }

    wp_send_json_success(__('Cache de páginas limpo com sucesso!', 'thabatta-adv'));
}
add_action('wp_ajax_thabatta_clear_page_cache', 'thabatta_ajax_clear_page_cache');

/**
 * Limpar cache de assets
 */
function thabatta_ajax_clear_assets_cache()
{
    // Verificar nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'thabatta_cache_nonce')) {
        wp_send_json_error(__('Erro de segurança. Por favor, atualize a página e tente novamente.', 'thabatta-adv'));
    }

    // Verificar permissões
    if (!current_user_can('manage_options')) {
        wp_send_json_error(__('Você não tem permissão para realizar esta ação.', 'thabatta-adv'));
    }

    // Limpar cache de assets do Jetpack
    if (class_exists('Jetpack') && function_exists('jetpack_page_cache_invalidate_all')) {
        jetpack_page_cache_invalidate_all();
    }

    wp_send_json_success(__('Cache de assets limpo com sucesso!', 'thabatta-adv'));
}
add_action('wp_ajax_thabatta_clear_assets_cache', 'thabatta_ajax_clear_assets_cache');

/**
 * Exibir aviso de Jetpack não ativo
 */
function render_jetpack_not_active_notice()
{
    ?>
    <div class="notice notice-warning">
        <p><?php esc_html_e('O plugin Jetpack não está ativo. Algumas funcionalidades de otimização não estarão disponíveis.', 'thabatta-adv'); ?></p>
        <p><a href="<?php echo esc_url(admin_url('plugins.php')); ?>" class="button"><?php esc_html_e('Instalar e Ativar Jetpack', 'thabatta-adv'); ?></a></p>
    </div>
    <?php
}

/**
 * Renderizar página de configurações de cache
 */
function thabatta_render_cache_settings_page()
{
    // Verificar permissões
    if (!current_user_can('manage_options')) {
        return;
    }

    // Verificar se o Jetpack está ativo
    $jetpack_active = class_exists('Jetpack');

    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <?php if (!$jetpack_active) : ?>
            <?php render_jetpack_not_active_notice(); ?>
        <?php endif; ?>
        
        <form action="options.php" method="post">
            <?php
            settings_fields('thabatta_cache_settings');
    do_settings_sections('thabatta-cache-settings');
    submit_button();
    ?>
        </form>
        
        <div class="thabatta-cache-actions">
            <h2><?php esc_html_e('Ações de Cache', 'thabatta-adv'); ?></h2>
            <p><?php esc_html_e('Use os botões abaixo para limpar diferentes tipos de cache.', 'thabatta-adv'); ?></p>
            
            <div class="thabatta-cache-buttons">
                <button type="button" id="thabatta-clear-all-cache" class="button button-primary">
                    <?php esc_html_e('Limpar Todo o Cache', 'thabatta-adv'); ?>
                </button>
                
                <button type="button" id="thabatta-clear-page-cache" class="button">
                    <?php esc_html_e('Limpar Cache de Páginas', 'thabatta-adv'); ?>
                </button>
                
                <button type="button" id="thabatta-clear-assets-cache" class="button">
                    <?php esc_html_e('Limpar Cache de Assets', 'thabatta-adv'); ?>
                </button>
            </div>
            
            <div id="thabatta-cache-message" class="hidden"></div>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Limpar todo o cache
        $('#thabatta-clear-all-cache').on('click', function() {
            var button = $(this);
            var originalText = button.text();
            
            button.text('<?php esc_html_e('Limpando...', 'thabatta-adv'); ?>').prop('disabled', true);
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'thabatta_clear_all_cache',
                    nonce: '<?php echo wp_create_nonce('thabatta_cache_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        $('#thabatta-cache-message').removeClass('hidden notice-error').addClass('notice notice-success').html('<p>' + response.data + '</p>').show();
                    } else {
                        $('#thabatta-cache-message').removeClass('hidden notice-success').addClass('notice notice-error').html('<p>' + response.data + '</p>').show();
                    }
                    
                    button.text(originalText).prop('disabled', false);
                },
                error: function() {
                    $('#thabatta-cache-message').removeClass('hidden notice-success').addClass('notice notice-error').html('<p><?php esc_html_e('Erro ao processar a solicitação.', 'thabatta-adv'); ?></p>').show();
                    button.text(originalText).prop('disabled', false);
                }
            });
        });
        
        // Limpar cache de páginas
        $('#thabatta-clear-page-cache').on('click', function() {
            var button = $(this);
            var originalText = button.text();
            
            button.text('<?php esc_html_e('Limpando...', 'thabatta-adv'); ?>').prop('disabled', true);
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'thabatta_clear_page_cache',
                    nonce: '<?php echo wp_create_nonce('thabatta_cache_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        $('#thabatta-cache-message').removeClass('hidden notice-error').addClass('notice notice-success').html('<p>' + response.data + '</p>').show();
                    } else {
                        $('#thabatta-cache-message').removeClass('hidden notice-success').addClass('notice notice-error').html('<p>' + response.data + '</p>').show();
                    }
                    
                    button.text(originalText).prop('disabled', false);
                },
                error: function() {
                    $('#thabatta-cache-message').removeClass('hidden notice-success').addClass('notice notice-error').html('<p><?php esc_html_e('Erro ao processar a solicitação.', 'thabatta-adv'); ?></p>').show();
                    button.text(originalText).prop('disabled', false);
                }
            });
        });
        
        // Limpar cache de assets
        $('#thabatta-clear-assets-cache').on('click', function() {
            var button = $(this);
            var originalText = button.text();
            
            button.text('<?php esc_html_e('Limpando...', 'thabatta-adv'); ?>').prop('disabled', true);
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'thabatta_clear_assets_cache',
                    nonce: '<?php echo wp_create_nonce('thabatta_cache_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        $('#thabatta-cache-message').removeClass('hidden notice-error').addClass('notice notice-success').html('<p>' + response.data + '</p>').show();
                    } else {
                        $('#thabatta-cache-message').removeClass('hidden notice-success').addClass('notice notice-error').html('<p>' + response.data + '</p>').show();
                    }
                    
                    button.text(originalText).prop('disabled', false);
                },
                error: function() {
                    $('#thabatta-cache-message').removeClass('hidden notice-success').addClass('notice notice-error').html('<p><?php esc_html_e('Erro ao processar a solicitação.', 'thabatta-adv'); ?></p>').show();
                    button.text(originalText).prop('disabled', false);
                }
            });
        });
    });
    </script>
    <?php
}

/**
 * Adicionar scripts e estilos para a página de configurações de cache
 */
function thabatta_enqueue_cache_settings_scripts($hook)
{
    if ($hook !== 'settings_page_thabatta-cache-settings') {
        return;
    }

    wp_enqueue_style('thabatta-admin-style', get_template_directory_uri() . '/assets/css/admin.css', array(), '1.0.0');
}
add_action('admin_enqueue_scripts', 'thabatta_enqueue_cache_settings_scripts');

/**
 * Adicionar cabeçalhos de cache do navegador
 */
function thabatta_add_browser_cache_headers()
{
    // Verificar se o cache do navegador está habilitado
    if (get_option('thabatta_enable_browser_cache', 1)) {
        // Definir cabeçalhos de cache para recursos estáticos
        if (!is_admin()) {
            $file_types = array(
                'image/jpeg' => 'jpg|jpeg|jpe',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                'image/svg+xml' => 'svg',
                'text/css' => 'css',
                'text/javascript' => 'js',
                'application/javascript' => 'js',
                'font/woff' => 'woff',
                'font/woff2' => 'woff2',
                'application/x-font-ttf' => 'ttf',
                'application/vnd.ms-fontobject' => 'eot',
                'application/font-sfnt' => 'ttf|otf'
            );

            $current_file_type = '';

            foreach ($file_types as $mime_type => $extensions) {
                if (preg_match('~\.(' . $extensions . ')$~i', $_SERVER['REQUEST_URI'])) {
                    $current_file_type = $mime_type;
                    break;
                }
            }

            if (!empty($current_file_type)) {
                // Definir tempo de expiração para 1 semana
                header('Cache-Control: public, max-age=604800');
                header('Pragma: public');
                header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 604800) . ' GMT');
                header('Content-Type: ' . $current_file_type);
            }
        }
    }
}
add_action('init', 'thabatta_add_browser_cache_headers');

/**
 * Adicionar metabox para configurações de cache por página
 */
function thabatta_add_cache_meta_box()
{
    // Verificar se o cache está habilitado
    if (!get_option('thabatta_enable_cache', 1)) {
        return;
    }

    add_meta_box(
        'thabatta_cache_meta_box',
        __('Configurações de Cache', 'thabatta-adv'),
        'thabatta_render_cache_meta_box',
        array('post', 'page', 'area_atuacao'),
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'thabatta_add_cache_meta_box');

/**
 * Renderizar metabox de configurações de cache
 */
function thabatta_render_cache_meta_box($post)
{
    // Adicionar nonce para verificação
    wp_nonce_field('thabatta_cache_meta_box', 'thabatta_cache_meta_box_nonce');

    // Obter valores salvos
    $exclude_from_cache = get_post_meta($post->ID, '_thabatta_exclude_from_cache', true);
    $custom_cache_expiry = get_post_meta($post->ID, '_thabatta_custom_cache_expiry', true);

    ?>
    <p>
        <label for="thabatta_exclude_from_cache">
            <input type="checkbox" id="thabatta_exclude_from_cache" name="thabatta_exclude_from_cache" value="1" <?php checked($exclude_from_cache, '1'); ?> />
            <?php esc_html_e('Excluir do cache', 'thabatta-adv'); ?>
        </label>
    </p>
    
    <p>
        <label for="thabatta_custom_cache_expiry"><?php esc_html_e('Tempo de expiração:', 'thabatta-adv'); ?></label>
        <select id="thabatta_custom_cache_expiry" name="thabatta_custom_cache_expiry">
            <option value="" <?php selected($custom_cache_expiry, ''); ?>><?php esc_html_e('Padrão', 'thabatta-adv'); ?></option>
            <option value="1800" <?php selected($custom_cache_expiry, '1800'); ?>><?php esc_html_e('30 minutos', 'thabatta-adv'); ?></option>
            <option value="3600" <?php selected($custom_cache_expiry, '3600'); ?>><?php esc_html_e('1 hora', 'thabatta-adv'); ?></option>
            <option value="7200" <?php selected($custom_cache_expiry, '7200'); ?>><?php esc_html_e('2 horas', 'thabatta-adv'); ?></option>
            <option value="21600" <?php selected($custom_cache_expiry, '21600'); ?>><?php esc_html_e('6 horas', 'thabatta-adv'); ?></option>
            <option value="43200" <?php selected($custom_cache_expiry, '43200'); ?>><?php esc_html_e('12 horas', 'thabatta-adv'); ?></option>
            <option value="86400" <?php selected($custom_cache_expiry, '86400'); ?>><?php esc_html_e('1 dia', 'thabatta-adv'); ?></option>
        </select>
    </p>
    <?php
}

/**
 * Salvar dados da metabox de cache
 */
function thabatta_save_cache_meta_box($post_id)
{
    // Verificar se o cache está habilitado
    if (!get_option('thabatta_enable_cache', 1)) {
        return;
    }

    // Verificar se é um salvamento automático
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Verificar nonce
    if (!isset($_POST['thabatta_cache_meta_box_nonce']) || !wp_verify_nonce($_POST['thabatta_cache_meta_box_nonce'], 'thabatta_cache_meta_box')) {
        return;
    }

    // Verificar permissões
    if ('page' === $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    // Salvar dados
    if (isset($_POST['thabatta_exclude_from_cache'])) {
        update_post_meta($post_id, '_thabatta_exclude_from_cache', '1');
    } else {
        delete_post_meta($post_id, '_thabatta_exclude_from_cache');
    }

    if (isset($_POST['thabatta_custom_cache_expiry'])) {
        update_post_meta($post_id, '_thabatta_custom_cache_expiry', sanitize_text_field($_POST['thabatta_custom_cache_expiry']));
    } else {
        delete_post_meta($post_id, '_thabatta_custom_cache_expiry');
    }
}
add_action('save_post', 'thabatta_save_cache_meta_box');

/**
 * Verificar se a página atual deve ser cacheada
 */
function thabatta_should_cache_page()
{
    // Verificar se o cache está habilitado
    if (!get_option('thabatta_enable_cache', 1)) {
        return false;
    }

    // Não cachear para usuários logados
    if (is_user_logged_in()) {
        return false;
    }

    // Não cachear páginas de administração
    if (is_admin()) {
        return false;
    }

    // Não cachear páginas de busca, 404, etc.
    if (is_search() || is_404() || is_feed()) {
        return false;
    }

    // Verificar exclusão de cache para a página atual
    if (is_singular()) {
        global $post;
        $exclude_from_cache = get_post_meta($post->ID, '_thabatta_exclude_from_cache', true);

        if ($exclude_from_cache === '1') {
            return false;
        }
    }

    return true;
}

/**
 * Obter tempo de expiração do cache para a página atual
 */
function thabatta_get_cache_expiry()
{
    // Obter tempo de expiração padrão
    $default_expiry = get_option('thabatta_cache_expiry', 3600);

    // Verificar tempo de expiração personalizado para a página atual
    if (is_singular()) {
        global $post;
        $custom_expiry = get_post_meta($post->ID, '_thabatta_custom_cache_expiry', true);

        if (!empty($custom_expiry)) {
            return intval($custom_expiry);
        }
    }

    return $default_expiry;
}

/**
 * Adicionar suporte para Jetpack Boost
 */
function thabatta_jetpack_boost_support()
{
    // Verificar se o Jetpack Boost está ativo
    if (class_exists('Jetpack_Boost')) {
        // Adicionar suporte para otimização de CSS
        add_theme_support('jetpack-boost-critical-css');

        // Adicionar suporte para carregamento preguiçoso de imagens
        add_theme_support('jetpack-boost-lazy-images');

        // Adicionar suporte para otimização de fontes
        add_theme_support('jetpack-boost-optimize-fonts');
    }
}
add_action('after_setup_theme', 'thabatta_jetpack_boost_support');

/**
 * Adicionar widget de status de cache ao dashboard
 */
function thabatta_add_cache_dashboard_widget()
{
    // Verificar se o cache está habilitado
    if (!get_option('thabatta_enable_cache', 1)) {
        return;
    }

    wp_add_dashboard_widget(
        'thabatta_cache_dashboard_widget',
        __('Status do Cache', 'thabatta-adv'),
        'thabatta_render_cache_dashboard_widget'
    );
}
add_action('wp_dashboard_setup', 'thabatta_add_cache_dashboard_widget');

/**
 * Renderizar widget de status de cache
 */
function thabatta_render_cache_dashboard_widget()
{
    // Verificar se o Jetpack está ativo
    $jetpack_active = class_exists('Jetpack');

    // Obter configurações de cache
    $cache_enabled = get_option('thabatta_enable_cache', 1);
    $cache_expiry = get_option('thabatta_cache_expiry', 3600);
    $browser_cache = get_option('thabatta_enable_browser_cache', 1);
    $gzip = get_option('thabatta_enable_gzip', 1);

    // Formatar tempo de expiração
    $expiry_text = '';
    switch ($cache_expiry) {
        case 1800:
            $expiry_text = __('30 minutos', 'thabatta-adv');
            break;
        case 3600:
            $expiry_text = __('1 hora', 'thabatta-adv');
            break;
        case 7200:
            $expiry_text = __('2 horas', 'thabatta-adv');
            break;
        case 21600:
            $expiry_text = __('6 horas', 'thabatta-adv');
            break;
        case 43200:
            $expiry_text = __('12 horas', 'thabatta-adv');
            break;
        case 86400:
            $expiry_text = __('1 dia', 'thabatta-adv');
            break;
        case 604800:
            $expiry_text = __('1 semana', 'thabatta-adv');
            break;
        default:
            $expiry_text = human_time_diff(0, $cache_expiry);
    }

    ?>
    <div class="thabatta-cache-status">
        <?php if (!$jetpack_active) : ?>
            <div class="thabatta-cache-warning">
                <p><?php esc_html_e('O plugin Jetpack não está ativo. Algumas funcionalidades de cache podem não estar disponíveis.', 'thabatta-adv'); ?></p>
            </div>
        <?php endif; ?>
        
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
                    <td><?php echo esc_html($expiry_text); ?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e('Cache do Navegador:', 'thabatta-adv'); ?></td>
                    <td>
                        <?php if ($browser_cache) : ?>
                            <span class="thabatta-status-active"><?php esc_html_e('Ativo', 'thabatta-adv'); ?></span>
                        <?php else : ?>
                            <span class="thabatta-status-inactive"><?php esc_html_e('Inativo', 'thabatta-adv'); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?php esc_html_e('Compressão GZIP:', 'thabatta-adv'); ?></td>
                    <td>
                        <?php if ($gzip) : ?>
                            <span class="thabatta-status-active"><?php esc_html_e('Ativo', 'thabatta-adv'); ?></span>
                        <?php else : ?>
                            <span class="thabatta-status-inactive"><?php esc_html_e('Inativo', 'thabatta-adv'); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <div class="thabatta-cache-actions">
            <button type="button" id="thabatta-dashboard-clear-cache" class="button button-primary">
                <?php esc_html_e('Limpar Cache', 'thabatta-adv'); ?>
            </button>
            
            <a href="<?php echo esc_url(admin_url('options-general.php?page=thabatta-cache-settings')); ?>" class="button">
                <?php esc_html_e('Configurações', 'thabatta-adv'); ?>
            </a>
        </div>
        
        <div id="thabatta-dashboard-cache-message" class="hidden"></div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('#thabatta-dashboard-clear-cache').on('click', function() {
            var button = $(this);
            var originalText = button.text();
            
            button.text('<?php esc_html_e('Limpando...', 'thabatta-adv'); ?>').prop('disabled', true);
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'thabatta_clear_all_cache',
                    nonce: '<?php echo wp_create_nonce('thabatta_cache_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        $('#thabatta-dashboard-cache-message').removeClass('hidden notice-error').addClass('notice notice-success').html('<p>' + response.data + '</p>').show();
                    } else {
                        $('#thabatta-dashboard-cache-message').removeClass('hidden notice-success').addClass('notice notice-error').html('<p>' + response.data + '</p>').show();
                    }
                    
                    button.text(originalText).prop('disabled', false);
                },
                error: function() {
                    $('#thabatta-dashboard-cache-message').removeClass('hidden notice-success').addClass('notice notice-error').html('<p><?php esc_html_e('Erro ao processar a solicitação.', 'thabatta-adv'); ?></p>').show();
                    button.text(originalText).prop('disabled', false);
                }
            });
        });
    });
    </script>
    <?php
}
