<?php
/**
 * Classe para gerenciar componentes web personalizados
 *
 * @package Thabatta_Advocacia
 */

if (!defined('ABSPATH')) {
    exit; // Saída direta se acessado diretamente
}

/**
 * Classe para gerenciar componentes web personalizados
 */
class Thabatta_Web_Components
{
    /**
     * Inicializa a classe
     */
    public function __construct()
    {
        // Registrar componentes web
        add_action('wp_enqueue_scripts', array($this, 'register_web_components'));

        // Adicionar shortcodes para componentes
        add_shortcode('thabatta_card', array($this, 'card_shortcode'));
        add_shortcode('thabatta_accordion', array($this, 'accordion_shortcode'));
        add_shortcode('thabatta_tabs', array($this, 'tabs_shortcode'));
        add_shortcode('thabatta_slider', array($this, 'slider_shortcode'));
        add_shortcode('thabatta_testimonial', array($this, 'testimonial_shortcode'));
        add_shortcode('thabatta_cta', array($this, 'cta_shortcode'));
        add_shortcode('thabatta_icon_box', array($this, 'icon_box_shortcode'));
        add_shortcode('thabatta_team_member', array($this, 'team_member_shortcode'));
        add_shortcode('thabatta_counter', array($this, 'counter_shortcode'));
        add_shortcode('thabatta_timeline', array($this, 'timeline_shortcode'));

        // Adicionar suporte a componentes no editor Gutenberg
        add_action('init', array($this, 'register_gutenberg_blocks'));
    }

    /**
     * Registrar componentes web
     */
    public function register_web_components()
    {
        // Registrar script de componentes web
        wp_enqueue_script(
            'thabatta-web-components',
            get_template_directory_uri() . '/assets/js/web-components.js',
            array(),
            THABATTA_VERSION,
            true
        );
    }

