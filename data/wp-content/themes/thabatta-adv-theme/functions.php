<?php
/**
 * Funções e definições do tema Thabatta Advocacia
 *
 * @package Thabatta_Advocacia
 */

if (!defined('ABSPATH')) {
    exit; // Saída direta se acessado diretamente
}

// Definir constantes do tema
define('THABATTA_THEME_DIR', get_template_directory());
define('THABATTA_THEME_URI', get_template_directory_uri());
define('THABATTA_THEME_VERSION', '1.0.0');

/**
 * Configuração do tema
 */
function thabatta_setup() {
    // Adicionar suporte a título automático para documentos
    add_theme_support('title-tag');

    // Adicionar suporte a miniaturas em posts e páginas
    add_theme_support('post-thumbnails');

    // Adicionar suporte a HTML5 para formulários de pesquisa, comentários, etc.
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // Adicionar suporte a logotipo personalizado
    add_theme_support('custom-logo', array(
        'height'      => 150,
        'width'       => 300,
        'flex-width'  => true,
        'flex-height' => true,
    ));

    // Registrar menus de navegação
    register_nav_menus(array(
        'primary' => esc_html__('Menu Principal', 'thabatta-adv'),
        'footer'  => esc_html__('Menu Rodapé', 'thabatta-adv'),
    ));

    // Adicionar tamanhos de imagem personalizados
    add_image_size('thabatta-featured', 1200, 600, true);
    add_image_size('thabatta-card', 400, 300, true);
    add_image_size('thabatta-thumbnail', 150, 150, true);
}
add_action('after_setup_theme', 'thabatta_setup');

/**
 * Registrar e carregar scripts e estilos
 */
function thabatta_scripts() {
    // Estilos
    wp_enqueue_style('thabatta-style', get_stylesheet_uri(), array(), THABATTA_THEME_VERSION);
    wp_enqueue_style('thabatta-main', THABATTA_THEME_URI . '/assets/css/main.min.css', array(), THABATTA_THEME_VERSION);
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css', array(), '6.0.0');
    
    // Scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script('slick-slider', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', array('jquery'), '1.8.1', true);
    wp_enqueue_script('thabatta-main', THABATTA_THEME_URI . '/assets/js/main.min.js', array('jquery'), THABATTA_THEME_VERSION, true);

    // Passar variáveis para o JavaScript
    wp_localize_script('thabatta-main', 'thabattaData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('thabatta_nonce'),
    ));

    // Adicionar script de comentários apenas quando necessário
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'thabatta_scripts');

/**
 * Registrar áreas de widgets
 */
