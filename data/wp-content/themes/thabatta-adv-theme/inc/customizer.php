<?php
/**
 * Thabatta Advocacia Theme Customizer
 *
 * @package Thabatta_Advocacia
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function thabatta_customize_register($wp_customize) {
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport = 'postMessage';
    $wp_customize->get_setting('header_textcolor')->transport = 'postMessage';

    if (isset($wp_customize->selective_refresh)) {
        $wp_customize->selective_refresh->add_partial('blogname', array(
            'selector' => '.site-title a',
            'render_callback' => 'thabatta_customize_partial_blogname',
        ));
        $wp_customize->selective_refresh->add_partial('blogdescription', array(
            'selector' => '.site-description',
            'render_callback' => 'thabatta_customize_partial_blogdescription',
        ));
    }

    // Seção de cores do tema
    $wp_customize->add_section('thabatta_colors', array(
        'title' => __('Cores do Tema', 'thabatta-adv'),
        'priority' => 30,
    ));

    // Cor primária
    $wp_customize->add_setting('primary_color', array(
        'default' => '#8b0000',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_color', array(
        'label' => __('Cor Primária', 'thabatta-adv'),
        'section' => 'thabatta_colors',
        'settings' => 'primary_color',
    )));

    // Cor secundária
    $wp_customize->add_setting('secondary_color', array(
        'default' => '#d4af37',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'secondary_color', array(
        'label' => __('Cor Secundária (Dourado)', 'thabatta-adv'),
        'section' => 'thabatta_colors',
        'settings' => 'secondary_color',
    )));

    // Cor de texto
    $wp_customize->add_setting('text_color', array(
        'default' => '#333333',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'text_color', array(
        'label' => __('Cor de Texto', 'thabatta-adv'),
        'section' => 'thabatta_colors',
        'settings' => 'text_color',
    )));

    // Cor de fundo
    $wp_customize->add_setting('background_color', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'background_color', array(
        'label' => __('Cor de Fundo', 'thabatta-adv'),
        'section' => 'thabatta_colors',
        'settings' => 'background_color',
    )));

    // Seção de layout
    $wp_customize->add_section('thabatta_layout', array(
        'title' => __('Layout', 'thabatta-adv'),
        'priority' => 40,
    ));

    // Layout da sidebar
    $wp_customize->add_setting('sidebar_position', array(
        'default' => 'right',
        'sanitize_callback' => 'thabatta_sanitize_select',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('sidebar_position', array(
        'label' => __('Posição da Sidebar', 'thabatta-adv'),
        'section' => 'thabatta_layout',
        'type' => 'select',
        'choices' => array(
            'right' => __('Direita', 'thabatta-adv'),
            'left' => __('Esquerda', 'thabatta-adv'),
            'none' => __('Sem Sidebar', 'thabatta-adv'),
        ),
    ));

    // Container width
    $wp_customize->add_setting('container_width', array(
        'default' => '1200',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('container_width', array(
        'label' => __('Largura do Container (px)', 'thabatta-adv'),
        'section' => 'thabatta_layout',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 960,
            'max' => 1600,
            'step' => 10,
        ),
    ));

    // Seção de tipografia
    $wp_customize->add_section('thabatta_typography', array(
        'title' => __('Tipografia', 'thabatta-adv'),
        'priority' => 50,
    ));

    // Fonte do corpo
    $wp_customize->add_setting('body_font', array(
        'default' => 'Roboto',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('body_font', array(
        'label' => __('Fonte do Corpo', 'thabatta-adv'),
        'section' => 'thabatta_typography',
        'type' => 'select',
        'choices' => array(
            'Roboto' => 'Roboto',
            'Open Sans' => 'Open Sans',
            'Lato' => 'Lato',
            'Montserrat' => 'Montserrat',
            'Raleway' => 'Raleway',
            'Playfair Display' => 'Playfair Display',
            'Merriweather' => 'Merriweather',
        ),
    ));

    // Fonte dos títulos
    $wp_customize->add_setting('heading_font', array(
        'default' => 'Playfair Display',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('heading_font', array(
        'label' => __('Fonte dos Títulos', 'thabatta-adv'),
        'section' => 'thabatta_typography',
        'type' => 'select',
        'choices' => array(
            'Roboto' => 'Roboto',
            'Open Sans' => 'Open Sans',
            'Lato' => 'Lato',
            'Montserrat' => 'Montserrat',
            'Raleway' => 'Raleway',
            'Playfair Display' => 'Playfair Display',
            'Merriweather' => 'Merriweather',
        ),
    ));

    // Tamanho da fonte base
    $wp_customize->add_setting('base_font_size', array(
        'default' => '16',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('base_font_size', array(
        'label' => __('Tamanho da Fonte Base (px)', 'thabatta-adv'),
        'section' => 'thabatta_typography',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 12,
            'max' => 24,
            'step' => 1,
        ),
    ));

    // Seção de rodapé
    $wp_customize->add_section('thabatta_footer', array(
        'title' => __('Rodapé', 'thabatta-adv'),
        'priority' => 60,
    ));

    // Texto do copyright
    $wp_customize->add_setting('copyright_text', array(
        'default' => '© ' . date('Y') . ' Thabatta Apolinário Advocacia. Todos os direitos reservados.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('copyright_text', array(
        'label' => __('Texto de Copyright', 'thabatta-adv'),
        'section' => 'thabatta_footer',
        'type' => 'text',
    ));

    // Mostrar créditos do tema
    $wp_customize->add_setting('show_credits', array(
        'default' => true,
        'sanitize_callback' => 'thabatta_sanitize_checkbox',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('show_credits', array(
        'label' => __('Mostrar Créditos do Tema', 'thabatta-adv'),
        'section' => 'thabatta_footer',
        'type' => 'checkbox',
    ));
}
add_action('customize_register', 'thabatta_customize_register');

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function thabatta_customize_partial_blogname() {
    bloginfo('name');
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function thabatta_customize_partial_blogdescription() {
    bloginfo('description');
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function thabatta_customize_preview_js() {
    wp_enqueue_script('thabatta-customizer', get_template_directory_uri() . '/assets/js/customizer.js', array('customize-preview'), THABATTA_VERSION, true);
}
add_action('customize_preview_init', 'thabatta_customize_preview_js');

/**
 * Sanitize select field
 *
 * @param string $input The input from the setting
 * @param object $setting The selected setting
 * @return string The sanitized input
 */
