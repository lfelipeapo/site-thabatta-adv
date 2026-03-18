<?php
/**
 * Página de configurações
 *
 * @package AICG
 */

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Salva configurações
if (isset($_POST['aicg_save_settings']) && check_admin_referer('aicg_settings_nonce')) {
    // API Key
    if (!empty($_POST['aicg_api_key'])) {
        $encryption = new \AICG\Security\Encryption();
        update_option('aicg_api_key_encrypted', $encryption->encrypt(sanitize_text_field($_POST['aicg_api_key'])));
    }

    // Preferências
    update_option('aicg_default_model', sanitize_text_field($_POST['aicg_default_model'] ?? ''));
    update_option('aicg_default_tone', sanitize_key($_POST['aicg_default_tone'] ?? 'professional'));
    update_option('aicg_default_length', sanitize_key($_POST['aicg_default_length'] ?? 'medium'));
    update_option('aicg_include_images', isset($_POST['aicg_include_images']));
    
    // Avançado
    update_option('aicg_cache_enabled', isset($_POST['aicg_cache_enabled']));
    update_option('aicg_async_generation', isset($_POST['aicg_async_generation']));
    update_option('aicg_enable_notifications', isset($_POST['aicg_enable_notifications']));

    echo '<div class="notice notice-success"><p>' . esc_html__('Configurações salvas.', 'ai-content-generator') . '</p></div>';
}

// Obtém valores atuais
$api_key_configured = !empty(get_option('aicg_api_key_encrypted'));
$default_model = get_option('aicg_default_model', '');
$default_tone = get_option('aicg_default_tone', 'professional');
$default_length = get_option('aicg_default_length', 'medium');
$include_images = get_option('aicg_include_images', true);
$cache_enabled = get_option('aicg_cache_enabled', true);
$async_generation = get_option('aicg_async_generation', true);
$enable_notifications = get_option('aicg_enable_notifications', true);
$async_available = !(defined('DISABLE_WP_CRON') && DISABLE_WP_CRON);
$available_models = get_option('aicg_available_models', []);

if ($api_key_configured) {
    $client = new \AICG\API\GroqClient();
    $models_result = $client->get_available_models(true);

    if (!is_wp_error($models_result) && !empty($models_result)) {
        $available_models = $models_result;
    }
}

if (empty($available_models) && !empty($default_model)) {
    $available_models = [
        [
            'id' => $default_model,
            'name' => $default_model,
        ],
    ];
}

