<?php
/**
 * Classe para gerenciar recursos personalizados do tema
 *
 * @package Thabatta_Advocacia
 */

if (!defined('ABSPATH')) {
    exit; // Saída direta se acessado diretamente
}

/**
 * Classe para gerenciar recursos personalizados do tema
 */
class Thabatta_Admin_Features
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

        // Adicionar scripts e estilos de administração
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        // Adicionar metabox para relacionar posts
        add_action('add_meta_boxes', array($this, 'add_related_posts_meta_box'));
        add_action('save_post', array($this, 'save_related_posts_meta_box'));

        // Adicionar metabox para configurações de SEO
        add_action('add_meta_boxes', array($this, 'add_seo_meta_box'));
        add_action('save_post', array($this, 'save_seo_meta_box'));

        // Adicionar dashboard widget
        add_action('wp_dashboard_setup', array($this, 'add_dashboard_widgets'));

        // Adicionar coluna de SEO na lista de posts
        add_filter('manage_posts_columns', array($this, 'add_seo_column'));
        add_filter('manage_pages_columns', array($this, 'add_seo_column'));
        add_action('manage_posts_custom_column', array($this, 'display_seo_column'), 10, 2);
        add_action('manage_pages_custom_column', array($this, 'display_seo_column'), 10, 2);

        // Adicionar filtro de posts por status de SEO
        add_action('restrict_manage_posts', array($this, 'add_seo_filter'));
        add_filter('parse_query', array($this, 'filter_posts_by_seo_status'));
    }

    /**
     * Adicionar menu de administração
     */
    public function add_admin_menu()
    {
        add_menu_page(
            __('Configurações do Tema', 'thabatta-adv'),
            __('Tema Thabatta', 'thabatta-adv'),
            'manage_options',
            'thabatta-redirect-to-customizer',
            array($this, 'redirect_to_customizer'),
            'dashicons-admin-customizer',
            60
        );

        // Redirecionar todas as páginas antigas para o customizer
        add_action('admin_init', array($this, 'handle_old_settings_redirect'));
    }

    /**
     * Redireciona qualquer tentativa de acessar as antigas páginas de configuração para o customizer
     */
    public function handle_old_settings_redirect() 
    {
        global $pagenow;
        
        if ($pagenow === 'admin.php' && isset($_GET['page'])) {
            $old_pages = array(
                'thabatta-theme-settings',
                'thabatta-social-settings',
                'thabatta-seo-settings'
            );
            
            if (in_array($_GET['page'], $old_pages)) {
                wp_redirect(admin_url('customize.php'));
                exit;
            }
        }
    }

    /**
     * Função de redirecionamento para o customizer
     */
    public function redirect_to_customizer()
    {
        wp_redirect(admin_url('customize.php'));
        exit;
    }

    /**
     * Registrar configurações
     */
    public function register_settings()
    {
        // Mantendo apenas o registro para compatibilidade com código legado
        // As configurações reais agora são gerenciadas pelo customizer
        
        // Configurações gerais
        register_setting('thabatta_theme_settings', 'thabatta_logo');
        register_setting('thabatta_theme_settings', 'thabatta_favicon');
        register_setting('thabatta_theme_settings', 'thabatta_footer_text');
        register_setting('thabatta_theme_settings', 'thabatta_google_analytics');
        register_setting('thabatta_theme_settings', 'thabatta_enable_preloader', array('default' => '1'));
        register_setting('thabatta_theme_settings', 'thabatta_enable_back_to_top', array('default' => '1'));

        // Configurações de redes sociais
        register_setting('thabatta_social_settings', 'thabatta_facebook_url');
        register_setting('thabatta_social_settings', 'thabatta_instagram_url');
        register_setting('thabatta_social_settings', 'thabatta_linkedin_url');
        register_setting('thabatta_social_settings', 'thabatta_twitter_url');
        register_setting('thabatta_social_settings', 'thabatta_youtube_url');
        register_setting('thabatta_social_settings', 'thabatta_whatsapp_number');
        register_setting('thabatta_social_settings', 'thabatta_enable_social_sharing', array('default' => '1'));

        // Configurações de SEO
        register_setting('thabatta_seo_settings', 'thabatta_default_meta_description');
        register_setting('thabatta_seo_settings', 'thabatta_default_meta_keywords');
        register_setting('thabatta_seo_settings', 'thabatta_enable_schema_markup', array('default' => '1'));
        register_setting('thabatta_seo_settings', 'thabatta_enable_breadcrumbs', array('default' => '1'));
        register_setting('thabatta_seo_settings', 'thabatta_enable_open_graph', array('default' => '1'));
        register_setting('thabatta_seo_settings', 'thabatta_enable_twitter_cards', array('default' => '1'));

        // Configurações de personalização
        register_setting('thabatta_customization_settings', 'thabatta_primary_color', array('default' => '#8B0000'));
        register_setting('thabatta_customization_settings', 'thabatta_secondary_color', array('default' => '#D4AF37'));
        register_setting('thabatta_customization_settings', 'thabatta_accent_color', array('default' => '#4A0404'));
        register_setting('thabatta_customization_settings', 'thabatta_text_color', array('default' => '#333333'));
        register_setting('thabatta_customization_settings', 'thabatta_heading_font', array('default' => 'Playfair Display'));
        register_setting('thabatta_customization_settings', 'thabatta_body_font', array('default' => 'Roboto'));
    }

    /**
     * Adicionar scripts e estilos de administração
     */
    public function enqueue_admin_scripts($hook)
    {
        // Verificar se estamos em uma página de configurações do tema
        if (strpos($hook, 'thabatta') !== false) {
            // Adicionar Color Picker
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');

            // Adicionar Media Uploader
            wp_enqueue_media();

            // Adicionar scripts e estilos personalizados
            wp_enqueue_style('thabatta-admin-style', get_template_directory_uri() . '/assets/css/admin.css', array(), '1.0.0');
            wp_enqueue_script('thabatta-admin-script', get_template_directory_uri() . '/assets/js/admin.js', array('jquery', 'wp-color-picker'), '1.0.0', true);

            // Passar variáveis para o script
            wp_localize_script('thabatta-admin-script', 'thabattaAdmin', array(
                'mediaTitle' => __('Selecionar ou Enviar Mídia', 'thabatta-adv'),
                'mediaButton' => __('Usar esta mídia', 'thabatta-adv')
            ));
        }
    }

    /**
     * Adicionar metabox para relacionar posts
     */
    public function add_related_posts_meta_box()
    {
        add_meta_box(
            'thabatta_related_posts',
            __('Posts Relacionados', 'thabatta-adv'),
            array($this, 'render_related_posts_meta_box'),
            array('post', 'page', 'area_atuacao'),
            'side',
            'default'
        );
    }

    /**
     * Renderizar metabox de posts relacionados
     */
    public function render_related_posts_meta_box($post)
    {
        wp_nonce_field('thabatta_related_posts_nonce', 'thabatta_related_posts_nonce');

        // Obter posts relacionados salvos
        $related_posts = get_post_meta($post->ID, '_thabatta_related_posts', true);

        // Configurar argumentos para busca de posts
        $args = array(
            'post_type' => array('post', 'page', 'area_atuacao'),
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'post__not_in' => array($post->ID),
            'orderby' => 'title',
            'order' => 'ASC'
        );

        // Buscar posts
        $posts = get_posts($args);

        // Exibir campo de seleção
        ?>
        <p><?php _e('Selecione os posts relacionados a este conteúdo:', 'thabatta-adv'); ?></p>
        <select name="thabatta_related_posts[]" id="thabatta_related_posts" multiple="multiple" style="width: 100%; height: 200px;">
            <?php foreach ($posts as $related_post) : ?>
                <option value="<?php echo $related_post->ID; ?>" <?php selected(is_array($related_posts) && in_array($related_post->ID, $related_posts)); ?>>
                    <?php echo $related_post->post_title; ?> (<?php echo get_post_type_object($related_post->post_type)->labels->singular_name; ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php _e('Segure a tecla Ctrl (Windows) ou Command (Mac) para selecionar múltiplos posts.', 'thabatta-adv'); ?></p>
        <?php
    }

    /**
     * Salvar dados da metabox de posts relacionados
     */
    public function save_related_posts_meta_box($post_id)
    {
        // Verificar se é um salvamento automático
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Verificar nonce
        if (!isset($_POST['thabatta_related_posts_nonce']) || !wp_verify_nonce($_POST['thabatta_related_posts_nonce'], 'thabatta_related_posts_nonce')) {
            return;
        }

        // Verificar permissões
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Salvar posts relacionados
        if (isset($_POST['thabatta_related_posts'])) {
            update_post_meta($post_id, '_thabatta_related_posts', $_POST['thabatta_related_posts']);
        } else {
            delete_post_meta($post_id, '_thabatta_related_posts');
        }
    }

    /**
     * Adicionar metabox para configurações de SEO
     */
    public function add_seo_meta_box()
    {
        add_meta_box(
            'thabatta_seo',
            __('Configurações de SEO', 'thabatta-adv'),
            array($this, 'render_seo_meta_box'),
            array('post', 'page', 'area_atuacao'),
            'normal',
            'high'
        );
    }

    /**
     * Renderizar metabox de configurações de SEO
     */
    public function render_seo_meta_box($post)
    {
        wp_nonce_field('thabatta_seo_nonce', 'thabatta_seo_nonce');

        // Obter dados salvos
        $meta_title = get_post_meta($post->ID, '_thabatta_meta_title', true);
        $meta_description = get_post_meta($post->ID, '_thabatta_meta_description', true);
        $meta_keywords = get_post_meta($post->ID, '_thabatta_meta_keywords', true);
        $no_index = get_post_meta($post->ID, '_thabatta_no_index', true);
        $no_follow = get_post_meta($post->ID, '_thabatta_no_follow', true);

        ?>
        <div class="thabatta-seo-box">
            <p>
                <label for="thabatta_meta_title"><?php _e('Meta Título:', 'thabatta-adv'); ?></label><br>
                <input type="text" name="thabatta_meta_title" id="thabatta_meta_title" value="<?php echo esc_attr($meta_title); ?>" class="large-text">
                <span class="description"><?php _e('Deixe em branco para usar o título da página. Recomendado: até 60 caracteres.', 'thabatta-adv'); ?></span>
                <span class="thabatta-char-count" data-target="#thabatta_meta_title" data-recommended="60"></span>
            </p>
            
            <p>
                <label for="thabatta_meta_description"><?php _e('Meta Descrição:', 'thabatta-adv'); ?></label><br>
                <textarea name="thabatta_meta_description" id="thabatta_meta_description" rows="3" class="large-text"><?php echo esc_textarea($meta_description); ?></textarea>
                <span class="description"><?php _e('Breve descrição da página. Recomendado: até 160 caracteres.', 'thabatta-adv'); ?></span>
                <span class="thabatta-char-count" data-target="#thabatta_meta_description" data-recommended="160"></span>
            </p>
            
            <p>
                <label for="thabatta_meta_keywords"><?php _e('Meta Keywords:', 'thabatta-adv'); ?></label><br>
                <input type="text" name="thabatta_meta_keywords" id="thabatta_meta_keywords" value="<?php echo esc_attr($meta_keywords); ?>" class="large-text">
                <span class="description"><?php _e('Palavras-chave separadas por vírgula.', 'thabatta-adv'); ?></span>
            </p>
            
            <p>
                <label for="thabatta_no_index">
                    <input type="checkbox" name="thabatta_no_index" id="thabatta_no_index" value="1" <?php checked($no_index, '1'); ?>>
                    <?php _e('Não indexar esta página (noindex)', 'thabatta-adv'); ?>
                </label>
            </p>
            
            <p>
                <label for="thabatta_no_follow">
                    <input type="checkbox" name="thabatta_no_follow" id="thabatta_no_follow" value="1" <?php checked($no_follow, '1'); ?>>
                    <?php _e('Não seguir links nesta página (nofollow)', 'thabatta-adv'); ?>
                </label>
            </p>
            
            <div class="thabatta-seo-preview">
                <h4><?php _e('Pré-visualização nos resultados de pesquisa:', 'thabatta-adv'); ?></h4>
                <div class="thabatta-serp-preview">
                    <div class="thabatta-serp-title" id="thabatta-serp-title">
                        <?php echo esc_html($meta_title ? $meta_title : get_the_title($post->ID)); ?>
                    </div>
                    <div class="thabatta-serp-url">
                        <?php echo esc_url(get_permalink($post->ID)); ?>
                    </div>
                    <div class="thabatta-serp-description" id="thabatta-serp-description">
                        <?php
                        if ($meta_description) {
                            echo esc_html($meta_description);
                        } else {
                            $excerpt = has_excerpt($post->ID) ? get_the_excerpt($post->ID) : wp_trim_words(strip_shortcodes(strip_tags($post->post_content)), 30, '...');
                            echo esc_html($excerpt);
                        }
        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Salvar dados da metabox de SEO
     */
    public function save_seo_meta_box($post_id)
    {
        // Verificar se é um salvamento automático
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Verificar nonce
        if (!isset($_POST['thabatta_seo_nonce']) || !wp_verify_nonce($_POST['thabatta_seo_nonce'], 'thabatta_seo_nonce')) {
            return;
        }

        // Verificar permissões
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Salvar meta título
        if (isset($_POST['thabatta_meta_title'])) {
            update_post_meta($post_id, '_thabatta_meta_title', sanitize_text_field($_POST['thabatta_meta_title']));
        }

        // Salvar meta descrição
        if (isset($_POST['thabatta_meta_description'])) {
            update_post_meta($post_id, '_thabatta_meta_description', sanitize_textarea_field($_POST['thabatta_meta_description']));
        }

        // Salvar meta keywords
        if (isset($_POST['thabatta_meta_keywords'])) {
            update_post_meta($post_id, '_thabatta_meta_keywords', sanitize_text_field($_POST['thabatta_meta_keywords']));
        }

        // Salvar noindex
        $no_index = isset($_POST['thabatta_no_index']) ? '1' : '';
        update_post_meta($post_id, '_thabatta_no_index', $no_index);

        // Salvar nofollow
        $no_follow = isset($_POST['thabatta_no_follow']) ? '1' : '';
        update_post_meta($post_id, '_thabatta_no_follow', $no_follow);
    }

    /**
     * Adicionar dashboard widgets
     */
    public function add_dashboard_widgets()
    {
        wp_add_dashboard_widget(
            'thabatta_dashboard_widget',
            __('Thabatta Advocacia - Visão Geral', 'thabatta-adv'),
            array($this, 'render_dashboard_widget')
        );
    }

    /**
     * Renderizar dashboard widget
     */
    public function render_dashboard_widget()
    {
        // Contar posts
        $post_count = wp_count_posts();
        $published_posts = $post_count->publish;

        // Contar páginas
        $page_count = wp_count_posts('page');
        $published_pages = $page_count->publish;

        // Contar áreas de atuação
        $area_count = wp_count_posts('area_atuacao');
        $published_areas = $area_count->publish;

        // Contar equipe
        $team_count = wp_count_posts('equipe');
        $published_team = $team_count->publish;

        // Contar comentários
        $comment_count = wp_count_comments();
        $approved_comments = $comment_count->approved;
        $pending_comments = $comment_count->moderated;

        ?>
        <div class="thabatta-dashboard-widget">
            <div class="thabatta-dashboard-stats">
                <div class="thabatta-stat-item">
                    <span class="thabatta-stat-number"><?php echo esc_html($published_posts); ?></span>
                    <span class="thabatta-stat-label"><?php _e('Posts Publicados', 'thabatta-adv'); ?></span>
                </div>
                <div class="thabatta-stat-item">
                    <span class="thabatta-stat-number"><?php echo esc_html($published_pages); ?></span>
                    <span class="thabatta-stat-label"><?php _e('Páginas Publicadas', 'thabatta-adv'); ?></span>
                </div>
                <div class="thabatta-stat-item">
                    <span class="thabatta-stat-number"><?php echo esc_html($published_areas); ?></span>
                    <span class="thabatta-stat-label"><?php _e('Áreas de Atuação', 'thabatta-adv'); ?></span>
                </div>
                <div class="thabatta-stat-item">
                    <span class="thabatta-stat-number"><?php echo esc_html($published_team); ?></span>
                    <span class="thabatta-stat-label"><?php _e('Membros da Equipe', 'thabatta-adv'); ?></span>
                </div>
            </div>
            
            <div class="thabatta-dashboard-comments">
                <h4><?php _e('Comentários', 'thabatta-adv'); ?></h4>
                <div class="thabatta-comment-stats">
                    <div class="thabatta-stat-item">
                        <span class="thabatta-stat-number"><?php echo esc_html(strval($approved_comments)); ?></span>
                        <span class="thabatta-stat-label"><?php _e('Aprovados', 'thabatta-adv'); ?></span>
                    </div>
                    <div class="thabatta-stat-item">
                        <span class="thabatta-stat-number"><?php echo esc_html(strval($pending_comments)); ?></span>
                        <span class="thabatta-stat-label"><?php _e('Pendentes', 'thabatta-adv'); ?></span>
                    </div>
                </div>
                <?php if ($pending_comments > 0) : ?>
                    <a href="<?php echo esc_url(admin_url('edit-comments.php?comment_status=moderated')); ?>" class="button button-secondary">
                        <?php _e('Moderar Comentários', 'thabatta-adv'); ?>
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="thabatta-dashboard-links">
                <h4><?php _e('Links Rápidos', 'thabatta-adv'); ?></h4>
                <ul>
                    <li><a href="<?php echo esc_url(admin_url('post-new.php')); ?>"><?php _e('Adicionar Novo Post', 'thabatta-adv'); ?></a></li>
                    <li><a href="<?php echo esc_url(admin_url('post-new.php?post_type=page')); ?>"><?php _e('Adicionar Nova Página', 'thabatta-adv'); ?></a></li>
                    <li><a href="<?php echo esc_url(admin_url('post-new.php?post_type=area_atuacao')); ?>"><?php _e('Adicionar Nova Área de Atuação', 'thabatta-adv'); ?></a></li>
                    <li><a href="<?php echo esc_url(admin_url('post-new.php?post_type=equipe')); ?>"><?php _e('Adicionar Novo Membro da Equipe', 'thabatta-adv'); ?></a></li>
                    <li><a href="<?php echo esc_url(admin_url('admin.php?page=thabatta-theme-settings')); ?>"><?php _e('Configurações do Tema', 'thabatta-adv'); ?></a></li>
                </ul>
            </div>
        </div>
        <?php
    }

    /**
     * Adicionar coluna de SEO na lista de posts
     */
    public function add_seo_column($columns)
    {
        $new_columns = array();

        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;

            // Adicionar coluna de SEO após o título
            if ($key === 'title') {
                $new_columns['thabatta_seo'] = __('SEO', 'thabatta-adv');
            }
        }

        return $new_columns;
    }

    /**
     * Exibir conteúdo da coluna de SEO
     */
    public function display_seo_column($column, $post_id)
    {
        if ($column === 'thabatta_seo') {
            // Verificar se há meta título e descrição
            $meta_title = get_post_meta($post_id, '_thabatta_meta_title', true);
            $meta_description = get_post_meta($post_id, '_thabatta_meta_description', true);
            $no_index = get_post_meta($post_id, '_thabatta_no_index', true);
            $no_follow = get_post_meta($post_id, '_thabatta_no_follow', true);

            // Determinar status de SEO
            $seo_status = 'good';
            $seo_message = __('Bom', 'thabatta-adv');

            if ($no_index === '1') {
                $seo_status = 'warning';
                $seo_message = __('Não Indexado', 'thabatta-adv');
            } elseif (empty($meta_description)) {
                $seo_status = 'warning';
                $seo_message = __('Sem Descrição', 'thabatta-adv');
            }

            // Exibir indicador de status
            echo '<div class="thabatta-seo-status thabatta-seo-' . esc_attr($seo_status) . '">';
            echo '<span class="thabatta-seo-indicator"></span>';
            echo '<span class="thabatta-seo-text">' . esc_html($seo_message) . '</span>';
            echo '</div>';
        }
    }

    /**
     * Adicionar filtro de posts por status de SEO
     */
    public function add_seo_filter()
    {
        global $typenow;

        // Verificar se estamos na lista de posts ou páginas
        if (in_array($typenow, array('post', 'page', 'area_atuacao'))) {
            $current_seo_filter = isset($_GET['thabatta_seo_filter']) ? $_GET['thabatta_seo_filter'] : '';

            ?>
            <select name="thabatta_seo_filter" id="thabatta_seo_filter">
                <option value=""><?php _e('Todos os status de SEO', 'thabatta-adv'); ?></option>
                <option value="no_meta" <?php selected($current_seo_filter, 'no_meta'); ?>><?php _e('Sem Meta Descrição', 'thabatta-adv'); ?></option>
                <option value="no_index" <?php selected($current_seo_filter, 'no_index'); ?>><?php _e('Não Indexado', 'thabatta-adv'); ?></option>
                <option value="good" <?php selected($current_seo_filter, 'good'); ?>><?php _e('SEO Bom', 'thabatta-adv'); ?></option>
            </select>
            <?php
        }
    }

    /**
     * Filtrar posts por status de SEO
     */
    public function filter_posts_by_seo_status($query)
    {
        global $pagenow;

        // Verificar se estamos na lista de posts e há um filtro de SEO
        if (is_admin() && $pagenow === 'edit.php' && isset($_GET['thabatta_seo_filter']) && $_GET['thabatta_seo_filter'] !== '') {
            $meta_query = array();

            switch ($_GET['thabatta_seo_filter']) {
                case 'no_meta':
                    // Posts sem meta descrição
                    $meta_query[] = array(
                        'key' => '_thabatta_meta_description',
                        'compare' => 'NOT EXISTS'
                    );
                    break;

                case 'no_index':
                    // Posts com noindex
                    $meta_query[] = array(
                        'key' => '_thabatta_no_index',
                        'value' => '1',
                        'compare' => '='
                    );
                    break;

                case 'good':
                    // Posts com boa configuração de SEO
                    $meta_query['relation'] = 'AND';
                    $meta_query[] = array(
                        'key' => '_thabatta_meta_description',
                        'compare' => 'EXISTS'
                    );
                    $meta_query[] = array(
                        'key' => '_thabatta_no_index',
                        'value' => '1',
                        'compare' => '!='
                    );
                    break;
            }

            $query->set('meta_query', $meta_query);
        }

        return $query;
    }
}

// Inicializar a classe
new Thabatta_Admin_Features();
