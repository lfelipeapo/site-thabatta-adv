<?php
/**
 * Funções de template tags para o tema Thabatta Advocacia
 *
 * @package Thabatta_Advocacia
 */

if (!defined('ABSPATH')) {
    exit; // Saída direta se acessado diretamente
}

/**
 * Exibe a data de publicação do post formatada
 */
function thabatta_posted_on()
{
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
    if (get_the_time('U') !== get_the_modified_time('U')) {
        $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
    }

    $time_string = sprintf(
        $time_string,
        esc_attr(get_the_date(DATE_W3C)),
        esc_html(get_the_date()),
        esc_attr(get_the_modified_date(DATE_W3C)),
        esc_html(get_the_modified_date())
    );

    $posted_on = sprintf(
        /* translators: %s: data de publicação. */
        esc_html_x('Publicado em %s', 'data de publicação', 'thabatta-adv'),
        '<a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $time_string . '</a>'
    );

    echo '<span class="posted-on"><i class="far fa-calendar-alt"></i> ' . $posted_on . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Exibe o autor do post
 */
function thabatta_posted_by()
{
    $byline = sprintf(
        /* translators: %s: nome do autor do post. */
        esc_html_x('por %s', 'nome do autor do post', 'thabatta-adv'),
        '<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>'
    );

    echo '<span class="byline"><i class="far fa-user"></i> ' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Exibe as categorias do post
 */
function thabatta_post_categories()
{
    // Ocultar categoria para páginas.
    if ('post' !== get_post_type()) {
        return;
    }

    /* translators: usado entre itens da lista, há um espaço após a vírgula */
    $categories_list = get_the_category_list(esc_html__(', ', 'thabatta-adv'));
    if ($categories_list) {
        /* translators: 1: lista de categorias. */
        printf('<span class="cat-links"><i class="far fa-folder-open"></i> ' . esc_html__('Categorias: %1$s', 'thabatta-adv') . '</span>', $categories_list); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

/**
 * Exibe as tags do post
 */
function thabatta_post_tags()
{
    // Ocultar tag para páginas.
    if ('post' !== get_post_type()) {
        return;
    }

    /* translators: usado entre itens da lista, há um espaço após a vírgula */
    $tags_list = get_the_tag_list('', esc_html_x(', ', 'lista de tags', 'thabatta-adv'));
    if ($tags_list) {
        /* translators: 1: lista de tags. */
        printf('<span class="tags-links"><i class="fas fa-tags"></i> ' . esc_html__('Tags: %1$s', 'thabatta-adv') . '</span>', $tags_list); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

/**
 * Exibe o link de edição para posts e páginas
 */
function thabatta_edit_link()
{
    // Adicionar um link de edição para posts e páginas.
    edit_post_link(
        sprintf(
            wp_kses(
                /* translators: %s: Nome do post atual. Visível apenas para usuários que podem editar. */
                __('Editar <span class="screen-reader-text">%s</span>', 'thabatta-adv'),
                array(
                    'span' => array(
                        'class' => array(),
                    ),
                )
            ),
            wp_kses_post(get_the_title())
        ),
        '<span class="edit-link">',
        '</span>'
    );
}

/**
 * Exibe a imagem em destaque com verificação de existência
 */
function thabatta_post_thumbnail()
{
    if (post_password_required() || is_attachment() || !has_post_thumbnail()) {
        return;
    }

    if (is_singular()) :
        ?>
        <div class="post-thumbnail">
            <?php the_post_thumbnail('thabatta-featured', array('class' => 'featured-image')); ?>
        </div><!-- .post-thumbnail -->
    <?php else : ?>
        <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
            <?php
            the_post_thumbnail(
                'thabatta-card',
                array(
                    'alt' => the_title_attribute(
                        array(
                            'echo' => false,
                        )
                    ),
                    'class' => 'card-image',
                )
            );
        ?>
        </a>
    <?php
    endif; // End is_singular().
}

/**
 * Exibe a paginação de posts
 */
function thabatta_posts_pagination()
{
    the_posts_pagination(
        array(
            'mid_size'  => 2,
            'prev_text' => '<i class="fas fa-arrow-left"></i> ' . esc_html__('Anterior', 'thabatta-adv'),
            'next_text' => esc_html__('Próximo', 'thabatta-adv') . ' <i class="fas fa-arrow-right"></i>',
            'screen_reader_text' => esc_html__('Navegação de posts', 'thabatta-adv'),
        )
    );
}

/**
 * Exibe a navegação de posts individuais
 */
function thabatta_post_navigation()
{
    the_post_navigation(
        array(
            'prev_text' => '<span class="nav-subtitle"><i class="fas fa-arrow-left"></i> ' . esc_html__('Anterior:', 'thabatta-adv') . '</span> <span class="nav-title">%title</span>',
            'next_text' => '<span class="nav-subtitle">' . esc_html__('Próximo:', 'thabatta-adv') . ' <i class="fas fa-arrow-right"></i></span> <span class="nav-title">%title</span>',
            'screen_reader_text' => esc_html__('Navegação de posts', 'thabatta-adv'),
        )
    );
}

/**
 * Exibe os comentários e o formulário de comentários
 */
function thabatta_comments()
{
    // Se os comentários estão abertos ou temos pelo menos um comentário, carregue o template de comentários.
    if (comments_open() || get_comments_number()) :
        comments_template();
    endif;
}

/**
 * Exibe o título da página com verificação de contexto
 */
function thabatta_page_title()
{
    if (is_home() && !is_front_page()) :
        ?>
        <header class="page-header">
            <h1 class="page-title"><?php single_post_title(); ?></h1>
        </header>
    <?php elseif (is_archive()) : ?>
        <header class="page-header">
            <?php
            the_archive_title('<h1 class="page-title">', '</h1>');
        the_archive_description('<div class="archive-description">', '</div>');
        ?>
        </header>
    <?php elseif (is_search()) : ?>
        <header class="page-header">
            <h1 class="page-title">
                <?php
            /* translators: %s: termo de pesquisa. */
            printf(esc_html__('Resultados da pesquisa para: %s', 'thabatta-adv'), '<span>' . get_search_query() . '</span>');
        ?>
            </h1>
        </header>
    <?php elseif (is_404()) : ?>
        <header class="page-header">
            <h1 class="page-title"><?php esc_html_e('Página não encontrada', 'thabatta-adv'); ?></h1>
        </header>
    <?php endif;
}

/**
 * Exibe os posts relacionados
 */
function thabatta_related_posts_display()
{
    if (!function_exists('thabatta_get_related_posts')) {
        return;
    }

    $related_posts = thabatta_get_related_posts();

    if (!empty($related_posts)) :
        ?>
        <div class="related-posts">
            <h3><?php esc_html_e('Posts Relacionados', 'thabatta-adv'); ?></h3>
            <div class="grid grid-3">
                <?php foreach ($related_posts as $related_post) : ?>
                    <div class="card">
                        <?php if (has_post_thumbnail($related_post->ID)) : ?>
                            <div class="card-image">
                                <a href="<?php echo esc_url(get_permalink($related_post->ID)); ?>">
                                    <?php echo get_the_post_thumbnail($related_post->ID, 'thabatta-card'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="card-content">
                            <h4><a href="<?php echo esc_url(get_permalink($related_post->ID)); ?>"><?php echo esc_html(get_the_title($related_post->ID)); ?></a></h4>
                            <p><?php echo get_the_excerpt($related_post->ID); ?></p>
                            <a href="<?php echo esc_url(get_permalink($related_post->ID)); ?>" class="read-more">
                                <?php esc_html_e('Leia mais', 'thabatta-adv'); ?> <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif;
}

/**
 * Exibe os links de compartilhamento social
 */
function thabatta_social_sharing()
{
    $share_url = urlencode(get_permalink());
    $share_title = urlencode(get_the_title());
    ?>
    <div class="share-links">
        <span><?php esc_html_e('Compartilhar:', 'thabatta-adv'); ?></span>
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Compartilhar no Facebook', 'thabatta-adv'); ?>">
            <i class="fab fa-facebook-f"></i>
        </a>
        <a href="https://twitter.com/intent/tweet?url=<?php echo $share_url; ?>&text=<?php echo $share_title; ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Compartilhar no Twitter', 'thabatta-adv'); ?>">
            <i class="fab fa-twitter"></i>
        </a>
        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $share_url; ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Compartilhar no LinkedIn', 'thabatta-adv'); ?>">
            <i class="fab fa-linkedin-in"></i>
        </a>
        <a href="https://api.whatsapp.com/send?text=<?php echo $share_title . '%20' . $share_url; ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Compartilhar no WhatsApp', 'thabatta-adv'); ?>">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>
    <?php
}

/**
 * Exibe o formulário de pesquisa personalizado
 */
function thabatta_search_form()
{
    ?>
    <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
        <label class="screen-reader-text" for="s"><?php esc_html_e('Pesquisar por:', 'thabatta-adv'); ?></label>
        <div class="search-form-inner">
            <input type="search" id="s" class="search-field" placeholder="<?php esc_attr_e('Pesquisar...', 'thabatta-adv'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
            <button type="submit" class="search-submit"><i class="fas fa-search"></i><span class="screen-reader-text"><?php esc_html_e('Pesquisar', 'thabatta-adv'); ?></span></button>
        </div>
        
        <?php if (is_search()) : ?>
            <div class="search-filters">
                <div class="filter-group">
                    <label for="post_type"><?php esc_html_e('Tipo:', 'thabatta-adv'); ?></label>
                    <select name="post_type" id="post_type">
                        <option value="any" <?php selected(get_query_var('post_type'), 'any'); ?>><?php esc_html_e('Todos', 'thabatta-adv'); ?></option>
                        <option value="post" <?php selected(get_query_var('post_type'), 'post'); ?>><?php esc_html_e('Posts', 'thabatta-adv'); ?></option>
                        <option value="page" <?php selected(get_query_var('post_type'), 'page'); ?>><?php esc_html_e('Páginas', 'thabatta-adv'); ?></option>
                        <option value="area_atuacao" <?php selected(get_query_var('post_type'), 'area_atuacao'); ?>><?php esc_html_e('Áreas de Atuação', 'thabatta-adv'); ?></option>
                    </select>
                </div>
                
                <?php
                // Exibir filtro de categorias apenas se o tipo de post for 'post' ou 'any'
                $post_type = get_query_var('post_type');
            if (empty($post_type) || $post_type === 'any' || $post_type === 'post') :
                $categories = get_categories(array(
                    'orderby' => 'name',
                    'order'   => 'ASC',
                    'hide_empty' => true,
                ));

                if (!empty($categories)) :
                    ?>
                    <div class="filter-group">
                        <label for="category"><?php esc_html_e('Categoria:', 'thabatta-adv'); ?></label>
                        <select name="category" id="category">
                            <option value=""><?php esc_html_e('Todas as categorias', 'thabatta-adv'); ?></option>
                            <?php foreach ($categories as $category) : ?>
                                <option value="<?php echo esc_attr($category->term_id); ?>" <?php selected(get_query_var('cat'), $category->term_id); ?>>
                                    <?php echo esc_html($category->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <div class="filter-group">
                    <label for="orderby"><?php esc_html_e('Ordenar por:', 'thabatta-adv'); ?></label>
                    <select name="orderby" id="orderby">
                        <option value="date" <?php selected(get_query_var('orderby'), 'date'); ?>><?php esc_html_e('Data', 'thabatta-adv'); ?></option>
                        <option value="title" <?php selected(get_query_var('orderby'), 'title'); ?>><?php esc_html_e('Título', 'thabatta-adv'); ?></option>
                        <option value="relevance" <?php selected(get_query_var('orderby'), 'relevance'); ?>><?php esc_html_e('Relevância', 'thabatta-adv'); ?></option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="order"><?php esc_html_e('Ordem:', 'thabatta-adv'); ?></label>
                    <select name="order" id="order">
                        <option value="DESC" <?php selected(get_query_var('order'), 'DESC'); ?>><?php esc_html_e('Decrescente', 'thabatta-adv'); ?></option>
                        <option value="ASC" <?php selected(get_query_var('order'), 'ASC'); ?>><?php esc_html_e('Crescente', 'thabatta-adv'); ?></option>
                    </select>
                </div>
            </div>
        <?php endif; ?>
    </form>
    <?php
}

/**
 * Exibe o botão de WhatsApp flutuante
 */
function thabatta_whatsapp_button()
{
    // Verificar se a função de obter número de WhatsApp existe
    if (!function_exists('thabatta_get_whatsapp_url')) {
        return;
    }

    $whatsapp_url = thabatta_get_whatsapp_url();

    if (!empty($whatsapp_url)) :
        ?>
    <div class="whatsapp-button">
        <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Fale conosco pelo WhatsApp', 'thabatta-adv'); ?>">
            <i class="fab fa-whatsapp"></i>
            <span class="whatsapp-text"><?php esc_html_e('Fale conosco', 'thabatta-adv'); ?></span>
        </a>
    </div>
    <?php
    endif;
}

/**
 * Exibe as informações de contato
 */
function thabatta_contact_info()
{
    // Verificar se a função de obter informações de contato existe
    if (!function_exists('thabatta_get_contact_info')) {
        return;
    }

    $contact_info = thabatta_get_contact_info();

    if (!empty($contact_info)) :
        ?>
    <div class="contact-info">
        <?php if (!empty($contact_info['phone'])) : ?>
            <div class="contact-item">
                <i class="fas fa-phone-alt"></i>
                <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $contact_info['phone'])); ?>">
                    <?php echo esc_html($contact_info['phone']); ?>
                </a>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($contact_info['email'])) : ?>
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <a href="mailto:<?php echo esc_attr($contact_info['email']); ?>">
                    <?php echo esc_html($contact_info['email']); ?>
                </a>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($contact_info['address'])) : ?>
            <div class="contact-item">
                <i class="fas fa-map-marker-alt"></i>
                <address><?php echo esc_html($contact_info['address']); ?></address>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($contact_info['hours'])) : ?>
            <div class="contact-item">
                <i class="far fa-clock"></i>
                <span><?php echo esc_html($contact_info['hours']); ?></span>
            </div>
        <?php endif; ?>
    </div>
    <?php
    endif;
}

/**
 * Exibe as redes sociais
 */
function thabatta_social_links()
{
    // Verificar se a função de obter redes sociais existe
    if (!function_exists('thabatta_get_social_links')) {
        return;
    }

    $social_links = thabatta_get_social_links();

    if (!empty($social_links)) :
        ?>
    <div class="social-links">
        <?php foreach ($social_links as $network => $url) :
            if (empty($url)) {
                continue;
            }

            $icon_class = '';
            switch ($network) {
                case 'facebook':
                    $icon_class = 'fab fa-facebook-f';
                    $label = __('Facebook', 'thabatta-adv');
                    break;
                case 'instagram':
                    $icon_class = 'fab fa-instagram';
                    $label = __('Instagram', 'thabatta-adv');
                    break;
                case 'twitter':
                    $icon_class = 'fab fa-twitter';
                    $label = __('Twitter', 'thabatta-adv');
                    break;
                case 'linkedin':
                    $icon_class = 'fab fa-linkedin-in';
                    $label = __('LinkedIn', 'thabatta-adv');
                    break;
                case 'youtube':
                    $icon_class = 'fab fa-youtube';
                    $label = __('YouTube', 'thabatta-adv');
                    break;
                case 'tiktok':
                    $icon_class = 'fab fa-tiktok';
                    $label = __('TikTok', 'thabatta-adv');
                    break;
                default:
                    $icon_class = 'fas fa-link';
                    $label = $network;
            }
            ?>
            <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($label); ?>">
                <i class="<?php echo esc_attr($icon_class); ?>"></i>
            </a>
        <?php endforeach; ?>
    </div>
    <?php
    endif;
}

/**
 * Exibe o banner de cookies
 */
function thabatta_cookie_banner()
{
    // Verificar se o banner já foi aceito via cookie
    if (isset($_COOKIE['thabatta_cookies_accepted'])) {
        return;
    }
    ?>
    <div id="cookie-banner" class="cookie-banner">
        <div class="cookie-content">
            <p>
                <?php esc_html_e('Utilizamos cookies para melhorar sua experiência em nosso site. Ao continuar navegando, você concorda com a nossa', 'thabatta-adv'); ?>
                <a href="<?php echo esc_url(get_privacy_policy_url()); ?>"><?php esc_html_e('Política de Privacidade', 'thabatta-adv'); ?></a>.
            </p>
            <div class="cookie-buttons">
                <button id="accept-cookies" class="btn btn-primary"><?php esc_html_e('Aceitar', 'thabatta-adv'); ?></button>
                <button id="reject-cookies" class="btn btn-outline"><?php esc_html_e('Rejeitar', 'thabatta-adv'); ?></button>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var cookieBanner = document.getElementById('cookie-banner');
        var acceptButton = document.getElementById('accept-cookies');
        var rejectButton = document.getElementById('reject-cookies');
        
        function setCookie(name, value, days) {
            var expires = '';
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = '; expires=' + date.toUTCString();
            }
            document.cookie = name + '=' + (value || '') + expires + '; path=/';
        }
        
        function hideBanner() {
            cookieBanner.style.display = 'none';
        }
        
        acceptButton.addEventListener('click', function() {
            setCookie('thabatta_cookies_accepted', 'true', 365);
            hideBanner();
        });
        
        rejectButton.addEventListener('click', function() {
            setCookie('thabatta_cookies_accepted', 'false', 365);
            hideBanner();
        });
    });
    </script>
    <?php
}