    /**
     * Shortcode para componente de card
     */
    public function card_shortcode($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'title' => '',
            'image' => '',
            'link' => '',
            'button_text' => __('Saiba mais', 'thabatta-adv'),
            'class' => '',
        ), $atts);

        ob_start();
        ?>
        <thabatta-card class="thabatta-card <?php echo esc_attr($atts['class']); ?>">
            <?php if ($atts['image']) : ?>
                <div class="thabatta-card-image">
                    <?php if ($atts['link']) : ?>
                        <a href="<?php echo esc_url($atts['link']); ?>">
                            <img src="<?php echo esc_url($atts['image']); ?>" alt="<?php echo esc_attr($atts['title']); ?>">
                        </a>
                    <?php else : ?>
                        <img src="<?php echo esc_url($atts['image']); ?>" alt="<?php echo esc_attr($atts['title']); ?>">
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="thabatta-card-content">
                <?php if ($atts['title']) : ?>
                    <h3 class="thabatta-card-title">
                        <?php if ($atts['link']) : ?>
                            <a href="<?php echo esc_url($atts['link']); ?>"><?php echo esc_html($atts['title']); ?></a>
                        <?php else : ?>
                            <?php echo esc_html($atts['title']); ?>
                        <?php endif; ?>
                    </h3>
                <?php endif; ?>
                <div class="thabatta-card-text">
                    <?php echo wp_kses_post($content); ?>
                </div>
                <?php if ($atts['link'] && $atts['button_text']) : ?>
                    <a href="<?php echo esc_url($atts['link']); ?>" class="thabatta-card-button">
                        <?php echo esc_html($atts['button_text']); ?>
                    </a>
                <?php endif; ?>
            </div>
        </thabatta-card>
        <?php
        return ob_get_clean();
    }

    /**
     * Shortcode para componente de acordeão
     */
    public function accordion_shortcode($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'id' => 'accordion-' . uniqid(),
            'class' => '',
        ), $atts);

        // Processar conteúdo para extrair itens
        $pattern = '/\[accordion_item title="([^"]*)"( open="([^"]*)")?\](.*?)\[\/accordion_item\]/s';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        ob_start();
        ?>
        <thabatta-accordion class="thabatta-accordion <?php echo esc_attr($atts['class']); ?>" id="<?php echo esc_attr($atts['id']); ?>">
            <?php foreach ($matches as $match) : ?>
                <?php
                $item_title = $match[1];
                $item_open = $match[3] === 'true' ? 'true' : 'false';
                $item_content = $match[4];
                $item_id = sanitize_title($item_title) . '-' . uniqid();
                ?>
                <div class="thabatta-accordion-item">
                    <h3 class="thabatta-accordion-header">
                        <button class="thabatta-accordion-button" type="button" 
                                aria-expanded="<?php echo $item_open === 'true' ? 'true' : 'false'; ?>" 
                                aria-controls="<?php echo esc_attr($item_id); ?>">
                            <?php echo esc_html($item_title); ?>
                            <span class="thabatta-accordion-icon"></span>
                        </button>
                    </h3>
                    <div id="<?php echo esc_attr($item_id); ?>" 
                         class="thabatta-accordion-collapse" 
                         aria-labelledby="heading-<?php echo esc_attr($item_id); ?>" 
                         <?php echo $item_open === 'true' ? '' : 'hidden'; ?>>
                        <div class="thabatta-accordion-body">
                            <?php echo do_shortcode($item_content); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </thabatta-accordion>
        <?php
        return ob_get_clean();
    }

    /**
     * Shortcode para componente de abas
     */
    public function tabs_shortcode($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'id' => 'tabs-' . uniqid(),
            'class' => '',
        ), $atts);

        // Processar conteúdo para extrair itens
        $pattern = '/\[tab title="([^"]*)"( active="([^"]*)")?\](.*?)\[\/tab\]/s';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        ob_start();
        ?>
        <thabatta-tabs class="thabatta-tabs <?php echo esc_attr($atts['class']); ?>" id="<?php echo esc_attr($atts['id']); ?>">
            <div class="thabatta-tabs-nav" role="tablist">
                <?php foreach ($matches as $index => $match) : ?>
                    <?php
                    $tab_title = $match[1];
                    $tab_active = $match[3] === 'true' ? 'true' : 'false';
                    if ($index === 0 && $tab_active !== 'true') {
                        $tab_active = 'true';
                    }
                    $tab_id = sanitize_title($tab_title) . '-' . uniqid();
                    ?>
                    <button class="thabatta-tab-button <?php echo $tab_active === 'true' ? 'active' : ''; ?>" 
                            id="tab-button-<?php echo esc_attr($tab_id); ?>" 
                            role="tab" 
                            aria-selected="<?php echo $tab_active === 'true' ? 'true' : 'false'; ?>" 
                            aria-controls="tab-panel-<?php echo esc_attr($tab_id); ?>">
                        <?php echo esc_html($tab_title); ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <div class="thabatta-tabs-content">
                <?php foreach ($matches as $index => $match) : ?>
                    <?php
                    $tab_title = $match[1];
                    $tab_active = $match[3] === 'true' ? 'true' : 'false';
                    if ($index === 0 && $tab_active !== 'true') {
                        $tab_active = 'true';
                    }
                    $tab_content = $match[4];
                    $tab_id = sanitize_title($tab_title) . '-' . uniqid();
                    ?>
                    <div class="thabatta-tab-panel <?php echo $tab_active === 'true' ? 'active' : ''; ?>" 
                         id="tab-panel-<?php echo esc_attr($tab_id); ?>" 
                         role="tabpanel" 
                         aria-labelledby="tab-button-<?php echo esc_attr($tab_id); ?>" 
                         <?php echo $tab_active === 'true' ? '' : 'hidden'; ?>>
                        <?php echo do_shortcode($tab_content); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </thabatta-tabs>
        <?php
        return ob_get_clean();
    }

    /**
     * Shortcode para componente de slider
     */
    public function slider_shortcode($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'id' => 'slider-' . uniqid(),
            'class' => '',
            'autoplay' => 'false',
            'interval' => '5000',
            'arrows' => 'true',
            'dots' => 'true',
        ), $atts);

        // Processar conteúdo para extrair itens
        $pattern = '/\[slide( image="([^"]*)")?\](.*?)\[\/slide\]/s';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        ob_start();
        ?>
        <thabatta-slider class="thabatta-slider <?php echo esc_attr($atts['class']); ?>" 
                         id="<?php echo esc_attr($atts['id']); ?>" 
                         data-autoplay="<?php echo esc_attr($atts['autoplay']); ?>" 
                         data-interval="<?php echo esc_attr($atts['interval']); ?>" 
                         data-arrows="<?php echo esc_attr($atts['arrows']); ?>" 
                         data-dots="<?php echo esc_attr($atts['dots']); ?>">
            <div class="thabatta-slider-container">
                <?php foreach ($matches as $index => $match) : ?>
                    <?php
                    $slide_image = $match[2] ?? '';
                    $slide_content = $match[3];
                    $slide_id = 'slide-' . $index . '-' . uniqid();
                    ?>
                    <div class="thabatta-slide" id="<?php echo esc_attr($slide_id); ?>">
                        <?php if ($slide_image) : ?>
                            <div class="thabatta-slide-image">
                                <img src="<?php echo esc_url($slide_image); ?>" alt="">
                            </div>
                        <?php endif; ?>
                        <div class="thabatta-slide-content">
                            <?php echo do_shortcode($slide_content); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($atts['arrows'] === 'true') : ?>
                <div class="thabatta-slider-arrows">
                    <button class="thabatta-slider-arrow thabatta-slider-prev" aria-label="<?php esc_attr_e('Anterior', 'thabatta-adv'); ?>">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="thabatta-slider-arrow thabatta-slider-next" aria-label="<?php esc_attr_e('Próximo', 'thabatta-adv'); ?>">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            <?php endif; ?>
            
            <?php if ($atts['dots'] === 'true') : ?>
                <div class="thabatta-slider-dots">
                    <?php foreach ($matches as $index => $match) : ?>
                        <button class="thabatta-slider-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                                aria-label="<?php printf(esc_attr__('Slide %d', 'thabatta-adv'), $index + 1); ?>" 
                                data-slide="<?php echo esc_attr(strval($index)); ?>"></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </thabatta-slider>
        <?php
        return ob_get_clean();
    }

    /**
     * Shortcode para componente de depoimento
     */
    public function testimonial_shortcode($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'author' => '',
            'position' => '',
            'company' => '',
            'image' => '',
            'rating' => '5',
            'class' => '',
        ), $atts);

        ob_start();
        ?>
        <thabatta-testimonial class="thabatta-testimonial <?php echo esc_attr($atts['class']); ?>">
            <div class="thabatta-testimonial-content">
                <?php if ($atts['rating']) : ?>
                    <div class="thabatta-testimonial-rating">
                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                            <?php if ($i <= intval($atts['rating'])) : ?>
                                <i class="fas fa-star"></i>
                            <?php else : ?>
                                <i class="far fa-star"></i>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
                
                <div class="thabatta-testimonial-text">
                    <?php echo wp_kses_post($content); ?>
                </div>
                
                <div class="thabatta-testimonial-author">
                    <?php if ($atts['image']) : ?>
                        <div class="thabatta-testimonial-image">
                            <img src="<?php echo esc_url($atts['image']); ?>" alt="<?php echo esc_attr($atts['author']); ?>">
                        </div>
                    <?php endif; ?>
                    
                    <div class="thabatta-testimonial-info">
                        <?php if ($atts['author']) : ?>
                            <div class="thabatta-testimonial-name"><?php echo esc_html($atts['author']); ?></div>
                        <?php endif; ?>
                        
                        <?php if ($atts['position'] || $atts['company']) : ?>
                            <div class="thabatta-testimonial-position">
                                <?php
                                if ($atts['position'] && $atts['company']) {
                                    echo esc_html($atts['position']) . ', ' . esc_html($atts['company']);
                                } elseif ($atts['position']) {
                                    echo esc_html($atts['position']);
                                } elseif ($atts['company']) {
                                    echo esc_html($atts['company']);
                                }
        ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </thabatta-testimonial>
        <?php
        return ob_get_clean();
    }

    /**
     * Shortcode para componente de CTA (Call to Action)
     */
    public function cta_shortcode($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'title' => '',
            'button_text' => '',
            'button_url' => '',
            'background_image' => '',
            'class' => '',
        ), $atts);

        $style = '';
        if ($atts['background_image']) {
            $style = 'style="background-image: url(' . esc_url($atts['background_image']) . ');"';
        }

        ob_start();
        ?>
        <thabatta-cta class="thabatta-cta <?php echo esc_attr($atts['class']); ?>" <?php echo $style; ?>>
            <div class="thabatta-cta-content">
                <?php if ($atts['title']) : ?>
                    <h2 class="thabatta-cta-title"><?php echo esc_html($atts['title']); ?></h2>
                <?php endif; ?>
                
                <div class="thabatta-cta-text">
                    <?php echo wp_kses_post($content); ?>
                </div>
                
                <?php if ($atts['button_text'] && $atts['button_url']) : ?>
                    <a href="<?php echo esc_url($atts['button_url']); ?>" class="thabatta-cta-button">
                        <?php echo esc_html($atts['button_text']); ?>
                    </a>
                <?php endif; ?>
            </div>
        </thabatta-cta>
        <?php
        return ob_get_clean();
    }

    /**
     * Shortcode para componente de caixa de ícone
     */
    public function icon_box_shortcode($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'title' => '',
            'icon' => '',
            'link' => '',
            'class' => '',
        ), $atts);

        ob_start();
        ?>
        <thabatta-icon-box class="thabatta-icon-box <?php echo esc_attr($atts['class']); ?>">
            <?php if ($atts['icon']) : ?>
                <div class="thabatta-icon-box-icon">
                    <i class="<?php echo esc_attr($atts['icon']); ?>"></i>
                </div>
            <?php endif; ?>
            
            <div class="thabatta-icon-box-content">
                <?php if ($atts['title']) : ?>
                    <h3 class="thabatta-icon-box-title">
                        <?php if ($atts['link']) : ?>
                            <a href="<?php echo esc_url($atts['link']); ?>"><?php echo esc_html($atts['title']); ?></a>
                        <?php else : ?>
                            <?php echo esc_html($atts['title']); ?>
                        <?php endif; ?>
                    </h3>
                <?php endif; ?>
                
                <div class="thabatta-icon-box-text">
                    <?php echo wp_kses_post($content); ?>
                </div>
                
                <?php if ($atts['link']) : ?>
                    <a href="<?php echo esc_url($atts['link']); ?>" class="thabatta-icon-box-link">
                        <?php esc_html_e('Saiba mais', 'thabatta-adv'); ?> <i class="fas fa-arrow-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </thabatta-icon-box>
        <?php
        return ob_get_clean();
    }

    /**
     * Shortcode para componente de membro da equipe
     */
    public function team_member_shortcode($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'name' => '',
            'position' => '',
            'image' => '',
            'link' => '',
            'facebook' => '',
            'twitter' => '',
            'linkedin' => '',
            'instagram' => '',
            'class' => '',
        ), $atts);

        ob_start();
        ?>
        <thabatta-team-member class="thabatta-team-member <?php echo esc_attr($atts['class']); ?>">
            <?php if ($atts['image']) : ?>
                <div class="thabatta-team-member-image">
                    <?php if ($atts['link']) : ?>
                        <a href="<?php echo esc_url($atts['link']); ?>">
                            <img src="<?php echo esc_url($atts['image']); ?>" alt="<?php echo esc_attr($atts['name']); ?>">
                        </a>
                    <?php else : ?>
                        <img src="<?php echo esc_url($atts['image']); ?>" alt="<?php echo esc_attr($atts['name']); ?>">
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="thabatta-team-member-content">
                <?php if ($atts['name']) : ?>
                    <h3 class="thabatta-team-member-name">
                        <?php if ($atts['link']) : ?>
                            <a href="<?php echo esc_url($atts['link']); ?>"><?php echo esc_html($atts['name']); ?></a>
                        <?php else : ?>
                            <?php echo esc_html($atts['name']); ?>
                        <?php endif; ?>
                    </h3>
                <?php endif; ?>
                
                <?php if ($atts['position']) : ?>
                    <div class="thabatta-team-member-position"><?php echo esc_html($atts['position']); ?></div>
                <?php endif; ?>
                
                <div class="thabatta-team-member-bio">
                    <?php echo wp_kses_post($content); ?>
                </div>
                
                <?php
                $has_social = $atts['facebook'] || $atts['twitter'] || $atts['linkedin'] || $atts['instagram'];
        if ($has_social) :
            ?>
                    <div class="thabatta-team-member-social">
                        <?php if ($atts['facebook']) : ?>
                            <a href="<?php echo esc_url($atts['facebook']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Facebook', 'thabatta-adv'); ?>">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($atts['twitter']) : ?>
                            <a href="<?php echo esc_url($atts['twitter']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Twitter', 'thabatta-adv'); ?>">
                                <i class="fab fa-twitter"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($atts['linkedin']) : ?>
                            <a href="<?php echo esc_url($atts['linkedin']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('LinkedIn', 'thabatta-adv'); ?>">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($atts['instagram']) : ?>
                            <a href="<?php echo esc_url($atts['instagram']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Instagram', 'thabatta-adv'); ?>">
                                <i class="fab fa-instagram"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </thabatta-team-member>
        <?php
        return ob_get_clean();
    }

    /**
     * Shortcode para componente de contador
     */
    public function counter_shortcode($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'start' => '0',
            'end' => '100',
            'duration' => '2000',
            'prefix' => '',
            'suffix' => '',
            'title' => '',
            'icon' => '',
            'class' => '',
        ), $atts);

        ob_start();
        ?>
        <thabatta-counter class="thabatta-counter <?php echo esc_attr($atts['class']); ?>" 
                          data-start="<?php echo esc_attr($atts['start']); ?>" 
                          data-end="<?php echo esc_attr($atts['end']); ?>" 
                          data-duration="<?php echo esc_attr($atts['duration']); ?>">
            <?php if ($atts['icon']) : ?>
                <div class="thabatta-counter-icon">
                    <i class="<?php echo esc_attr($atts['icon']); ?>"></i>
                </div>
            <?php endif; ?>
            
            <div class="thabatta-counter-content">
                <div class="thabatta-counter-number">
                    <?php if ($atts['prefix']) : ?>
                        <span class="thabatta-counter-prefix"><?php echo esc_html($atts['prefix']); ?></span>
                    <?php endif; ?>
                    
                    <span class="thabatta-counter-value"><?php echo esc_html($atts['start']); ?></span>
                    
                    <?php if ($atts['suffix']) : ?>
                        <span class="thabatta-counter-suffix"><?php echo esc_html($atts['suffix']); ?></span>
                    <?php endif; ?>
                </div>
                
                <?php if ($atts['title']) : ?>
                    <h3 class="thabatta-counter-title"><?php echo esc_html($atts['title']); ?></h3>
                <?php endif; ?>
                
                <?php if ($content) : ?>
                    <div class="thabatta-counter-text">
                        <?php echo wp_kses_post($content); ?>
                    </div>
                <?php endif; ?>
            </div>
        </thabatta-counter>
        <?php
        return ob_get_clean();
    }

    /**
     * Shortcode para componente de linha do tempo
     */
    public function timeline_shortcode($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'id' => 'timeline-' . uniqid(),
            'class' => '',
        ), $atts);

        // Processar conteúdo para extrair itens
        $pattern = '/\[timeline_item date="([^"]*)"( title="([^"]*)")?( icon="([^"]*)")?\](.*?)\[\/timeline_item\]/s';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        ob_start();
        ?>
        <thabatta-timeline class="thabatta-timeline <?php echo esc_attr($atts['class']); ?>" id="<?php echo esc_attr($atts['id']); ?>">
            <?php foreach ($matches as $index => $match) : ?>
                <?php
                $item_date = $match[1];
                $item_title = $match[3] ?? '';
                $item_icon = $match[5] ?? '';
                $item_content = $match[6];
                $item_id = 'timeline-item-' . $index . '-' . uniqid();
                ?>
                <div class="thabatta-timeline-item" id="<?php echo esc_attr($item_id); ?>">
                    <div class="thabatta-timeline-marker"></div>
                    
                    <div class="thabatta-timeline-content">
                        <div class="thabatta-timeline-date"><?php echo esc_html($item_date); ?></div>
                        
                        <?php if ($item_title) : ?>
                            <h3 class="thabatta-timeline-title"><?php echo esc_html($item_title); ?></h3>
                        <?php endif; ?>
                        
                        <div class="thabatta-timeline-text">
                            <?php echo do_shortcode($item_content); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </thabatta-timeline>
        <?php
        return ob_get_clean();
    }

    /**
     * Registrar blocos Gutenberg para componentes
     */
    public function register_gutenberg_blocks()
    {
        // Verificar se o Gutenberg está ativo
        if (!function_exists('register_block_type')) {
            return;
        }

        // Registrar script para blocos
        wp_register_script(
            'thabatta-gutenberg-blocks',
            get_template_directory_uri() . '/assets/js/gutenberg-blocks.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
            THABATTA_VERSION,
            true
        );

        // Registrar estilo para blocos
        wp_register_style(
            'thabatta-gutenberg-blocks',
            get_template_directory_uri() . '/assets/css/gutenberg-blocks.css',
            array(),
            THABATTA_VERSION
        );

        // Registrar blocos
        register_block_type('thabatta/card', array(
            'editor_script' => 'thabatta-gutenberg-blocks',
            'editor_style' => 'thabatta-gutenberg-blocks',
            'render_callback' => array($this, 'card_shortcode'),
            'attributes' => array(
                'title' => array('type' => 'string'),
                'image' => array('type' => 'string'),
                'link' => array('type' => 'string'),
                'button_text' => array('type' => 'string', 'default' => __('Saiba mais', 'thabatta-adv')),
                'class' => array('type' => 'string'),
                'content' => array('type' => 'string'),
            ),
        ));

        register_block_type('thabatta/accordion', array(
            'editor_script' => 'thabatta-gutenberg-blocks',
            'editor_style' => 'thabatta-gutenberg-blocks',
            'render_callback' => array($this, 'accordion_shortcode'),
            'attributes' => array(
                'id' => array('type' => 'string'),
                'class' => array('type' => 'string'),
                'content' => array('type' => 'string'),
            ),
        ));

        register_block_type('thabatta/tabs', array(
            'editor_script' => 'thabatta-gutenberg-blocks',
            'editor_style' => 'thabatta-gutenberg-blocks',
            'render_callback' => array($this, 'tabs_shortcode'),
            'attributes' => array(
                'id' => array('type' => 'string'),
                'class' => array('type' => 'string'),
                'content' => array('type' => 'string'),
            ),
        ));

        register_block_type('thabatta/slider', array(
            'editor_script' => 'thabatta-gutenberg-blocks',
            'editor_style' => 'thabatta-gutenberg-blocks',
            'render_callback' => array($this, 'slider_shortcode'),
            'attributes' => array(
                'id' => array('type' => 'string'),
                'class' => array('type' => 'string'),
                'autoplay' => array('type' => 'string', 'default' => 'false'),
                'interval' => array('type' => 'string', 'default' => '5000'),
                'arrows' => array('type' => 'string', 'default' => 'true'),
                'dots' => array('type' => 'string', 'default' => 'true'),
                'content' => array('type' => 'string'),
            ),
        ));

        register_block_type('thabatta/testimonial', array(
            'editor_script' => 'thabatta-gutenberg-blocks',
            'editor_style' => 'thabatta-gutenberg-blocks',
            'render_callback' => array($this, 'testimonial_shortcode'),
            'attributes' => array(
                'author' => array('type' => 'string'),
                'position' => array('type' => 'string'),
                'company' => array('type' => 'string'),
                'image' => array('type' => 'string'),
                'rating' => array('type' => 'string', 'default' => '5'),
                'class' => array('type' => 'string'),
                'content' => array('type' => 'string'),
            ),
        ));

        register_block_type('thabatta/cta', array(
            'editor_script' => 'thabatta-gutenberg-blocks',
            'editor_style' => 'thabatta-gutenberg-blocks',
            'render_callback' => array($this, 'cta_shortcode'),
            'attributes' => array(
                'title' => array('type' => 'string'),
                'button_text' => array('type' => 'string'),
                'button_url' => array('type' => 'string'),
                'background_image' => array('type' => 'string'),
                'class' => array('type' => 'string'),
                'content' => array('type' => 'string'),
            ),
        ));

        register_block_type('thabatta/icon-box', array(
            'editor_script' => 'thabatta-gutenberg-blocks',
            'editor_style' => 'thabatta-gutenberg-blocks',
            'render_callback' => array($this, 'icon_box_shortcode'),
            'attributes' => array(
                'title' => array('type' => 'string'),
                'icon' => array('type' => 'string'),
                'link' => array('type' => 'string'),
                'class' => array('type' => 'string'),
                'content' => array('type' => 'string'),
            ),
        ));

        register_block_type('thabatta/team-member', array(
            'editor_script' => 'thabatta-gutenberg-blocks',
            'editor_style' => 'thabatta-gutenberg-blocks',
            'render_callback' => array($this, 'team_member_shortcode'),
            'attributes' => array(
                'name' => array('type' => 'string'),
                'position' => array('type' => 'string'),
                'image' => array('type' => 'string'),
                'link' => array('type' => 'string'),
                'facebook' => array('type' => 'string'),
                'twitter' => array('type' => 'string'),
                'linkedin' => array('type' => 'string'),
                'instagram' => array('type' => 'string'),
                'class' => array('type' => 'string'),
                'content' => array('type' => 'string'),
            ),
        ));

        register_block_type('thabatta/counter', array(
            'editor_script' => 'thabatta-gutenberg-blocks',
            'editor_style' => 'thabatta-gutenberg-blocks',
            'render_callback' => array($this, 'counter_shortcode'),
            'attributes' => array(
                'start' => array('type' => 'string', 'default' => '0'),
                'end' => array('type' => 'string', 'default' => '100'),
                'duration' => array('type' => 'string', 'default' => '2000'),
                'prefix' => array('type' => 'string'),
                'suffix' => array('type' => 'string'),
                'title' => array('type' => 'string'),
                'icon' => array('type' => 'string'),
                'class' => array('type' => 'string'),
                'content' => array('type' => 'string'),
            ),
        ));

        register_block_type('thabatta/timeline', array(
            'editor_script' => 'thabatta-gutenberg-blocks',
            'editor_style' => 'thabatta-gutenberg-blocks',
            'render_callback' => array($this, 'timeline_shortcode'),
            'attributes' => array(
                'id' => array('type' => 'string'),
                'class' => array('type' => 'string'),
                'content' => array('type' => 'string'),
            ),
        ));
    }
}

