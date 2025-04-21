<?php
/**
 * Plugin Name: Thabatta Web Components CPT
 * Description: Cria um Custom Post Type para gerenciar Web Components
 * Version: 1.0.0
 * Author: Thabatta
 * Text Domain: thabatta-web-components
 */

if (!defined('ABSPATH')) {
    exit; // Saída direta se acessado diretamente
}

/**
 * Registra o Custom Post Type para Web Components
 */
function thabatta_register_web_component_post_type() {
    register_post_type('web_component', array(
        'labels' => array(
            'name' => 'Web Components',
            'singular_name' => 'Web Component',
        ),
        'public' => true,
        'has_archive' => false,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-editor-code',
        'supports' => array('title'),
    ));
}
add_action('init', 'thabatta_register_web_component_post_type');

/**
 * Adiciona meta box para configuração do Web Component
 */
function thabatta_add_web_component_meta_box() {
    add_meta_box(
        'thabatta_web_component_meta',
        'Configuração do Web Component',
        'thabatta_render_web_component_meta_box',
        'web_component',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'thabatta_add_web_component_meta_box');

/**
 * Renderiza o meta box para configuração do Web Component
 */
function thabatta_render_web_component_meta_box($post) {
    $fields = array(
        'tag_name' => 'Tag Name',
        'html_code' => 'HTML',
        'css_code' => 'CSS',
        'js_code' => 'JS',
        'use_shadow_dom' => 'Usar Shadow DOM?',
        'shadow_dom_mode' => 'Modo do Shadow DOM (open/closed)',
    );

    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, $key, true);
        echo "<p><label for='{$key}'><strong>{$label}</strong></label><br>";

        if ($key === 'use_shadow_dom') {
            echo "<input type='checkbox' name='{$key}' id='{$key}' value='1' " . checked($value, '1', false) . " />";
        } elseif ($key === 'shadow_dom_mode') {
            echo "<select name='{$key}' id='{$key}'>
                <option value='open' " . selected($value, 'open', false) . ">open</option>
                <option value='closed' " . selected($value, 'closed', false) . ">closed</option>
            </select>";
        } elseif (str_ends_with($key, '_code')) {
            echo "<textarea name='{$key}' id='{$key}' rows='5' style='width:100%'>" . esc_textarea($value) . "</textarea>";
        } else {
            echo "<input type='text' name='{$key}' id='{$key}' value='" . esc_attr($value) . "' style='width:100%' />";
        }

        echo "</p>";
    }
}

/**
 * Salva os metadados do Web Component
 */
function thabatta_save_web_component_meta($post_id) {
    $keys = ['tag_name', 'html_code', 'css_code', 'js_code', 'use_shadow_dom', 'shadow_dom_mode'];
    foreach ($keys as $key) {
        if (isset($_POST[$key])) {
            if ( in_array($key, ['html_code','css_code','js_code'], true) ) {
                $value = sanitize_textarea_field( $_POST[$key] );
            } else {
                $value = sanitize_text_field( $_POST[$key] );
            }
            update_post_meta($post_id, $key, $value);
        } elseif ($key === 'use_shadow_dom') {
            update_post_meta($post_id, $key, '0');
        }
    }
}
add_action('save_post', 'thabatta_save_web_component_meta');

/**
 * Função para decodificar e limpar HTML de forma segura
 */