function thabatta_widgets_init() {
    register_sidebar(array(
        'name'          => esc_html__('Sidebar Principal', 'thabatta-adv'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Adicione widgets aqui para aparecerem na sidebar.', 'thabatta-adv'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar(array(
        'name'          => esc_html__('Rodapé 1', 'thabatta-adv'),
        'id'            => 'footer-1',
        'description'   => esc_html__('Primeira coluna do rodapé.', 'thabatta-adv'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => esc_html__('Rodapé 2', 'thabatta-adv'),
        'id'            => 'footer-2',
        'description'   => esc_html__('Segunda coluna do rodapé.', 'thabatta-adv'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => esc_html__('Rodapé 3', 'thabatta-adv'),
        'id'            => 'footer-3',
        'description'   => esc_html__('Terceira coluna do rodapé.', 'thabatta-adv'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => esc_html__('Rodapé 4', 'thabatta-adv'),
        'id'            => 'footer-4',
        'description'   => esc_html__('Quarta coluna do rodapé.', 'thabatta-adv'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'thabatta_widgets_init');

/**
 * Limitar o tamanho do resumo
 */
function thabatta_custom_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'thabatta_custom_excerpt_length', 999);

/**
 * Alterar o sufixo do resumo
 */
function thabatta_custom_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'thabatta_custom_excerpt_more');

/**
 * Função para verificar se a página atual é a página inicial
 */
function thabatta_is_frontpage() {
    return (is_front_page() && !is_home());
}

/**
 * Incluir arquivos adicionais
 */
require THABATTA_THEME_DIR . '/inc/template-functions.php';
require THABATTA_THEME_DIR . '/inc/template-tags.php';
require THABATTA_THEME_DIR . '/inc/customizer.php';
require THABATTA_THEME_DIR . '/inc/acf-fields.php';
require THABATTA_THEME_DIR . '/inc/jetpack-integration.php';
require THABATTA_THEME_DIR . '/inc/security.php';

/**
 * Filtrar comentários para evitar spam e phishing
 */
function thabatta_filter_comment($commentdata) {
    $comment_content = $commentdata['comment_content'];
    
    // Lista de palavras e padrões suspeitos
    $suspicious_patterns = array(
        '/\b(?:viagra|cialis|levitra|pharmacy|casino|poker|loan|payday|sex|xxx)\b/i',
        '/https?:\/\/(?!(?:(?:www\.|(?:m\.))?(?:youtube\.com|youtu\.be|vimeo\.com|twitter\.com|facebook\.com|linkedin\.com|instagram\.com)))[^\s]+/i',
        '/<a\s+href/i',
        '/\[url=/i',
    );
    
    // Verificar padrões suspeitos
    foreach ($suspicious_patterns as $pattern) {
        if (preg_match($pattern, $comment_content)) {
            wp_die(esc_html__('Comentário bloqueado por conter conteúdo suspeito. Por favor, remova links ou palavras suspeitas e tente novamente.', 'thabatta-adv'), '', array('response' => 403));
        }
    }
    
    // Verificar número excessivo de links (possível spam)
    $link_count = substr_count(strtolower($comment_content), 'http');
    if ($link_count > 2) {
        wp_die(esc_html__('Comentário bloqueado por conter muitos links. Por favor, limite o número de links e tente novamente.', 'thabatta-adv'), '', array('response' => 403));
    }
    
    return $commentdata;
}
add_filter('preprocess_comment', 'thabatta_filter_comment');

/**
 * Sanitizar entradas de pesquisa para evitar ataques
 */
function thabatta_sanitize_search_query($query) {
    return sanitize_text_field($query);
}
add_filter('get_search_query', 'thabatta_sanitize_search_query');

/**
 * Registrar tipos de post personalizados
 */
function thabatta_register_post_types() {
    // Tipo de post para Áreas de Atuação
    register_post_type('area_atuacao', array(
        'labels' => array(
            'name'               => esc_html__('Áreas de Atuação', 'thabatta-adv'),
            'singular_name'      => esc_html__('Área de Atuação', 'thabatta-adv'),
            'add_new'            => esc_html__('Adicionar Nova', 'thabatta-adv'),
            'add_new_item'       => esc_html__('Adicionar Nova Área', 'thabatta-adv'),
            'edit_item'          => esc_html__('Editar Área', 'thabatta-adv'),
            'new_item'           => esc_html__('Nova Área', 'thabatta-adv'),
            'view_item'          => esc_html__('Ver Área', 'thabatta-adv'),
            'search_items'       => esc_html__('Buscar Áreas', 'thabatta-adv'),
            'not_found'          => esc_html__('Nenhuma área encontrada', 'thabatta-adv'),
            'not_found_in_trash' => esc_html__('Nenhuma área encontrada na lixeira', 'thabatta-adv'),
            'menu_name'          => esc_html__('Áreas de Atuação', 'thabatta-adv'),
        ),
        'public'              => true,
        'hierarchical'        => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-portfolio',
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
        'has_archive'         => true,
        'rewrite'             => array('slug' => 'areas-de-atuacao'),
        'show_in_rest'        => true,
    ));

    // Tipo de post para Equipe
    register_post_type('equipe', array(
        'labels' => array(
            'name'               => esc_html__('Equipe', 'thabatta-adv'),
            'singular_name'      => esc_html__('Membro da Equipe', 'thabatta-adv'),
            'add_new'            => esc_html__('Adicionar Novo', 'thabatta-adv'),
            'add_new_item'       => esc_html__('Adicionar Novo Membro', 'thabatta-adv'),
            'edit_item'          => esc_html__('Editar Membro', 'thabatta-adv'),
            'new_item'           => esc_html__('Novo Membro', 'thabatta-adv'),
            'view_item'          => esc_html__('Ver Membro', 'thabatta-adv'),
            'search_items'       => esc_html__('Buscar Membros', 'thabatta-adv'),
            'not_found'          => esc_html__('Nenhum membro encontrado', 'thabatta-adv'),
            'not_found_in_trash' => esc_html__('Nenhum membro encontrado na lixeira', 'thabatta-adv'),
            'menu_name'          => esc_html__('Equipe', 'thabatta-adv'),
        ),
        'public'              => true,
        'hierarchical'        => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 6,
        'menu_icon'           => 'dashicons-groups',
        'supports'            => array('title', 'editor', 'thumbnail'),
        'has_archive'         => true,
        'rewrite'             => array('slug' => 'equipe'),
        'show_in_rest'        => true,
    ));

    // Tipo de post para Depoimentos
    register_post_type('depoimento', array(
        'labels' => array(
            'name'               => esc_html__('Depoimentos', 'thabatta-adv'),
            'singular_name'      => esc_html__('Depoimento', 'thabatta-adv'),
            'add_new'            => esc_html__('Adicionar Novo', 'thabatta-adv'),
            'add_new_item'       => esc_html__('Adicionar Novo Depoimento', 'thabatta-adv'),
            'edit_item'          => esc_html__('Editar Depoimento', 'thabatta-adv'),
            'new_item'           => esc_html__('Novo Depoimento', 'thabatta-adv'),
            'view_item'          => esc_html__('Ver Depoimento', 'thabatta-adv'),
            'search_items'       => esc_html__('Buscar Depoimentos', 'thabatta-adv'),
            'not_found'          => esc_html__('Nenhum depoimento encontrado', 'thabatta-adv'),
            'not_found_in_trash' => esc_html__('Nenhum depoimento encontrado na lixeira', 'thabatta-adv'),
            'menu_name'          => esc_html__('Depoimentos', 'thabatta-adv'),
        ),
        'public'              => true,
        'hierarchical'        => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 7,
        'menu_icon'           => 'dashicons-format-quote',
        'supports'            => array('title', 'editor', 'thumbnail'),
        'has_archive'         => false,
        'rewrite'             => array('slug' => 'depoimentos'),
        'show_in_rest'        => true,
    ));
}
add_action('init', 'thabatta_register_post_types');

/**
 * Registrar taxonomias personalizadas
 */
function thabatta_register_taxonomies() {
    // Taxonomia para Categorias de Áreas de Atuação
    register_taxonomy('categoria_area', 'area_atuacao', array(
        'labels' => array(
            'name'              => esc_html__('Categorias de Áreas', 'thabatta-adv'),
            'singular_name'     => esc_html__('Categoria de Área', 'thabatta-adv'),
            'search_items'      => esc_html__('Buscar Categorias', 'thabatta-adv'),
            'all_items'         => esc_html__('Todas as Categorias', 'thabatta-adv'),
            'parent_item'       => esc_html__('Categoria Pai', 'thabatta-adv'),
            'parent_item_colon' => esc_html__('Categoria Pai:', 'thabatta-adv'),
            'edit_item'         => esc_html__('Editar Categoria', 'thabatta-adv'),
            'update_item'       => esc_html__('Atualizar Categoria', 'thabatta-adv'),
            'add_new_item'      => esc_html__('Adicionar Nova Categoria', 'thabatta-adv'),
            'new_item_name'     => esc_html__('Nova Categoria', 'thabatta-adv'),
            'menu_name'         => esc_html__('Categorias', 'thabatta-adv'),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'categoria-area'),
        'show_in_rest'      => true,
    ));
}
add_action('init', 'thabatta_register_taxonomies');

/**
 * Adicionar suporte a blocos reutilizáveis no menu admin
 */
function thabatta_reusable_blocks_admin_menu() {
    add_menu_page(
        esc_html__('Blocos Reutilizáveis', 'thabatta-adv'),
        esc_html__('Blocos Reutilizáveis', 'thabatta-adv'),
        'edit_posts',
        'edit.php?post_type=wp_block',
        '',
        'dashicons-editor-table',
        22
    );
}
add_action('admin_menu', 'thabatta_reusable_blocks_admin_menu');

/**
 * Adicionar opções de tema no ACF
 */
if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title' => esc_html__('Opções do Tema', 'thabatta-adv'),
        'menu_title' => esc_html__('Opções do Tema', 'thabatta-adv'),
        'menu_slug'  => 'theme-general-settings',
        'capability' => 'edit_posts',
        'redirect'   => false,
        'icon_url'   => 'dashicons-admin-customizer',
        'position'   => 59,
    ));

    acf_add_options_sub_page(array(
        'page_title'  => esc_html__('Redes Sociais', 'thabatta-adv'),
        'menu_title'  => esc_html__('Redes Sociais', 'thabatta-adv'),
        'parent_slug' => 'theme-general-settings',
    ));

    acf_add_options_sub_page(array(
        'page_title'  => esc_html__('Informações de Contato', 'thabatta-adv'),
        'menu_title'  => esc_html__('Informações de Contato', 'thabatta-adv'),
        'parent_slug' => 'theme-general-settings',
    ));
}

/**
 * Adicionar meta box para relacionar posts
 */
function thabatta_add_related_posts_meta_box() {
    add_meta_box(
        'thabatta_related_posts',
        esc_html__('Posts Relacionados', 'thabatta-adv'),
        'thabatta_related_posts_meta_box_callback',
        array('post', 'page', 'area_atuacao'),
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'thabatta_add_related_posts_meta_box');

/**
 * Callback para a meta box de posts relacionados
 */
function thabatta_related_posts_meta_box_callback($post)
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
 * Salvar dados da meta box de posts relacionados
 */
function thabatta_save_related_posts_meta_box($post_id)
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
add_action('save_post', 'thabatta_save_related_posts_meta_box');

/**
 * Obter posts relacionados
 */
function thabatta_get_related_posts($post_id = null, $limit = 3)
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    // Obter posts relacionados manualmente definidos
    $manual_related = get_post_meta($post_id, '_thabatta_related_posts', true);

    if (is_array($manual_related) && !empty($manual_related)) {
        // Limitar número de posts
        $manual_related = array_slice($manual_related, 0, $limit);

        // Buscar posts
        $related_posts = get_posts(array(
            'post__in' => $manual_related,
            'post_type' => array('post', 'page', 'area_atuacao'),
            'posts_per_page' => $limit,
            'orderby' => 'post__in'
        ));

        return $related_posts;
    }

    // Se não houver posts relacionados manualmente, buscar por categoria/tag
    $current_post_type = get_post_type($post_id);

    if ($current_post_type === 'post') {
        // Obter categorias e tags do post atual
        $categories = wp_get_post_categories($post_id);
        $tags = wp_get_post_tags($post_id, array('fields' => 'ids'));

        // Configurar argumentos para busca de posts relacionados
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => $limit,
            'post__not_in' => array($post_id),
            'orderby' => 'rand'
        );

        if (!empty($categories)) {
            $args['category__in'] = $categories;
        }

        if (!empty($tags)) {
            $args['tag__in'] = $tags;
        }

        // Buscar posts relacionados
        $related_posts = get_posts($args);

        return $related_posts;
    } elseif ($current_post_type === 'area_atuacao') {
        // Obter termos de especialidade
        $especialidades = wp_get_object_terms($post_id, 'especialidade', array('fields' => 'ids'));

        // Configurar argumentos para busca de posts relacionados
        $args = array(
            'post_type' => 'area_atuacao',
            'posts_per_page' => $limit,
            'post__not_in' => array($post_id),
            'orderby' => 'rand'
        );

        if (!empty($especialidades)) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'especialidade',
                    'field' => 'id',
                    'terms' => $especialidades
                )
            );
        }

        // Buscar posts relacionados
        $related_posts = get_posts($args);

        return $related_posts;
    }

    return array();
}