// Inicializar a classe
$thabatta_web_components = new Thabatta_Web_Components();

/**
 * Função auxiliar para registrar componentes web personalizados
 */
function thabatta_register_web_components()
{
    global $thabatta_web_components;

    if ($thabatta_web_components) {
        $thabatta_web_components->register_web_components();
    }
}

/**
 * Função auxiliar para gerar o script de componentes web
 */
function thabatta_generate_web_components_script()
{
    // Caminho para o arquivo de script
    $script_file = get_template_directory() . '/assets/js/web-components.js';

    // Verificar se o diretório existe
    $script_dir = dirname($script_file);
    if (!file_exists($script_dir)) {
        wp_mkdir_p($script_dir);
    }

    // Conteúdo do script
    $script_content = <<<'EOT'
/**
 * Web Components para o tema Thabatta Advocacia
 */
(function() {
    'use strict';
    
    // Componente de Card
    class ThabattaCard extends HTMLElement {
        constructor() {
            super();
            this.attachShadow({ mode: 'open' });
            this.render();
        }
        
        render() {
            const style = document.createElement('style');
            style.textContent = `
                :host {
                    display: block;
                    margin-bottom: 2rem;
                }
                .card {
                    background-color: var(--card-bg, #fff);
                    border-radius: var(--card-radius, 0.5rem);
                    box-shadow: var(--card-shadow, 0 4px 6px rgba(0, 0, 0, 0.1));
                    overflow: hidden;
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                }
                .card:hover {
                    transform: translateY(-5px);
                    box-shadow: var(--card-shadow-hover, 0 10px 15px rgba(0, 0, 0, 0.1));
                }
                ::slotted(.thabatta-card-image) {
                    width: 100%;
                    height: auto;
                    display: block;
                }
                ::slotted(.thabatta-card-content) {
                    padding: 1.5rem;
                }
                ::slotted(.thabatta-card-title) {
                    margin-top: 0;
                    margin-bottom: 1rem;
                    color: var(--heading-color, #333);
                }
                ::slotted(.thabatta-card-text) {
                    margin-bottom: 1.5rem;
                    color: var(--text-color, #666);
                }
                ::slotted(.thabatta-card-button) {
                    display: inline-block;
                    padding: 0.75rem 1.5rem;
                    background-color: var(--primary-color, #8b0000);
                    color: #fff;
                    text-decoration: none;
                    border-radius: 0.25rem;
                    transition: background-color 0.3s ease;
                }
                ::slotted(.thabatta-card-button:hover) {
                    background-color: var(--primary-color-hover, #6b0000);
                }
            `;
            
            const card = document.createElement('div');
            card.className = 'card';
            
            const slot = document.createElement('slot');
            
            card.appendChild(slot);
            this.shadowRoot.appendChild(style);
            this.shadowRoot.appendChild(card);
        }
    }
    
    // Componente de Acordeão
    class ThabattaAccordion extends HTMLElement {
        constructor() {
            super();
            this.attachShadow({ mode: 'open' });
            this.render();
        }
        
        connectedCallback() {
            this.shadowRoot.querySelectorAll('.accordion-button').forEach(button => {
                button.addEventListener('click', this.toggleAccordion.bind(this));
            });
        }
        
        toggleAccordion(event) {
            const button = event.currentTarget;
            const item = button.closest('.accordion-item');
            const content = item.querySelector('.accordion-content');
            
            // Toggle current item
            const isOpen = button.getAttribute('aria-expanded') === 'true';
            button.setAttribute('aria-expanded', !isOpen);
            content.style.maxHeight = isOpen ? '0' : `${content.scrollHeight}px`;
            
            // Close other items if single mode
            if (this.getAttribute('data-single') === 'true' && !isOpen) {
                this.shadowRoot.querySelectorAll('.accordion-button').forEach(btn => {
                    if (btn !== button) {
                        btn.setAttribute('aria-expanded', 'false');
                        const otherContent = btn.closest('.accordion-item').querySelector('.accordion-content');
                        otherContent.style.maxHeight = '0';
                    }
                });
            }
        }
        
        render() {
            const style = document.createElement('style');
            style.textContent = `
                :host {
                    display: block;
                    margin-bottom: 2rem;
                }
                .accordion {
                    border: 1px solid var(--border-color, #ddd);
                    border-radius: var(--accordion-radius, 0.5rem);
                    overflow: hidden;
                }
                .accordion-item {
                    border-bottom: 1px solid var(--border-color, #ddd);
                }
                .accordion-item:last-child {
                    border-bottom: none;
                }
                .accordion-header {
                    margin: 0;
                }
                .accordion-button {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    width: 100%;
                    padding: 1rem 1.5rem;
                    background-color: var(--accordion-header-bg, #f9f9f9);
                    color: var(--heading-color, #333);
                    font-size: 1rem;
                    font-weight: 600;
                    text-align: left;
                    border: none;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                }
                .accordion-button:hover {
                    background-color: var(--accordion-header-hover-bg, #f0f0f0);
                }
                .accordion-icon {
                    position: relative;
                    width: 16px;
                    height: 16px;
                }
                .accordion-icon::before,
                .accordion-icon::after {
                    content: '';
                    position: absolute;
                    background-color: var(--primary-color, #8b0000);
                    transition: transform 0.3s ease;
                }
                .accordion-icon::before {
                    top: 7px;
                    left: 0;
                    width: 16px;
                    height: 2px;
                }
                .accordion-icon::after {
                    top: 0;
                    left: 7px;
                    width: 2px;
                    height: 16px;
                }
                .accordion-button[aria-expanded="true"] .accordion-icon::after {
                    transform: rotate(90deg);
                }
                .accordion-content {
                    max-height: 0;
                    overflow: hidden;
                    transition: max-height 0.3s ease;
                }
                .accordion-body {
                    padding: 1rem 1.5rem;
                    background-color: var(--accordion-content-bg, #fff);
                }
                ::slotted(*) {
                    margin: 0;
                }
            `;
            
            const accordion = document.createElement('div');
            accordion.className = 'accordion';
            
            // Create accordion items from slots
            const items = Array.from(this.children);
            items.forEach((item, index) => {
                const header = item.querySelector('.thabatta-accordion-header');
                const button = item.querySelector('.thabatta-accordion-button');
                const content = item.querySelector('.thabatta-accordion-collapse');
                
                if (header && button && content) {
                    const accordionItem = document.createElement('div');
                    accordionItem.className = 'accordion-item';
                    
                    const accordionHeader = document.createElement('h3');
                    accordionHeader.className = 'accordion-header';
                    
                    const accordionButton = document.createElement('button');
                    accordionButton.className = 'accordion-button';
                    accordionButton.setAttribute('aria-expanded', button.getAttribute('aria-expanded') || 'false');
                    accordionButton.textContent = button.textContent;
                    
                    const accordionIcon = document.createElement('span');
                    accordionIcon.className = 'accordion-icon';
                    
                    const accordionContent = document.createElement('div');
                    accordionContent.className = 'accordion-content';
                    
                    const accordionBody = document.createElement('div');
                    accordionBody.className = 'accordion-body';
                    
                    const slot = document.createElement('slot');
                    slot.setAttribute('name', `accordion-content-${index}`);
                    
                    // Set initial state
                    if (accordionButton.getAttribute('aria-expanded') === 'true') {
                        accordionContent.style.maxHeight = 'none';
                    }
                    
                    // Append elements
                    accordionButton.appendChild(accordionIcon);
                    accordionHeader.appendChild(accordionButton);
                    accordionBody.appendChild(slot);
                    accordionContent.appendChild(accordionBody);
                    
                    accordionItem.appendChild(accordionHeader);
                    accordionItem.appendChild(accordionContent);
                    
                    accordion.appendChild(accordionItem);
                    
                    // Set slot name for content
                    content.setAttribute('slot', `accordion-content-${index}`);
                }
            });
            
            this.shadowRoot.appendChild(style);
            this.shadowRoot.appendChild(accordion);
        }
    }
    
    // Componente de Abas
    class ThabattaTabs extends HTMLElement {
        constructor() {
            super();
            this.attachShadow({ mode: 'open' });
            this.render();
        }
        
        connectedCallback() {
            this.shadowRoot.querySelectorAll('.tab-button').forEach(button => {
                button.addEventListener('click', this.switchTab.bind(this));
            });
        }
        
        switchTab(event) {
            const button = event.currentTarget;
            const tabId = button.getAttribute('data-tab');
            
            // Deactivate all tabs
            this.shadowRoot.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
                btn.setAttribute('aria-selected', 'false');
            });
            
            this.shadowRoot.querySelectorAll('.tab-panel').forEach(panel => {
                panel.classList.remove('active');
                panel.setAttribute('hidden', '');
            });
            
            // Activate selected tab
            button.classList.add('active');
            button.setAttribute('aria-selected', 'true');
            
            const panel = this.shadowRoot.querySelector(`.tab-panel[data-tab="${tabId}"]`);
            if (panel) {
                panel.classList.add('active');
                panel.removeAttribute('hidden');
            }
        }
        
        render() {
            const style = document.createElement('style');
            style.textContent = `
                :host {
                    display: block;
                    margin-bottom: 2rem;
                }
                .tabs {
                    display: flex;
                    flex-direction: column;
                }
                .tab-nav {
                    display: flex;
                    border-bottom: 2px solid var(--border-color, #ddd);
                    margin-bottom: 1.5rem;
                    overflow-x: auto;
                    scrollbar-width: thin;
                }
                .tab-button {
                    padding: 1rem 1.5rem;
                    background-color: transparent;
                    color: var(--text-color, #666);
                    font-size: 1rem;
                    font-weight: 600;
                    border: none;
                    border-bottom: 2px solid transparent;
                    margin-bottom: -2px;
                    cursor: pointer;
                    transition: color 0.3s ease, border-color 0.3s ease;
                    white-space: nowrap;
                }
                .tab-button:hover {
                    color: var(--primary-color, #8b0000);
                }
                .tab-button.active {
                    color: var(--primary-color, #8b0000);
                    border-bottom-color: var(--primary-color, #8b0000);
                }
                .tab-content {
                    position: relative;
                }
                .tab-panel {
                    display: none;
                }
                .tab-panel.active {
                    display: block;
                }
                ::slotted(*) {
                    margin-top: 0;
                }
            `;
            
            const tabs = document.createElement('div');
            tabs.className = 'tabs';
            
            const tabNav = document.createElement('div');
            tabNav.className = 'tab-nav';
            tabNav.setAttribute('role', 'tablist');
            
            const tabContent = document.createElement('div');
            tabContent.className = 'tab-content';
            
            // Create tabs from slots
            const items = Array.from(this.children);
            items.forEach((item, index) => {
                const title = item.querySelector('.thabatta-tab-button');
                const panel = item.querySelector('.thabatta-tab-panel');
                
                if (title && panel) {
                    const tabId = `tab-${index}`;
                    const isActive = title.classList.contains('active');
                    
                    const tabButton = document.createElement('button');
                    tabButton.className = `tab-button ${isActive ? 'active' : ''}`;
                    tabButton.setAttribute('role', 'tab');
                    tabButton.setAttribute('aria-selected', isActive ? 'true' : 'false');
                    tabButton.setAttribute('data-tab', tabId);
                    tabButton.textContent = title.textContent;
                    
                    const tabPanel = document.createElement('div');
                    tabPanel.className = `tab-panel ${isActive ? 'active' : ''}`;
                    tabPanel.setAttribute('role', 'tabpanel');
                    tabPanel.setAttribute('data-tab', tabId);
                    if (!isActive) {
                        tabPanel.setAttribute('hidden', '');
                    }
                    
                    const slot = document.createElement('slot');
                    slot.setAttribute('name', `tab-content-${index}`);
                    
                    // Append elements
                    tabNav.appendChild(tabButton);
                    tabPanel.appendChild(slot);
                    tabContent.appendChild(tabPanel);
                    
                    // Set slot name for content
                    panel.setAttribute('slot', `tab-content-${index}`);
                }
            });
            
            tabs.appendChild(tabNav);
            tabs.appendChild(tabContent);
            
            this.shadowRoot.appendChild(style);
            this.shadowRoot.appendChild(tabs);
        }
    }
    
    // Componente de Slider
    class ThabattaSlider extends HTMLElement {
        constructor() {
            super();
            this.attachShadow({ mode: 'open' });
            this.currentSlide = 0;
            this.autoplayInterval = null;
            this.render();
        }
        
        connectedCallback() {
            this.slides = this.shadowRoot.querySelectorAll('.slide');
            this.dots = this.shadowRoot.querySelectorAll('.slider-dot');
            
            // Set up navigation
            this.shadowRoot.querySelector('.slider-prev')?.addEventListener('click', () => this.prevSlide());
            this.shadowRoot.querySelector('.slider-next')?.addEventListener('click', () => this.nextSlide());
            
            // Set up dots
            this.dots.forEach((dot, index) => {
                dot.addEventListener('click', () => this.goToSlide(index));
            });
            
            // Set up autoplay
            if (this.getAttribute('data-autoplay') === 'true') {
                const interval = parseInt(this.getAttribute('data-interval')) || 5000;
                this.startAutoplay(interval);
            }
            
            // Set up swipe
            this.setupSwipe();
        }
        
        disconnectedCallback() {
            this.stopAutoplay();
        }
        
        startAutoplay(interval) {
            this.autoplayInterval = setInterval(() => {
                this.nextSlide();
            }, interval);
        }
        
        stopAutoplay() {
            if (this.autoplayInterval) {
                clearInterval(this.autoplayInterval);
                this.autoplayInterval = null;
            }
        }
        
        setupSwipe() {
            const slider = this.shadowRoot.querySelector('.slider-container');
            let startX, endX;
            
            slider.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
            });
            
            slider.addEventListener('touchend', (e) => {
                endX = e.changedTouches[0].clientX;
                
                if (startX - endX > 50) {
                    // Swipe left
                    this.nextSlide();
                } else if (endX - startX > 50) {
                    // Swipe right
                    this.prevSlide();
                }
            });
        }
        
        prevSlide() {
            this.goToSlide(this.currentSlide - 1);
        }
        
        nextSlide() {
            this.goToSlide(this.currentSlide + 1);
        }
        
        goToSlide(index) {
            // Reset autoplay
            if (this.autoplayInterval) {
                this.stopAutoplay();
                const interval = parseInt(this.getAttribute('data-interval')) || 5000;
                this.startAutoplay(interval);
            }
            
            // Handle index bounds
            if (index < 0) {
                index = this.slides.length - 1;
            } else if (index >= this.slides.length) {
                index = 0;
            }
            
            // Update current slide
            this.slides[this.currentSlide].classList.remove('active');
            this.slides[index].classList.add('active');
            
            // Update dots
            if (this.dots.length) {
                this.dots[this.currentSlide].classList.remove('active');
                this.dots[index].classList.add('active');
            }
            
            this.currentSlide = index;
        }
        
        render() {
            const style = document.createElement('style');
            style.textContent = `
                :host {
                    display: block;
                    margin-bottom: 2rem;
                }
                .slider {
                    position: relative;
                    overflow: hidden;
                    border-radius: var(--slider-radius, 0.5rem);
                }
                .slider-container {
                    position: relative;
                    width: 100%;
                    height: 100%;
                }
                .slide {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    opacity: 0;
                    transition: opacity 0.5s ease;
                }
                .slide.active {
                    opacity: 1;
                    position: relative;
                }
                .slider-arrows {
                    position: absolute;
                    top: 50%;
                    left: 0;
                    right: 0;
                    display: flex;
                    justify-content: space-between;
                    transform: translateY(-50%);
                    z-index: 1;
                }
                .slider-arrow {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 40px;
                    height: 40px;
                    background-color: rgba(255, 255, 255, 0.8);
                    color: var(--primary-color, #8b0000);
                    border: none;
                    border-radius: 50%;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                    margin: 0 1rem;
                }
                .slider-arrow:hover {
                    background-color: rgba(255, 255, 255, 1);
                }
                .slider-dots {
                    position: absolute;
                    bottom: 1rem;
                    left: 0;
                    right: 0;
                    display: flex;
                    justify-content: center;
                    z-index: 1;
                }
                .slider-dot {
                    width: 10px;
                    height: 10px;
                    background-color: rgba(255, 255, 255, 0.5);
                    border: none;
                    border-radius: 50%;
                    margin: 0 0.25rem;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                }
                .slider-dot.active {
                    background-color: var(--primary-color, #8b0000);
                }
                ::slotted(*) {
                    margin: 0;
                }
            `;
            
            const slider = document.createElement('div');
            slider.className = 'slider';
            
            const sliderContainer = document.createElement('div');
            sliderContainer.className = 'slider-container';
            
            // Create slides from slots
            const items = Array.from(this.children);
            items.forEach((item, index) => {
                const slide = document.createElement('div');
                slide.className = `slide ${index === 0 ? 'active' : ''}`;
                
                const slot = document.createElement('slot');
                slot.setAttribute('name', `slide-${index}`);
                
                slide.appendChild(slot);
                sliderContainer.appendChild(slide);
                
                // Set slot name for content
                item.setAttribute('slot', `slide-${index}`);
            });
            
            slider.appendChild(sliderContainer);
            
            // Add arrows if enabled
            if (this.getAttribute('data-arrows') !== 'false') {
                const sliderArrows = document.createElement('div');
                sliderArrows.className = 'slider-arrows';
                
                const prevButton = document.createElement('button');
                prevButton.className = 'slider-arrow slider-prev';
                prevButton.innerHTML = '<span>&lt;</span>';
                prevButton.setAttribute('aria-label', 'Previous slide');
                
                const nextButton = document.createElement('button');
                nextButton.className = 'slider-arrow slider-next';
                nextButton.innerHTML = '<span>&gt;</span>';
                nextButton.setAttribute('aria-label', 'Next slide');
                
                sliderArrows.appendChild(prevButton);
                sliderArrows.appendChild(nextButton);
                slider.appendChild(sliderArrows);
            }
            
            // Add dots if enabled
            if (this.getAttribute('data-dots') !== 'false') {
                const sliderDots = document.createElement('div');
                sliderDots.className = 'slider-dots';
                
                items.forEach((_, index) => {
                    const dot = document.createElement('button');
                    dot.className = `slider-dot ${index === 0 ? 'active' : ''}`;
                    dot.setAttribute('aria-label', `Go to slide ${index + 1}`);
                    sliderDots.appendChild(dot);
                });
                
                slider.appendChild(sliderDots);
            }
            
            this.shadowRoot.appendChild(style);
            this.shadowRoot.appendChild(slider);
        }
    }
    
    // Registrar componentes
    customElements.define('thabatta-card', ThabattaCard);
    customElements.define('thabatta-accordion', ThabattaAccordion);
    customElements.define('thabatta-tabs', ThabattaTabs);
    customElements.define('thabatta-slider', ThabattaSlider);
})();
EOT;

    // Escrever o arquivo
    file_put_contents($script_file, $script_content);

    return true;
}