/**
 * Exibe o botão "Voltar ao topo"
 */
function thabatta_back_to_top()
{
    ?>
    <a href="#" id="back-to-top" class="back-to-top" aria-label="<?php esc_attr_e('Voltar ao topo', 'thabatta-adv'); ?>">
        <i class="fas fa-arrow-up"></i>
    </a>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var backToTop = document.getElementById('back-to-top');
        
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        });
        
        backToTop.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
    </script>
    <?php
}

/**
 * Exibe a área de atuação em destaque
 */
function thabatta_featured_area_atuacao($post_id = null)
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    // Verificar se é uma área de atuação
    if (get_post_type($post_id) !== 'area_atuacao') {
        return;
    }

    // Obter campos ACF se disponíveis
    $icon = '';
    $resumo = '';

    if (function_exists('get_field')) {
        $icon = get_field('icone', $post_id);
        $resumo = get_field('resumo', $post_id);
    }

    if (empty($resumo)) {
        $resumo = get_the_excerpt($post_id);
    }
    ?>
    <div class="area-atuacao-card">
        <?php if (!empty($icon)) : ?>
            <div class="area-icon">
                <i class="<?php echo esc_attr($icon); ?>"></i>
            </div>
        <?php endif; ?>
        
        <h3 class="area-title">
            <a href="<?php echo esc_url(get_permalink($post_id)); ?>">
                <?php echo esc_html(get_the_title($post_id)); ?>
            </a>
        </h3>
        
        <?php if (!empty($resumo)) : ?>
            <div class="area-resumo">
                <?php echo wp_kses_post($resumo); ?>
            </div>
        <?php endif; ?>
        
        <a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="read-more">
            <?php esc_html_e('Saiba mais', 'thabatta-adv'); ?> <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    <?php
}

