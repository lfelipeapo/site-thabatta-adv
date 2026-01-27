<?php
/**
 * Funções e definições do tema Thabatta Advocacia
 *
 * @package Thabatta_Advocacia
 */

// Debug
error_reporting(E_ALL);
ini_set('display_errors', 1);   

if (!defined('ABSPATH')) {
    exit; // Saída direta se acessado diretamente
}

// Definição da versão do tema
if (!defined('THABATTA_VERSION')) {
    define('THABATTA_VERSION', wp_get_theme()->get('Version') ? wp_get_theme()->get('Version') : '1.0.0');
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
 * Carregar domínio de texto do tema
 */
function thabatta_load_theme_textdomain() {
    // Garantir que o diretório de idiomas existe
    $locale = apply_filters('theme_locale', determine_locale(), 'thabatta-adv');
    $mofile = get_template_directory() . '/languages/thabatta-adv-' . $locale . '.mo';
    
    // Carregar a tradução
    load_textdomain('thabatta-adv', $mofile);
    
    // Método padrão (backup)
    load_theme_textdomain('thabatta-adv', THABATTA_THEME_DIR . '/languages');
}
add_action('init', 'thabatta_load_theme_textdomain', 100);

// Corrigir aviso de carregamento precoce de tradução do jwt-auth
add_action('init', function() {
    if ( function_exists('load_plugin_textdomain') ) {
        load_plugin_textdomain(
            'jwt-auth', 
            false, 
            WP_PLUGIN_DIR . '/jwt-auth/languages'
        );
    }
}, 5);


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
 * Função para verificar se a página atual é a página inicial
 */
function thabatta_is_frontpage() {
    return (is_front_page() && !is_home());
}

/**
 * Incluir formulário de consulta em todas as páginas
 */
function thabatta_include_consultation_form() {
    get_template_part('template-parts/form-consultation');
}
add_action('wp_footer', 'thabatta_include_consultation_form', 20);

/**
 * Incluir arquivos adicionais
 */
require THABATTA_THEME_DIR . '/inc/template-functions.php';
require THABATTA_THEME_DIR . '/inc/template-tags.php';
require THABATTA_THEME_DIR . '/inc/form-helpers.php';
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
 * Adicionar suporte a blocos reutilizáveis no menu admin
 */
function thabatta_reusable_blocks_admin_menu() {
    add_menu_page(
        esc_html__('Blocos Reutilizáveis', 'thabatta-adv'),
        esc_html__('Blocos Reutilizáveis', 'thabatta-adv'),
        'edit_posts',
        'edit.php?post_type=wp_block',
        null, // Callback pode ser null
        'dashicons-editor-table',
        22
    );
}
add_action('admin_menu', 'thabatta_reusable_blocks_admin_menu');

/**
 * Adicionar opções de tema no ACF (se ACF Pro estiver ativo)
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
 * Adicionar classes ao body
 */
function thabatta_body_classes($classes, $class = '')
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

// Verificar se o ACF está ativo
if (class_exists('ACF')) {
    require THABATTA_THEME_DIR . '/inc/admin/admin-features.php';
    require THABATTA_THEME_DIR . '/inc/admin/jetpack-integration.php';
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
 * Permitir upload de mais tipos de arquivo
 */
function thabatta_mime_types($mimes) {
    // Adicionar suporte para SVG
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    
    // Adicionar suporte para WebP
    $mimes['webp'] = 'image/webp';
    
    // Adicionar suporte para documentos
    $mimes['doc'] = 'application/msword';
    $mimes['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    $mimes['xls'] = 'application/vnd.ms-excel';
    $mimes['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    $mimes['ppt'] = 'application/vnd.ms-powerpoint';
    $mimes['pptx'] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
    
    // Adicionar suporte para PDF
    $mimes['pdf'] = 'application/pdf';
    
    // Adicionar suporte para arquivos de texto
    $mimes['txt'] = 'text/plain';
    
    return $mimes;
}
add_filter('upload_mimes', 'thabatta_mime_types');

/**
 * Aumentar limite de upload
 */
function thabatta_upload_size_limit($size) {
    return 64 * 1024 * 1024; // 64MB em bytes
}
add_filter('upload_size_limit', 'thabatta_upload_size_limit');

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

/**
 * Adiciona página de opções do tema no menu do WordPress.
 */


/**
 * Importar manipuladores AJAX
 */
require get_template_directory() . '/inc/ajax-handlers.php';


// 1) registra a rewrite rule
add_action('init', function () {
    add_rewrite_tag('%wpai_admin_path%', '([^&]+)');
    add_rewrite_rule('^admin/(.+)/?$', 'index.php?wpai_admin_path=$matches[1]', 'top');
});

// 2) expõe a query var
add_filter('query_vars', function ($vars) {
    $vars[] = 'wpai_admin_path';
    return $vars;
});

// 3) antes do WP buscar posts, altera o main query
add_action('pre_get_posts', function (\WP_Query $query) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    $path = get_query_var('wpai_admin_path');
    if ( empty($path) ) {
        return;
    }

    $slug = basename( trim($path, '/') );

    $query->set('name',      $slug);
    $query->set('post_type', 'post');

    // ajusta flags de template
    $query->is_single   = true;
    $query->is_singular = true;
    $query->is_home     = false;
    $query->is_archive  = false;
    $query->is_404      = false;
});

// 4) no carregamento do template, força a admin‐bar
add_action('template_redirect', function(){
    if ( get_query_var('wpai_admin_path') ) {
        // pra garantir que o WP injete os estilos/js da barra
        show_admin_bar(true);
    }
});

add_filter('allowed_http_origins', function (array $origins) {
    $origins[] = 'oaidalleapiprodscus.blob.core.windows.net';
    return $origins;
});

/**
 * Remove os botões de compartilhamento do Jetpack apenas em páginas de blog e arquivos
 */
function thabatta_remove_jetpack_sharing() {
    // Verifica se é uma página de blog ou arquivo
    if (is_home() || is_archive() || is_category() || is_tag() || is_author() || is_date()) {
        remove_filter('the_content', 'sharing_display', 19);
        remove_filter('the_excerpt', 'sharing_display', 19);
        
        if (class_exists('Jetpack_Likes')) {
            remove_filter('the_content', array(Jetpack_Likes::init(), 'post_likes'), 30);
        }
    }
}
add_action('loop_start', 'thabatta_remove_jetpack_sharing');

/**
 * Ajusta o número de posts por página em diferentes tipos de arquivo
 */
function thabatta_posts_per_page($query) {
    // Não afeta queries do admin
    if (!is_admin() && $query->is_main_query()) {
        // Página de blog
        if (is_page_template('page-blog.php')) {
            $query->set('posts_per_page', 10);
        }
        // Arquivos, categorias e outras páginas de arquivo
        elseif (is_archive() || is_category() || is_tag() || is_author() || is_date()) {
            $query->set('posts_per_page', 5);
        }
    }
}
add_action('pre_get_posts', 'thabatta_posts_per_page');