function thabatta_decode_html($html) {
    // Primeiro decodifica entidades HTML
    $html = html_entity_decode(stripslashes($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return $html;
}

/**
 * Função para escapar JavaScript de forma segura
 */
function thabatta_escape_js($js) {
    // Remove comentários de uma linha e múltiplas linhas
    $js = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $js);
    $js = preg_replace('/\/\/.*?(\r\n|\r|\n|$)/', '', $js);
    
    // Remove quebras de linha e espaços extras
    $js = preg_replace('/\s+/', ' ', $js);
    
    return trim($js);
}

/**
 * Função para registrar um componente específico
 */
function thabatta_register_single_component($component) {
    static $registered_components = array();
    if (isset($registered_components[$component->ID])) {
        return;
    }
    $registered_components[$component->ID] = true;

    add_action('wp_footer', function() use ($component) {
        $tag         = esc_attr( get_post_meta($component->ID, 'tag_name', true) );
        $html        = thabatta_decode_html( get_post_meta($component->ID, 'html_code', true) );
        $css         = thabatta_decode_html( get_post_meta($component->ID, 'css_code', true) );
        $js          = thabatta_escape_js( get_post_meta($component->ID, 'js_code', true) );
        $use_shadow  = get_post_meta($component->ID, 'use_shadow_dom', true ) === '1';
        $mode        = esc_attr( get_post_meta($component->ID, 'shadow_dom_mode', true) ?: 'open' );

        echo "<script type='module'>\n";
        echo "if (!customElements.get('{$tag}')) {\n";
        echo "  customElements.define('{$tag}', class extends HTMLElement {\n";
        echo "    constructor() {\n";
        echo "      super();\n";
        if ($use_shadow) {
            echo "      const root = this.attachShadow({ mode: '{$mode}' });\n";
            echo "      const style = document.createElement('style');\n";
            echo "      style.textContent = `{$css}`;\n";
            echo "      root.appendChild(style);\n";
            echo "      const wrapper = document.createElement('div');\n";
            echo "      wrapper.innerHTML = `{$html}`;\n";
            echo "      root.appendChild(wrapper);\n";
        } else {
            echo "      const style = document.createElement('style');\n";
            echo "      style.textContent = `{$css}`;\n";
            echo "      this.appendChild(style);\n";
            echo "      const wrapper = document.createElement('div');\n";
            echo "      wrapper.innerHTML = `{$html}`;\n";
            echo "      this.appendChild(wrapper);\n";
        }
        echo "    }\n";
        echo "    connectedCallback() {\n";
        if (trim($js) !== '') {
            echo "      {$js}\n";
        }
        echo "    }\n";
        echo "  });\n";
        echo "}\n";
        echo "</script>\n";
    }, 20);
}

/**
 * Shortcode para renderizar um web component
 */
function thabatta_web_component_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => 0,
        'tag' => '',
    ), $atts);
    
    if (empty($atts['id']) && empty($atts['tag'])) {
        return '<p>Erro: ID ou tag do componente não especificado.</p>';
    }
    
    $args = array(
        'post_type' => 'web_component',
        'posts_per_page' => 1,
    );
    
    if (!empty($atts['id'])) {
        $args['p'] = $atts['id'];
    } elseif (!empty($atts['tag'])) {
        $args['meta_query'] = array(
            array(
                'key' => 'tag_name',
                'value' => $atts['tag'],
                'compare' => '=',
            ),
        );
    }
    
    $query = new WP_Query($args);
    
    if (!$query->have_posts()) {
        return '<p>Erro: Componente não encontrado.</p>';
    }
    
    $query->the_post();
    $post_id = get_the_ID();
    $tag_name = get_post_meta($post_id, 'tag_name', true);
    
    // Registrar o componente específico
    thabatta_register_single_component($query->post);
    
    wp_reset_postdata();
    
    return "<{$tag_name}></{$tag_name}>";
}

/**
 * Função para detectar e registrar componentes usados na página atual
 */
function thabatta_detect_and_register_components() {
    global $post;
    if (!$post) return;
    
    // Obter conteúdo da página
    $content = $post->post_content;
    
    // Buscar todos os componentes
    $components = get_posts([
        'post_type' => 'web_component',
        'posts_per_page' => -1,
    ]);
    
    foreach ($components as $component) {
        $tag = get_post_meta($component->ID, 'tag_name', true);
        
        // Se a tag do componente está sendo usada na página, registrar o componente
        if (strpos($content, "<{$tag}") !== false || strpos($content, "[thabatta_web_component") !== false) {
            thabatta_register_single_component($component);
        }
    }
}
add_action('wp', 'thabatta_detect_and_register_components');

/**
 * Classe para importar componentes web personalizados da classe Thabatta_Web_Components
 */
class Thabatta_Web_Components_Importer {
    /**
     * Inicializa a classe
     */
    public function __construct() {
        // Adicionar página de administração para importação
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Registrar ação AJAX para importação
        add_action('wp_ajax_thabatta_import_web_component', array($this, 'import_web_component'));
        
        // Adicionar shortcode para renderizar componentes importados
        add_shortcode('thabatta_web_component', array($this, 'web_component_shortcode'));
    }
    
