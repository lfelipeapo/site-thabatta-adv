<?php
/**
 * Página de onboarding
 *
 * @package AICG
 */

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Salva e avança
if (isset($_POST['aicg_complete_onboarding']) && check_admin_referer('aicg_onboarding_nonce')) {
    update_option('aicg_onboarding_completed', true);
    
    // Salva API key se fornecida
    if (!empty($_POST['aicg_api_key'])) {
        $encryption = new \AICG\Security\Encryption();
        update_option('aicg_api_key_encrypted', $encryption->encrypt(sanitize_text_field($_POST['aicg_api_key'])));
    }

    wp_redirect(admin_url('admin.php?page=ai-content-generator'));
    exit;
}

$step = isset($_GET['step']) ? intval($_GET['step']) : 1;

?>
<div class="wrap aicg-onboarding">
    <div class="aicg-onboarding-header">
        <h1><?php esc_html_e('Bem-vindo ao AI Content Generator', 'ai-content-generator'); ?></h1>
        <p><?php esc_html_e('Vamos configurar o plugin em alguns passos simples.', 'ai-content-generator'); ?></p>
    </div>

    <div class="aicg-onboarding-steps">
        <div class="aicg-step <?php echo $step >= 1 ? 'active' : ''; ?>">
            <span class="step-number">1</span>
            <?php esc_html_e('API Key', 'ai-content-generator'); ?>
        </div>
        <div class="aicg-step <?php echo $step >= 2 ? 'active' : ''; ?>">
            <span class="step-number">2</span>
            <?php esc_html_e('Preferências', 'ai-content-generator'); ?>
        </div>
        <div class="aicg-step <?php echo $step >= 3 ? 'active' : ''; ?>">
            <span class="step-number">3</span>
            <?php esc_html_e('Pronto!', 'ai-content-generator'); ?>
        </div>
    </div>

    <div class="aicg-onboarding-content">
        <?php if ($step === 1): ?>
            <h2><?php esc_html_e('Configure sua API Groq', 'ai-content-generator'); ?></h2>
            <p>
                <?php esc_html_e('Para gerar conteúdo com IA, você precisa de uma chave API do Groq.', 'ai-content-generator'); ?>
            </p>
            <ol>
                <li>
                    <?php 
                    printf(
                        /* translators: %s: Link to Groq console */
                        esc_html__('Acesse %s e crie uma conta gratuita.', 'ai-content-generator'),
                        '<a href="https://console.groq.com/" target="_blank">console.groq.com</a>'
                    ); 
                    ?>
                </li>
                <li><?php esc_html_e('Crie uma nova API key no dashboard.', 'ai-content-generator'); ?></li>
                <li><?php esc_html_e('Cole a chave abaixo:', 'ai-content-generator'); ?></li>
            </ol>

            <form method="post" action="<?php echo esc_url(add_query_arg('step', '2')); ?>">
                <?php wp_nonce_field('aicg_onboarding_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="aicg_api_key">
                                <?php esc_html_e('Chave API Groq', 'ai-content-generator'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="aicg_api_key" 
                                   name="aicg_api_key" 
                                   class="regular-text" 
                                   placeholder="gsk_..."
                                   required>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary button-hero">
                        <?php esc_html_e('Continuar', 'ai-content-generator'); ?> →
                    </button>
                </p>
            </form>

        <?php elseif ($step === 2): ?>
            <h2><?php esc_html_e('Preferências Padrão', 'ai-content-generator'); ?></h2>
            <p><?php esc_html_e('Configure suas preferências iniciais. Você pode alterá-las depois nas configurações.', 'ai-content-generator'); ?></p>

            <form method="post" action="<?php echo esc_url(add_query_arg('step', '3')); ?>">
                <?php wp_nonce_field('aicg_onboarding_nonce'); ?>
                
                <!-- Mantém API key -->
                <input type="hidden" name="aicg_api_key" value="<?php echo esc_attr($_POST['aicg_api_key'] ?? ''); ?>">

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="aicg_default_tone">
                                <?php esc_html_e('Tom de Voz Padrão', 'ai-content-generator'); ?>
                            </label>
                        </th>
                        <td>
                            <select id="aicg_default_tone" name="aicg_default_tone">
                                <option value="professional"><?php esc_html_e('Profissional', 'ai-content-generator'); ?></option>
                                <option value="casual"><?php esc_html_e('Casual', 'ai-content-generator'); ?></option>
                                <option value="technical"><?php esc_html_e('Técnico', 'ai-content-generator'); ?></option>
                                <option value="persuasive"><?php esc_html_e('Persuasivo', 'ai-content-generator'); ?></option>
                                <option value="narrative"><?php esc_html_e('Narrativo', 'ai-content-generator'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="aicg_default_length">
                                <?php esc_html_e('Comprimento Padrão', 'ai-content-generator'); ?>
                            </label>
                        </th>
                        <td>
                            <select id="aicg_default_length" name="aicg_default_length">
                                <option value="short"><?php esc_html_e('Curto (300-500 palavras)', 'ai-content-generator'); ?></option>
                                <option value="medium" selected><?php esc_html_e('Médio (800-1200 palavras)', 'ai-content-generator'); ?></option>
                                <option value="long"><?php esc_html_e('Longo (1500-2500 palavras)', 'ai-content-generator'); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary button-hero">
                        <?php esc_html_e('Continuar', 'ai-content-generator'); ?> →
                    </button>
                </p>
            </form>

        <?php else: ?>
            <h2><?php esc_html_e('Tudo pronto!', 'ai-content-generator'); ?></h2>
            <p><?php esc_html_e('O plugin está configurado e pronto para uso.', 'ai-content-generator'); ?></p>

            <div class="aicg-features">
                <h3><?php esc_html_e('O que você pode fazer agora:', 'ai-content-generator'); ?></h3>
                <ul>
                    <li><?php esc_html_e('Gerar posts e páginas com conteúdo de alta qualidade', 'ai-content-generator'); ?></li>
                    <li><?php esc_html_e('Agendar publicações para datas futuras', 'ai-content-generator'); ?></li>
                    <li><?php esc_html_e('Incluir imagens destacadas automaticamente', 'ai-content-generator'); ?></li>
                    <li><?php esc_html_e('Otimizar SEO com integração automática', 'ai-content-generator'); ?></li>
                </ul>
            </div>

            <form method="post" action="">
                <?php wp_nonce_field('aicg_onboarding_nonce'); ?>
                
                <!-- Mantém dados -->
                <input type="hidden" name="aicg_api_key" value="<?php echo esc_attr($_POST['aicg_api_key'] ?? ''); ?>">
                <input type="hidden" name="aicg_default_tone" value="<?php echo esc_attr($_POST['aicg_default_tone'] ?? 'professional'); ?>">
                <input type="hidden" name="aicg_default_length" value="<?php echo esc_attr($_POST['aicg_default_length'] ?? 'medium'); ?>">

                <p class="submit">
                    <button type="submit" name="aicg_complete_onboarding" class="button button-primary button-hero">
                        <?php esc_html_e('Começar a Gerar Conteúdo', 'ai-content-generator'); ?> →
                    </button>
                </p>
            </form>
        <?php endif; ?>
    </div>
</div>

<style>
.aicg-onboarding {
    max-width: 800px;
    margin: 40px auto;
}
.aicg-onboarding-header {
    text-align: center;
    margin-bottom: 40px;
}
.aicg-onboarding-steps {
    display: flex;
    justify-content: center;
    margin-bottom: 40px;
}
.aicg-step {
    display: flex;
    align-items: center;
    margin: 0 20px;
    color: #646970;
}
.aicg-step.active {
    color: #2271b1;
    font-weight: 500;
}
.step-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #f0f0f1;
    margin-right: 10px;
    font-weight: bold;
}
.aicg-step.active .step-number {
    background: #2271b1;
    color: white;
}
.aicg-onboarding-content {
    background: white;
    padding: 30px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.aicg-features ul {
    list-style: disc;
    margin-left: 20px;
}
.aicg-features li {
    margin: 10px 0;
}
</style>