?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('aicg_settings_nonce'); ?>
        
        <h2><?php esc_html_e('Configurações de API', 'ai-content-generator'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="aicg_api_key">
                        <?php esc_html_e('Chave API Groq', 'ai-content-generator'); ?>
                    </label>
                </th>
                <td>
                    <input type="password" 
                           id="aicg_api_key" 
                           name="aicg_api_key" 
                           class="regular-text"
                           placeholder="<?php echo $api_key_configured ? '••••••••••••••••' : ''; ?>">
                    <p class="description">
                        <?php if ($api_key_configured): ?>
                            <span style="color: green;">✓</span> 
                            <?php esc_html_e('Chave API configurada.', 'ai-content-generator'); ?>
                        <?php else: ?>
                            <?php esc_html_e('Obtenha sua chave em https://console.groq.com/', 'ai-content-generator'); ?>
                        <?php endif; ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="aicg_default_model">
                        <?php esc_html_e('Modelo Padrão', 'ai-content-generator'); ?>
                    </label>
                </th>
                <td>
                    <select id="aicg_default_model" name="aicg_default_model">
                        <?php foreach ($available_models as $model): ?>
                            <option value="<?php echo esc_attr($model['id']); ?>" <?php selected($default_model, $model['id']); ?>>
                                <?php echo esc_html($model['name'] ?? $model['id']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">
                        <?php esc_html_e('Lista atualizada diretamente da API de modelos da Groq quando a chave estiver configurada.', 'ai-content-generator'); ?>
                    </p>
                </td>
            </tr>
        </table>

        <h2><?php esc_html_e('Preferências Padrão', 'ai-content-generator'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="aicg_default_tone">
                        <?php esc_html_e('Tom de Voz', 'ai-content-generator'); ?>
                    </label>
                </th>
                <td>
                    <select id="aicg_default_tone" name="aicg_default_tone">
                        <option value="professional" <?php selected($default_tone, 'professional'); ?>>
                            <?php esc_html_e('Profissional', 'ai-content-generator'); ?>
                        </option>
                        <option value="casual" <?php selected($default_tone, 'casual'); ?>>
                            <?php esc_html_e('Casual/Conversacional', 'ai-content-generator'); ?>
                        </option>
                        <option value="technical" <?php selected($default_tone, 'technical'); ?>>
                            <?php esc_html_e('Técnico/Especializado', 'ai-content-generator'); ?>
                        </option>
                        <option value="persuasive" <?php selected($default_tone, 'persuasive'); ?>>
                            <?php esc_html_e('Persuasivo/Vendas', 'ai-content-generator'); ?>
                        </option>
                        <option value="narrative" <?php selected($default_tone, 'narrative'); ?>>
                            <?php esc_html_e('Narrativo/Storytelling', 'ai-content-generator'); ?>
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="aicg_default_length">
                        <?php esc_html_e('Comprimento', 'ai-content-generator'); ?>
                    </label>
                </th>
                <td>
                    <select id="aicg_default_length" name="aicg_default_length">
                        <option value="short" <?php selected($default_length, 'short'); ?>>
                            <?php esc_html_e('Curto (300-500 palavras)', 'ai-content-generator'); ?>
                        </option>
                        <option value="medium" <?php selected($default_length, 'medium'); ?>>
                            <?php esc_html_e('Médio (800-1200 palavras)', 'ai-content-generator'); ?>
                        </option>
                        <option value="long" <?php selected($default_length, 'long'); ?>>
                            <?php esc_html_e('Longo (1500-2500 palavras)', 'ai-content-generator'); ?>
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('Opções', 'ai-content-generator'); ?>
                </th>
                <td>
                    <label>
                        <input type="checkbox" name="aicg_include_images" <?php checked($include_images); ?>>
                        <?php esc_html_e('Incluir imagens destacadas quando disponíveis', 'ai-content-generator'); ?>
                    </label>
                </td>
            </tr>
        </table>

        <h2><?php esc_html_e('Configurações Avançadas', 'ai-content-generator'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <?php esc_html_e('Performance', 'ai-content-generator'); ?>
                </th>
                <td>
                    <label>
                        <input type="checkbox" name="aicg_cache_enabled" <?php checked($cache_enabled); ?>>
                        <?php esc_html_e('Habilitar cache de respostas similares', 'ai-content-generator'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"></th>
                <td>
                    <label>
                        <input type="checkbox" name="aicg_async_generation" <?php checked($async_generation); ?> <?php disabled(!$async_available); ?>>
                        <?php esc_html_e('Usar processamento assíncrono (recomendado)', 'ai-content-generator'); ?>
                    </label>
                    <?php if (!$async_available): ?>
                        <p class="description">
                            <?php esc_html_e('O WP-Cron está desabilitado neste ambiente, então o plugin usa geração síncrona para evitar jobs presos.', 'ai-content-generator'); ?>
                        </p>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('Notificações', 'ai-content-generator'); ?>
                </th>
                <td>
                    <label>
                        <input type="checkbox" name="aicg_enable_notifications" <?php checked($enable_notifications); ?>>
                        <?php esc_html_e('Enviar email quando conteúdo agendado for publicado', 'ai-content-generator'); ?>
                    </label>
                </td>
            </tr>
        </table>

        <?php submit_button(esc_html__('Salvar Configurações', 'ai-content-generator'), 'primary', 'aicg_save_settings'); ?>
    </form>
</div>
