<?php

namespace ThabattaAdv\Infrastructure\WordPress;

class Assets
{
    public function register(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'register_public_assets'], 100);
        add_action('wp_enqueue_scripts', [$this, 'register_localized_scripts'], 20);
        add_action('wp_enqueue_scripts', [$this, 'register_contact_form_assets'], 101);
        add_action('admin_enqueue_scripts', [$this, 'register_admin_assets']);
    }

    public function register_public_assets(): void
    {
        wp_enqueue_style('thabatta-style', get_stylesheet_uri(), array(), THABATTA_VERSION);
        wp_enqueue_style('thabatta-main-style', get_template_directory_uri() . '/assets/css/style.min.css', array(), THABATTA_VERSION);

        wp_enqueue_style('fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', array(), '5.15.4');
        wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap', array(), null);

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-mask', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js', array('jquery'), '1.14.16', true);

        wp_enqueue_style('aos-css', 'https://unpkg.com/aos@2.3.1/dist/aos.css', array(), '2.3.1');
        wp_enqueue_script('aos-js', 'https://unpkg.com/aos@2.3.1/dist/aos.js', array(), '2.3.1', true);

        wp_enqueue_style('slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', array(), '1.8.1');
        wp_enqueue_style('slick-theme-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css', array(), '1.8.1');
        wp_enqueue_script('slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), '1.8.1', true);

        wp_enqueue_script('thabatta-script', get_template_directory_uri() . '/assets/js/bundle.min.js', array('jquery', 'jquery-mask'), THABATTA_VERSION, true);

        wp_localize_script('thabatta-script', 'thabattaData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('thabatta_consultation_nonce'),
            'homeUrl' => esc_url(home_url('/')),
            'themeUrl' => esc_url(get_template_directory_uri()),
        ));

        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
    }

    public function register_localized_scripts(): void
    {
        wp_localize_script('thabatta-script', 'thabattaData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'siteUrl' => get_site_url(),
            'themePath' => get_template_directory_uri(),
        ));
    }

    public function register_contact_form_assets(): void
    {
        if (!is_page_template('page-contato.php')) {
            return;
        }

        wp_enqueue_script(
            'thabatta-contact-form-multistep',
            get_template_directory_uri() . '/js/contact-form-multistep.js',
            array('jquery'),
            filemtime(get_template_directory() . '/js/contact-form-multistep.js'),
            true
        );

        wp_localize_script('thabatta-contact-form-multistep', 'thabattaData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('thabatta_consultation_nonce'),
        ));
    }

    public function register_admin_assets($hook_suffix): void
    {
        $theme_version = defined('THABATTA_THEME_VERSION') ? THABATTA_THEME_VERSION : '1.0.0';

        wp_enqueue_style('thabatta-admin-style', get_template_directory_uri() . '/assets/css/admin.css', array(), $theme_version);

        $screen = get_current_screen();
        $load_admin_js = false;

        if ($screen) {
            $theme_options_pages = array(
                'toplevel_page_theme-general-settings',
                'opcoes-do-tema_page_theme-options-social',
                'opcoes-do-tema_page_theme-options-contact',
                'toplevel_page_thabatta-theme-options',
                'appearance_page_thabatta-security-settings',
            );

            $post_edit_pages = array('post', 'page', 'lead', 'area_atuacao', 'membro_equipe');

            if (in_array($screen->id, $theme_options_pages, true) || ($screen->base === 'post' && in_array($screen->post_type, $post_edit_pages, true))) {
                $load_admin_js = true;
            }
        }

        if (!$load_admin_js) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('thabatta-admin-script', get_template_directory_uri() . '/assets/js/admin.min.js', array('jquery', 'wp-color-picker'), $theme_version, true);

        wp_localize_script('thabatta-admin-script', 'thabattaAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('thabatta_admin_nonce'),
            'mediaTitle' => esc_html__('Selecionar ou Enviar Mídia', 'thabatta-adv'),
            'mediaButton' => esc_html__('Usar esta mídia', 'thabatta-adv'),
        ));
    }
}