    /**
     * Adiciona página de administração para importação
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=web_component',
            'Importar Componentes',
            'Importar Componentes',
            'manage_options',
            'thabatta-import-components',
            array($this, 'render_import_page')
        );
    }
    
    /**
     * Renderiza a página de importação
     */
    public function render_import_page() {
        ?>
        <div class="wrap">
            <h1>Importar Web Components</h1>
            <p>Selecione os componentes da classe Thabatta_Web_Components que deseja importar:</p>
            
            <form id="thabatta-import-form">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">Componentes Disponíveis</th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text">Componentes Disponíveis</legend>
                                    <label>
                                        <input type="checkbox" name="components[]" value="card"> Card
                                    </label><br>
                                    <label>
                                        <input type="checkbox" name="components[]" value="accordion"> Accordion
                                    </label><br>
                                    <label>
                                        <input type="checkbox" name="components[]" value="tabs"> Tabs
                                    </label><br>
                                    <label>
                                        <input type="checkbox" name="components[]" value="slider"> Slider
                                    </label><br>
                                    <label>
                                        <input type="checkbox" name="components[]" value="testimonial"> Testimonial
                                    </label><br>
                                    <label>
                                        <input type="checkbox" name="components[]" value="cta"> CTA
                                    </label><br>
                                    <label>
                                        <input type="checkbox" name="components[]" value="icon_box"> Icon Box
                                    </label><br>
                                    <label>
                                        <input type="checkbox" name="components[]" value="team_member"> Team Member
                                    </label><br>
                                    <label>
                                        <input type="checkbox" name="components[]" value="counter"> Counter
                                    </label><br>
                                    <label>
                                        <input type="checkbox" name="components[]" value="timeline"> Timeline
                                    </label><br>
                                </fieldset>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <p class="submit">
                    <button type="button" id="thabatta-import-button" class="button button-primary">Importar Componentes Selecionados</button>
                </p>
            </form>
            
            <div id="thabatta-import-results" style="display: none;">
                <h2>Resultados da Importação</h2>
                <div id="thabatta-import-messages"></div>
            </div>
            
            <script>
                jQuery(document).ready(function($) {
                    $('#thabatta-import-button').on('click', function() {
                        var components = [];
                        $('input[name="components[]"]:checked').each(function() {
                            components.push($(this).val());
                        });
                        
                        if (components.length === 0) {
                            alert('Por favor, selecione pelo menos um componente para importar.');
                            return;
                        }
                        
                        $('#thabatta-import-button').prop('disabled', true).text('Importando...');
                        
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'thabatta_import_web_component',
                                components: components,
                                nonce: '<?php echo wp_create_nonce('thabatta_import_web_component'); ?>'
                            },
                            success: function(response) {
                                $('#thabatta-import-results').show();
                                $('#thabatta-import-messages').html(response);
                                $('#thabatta-import-button').prop('disabled', false).text('Importar Componentes Selecionados');
                            },
                            error: function() {
                                $('#thabatta-import-results').show();
                                $('#thabatta-import-messages').html('<div class="notice notice-error"><p>Ocorreu um erro durante a importação. Por favor, tente novamente.</p></div>');
                                $('#thabatta-import-button').prop('disabled', false).text('Importar Componentes Selecionados');
                            }
                        });
                    });
                });
            </script>
        </div>
        <?php
    }
    
    /**
     * Processa a importação de componentes via AJAX
     */
    public function import_web_component() {
        // Verificar nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'thabatta_import_web_component')) {
            wp_send_json_error('Erro de segurança. Por favor, recarregue a página e tente novamente.');
        }
        
        // Verificar permissões
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Você não tem permissão para realizar esta ação.');
        }
        
        // Verificar componentes selecionados
        if (!isset($_POST['components']) || !is_array($_POST['components'])) {
            wp_send_json_error('Nenhum componente selecionado.');
        }
        
        $components = $_POST['components'];
        $results = array();
        
        // Componentes disponíveis e seus detalhes
        $available_components = array(
            'card' => array(
                'tag_name' => 'thabatta-card',
                'html_code' => '<div class="thabatta-card-image">
    <img src="{{image}}" alt="{{title}}">
</div>
<div class="thabatta-card-content">
    <h3 class="thabatta-card-title">{{title}}</h3>
    <div class="thabatta-card-text">{{content}}</div>
    <a href="{{link}}" class="thabatta-card-button">{{button_text}}</a>
</div>',
                'css_code' => '.thabatta-card {
    display: flex;
    flex-direction: column;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.thabatta-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}
.thabatta-card-image img {
    width: 100%;
    height: auto;
    display: block;
}
.thabatta-card-content {
    padding: 20px;
}
.thabatta-card-title {
    margin-top: 0;
    margin-bottom: 10px;
    font-size: 1.5rem;
}
.thabatta-card-text {
    margin-bottom: 20px;
}
.thabatta-card-button {
    display: inline-block;
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}
.thabatta-card-button:hover {
    background-color: #0056b3;
}',
                'js_code' => '// Código JavaScript para o componente Card
console.log("Card component connected");',
                'use_shadow_dom' => '1',
                'shadow_dom_mode' => 'open',
            ),
            'accordion' => array(
                'tag_name' => 'thabatta-accordion',
                'html_code' => '<div class="thabatta-accordion">
    <div class="thabatta-accordion-item">
        <h3 class="thabatta-accordion-header">
            <button class="thabatta-accordion-button" type="button">
                {{title}}
                <span class="thabatta-accordion-icon"></span>
            </button>
        </h3>
        <div class="thabatta-accordion-collapse">
            <div class="thabatta-accordion-body">{{content}}</div>
        </div>
    </div>
</div>',
                'css_code' => '.thabatta-accordion {
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow: hidden;
}
.thabatta-accordion-item {
    border-bottom: 1px solid #ddd;
}
.thabatta-accordion-item:last-child {
    border-bottom: none;
}
.thabatta-accordion-header {
    margin: 0;
}
.thabatta-accordion-button {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    padding: 15px;
    background-color: #f8f9fa;
    border: none;
    text-align: left;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.thabatta-accordion-button:hover {
    background-color: #e9ecef;
}
.thabatta-accordion-icon {
    width: 10px;
    height: 10px;
    border-right: 2px solid #333;
    border-bottom: 2px solid #333;
    transform: rotate(45deg);
    transition: transform 0.3s ease;
}
.thabatta-accordion-button[aria-expanded="true"] .thabatta-accordion-icon {
    transform: rotate(-135deg);
}
.thabatta-accordion-collapse {
    display: none;
}
.thabatta-accordion-collapse[aria-hidden="false"] {
    display: block;
}
.thabatta-accordion-body {
    padding: 15px;
}',
                'js_code' => '// Código JavaScript para o componente Accordion
const buttons = this.querySelectorAll(".thabatta-accordion-button");
buttons.forEach(button => {
    button.addEventListener("click", () => {
        const expanded = button.getAttribute("aria-expanded") === "true";
        button.setAttribute("aria-expanded", !expanded);
        
        const target = button.parentElement.nextElementSibling;
        target.hidden = expanded;
    });
});',
                'use_shadow_dom' => '1',
                'shadow_dom_mode' => 'open',
            ),
            'tabs' => array(
                'tag_name' => 'thabatta-tabs',
                'html_code' => '<div class="thabatta-tabs">
    <div class="thabatta-tabs-nav" role="tablist">
        <button class="thabatta-tab-button active" role="tab" aria-selected="true">Tab 1</button>
        <button class="thabatta-tab-button" role="tab" aria-selected="false">Tab 2</button>
    </div>
    <div class="thabatta-tabs-content">
        <div class="thabatta-tab-panel active" role="tabpanel">Conteúdo da Tab 1</div>
        <div class="thabatta-tab-panel" role="tabpanel" hidden>Conteúdo da Tab 2</div>
    </div>
</div>',
                'css_code' => '.thabatta-tabs {
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow: hidden;
}
.thabatta-tabs-nav {
    display: flex;
    background-color: #f8f9fa;
    border-bottom: 1px solid #ddd;
}
.thabatta-tab-button {
    padding: 12px 20px;
    background: none;
    border: none;
    border-right: 1px solid #ddd;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.thabatta-tab-button:last-child {
    border-right: none;
}
.thabatta-tab-button:hover {
    background-color: #e9ecef;
}
.thabatta-tab-button.active {
    background-color: #fff;
    border-bottom: 2px solid #007bff;
}
.thabatta-tab-panel {
    padding: 20px;
    display: none;
}
.thabatta-tab-panel.active {
    display: block;
}',
                'js_code' => '// Código JavaScript para o componente Tabs