/**
 * Exibe o membro da equipe em destaque
 */
function thabatta_featured_team_member($post_id = null)
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    // Verificar se é um membro da equipe
    if (get_post_type($post_id) !== 'equipe') {
        return;
    }

    // Obter campos ACF se disponíveis
    $cargo = '';
    $oab = '';
    $areas = array();
    $redes_sociais = array();

    if (function_exists('get_field')) {
        $cargo = get_field('cargo', $post_id);
        $oab = get_field('oab', $post_id);
        $areas = get_field('areas_atuacao', $post_id);
        $redes_sociais = get_field('redes_sociais', $post_id);
    }

    // Obter termos de taxonomia se os campos ACF não estiverem disponíveis
    if (empty($cargo)) {
        $cargos = get_the_terms($post_id, 'cargo');
        if (!empty($cargos) && !is_wp_error($cargos)) {
            $cargo = $cargos[0]->name;
        }
    }
    ?>
    <div class="team-member-card">
        <?php if (has_post_thumbnail($post_id)) : ?>
            <div class="member-image">
                <a href="<?php echo esc_url(get_permalink($post_id)); ?>">
                    <?php echo get_the_post_thumbnail($post_id, 'thabatta-team'); ?>
                </a>
            </div>
        <?php endif; ?>
        
        <div class="member-info">
            <h3 class="member-name">
                <a href="<?php echo esc_url(get_permalink($post_id)); ?>">
                    <?php echo esc_html(get_the_title($post_id)); ?>
                </a>
            </h3>
            
            <?php if (!empty($cargo)) : ?>
                <div class="member-cargo"><?php echo esc_html($cargo); ?></div>
            <?php endif; ?>
            
            <?php if (!empty($oab)) : ?>
                <div class="member-oab">OAB: <?php echo esc_html($oab); ?></div>
            <?php endif; ?>
            
            <?php if (!empty($areas) && is_array($areas)) : ?>
                <div class="member-areas">
                    <span><?php esc_html_e('Áreas de Atuação:', 'thabatta-adv'); ?></span>
                    <ul>
                        <?php foreach ($areas as $area) : ?>
                            <li>
                                <a href="<?php echo esc_url(get_permalink($area->ID)); ?>">
                                    <?php echo esc_html(get_the_title($area->ID)); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($redes_sociais) && is_array($redes_sociais)) : ?>
                <div class="member-social">
                    <?php foreach ($redes_sociais as $rede => $url) :
                        if (empty($url)) {
                            continue;
                        }

                        $icon_class = '';
                        switch ($rede) {
                            case 'linkedin':
                                $icon_class = 'fab fa-linkedin-in';
                                $label = __('LinkedIn', 'thabatta-adv');
                                break;
                            case 'instagram':
                                $icon_class = 'fab fa-instagram';
                                $label = __('Instagram', 'thabatta-adv');
                                break;
                            case 'twitter':
                                $icon_class = 'fab fa-twitter';
                                $label = __('Twitter', 'thabatta-adv');
                                break;
                            case 'facebook':
                                $icon_class = 'fab fa-facebook-f';
                                $label = __('Facebook', 'thabatta-adv');
                                break;
                            default:
                                $icon_class = 'fas fa-link';
                                $label = $rede;
                        }
                        ?>
                        <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($label); ?>">
                            <i class="<?php echo esc_attr($icon_class); ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="read-more">
                <?php esc_html_e('Ver perfil completo', 'thabatta-adv'); ?> <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    <?php
}