function thabatta_sanitize_select($input, $setting) {
    // Get list of choices from the control associated with the setting
    $choices = $setting->manager->get_control($setting->id)->choices;
    
    // If the input is a valid key, return it; otherwise, return the default
    return (array_key_exists($input, $choices) ? $input : $setting->default);
}

/**
 * Sanitize checkbox field
 *
 * @param bool $checked Whether the checkbox is checked
 * @return bool Whether the checkbox is checked
 */
function thabatta_sanitize_checkbox($checked) {
    return ((isset($checked) && true == $checked) ? true : false);
}

/**
 * Generate CSS for the theme customizer options
 */
function thabatta_customizer_css() {
    ?>
    <style type="text/css">
        :root {
            --primary-color: <?php echo esc_attr(get_theme_mod('primary_color', '#8b0000')); ?>;
            --primary-color-hover: <?php echo esc_attr(thabatta_adjust_brightness(get_theme_mod('primary_color', '#8b0000'), -20)); ?>;
            --secondary-color: <?php echo esc_attr(get_theme_mod('secondary_color', '#d4af37')); ?>;
            --secondary-color-hover: <?php echo esc_attr(thabatta_adjust_brightness(get_theme_mod('secondary_color', '#d4af37'), -20)); ?>;
            --text-color: <?php echo esc_attr(get_theme_mod('text_color', '#333333')); ?>;
            --background-color: <?php echo esc_attr(get_theme_mod('background_color', '#ffffff')); ?>;
            --body-font: <?php echo esc_attr(get_theme_mod('body_font', 'Roboto')); ?>, sans-serif;
            --heading-font: <?php echo esc_attr(get_theme_mod('heading_font', 'Playfair Display')); ?>, serif;
            --base-font-size: <?php echo esc_attr(get_theme_mod('base_font_size', '16')); ?>px;
            --container-width: <?php echo esc_attr(get_theme_mod('container_width', '1200')); ?>px;
        }
        
        body {
            font-family: var(--body-font);
            font-size: var(--base-font-size);
            color: var(--text-color);
            background-color: var(--background-color);
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--heading-font);
            color: var(--primary-color);
        }
        
        a {
            color: var(--primary-color);
        }
        
        a:hover, a:focus {
            color: var(--primary-color-hover);
        }
        
        .btn-primary, .wp-block-button__link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover, .wp-block-button__link:hover {
            background-color: var(--primary-color-hover);
            border-color: var(--primary-color-hover);
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-secondary:hover {
            background-color: var(--secondary-color-hover);
            border-color: var(--secondary-color-hover);
        }
        
        .container {
            max-width: var(--container-width);
        }
        
        /* Sidebar positioning */
        <?php if (get_theme_mod('sidebar_position', 'right') === 'left') : ?>
        @media (min-width: 992px) {
            .content-area {
                order: 2;
            }
            .widget-area {
                order: 1;
            }
        }
        <?php elseif (get_theme_mod('sidebar_position', 'right') === 'none') : ?>
        @media (min-width: 992px) {
            .content-area {
                flex: 0 0 100%;
                max-width: 100%;
            }
            .widget-area {
                display: none;
            }
        }
        <?php endif; ?>
    </style>
    <?php
}
add_action('wp_head', 'thabatta_customizer_css');