const buttons = this.querySelectorAll(".thabatta-tab-button");
const panels = this.querySelectorAll(".thabatta-tab-panel");

buttons.forEach((button, index) => {
    button.addEventListener("click", () => {
        // Desativar todos os botões e painéis
        buttons.forEach(btn => {
            btn.classList.remove("active");
            btn.setAttribute("aria-selected", "false");
        });
        panels.forEach(panel => {
            panel.classList.remove("active");
            panel.hidden = true;
        });
        
        // Ativar o botão e painel clicados
        button.classList.add("active");
        button.setAttribute("aria-selected", "true");
        panels[index].classList.add("active");
        panels[index].hidden = false;
    });
});',
                'use_shadow_dom' => '1',
                'shadow_dom_mode' => 'open',
            ),
            'slider' => array(
                'tag_name' => 'thabatta-slider',
                'html_code' => '<div class="thabatta-slider">
    <div class="thabatta-slider-container">
        <div class="thabatta-slide active">
            <div class="thabatta-slide-image">
                <img src="{{image1}}" alt="">
            </div>
            <div class="thabatta-slide-content">{{content1}}</div>
        </div>
        <div class="thabatta-slide">
            <div class="thabatta-slide-image">
                <img src="{{image2}}" alt="">
            </div>
            <div class="thabatta-slide-content">{{content2}}</div>
        </div>
    </div>
    <div class="thabatta-slider-arrows">
        <button class="thabatta-slider-arrow thabatta-slider-prev">←</button>
        <button class="thabatta-slider-arrow thabatta-slider-next">→</button>
    </div>
    <div class="thabatta-slider-dots">
        <button class="thabatta-slider-dot active" data-slide="0"></button>
        <button class="thabatta-slider-dot" data-slide="1"></button>
    </div>
</div>',
                'css_code' => '.thabatta-slider {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
}
.thabatta-slider-container {
    display: flex;
    transition: transform 0.5s ease;
}
.thabatta-slide {
    flex: 0 0 100%;
    display: none;
}
.thabatta-slide.active {
    display: block;
}
.thabatta-slide-image img {
    width: 100%;
    height: auto;
    display: block;
}
.thabatta-slide-content {
    padding: 20px;
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
}
.thabatta-slider-arrows {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    display: flex;
    justify-content: space-between;
    transform: translateY(-50%);
    z-index: 1;
}
.thabatta-slider-arrow {
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.thabatta-slider-arrow:hover {
    background-color: rgba(0, 0, 0, 0.8);
}
.thabatta-slider-dots {
    position: absolute;
    bottom: 20px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    gap: 10px;
}
.thabatta-slider-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.5);
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.thabatta-slider-dot.active {
    background-color: white;
}',
                'js_code' => '// Código JavaScript para o componente Slider