/**
 * Exibe o depoimento em destaque
 */
function thabatta_featured_testimonial($post_id = null)
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    // Verificar se é um depoimento
    if (get_post_type($post_id) !== 'depoimento') {
        return;
    }

    // Obter campos ACF se disponíveis
    $autor = '';
    $cargo = '';
    $empresa = '';
    $avaliacao = 5;

    if (function_exists('get_field')) {
        $autor = get_field('autor', $post_id);
        $cargo = get_field('cargo', $post_id);
        $empresa = get_field('empresa', $post_id);
        $avaliacao = get_field('avaliacao', $post_id);
    }

    if (empty($autor)) {
        $autor = get_the_title($post_id);
    }
    ?>
    <div class="testimonial-card">
        <div class="testimonial-content">
            <?php if (!empty($avaliacao)) : ?>
                <div class="testimonial-rating">
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <?php if ($i <= $avaliacao) : ?>
                            <i class="fas fa-star"></i>
                        <?php else : ?>
                            <i class="far fa-star"></i>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
            
            <div class="testimonial-text">
                <?php echo wp_kses_post(get_the_content(null, false, $post_id)); ?>
            </div>
            
            <div class="testimonial-author">
                <?php if (has_post_thumbnail($post_id)) : ?>
                    <div class="author-image">
                        <?php echo get_the_post_thumbnail($post_id, 'thumbnail'); ?>
                    </div>
                <?php endif; ?>
                
                <div class="author-info">
                    <div class="author-name"><?php echo esc_html($autor); ?></div>
                    
                    <?php if (!empty($cargo) || !empty($empresa)) : ?>
                        <div class="author-position">
                            <?php
                            if (!empty($cargo) && !empty($empresa)) {
                                echo esc_html($cargo) . ', ' . esc_html($empresa);
                            } elseif (!empty($cargo)) {
                                echo esc_html($cargo);
                            } elseif (!empty($empresa)) {
                                echo esc_html($empresa);
                            }
    ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Exibe o formulário de contato
 */