/**
 * Função auxiliar para gerar o script de blocos Gutenberg
 */
function thabatta_generate_gutenberg_blocks_script()
{
    // Caminho para o arquivo de script
    $script_file = get_template_directory() . '/assets/js/gutenberg-blocks.js';

    // Verificar se o diretório existe
    $script_dir = dirname($script_file);
    if (!file_exists($script_dir)) {
        wp_mkdir_p($script_dir);
    }

    // Conteúdo do script
    $script_content = <<<'EOT'
/**
 * Blocos Gutenberg para o tema Thabatta Advocacia
 */
(function(blocks, element, blockEditor, components) {
    var el = element.createElement;
    var registerBlockType = blocks.registerBlockType;
    var RichText = blockEditor.RichText;
    var MediaUpload = blockEditor.MediaUpload;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelBody = components.PanelBody;
    var TextControl = components.TextControl;
    var ToggleControl = components.ToggleControl;
    var RangeControl = components.RangeControl;
    var SelectControl = components.SelectControl;
    
    // Bloco de Card
    registerBlockType('thabatta/card', {
        title: 'Card Thabatta',
        icon: 'index-card',
        category: 'design',
        attributes: {
            title: {
                type: 'string',
                default: 'Título do Card'
            },
            content: {
                type: 'string',
                default: 'Conteúdo do card...'
            },
            image: {
                type: 'string',
                default: ''
            },
            link: {
                type: 'string',
                default: ''
            },
            buttonText: {
                type: 'string',
                default: 'Saiba mais'
            },
            className: {
                type: 'string',
                default: ''
            }
        },
        edit: function(props) {
            var attributes = props.attributes;
            
            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: 'Configurações do Card', initialOpen: true },
                        el(TextControl, {
                            label: 'Link',
                            value: attributes.link,
                            onChange: function(value) {
                                props.setAttributes({ link: value });
                            }
                        }),
                        el(TextControl, {
                            label: 'Texto do Botão',
                            value: attributes.buttonText,
                            onChange: function(value) {
                                props.setAttributes({ buttonText: value });
                            }
                        })
                    )
                ),
                el('div', { className: 'thabatta-card-editor' },
                    el('div', { className: 'thabatta-card-image-upload' },
                        el(MediaUpload, {
                            onSelect: function(media) {
                                props.setAttributes({ image: media.url });
                            },
                            type: 'image',
                            render: function(obj) {
                                return el('div', {},
                                    attributes.image ?
                                        el('div', { className: 'thabatta-card-image-preview' },
                                            el('img', { src: attributes.image }),
                                            el('button', {
                                                className: 'button',
                                                onClick: obj.open
                                            }, 'Alterar Imagem'),
                                            el('button', {
                                                className: 'button',
                                                onClick: function() {
                                                    props.setAttributes({ image: '' });
                                                }
                                            }, 'Remover')
                                        ) :
                                        el('button', {
                                            className: 'button',
                                            onClick: obj.open
                                        }, 'Adicionar Imagem')
                                );
                            }
                        })
                    ),
                    el('div', { className: 'thabatta-card-content-editor' },
                        el(RichText, {
                            tagName: 'h3',
                            className: 'thabatta-card-title',
                            value: attributes.title,
                            onChange: function(value) {
                                props.setAttributes({ title: value });
                            },
                            placeholder: 'Título do Card'
                        }),
                        el(RichText, {
                            tagName: 'div',
                            className: 'thabatta-card-text',
                            value: attributes.content,
                            onChange: function(value) {
                                props.setAttributes({ content: value });
                            },
                            placeholder: 'Conteúdo do card...'
                        })
                    )
                )
            ];
        },
        save: function() {
            // Rendering in PHP
            return null;
        }
    });
    
    // Outros blocos podem ser adicionados aqui...
    
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components
);
EOT;

    // Escrever o arquivo
    file_put_contents($script_file, $script_content);

    return true;
}