/**
 * Adjust color brightness
 *
 * @param string $hex Hex color code
 * @param int $steps Steps to adjust brightness (negative for darker, positive for lighter)
 * @return string Adjusted hex color
 */
function thabatta_adjust_brightness($hex, $steps) {
    // Remove # if present
    $hex = ltrim($hex, '#');
    
    // Convert to RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Adjust brightness
    $r = max(0, min(255, $r + $steps));
    $g = max(0, min(255, $g + $steps));
    $b = max(0, min(255, $b + $steps));
    
    // Convert back to hex
    return sprintf('#%02x%02x%02x', $r, $g, $b);
}

/**
 * Generate customizer JavaScript
 */
function thabatta_generate_customizer_js() {
    // Caminho para o arquivo de script
    $script_file = get_template_directory() . '/assets/js/customizer.js';
    
    // Verificar se o diretório existe
    $script_dir = dirname($script_file);
    if (!file_exists($script_dir)) {
        wp_mkdir_p($script_dir);
    }
    
    // Conteúdo do script
    $script_content = <<<'EOT'
/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

(function($) {
    // Site title and description.
    wp.customize('blogname', function(value) {
        value.bind(function(to) {
            $('.site-title a').text(to);
        });
    });
    
    wp.customize('blogdescription', function(value) {
        value.bind(function(to) {
            $('.site-description').text(to);
        });
    });
    
    // Header text color.
    wp.customize('header_textcolor', function(value) {
        value.bind(function(to) {
            if ('blank' === to) {
                $('.site-title, .site-description').css({
                    'clip': 'rect(1px, 1px, 1px, 1px)',
                    'position': 'absolute'
                });
            } else {
                $('.site-title, .site-description').css({
                    'clip': 'auto',
                    'position': 'relative'
                });
                $('.site-title a, .site-description').css({
                    'color': to
                });
            }
        });
    });
    
    // Primary color
    wp.customize('primary_color', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--primary-color', to);
            
            // Calculate hover color (20% darker)
            const darker = adjustBrightness(to, -20);
            document.documentElement.style.setProperty('--primary-color-hover', darker);
        });
    });
    
    // Secondary color
    wp.customize('secondary_color', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--secondary-color', to);
            
            // Calculate hover color (20% darker)
            const darker = adjustBrightness(to, -20);
            document.documentElement.style.setProperty('--secondary-color-hover', darker);
        });
    });
    
    // Text color
    wp.customize('text_color', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--text-color', to);
        });
    });
    
    // Background color
    wp.customize('background_color', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--background-color', to);
        });
    });
    
    // Body font
    wp.customize('body_font', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--body-font', to + ', sans-serif');
        });
    });
    
    // Heading font
    wp.customize('heading_font', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--heading-font', to + ', serif');
        });
    });
    
    // Base font size
    wp.customize('base_font_size', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--base-font-size', to + 'px');
        });
    });
    
    // Container width
    wp.customize('container_width', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--container-width', to + 'px');
        });
    });
    
    // Copyright text
    wp.customize('copyright_text', function(value) {
        value.bind(function(to) {
            $('.site-info .copyright').text(to);
        });
    });
    
    // Show credits
    wp.customize('show_credits', function(value) {
        value.bind(function(to) {
            if (to) {
                $('.site-info .credits').show();
            } else {
                $('.site-info .credits').hide();
            }
        });
    });
    
    /**
     * Helper function to adjust brightness of a hex color
     */
         * Helper function to adjust brightness of a hex color
     */
    function adjustBrightness(hex, steps) {
        // Remove # if present
        hex = hex.replace(/^#/, '');
        
        // Convert to RGB
        let r = parseInt(hex.substring(0, 2), 16);
        let g = parseInt(hex.substring(2, 4), 16);
        let b = parseInt(hex.substring(4, 6), 16);
        
        // Adjust brightness
        r = Math.max(0, Math.min(255, r + steps));
        g = Math.max(0, Math.min(255, g + steps));
        b = Math.max(0, Math.min(255, b + steps));
        
        // Convert back to hex
        return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
    }
})(jQuery);
EOT;
    
    // Escrever o arquivo
    file_put_contents($script_file, $script_content);
}