/**
 * Registrar scripts e estilos
 */
function thabatta_enqueue_scripts()
{
    // Versão do tema
    $theme_version = wp_get_theme()->get('Version');

    // Estilos
    wp_enqueue_style('thabatta-style', get_stylesheet_uri(), array(), $theme_version);
    wp_enqueue_style('thabatta-main', get_template_directory_uri() . '/assets/css/main.css', array(), $theme_version);

    // Google Fonts
    wp_enqueue_style('thabatta-fonts', 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap', array(), null);

    // Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', array(), '5.15.4');

    // Scripts
    wp_enqueue_script('thabatta-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), $theme_version, true);
    wp_enqueue_script('thabatta-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), $theme_version, true);

    // Comentários
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    // Adicionar variáveis para o script
    wp_localize_script('thabatta-main', 'thabattaSettings', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'themeUrl' => get_template_directory_uri(),
        'siteUrl' => site_url(),
        'isHome' => is_home() || is_front_page() ? 1 : 0
    ));
}
add_action('wp_enqueue_scripts', 'thabatta_enqueue_scripts');

/**
 * Registrar scripts e estilos para o admin
 */
function thabatta_admin_enqueue_scripts()
{
    // Versão do tema
    $theme_version = wp_get_theme()->get('Version');

    // Estilos
    wp_enqueue_style('thabatta-admin-style', get_template_directory_uri() . '/assets/css/admin.css', array(), $theme_version);

    // Scripts
    wp_enqueue_script('thabatta-admin-script', get_template_directory_uri() . '/assets/js/admin.js', array('jquery'), $theme_version, true);

    // Adicionar variáveis para o script
    wp_localize_script('thabatta-admin-script', 'thabattaAdminSettings', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'themeUrl' => get_template_directory_uri(),
        'nonce' => wp_create_nonce('thabatta_admin_nonce')
    ));
}
add_action('admin_enqueue_scripts', 'thabatta_admin_enqueue_scripts');

/**
 * Adicionar classes ao body
 */
function thabatta_body_classes($classes)
{
    // Adicionar classe para páginas com sidebar
    if (is_active_sidebar('sidebar-1') && !is_page_template('templates/full-width.php')) {
        $classes[] = 'has-sidebar';
    } else {
        $classes[] = 'no-sidebar';
    }

    // Adicionar classe para página inicial
    if (is_home() || is_front_page()) {
        $classes[] = 'home-page';
    }

    // Adicionar classe para páginas de arquivo
    if (is_archive()) {
        $classes[] = 'archive-page';
    }

    // Adicionar classe para páginas de busca
    if (is_search()) {
        $classes[] = 'search-page';
    }

    // Adicionar classe para posts individuais
    if (is_singular('post')) {
        $classes[] = 'single-post-page';
    }

    // Adicionar classe para áreas de atuação
    if (is_singular('area_atuacao')) {
        $classes[] = 'area-atuacao-page';
    }

    // Adicionar classe para equipe
    if (is_singular('equipe')) {
        $classes[] = 'equipe-page';
    }

    return $classes;
}
add_filter('body_class', 'thabatta_body_classes');

/**
 * Filtrar o título do arquivo
 */
function thabatta_get_the_archive_title($title)
{
    if (is_category()) {
        $title = single_cat_title('', false);
    } elseif (is_tag()) {
        $title = single_tag_title('', false);
    } elseif (is_author()) {
        $title = get_the_author();
    } elseif (is_post_type_archive()) {
        $title = post_type_archive_title('', false);
    } elseif (is_tax()) {
        $title = single_term_title('', false);
    }

    return $title;
}
add_filter('get_the_archive_title', 'thabatta_get_the_archive_title');

/**
 * Filtrar o conteúdo para adicionar classes às imagens
 */
function thabatta_content_image_class($content)
{
    // Adicionar classe img-fluid às imagens
    $content = preg_replace('/<img(.*?)class="(.*?)"(.*?)>/i', '<img$1class="$2 img-fluid"$3>', $content);
    $content = preg_replace('/<img((?:(?!class=).)*?)>/i', '<img class="img-fluid"$1>', $content);

    return $content;
}
add_filter('the_content', 'thabatta_content_image_class');

/**
 * Incluir arquivos de componentes
 */
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/security-features.php';
require get_template_directory() . '/inc/jetpack-integration.php';
require get_template_directory() . '/inc/acf-fields.php';
require get_template_directory() . '/inc/web-components.php';
require get_template_directory() . '/inc/github-actions.php';

// Verificar se o ACF está ativo
if (class_exists('ACF')) {
    require get_template_directory() . '/inc/admin/admin-features.php';
    require get_template_directory() . '/inc/admin/jetpack-integration.php';
}

/**
 * Personalizar o comprimento do resumo
 */
function thabatta_excerpt_length($length)
{
    return 30;
}
add_filter('excerpt_length', 'thabatta_excerpt_length');

/**
 * Personalizar o "Leia mais" do resumo
 */
function thabatta_excerpt_more($more)
{
    return '... <a href="' . esc_url(get_permalink()) . '" class="read-more">' . __('Leia mais', 'thabatta-adv') . ' <i class="fas fa-arrow-right"></i></a>';
}
add_filter('excerpt_more', 'thabatta_excerpt_more');


/**
 * Adicionar suporte para SVG no uploader de mídia
 */
function thabatta_mime_types($mimes)
{
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'thabatta_mime_types');

/**
 * Corrigir exibição de SVG no painel administrativo
 */
function thabatta_fix_svg_thumb_display()
{
    echo '<style>
        td.media-icon img[src$=".svg"], img[src$=".svg"].attachment-post-thumbnail { 
            width: 100% !important; 
            height: auto !important; 
        }
    </style>';
}
add_action('admin_head', 'thabatta_fix_svg_thumb_display');

/**
 * Adicionar suporte para visualização de SVG no Media Library
 */
function thabatta_svg_media_thumbnails($response, $attachment, $meta)
{
    if ($response['type'] === 'image' && $response['subtype'] === 'svg+xml') {
        $attachment_url = $response['url'];
        $response['image'] = [
            'src' => $attachment_url
        ];
    }
    return $response;
}
add_filter('wp_prepare_attachment_for_js', 'thabatta_svg_media_thumbnails', 10, 3);

/**
 * Sanitizar arquivos SVG durante o upload
 */
function thabatta_sanitize_svg($file)
{
    if ($file['type'] === 'image/svg+xml') {
        // Verificar se o arquivo é realmente um SVG
        $file_content = file_get_contents($file['tmp_name']);

        // Verificar se o conteúdo começa com tag SVG
        if (strpos($file_content, '<svg') === false) {
            $file['error'] = __('O arquivo enviado não é um SVG válido.', 'thabatta-adv');
            return $file;
        }

        // Sanitizar o SVG (remover scripts e atributos perigosos)
        $sanitized_content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $file_content);
        $sanitized_content = preg_replace('/on\w+="[^"]*"/i', '', $sanitized_content);
        $sanitized_content = preg_replace('/on\w+=\'[^\']*\'/i', '', $sanitized_content);

        // Salvar o SVG sanitizado
        file_put_contents($file['tmp_name'], $sanitized_content);
    }

    return $file;
}
add_filter('wp_handle_upload_prefilter', 'thabatta_sanitize_svg');

/**
 * Adicionar classes aos links de imagens
 */
function thabatta_add_image_link_class($html, $id, $caption, $title, $align, $url, $size, $alt)
{
    $classes = 'image-link';

    if (strpos($html, '<a href=') !== false) {
        $html = str_replace('<a href=', '<a class="' . $classes . '" href=', $html);
    }

    return $html;
}
add_filter('image_send_to_editor', 'thabatta_add_image_link_class', 10, 8);

/**
 * Adicionar classes às imagens no conteúdo
 */
function thabatta_add_image_class($content)
{
    $content = preg_replace('/(<img[^>]+class=")[^"]*(")/i', '$1img-fluid $2', $content);
    $content = preg_replace('/(<img[^>]+)(\/?>)/i', '$1 class="img-fluid" $2', $content);

    return $content;
}
add_filter('the_content', 'thabatta_add_image_class');

/**
 * Adicionar suporte para lazy loading de imagens
 */
function thabatta_add_lazy_loading($content)
{
    if (is_admin() || is_feed()) {
        return $content;
    }

    $content = preg_replace('/(<img[^>]+)(\/?>)/i', '$1 loading="lazy" $2', $content);

    return $content;
}
add_filter('the_content', 'thabatta_add_lazy_loading');