function thabatta_contact_form($form_id = '')
{
    // Verificar se o Contact Form 7 está ativo
    if (!function_exists('wpcf7_contact_form')) {
        return;
    }

    // Se nenhum ID de formulário for fornecido, tentar obter do ACF
    if (empty($form_id) && function_exists('get_field')) {
        $form_id = get_field('formulario_contato', 'option');
    }

    // Se ainda estiver vazio, sair
    if (empty($form_id)) {
        return;
    }

    // Exibir o formulário
    echo do_shortcode('[contact-form-7 id="' . esc_attr($form_id) . '"]');
}

/**
 * Exibe o mapa do Google
 */
function thabatta_google_map($address = '')
{
    // Se nenhum endereço for fornecido, tentar obter do ACF
    if (empty($address) && function_exists('get_field')) {
        $address = get_field('endereco', 'option');
    }

    // Se ainda estiver vazio, sair
    if (empty($address)) {
        return;
    }

    // Obter a chave da API do Google Maps
    $api_key = '';
    if (function_exists('get_field')) {
        $api_key = get_field('google_maps_api_key', 'option');
    }

    // Preparar o endereço para a URL
    $address_url = urlencode($address);

    // Exibir o mapa
    ?>
    <div class="google-map">
        <?php if (!empty($api_key)) : ?>
            <iframe
                width="100%"
                height="450"
                style="border:0"
                loading="lazy"
                allowfullscreen
                src="https://www.google.com/maps/embed/v1/place?key=<?php echo esc_attr($api_key); ?>&q=<?php echo esc_attr($address_url); ?>">
            </iframe>
        <?php else : ?>
            <iframe
                width="100%"
                height="450"
                style="border:0"
                loading="lazy"
                allowfullscreen
                src="https://maps.google.com/maps?q=<?php echo esc_attr($address_url); ?>&output=embed">
            </iframe>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Exibe o banner de CTA (Call to Action)
 */
function thabatta_cta_banner($title = '', $text = '', $button_text = '', $button_url = '', $background_image = '')
{
    // Se os parâmetros não forem fornecidos, tentar obter do ACF
    if (function_exists('get_field') && (empty($title) || empty($text) || empty($button_text) || empty($button_url))) {
        $title = get_field('cta_titulo', 'option');
        $text = get_field('cta_texto', 'option');
        $button_text = get_field('cta_botao_texto', 'option');
        $button_url = get_field('cta_botao_url', 'option');
        $background_image = get_field('cta_imagem_fundo', 'option');
    }

    // Se ainda estiver vazio, sair
    if (empty($title) || empty($button_text) || empty($button_url)) {
        return;
    }

    // Preparar o estilo de fundo
    $style = '';
    if (!empty($background_image)) {
        if (is_numeric($background_image)) {
            $background_image = wp_get_attachment_image_url($background_image, 'full');
        }
        $style = 'style="background-image: url(' . esc_url($background_image) . ');"';
    }
    ?>
    <div class="cta-banner" <?php echo $style; ?>>
        <div class="cta-content">
            <h2 class="cta-title"><?php echo esc_html($title); ?></h2>
            
            <?php if (!empty($text)) : ?>
                <div class="cta-text"><?php echo wp_kses_post($text); ?></div>
            <?php endif; ?>
            
            <a href="<?php echo esc_url($button_url); ?>" class="cta-button btn btn-primary">
                <?php echo esc_html($button_text); ?>
            </a>
        </div>
    </div>
    <?php
}