const slides = this.querySelectorAll(".thabatta-slide");
const dots = this.querySelectorAll(".thabatta-slider-dot");
const prevButton = this.querySelector(".thabatta-slider-prev");
const nextButton = this.querySelector(".thabatta-slider-next");
let currentSlide = 0;

function showSlide(index) {
    slides.forEach(slide => slide.classList.remove("active"));
    dots.forEach(dot => dot.classList.remove("active"));
    
    slides[index].classList.add("active");
    dots[index].classList.add("active");
    currentSlide = index;
}

prevButton.addEventListener("click", () => {
    let index = currentSlide - 1;
    if (index < 0) index = slides.length - 1;
    showSlide(index);
});

nextButton.addEventListener("click", () => {
    let index = currentSlide + 1;
    if (index >= slides.length) index = 0;
    showSlide(index);
});

dots.forEach((dot, index) => {
    dot.addEventListener("click", () => {
        showSlide(index);
    });
});

// Autoplay se configurado
const autoplay = this.getAttribute("data-autoplay") === "true";
const interval = parseInt(this.getAttribute("data-interval") || "5000");

if (autoplay) {
    setInterval(() => {
        let index = currentSlide + 1;
        if (index >= slides.length) index = 0;
        showSlide(index);
    }, interval);
}',
                'use_shadow_dom' => '1',
                'shadow_dom_mode' => 'open',
            ),
            'testimonial' => array(
                'tag_name' => 'thabatta-testimonial',
                'html_code' => '<div class="thabatta-testimonial">
    <div class="thabatta-testimonial-content">
        <div class="thabatta-testimonial-rating">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
        </div>
        <div class="thabatta-testimonial-text">{{content}}</div>
        <div class="thabatta-testimonial-author">
            <div class="thabatta-testimonial-image">
                <img src="{{image}}" alt="{{author}}">
            </div>
            <div class="thabatta-testimonial-info">
                <div class="thabatta-testimonial-name">{{author}}</div>
                <div class="thabatta-testimonial-position">{{position}}, {{company}}</div>
            </div>
        </div>
    </div>
</div>',
                'css_code' => '.thabatta-testimonial {
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    background-color: #fff;
}
.thabatta-testimonial-rating {
    margin-bottom: 15px;
    color: #ffc107;
}
.thabatta-testimonial-text {
    font-style: italic;
    margin-bottom: 20px;
    position: relative;
}
.thabatta-testimonial-text::before {
    content: """;
    font-size: 60px;
    color: #f0f0f0;
    position: absolute;
    top: -20px;
    left: -10px;
    z-index: -1;
}
.thabatta-testimonial-author {
    display: flex;
    align-items: center;
}
.thabatta-testimonial-image {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
    margin-right: 15px;
}
.thabatta-testimonial-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.thabatta-testimonial-name {
    font-weight: bold;
    margin-bottom: 5px;
}
.thabatta-testimonial-position {
    font-size: 0.9em;
    color: #6c757d;
}',
                'js_code' => '// Código JavaScript para o componente Testimonial
console.log("Testimonial component connected");',
                'use_shadow_dom' => '1',
                'shadow_dom_mode' => 'open',
            ),
            'cta' => array(
                'tag_name' => 'thabatta-cta',
                'html_code' => '<div class="thabatta-cta">
    <div class="thabatta-cta-content">
        <h2 class="thabatta-cta-title">{{title}}</h2>
        <div class="thabatta-cta-text">{{content}}</div>
        <a href="{{button_url}}" class="thabatta-cta-button">{{button_text}}</a>
    </div>
</div>',
                'css_code' => '.thabatta-cta {
    padding: 60px 30px;
    text-align: center;
    background-color: #007bff;
    color: white;
    border-radius: 8px;
    background-size: cover;
    background-position: center;
    position: relative;
}
.thabatta-cta::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    border-radius: 8px;
}
.thabatta-cta-content {
    position: relative;
    z-index: 1;
    max-width: 800px;
    margin: 0 auto;
}
.thabatta-cta-title {
    font-size: 2.5rem;
    margin-bottom: 20px;
}
.thabatta-cta-text {
    margin-bottom: 30px;
    font-size: 1.2rem;
}
.thabatta-cta-button {
    display: inline-block;
    padding: 12px 30px;
    background-color: white;
    color: #007bff;
    text-decoration: none;
    border-radius: 4px;
    font-weight: bold;
    transition: background-color 0.3s ease, transform 0.3s ease;
}
.thabatta-cta-button:hover {
    background-color: #f8f9fa;
    transform: translateY(-3px);
}',
                'js_code' => '// Código JavaScript para o componente CTA
console.log("CTA component connected");',
                'use_shadow_dom' => '1',
                'shadow_dom_mode' => 'open',
            ),
            'icon_box' => array(
                'tag_name' => 'thabatta-icon-box',
                'html_code' => '<div class="thabatta-icon-box">
    <div class="thabatta-icon-box-icon">
        <i class="{{icon}}"></i>
    </div>
    <div class="thabatta-icon-box-content">
        <h3 class="thabatta-icon-box-title">{{title}}</h3>
        <div class="thabatta-icon-box-text">{{content}}</div>
        <a href="{{link}}" class="thabatta-icon-box-link">Saiba mais <i class="fas fa-arrow-right"></i></a>
    </div>
</div>',
                'css_code' => '.thabatta-icon-box {
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    background-color: #fff;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.thabatta-icon-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}
.thabatta-icon-box-icon {
    font-size: 3rem;
    color: #007bff;
    margin-bottom: 20px;
    text-align: center;
}
.thabatta-icon-box-title {
    margin-top: 0;
    margin-bottom: 15px;
    font-size: 1.5rem;
}
.thabatta-icon-box-text {
    margin-bottom: 20px;
}
.thabatta-icon-box-link {
    display: inline-flex;
    align-items: center;
    color: #007bff;
    text-decoration: none;
    font-weight: bold;
    transition: color 0.3s ease;
}
.thabatta-icon-box-link i {
    margin-left: 5px;
    transition: transform 0.3s ease;
}
.thabatta-icon-box-link:hover {
    color: #0056b3;
}
.thabatta-icon-box-link:hover i {
    transform: translateX(5px);
}',
                'js_code' => '// Código JavaScript para o componente Icon Box
console.log("Icon Box component connected");',
                'use_shadow_dom' => '1',
                'shadow_dom_mode' => 'open',
            ),
            'team_member' => array(
                'tag_name' => 'thabatta-team-member',
                'html_code' => '<div class="thabatta-team-member">
    <div class="thabatta-team-member-image">
        <img src="{{image}}" alt="{{name}}">
    </div>
    <div class="thabatta-team-member-content">
        <h3 class="thabatta-team-member-name">{{name}}</h3>
        <div class="thabatta-team-member-position">{{position}}</div>
        <div class="thabatta-team-member-bio">{{content}}</div>
        <div class="thabatta-team-member-social">
            <a href="{{facebook}}" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="{{twitter}}" target="_blank" rel="noopener noreferrer" aria-label="Twitter">
                <i class="fab fa-twitter"></i>
            </a>
            <a href="{{linkedin}}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                <i class="fab fa-linkedin-in"></i>
            </a>
            <a href="{{instagram}}" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                <i class="fab fa-instagram"></i>
            </a>
        </div>
    </div>
</div>',
                'css_code' => '.thabatta-team-member {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    background-color: #fff;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.thabatta-team-member:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}
.thabatta-team-member-image {
    position: relative;
    overflow: hidden;
}
.thabatta-team-member-image img {
    width: 100%;
    height: auto;
    display: block;
    transition: transform 0.5s ease;
}
.thabatta-team-member:hover .thabatta-team-member-image img {
    transform: scale(1.1);
}
.thabatta-team-member-content {
    padding: 20px;
}
.thabatta-team-member-name {
    margin-top: 0;
    margin-bottom: 5px;
    font-size: 1.5rem;
}
.thabatta-team-member-position {
    color: #6c757d;
    margin-bottom: 15px;
    font-style: italic;
}
.thabatta-team-member-bio {
    margin-bottom: 20px;
}
.thabatta-team-member-social {
    display: flex;
    gap: 10px;
}
.thabatta-team-member-social a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: #f8f9fa;
    color: #6c757d;
    transition: background-color 0.3s ease, color 0.3s ease;
}
.thabatta-team-member-social a:hover {
    background-color: #007bff;
    color: white;
}',
                'js_code' => '// Código JavaScript para o componente Team Member
console.log("Team Member component connected");',
                'use_shadow_dom' => '1',
                'shadow_dom_mode' => 'open',
            ),
            'counter' => array(
                'tag_name' => 'thabatta-counter',
                'html_code' => '<div class="thabatta-counter">
    <div class="thabatta-counter-icon">
        <i class="{{icon}}"></i>
    </div>
    <div class="thabatta-counter-content">
        <div class="thabatta-counter-number">
            <span class="thabatta-counter-prefix">{{prefix}}</span>
            <span class="thabatta-counter-value">0</span>
            <span class="thabatta-counter-suffix">{{suffix}}</span>
        </div>
        <h3 class="thabatta-counter-title">{{title}}</h3>
        <div class="thabatta-counter-text">{{content}}</div>
    </div>
</div>',
                'css_code' => '.thabatta-counter {
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    background-color: #fff;
    text-align: center;
}
.thabatta-counter-icon {
    font-size: 3rem;
    color: #007bff;
    margin-bottom: 20px;
}
.thabatta-counter-number {
    font-size: 3rem;
    font-weight: bold;
    color: #343a40;
    margin-bottom: 10px;
}
.thabatta-counter-title {
    margin-top: 0;
    margin-bottom: 15px;
    font-size: 1.5rem;
}
.thabatta-counter-text {
    color: #6c757d;
}',
                'js_code' => '// Código JavaScript para o componente Counter
const counterValue = this.querySelector(".thabatta-counter-value");
const startValue = parseInt(this.getAttribute("data-start") || "0");
const endValue = parseInt(this.getAttribute("data-end") || "100");
const duration = parseInt(this.getAttribute("data-duration") || "2000");

// Função para animar o contador
function animateCounter(start, end, duration) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const value = Math.floor(progress * (end - start) + start);
        counterValue.textContent = value;
        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else {
            counterValue.textContent = end;
        }
    };
    window.requestAnimationFrame(step);
}

// Iniciar animação quando o elemento estiver visível
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateCounter(startValue, endValue, duration);
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.1 });

observer.observe(this);',
                'use_shadow_dom' => '1',
                'shadow_dom_mode' => 'open',
            ),
            'timeline' => array(
                'tag_name' => 'thabatta-timeline',
                'html_code' => '<div class="thabatta-timeline">
    <div class="thabatta-timeline-item">
        <div class="thabatta-timeline-marker"></div>
        <div class="thabatta-timeline-content">
            <div class="thabatta-timeline-date">{{date}}</div>
            <h3 class="thabatta-timeline-title">{{title}}</h3>
            <div class="thabatta-timeline-text">{{content}}</div>
        </div>
    </div>
</div>',
                'css_code' => '.thabatta-timeline {
    position: relative;
    padding: 20px 0;
}
.thabatta-timeline::before {
    content: "";
    position: absolute;
    top: 0;
    bottom: 0;
    left: 20px;
    width: 4px;
    background-color: #e9ecef;
}
.thabatta-timeline-item {
    position: relative;
    padding-left: 50px;
    margin-bottom: 30px;
}
.thabatta-timeline-item:last-child {
    margin-bottom: 0;
}
.thabatta-timeline-marker {
    position: absolute;
    top: 0;
    left: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #007bff;
    z-index: 1;
}
.thabatta-timeline-content {
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    background-color: #fff;
}
.thabatta-timeline-date {
    display: inline-block;
    padding: 5px 10px;
    background-color: #f8f9fa;
    border-radius: 4px;
    margin-bottom: 10px;
    font-weight: bold;
}
.thabatta-timeline-title {
    margin-top: 0;
    margin-bottom: 10px;
    font-size: 1.5rem;
}
.thabatta-timeline-text {
    color: #6c757d;
}',
                'js_code' => '// Código JavaScript para o componente Timeline
console.log("Timeline component connected");

// Adicionar animação de entrada quando os itens ficam visíveis
const timelineItems = this.querySelectorAll(".thabatta-timeline-item");
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = "1";
            entry.target.style.transform = "translateX(0)";
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.1 });

timelineItems.forEach(item => {
    item.style.opacity = "0";
    item.style.transform = "translateX(-20px)";
    item.style.transition = "opacity 0.5s ease, transform 0.5s ease";
    observer.observe(item);
});',
                'use_shadow_dom' => '1',
                'shadow_dom_mode' => 'open',
            ),
        );
        
        // Importar componentes selecionados
        foreach ($components as $component) {
            if (!isset($available_components[$component])) {
                $results[] = "<div class='notice notice-error'><p>Componente <strong>{$component}</strong> não encontrado.</p></div>";
                continue;
            }
            
            $component_data = $available_components[$component];
            
            // Criar novo post para o componente
            $post_id = wp_insert_post(array(
                'post_title' => ucfirst(str_replace('_', ' ', $component)),
                'post_type' => 'web_component',
                'post_status' => 'publish',
            ));
            
            if (is_wp_error($post_id)) {
                $results[] = "<div class='notice notice-error'><p>Erro ao criar o componente <strong>{$component}</strong>: {$post_id->get_error_message()}</p></div>";
                continue;
            }
            
            // Adicionar metadados
            foreach ($component_data as $key => $value) {
                update_post_meta($post_id, $key, $value);
            }
            
            $results[] = "<div class='notice notice-success'><p>Componente <strong>{$component}</strong> importado com sucesso!</p></div>";
        }
        
        echo implode('', $results);
        wp_die();
    }
    
    /**
     * Shortcode para renderizar um web component
     */
    public function web_component_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
            'tag' => '',
        ), $atts);
        
        if (empty($atts['id']) && empty($atts['tag'])) {
            return '<p>Erro: ID ou tag do componente não especificado.</p>';
        }
        
        $args = array(
            'post_type' => 'web_component',
            'posts_per_page' => 1,
        );
        
        if (!empty($atts['id'])) {
            $args['p'] = $atts['id'];
        } elseif (!empty($atts['tag'])) {
            $args['meta_query'] = array(
                array(
                    'key' => 'tag_name',
                    'value' => $atts['tag'],
                    'compare' => '=',
                ),
            );
        }
        
        $query = new WP_Query($args);
        
        if (!$query->have_posts()) {
            return '<p>Erro: Componente não encontrado.</p>';
        }
        
        $query->the_post();
        $post_id = get_the_ID();
        $tag_name = get_post_meta($post_id, 'tag_name', true);
        
        // Registrar o componente específico
        thabatta_register_single_component($query->post);
        
        wp_reset_postdata();
        
        return "<{$tag_name}></{$tag_name}>";
    }
}

// Inicializar o importador de componentes
new Thabatta_Web_Components_Importer();

/**
 * Adiciona colunas personalizadas na listagem de Web Components
 */
function thabatta_add_web_component_columns($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        if ($key === 'title') {
            $new_columns[$key] = $value;
            $new_columns['tag_name'] = 'Tag HTML';
            $new_columns['shortcode'] = 'Shortcode';
        } else {
            $new_columns[$key] = $value;
        }
    }
    return $new_columns;
}
add_filter('manage_web_component_posts_columns', 'thabatta_add_web_component_columns');

/**
 * Renderiza o conteúdo das colunas personalizadas
 */
function thabatta_render_web_component_columns($column, $post_id) {
    switch ($column) {
        case 'tag_name':
            $tag_name = get_post_meta($post_id, 'tag_name', true);
            if ($tag_name) {
                echo '<code>&lt;' . esc_html($tag_name) . '&gt;&lt;/' . esc_html($tag_name) . '&gt;</code>';
            }
            break;
        case 'shortcode':
            echo '<code>[thabatta_web_component id="' . $post_id . '"]</code><br>';
            $tag_name = get_post_meta($post_id, 'tag_name', true);
            if ($tag_name) {
                echo '<code>[thabatta_web_component tag="' . esc_html($tag_name) . '"]</code>';
            }
            break;
    }
}
add_action('manage_web_component_posts_custom_column', 'thabatta_render_web_component_columns', 10, 2);

/**
 * Torna as colunas ordenáveis
 */
function thabatta_sortable_web_component_columns($columns) {
    $columns['tag_name'] = 'tag_name';
    return $columns;
}
add_filter('manage_edit-web_component_sortable_columns', 'thabatta_sortable_web_component_columns');

/**
 * Ajusta a query para ordenação
 */
function thabatta_web_component_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->get('post_type') === 'web_component') {
        $orderby = $query->get('orderby');

        if ('tag_name' === $orderby) {
            $query->set('meta_key', 'tag_name');
            $query->set('orderby', 'meta_value');
        }
    }
}
add_action('pre_get_posts', 'thabatta_web_component_orderby');

function thabatta_allow_web_component_tags( $allowed, $context ) {
    if ( 'post' === $context || 'pre_user_description' === $context ) {
        $components = get_posts([
            'post_type'      => 'web_component',
            'posts_per_page' => -1,
        ]);
        foreach ( $components as $comp ) {
            $tag = get_post_meta( $comp->ID, 'tag_name', true );
            if ( $tag ) {
                $allowed[ $tag ] = [];
            }
        }
    }
    return $allowed;
}
add_filter( 'wp_kses_allowed_html', 'thabatta_allow_web_component_tags', 10, 2 );
