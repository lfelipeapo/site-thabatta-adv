<?php
/**
 * Funções de template para o tema Thabatta Advocacia
 *
 * @package Thabatta_Advocacia
 */

if (!defined('ABSPATH')) {
    exit; // Saída direta se acessado diretamente
}

/**
 * Adicionar metadados estruturados (Schema.org) para SEO
 */
function thabatta_add_structured_data()
{
    if (is_singular('post')) {
        global $post;

        $author_id = $post->post_author;
        $author_name = get_the_author_meta('display_name', $author_id);
        $author_url = get_author_posts_url($author_id);

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title(),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => array(
                '@type' => 'Person',
                'name' => $author_name,
                'url' => $author_url
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_custom_logo_url()
                )
            ),
            'mainEntityOfPage' => get_permalink(),
            'description' => get_the_excerpt()
        );

        // Adicionar imagem em destaque se existir
        if (has_post_thumbnail()) {
            $image_data = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
            if ($image_data) {
                $schema['image'] = array(
                    '@type' => 'ImageObject',
                    'url' => $image_data[0],
                    'width' => $image_data[1],
                    'height' => $image_data[2]
                );
            }
        }

        echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>';
    } elseif (is_singular('area_atuacao')) {
        global $post;

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => get_the_title(),
            'description' => get_the_excerpt(),
            'provider' => array(
                '@type' => 'LegalService',
                'name' => get_bloginfo('name'),
                'url' => home_url()
            ),
            'url' => get_permalink()
        );

        // Adicionar imagem em destaque se existir
        if (has_post_thumbnail()) {
            $image_data = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
            if ($image_data) {
                $schema['image'] = $image_data[0];
            }
        }

        echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>';
    } elseif (is_front_page()) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'LegalService',
            'name' => get_bloginfo('name'),
            'description' => get_bloginfo('description'),
            'url' => home_url(),
            'logo' => get_custom_logo_url(),
            'contactPoint' => array(
                '@type' => 'ContactPoint',
                'telephone' => get_field('telefone', 'option'),
                'email' => get_field('email', 'option'),
                'contactType' => 'customer service'
            )
        );

        // Adicionar endereço se disponível
        if (function_exists('get_field') && get_field('endereco', 'option')) {
            $schema['address'] = array(
                '@type' => 'PostalAddress',
                'streetAddress' => get_field('endereco', 'option'),
                'addressLocality' => get_field('cidade_estado_cep', 'option')
            );
        }

        echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>';
    }
}
add_action('wp_head', 'thabatta_add_structured_data');

/**
 * Obter URL do logo personalizado
 */
function get_custom_logo_url()
{
    $custom_logo_id = get_theme_mod('custom_logo');
    $logo_url = '';

    if ($custom_logo_id) {
        $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
    }

    return $logo_url;
}

/**
 * Adicionar meta tags para SEO
 */
function thabatta_add_meta_tags()
{
    // Meta description
    if (is_singular()) {
        global $post;
        $description = '';

        // Usar o resumo se disponível
        if (has_excerpt()) {
            $description = get_the_excerpt();
        } else {
            // Caso contrário, usar os primeiros 160 caracteres do conteúdo
            $description = wp_trim_words(strip_shortcodes(strip_tags($post->post_content)), 20, '...');
        }

        if ($description) {
            echo '<meta name="description" content="' . esc_attr($description) . '" />' . "\n";
        }
    } elseif (is_home() || is_front_page()) {
        echo '<meta name="description" content="' . esc_attr(get_bloginfo('description')) . '" />' . "\n";
    } elseif (is_category() || is_tag() || is_tax()) {
        $term_description = term_description();
        if ($term_description) {
            echo '<meta name="description" content="' . esc_attr(wp_trim_words(strip_tags($term_description), 20, '...')) . '" />' . "\n";
        }
    }

    // Meta robots
    if (is_singular()) {
        // Verificar se o post deve ser indexado
        $robots = '';

        if (is_singular('post') || is_singular('page') || is_singular('area_atuacao')) {
            $robots = 'index, follow';
        } else {
            $robots = 'noindex, follow';
        }

        echo '<meta name="robots" content="' . esc_attr($robots) . '" />' . "\n";
    } elseif (is_search() || is_404()) {
        echo '<meta name="robots" content="noindex, follow" />' . "\n";
    }

    // Open Graph tags
    if (is_singular()) {
        echo '<meta property="og:type" content="article" />' . "\n";
        echo '<meta property="og:title" content="' . esc_attr(get_the_title()) . '" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '" />' . "\n";

        if (has_post_thumbnail()) {
            $image_data = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
            if ($image_data) {
                echo '<meta property="og:image" content="' . esc_url($image_data[0]) . '" />' . "\n";
                echo '<meta property="og:image:width" content="' . esc_attr(strval($image_data[1])) . '" />' . "\n";
                echo '<meta property="og:image:height" content="' . esc_attr(strval($image_data[2])) . '" />' . "\n";
            }
        }

        if (has_excerpt()) {
            echo '<meta property="og:description" content="' . esc_attr(get_the_excerpt()) . '" />' . "\n";
        }
    } elseif (is_front_page()) {
        echo '<meta property="og:type" content="website" />' . "\n";
        echo '<meta property="og:title" content="' . esc_attr(get_bloginfo('name')) . '" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url(home_url()) . '" />' . "\n";
        echo '<meta property="og:description" content="' . esc_attr(get_bloginfo('description')) . '" />' . "\n";

        $custom_logo_id = get_theme_mod('custom_logo');
        if ($custom_logo_id) {
            $image_data = wp_get_attachment_image_src($custom_logo_id, 'large');
            if ($image_data) {
                echo '<meta property="og:image" content="' . esc_url($image_data[0]) . '" />' . "\n";
            }
        }
    }

    // Twitter Card tags
    if (is_singular()) {
        echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr(get_the_title()) . '" />' . "\n";

        if (has_excerpt()) {
            echo '<meta name="twitter:description" content="' . esc_attr(get_the_excerpt()) . '" />' . "\n";
        }

        if (has_post_thumbnail()) {
            $image_data = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
            if ($image_data) {
                echo '<meta name="twitter:image" content="' . esc_url($image_data[0]) . '" />' . "\n";
            }
        }
    }
}
add_action('wp_head', 'thabatta_add_meta_tags', 1);

/**
 * Adicionar atributos de imagem para lazy loading
 */
function thabatta_add_lazyload_to_images($content)
{
    if (is_admin() || is_feed()) {
        return $content;
    }

    // Não aplicar lazy loading em AMP
    if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
        return $content;
    }

    // Substituir atributos de imagem para lazy loading
    $content = preg_replace('/<img(.*?)src="(.*?)"(.*?)>/i', '<img$1src="data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 1 1\'%3E%3C/svg%3E" data-src="$2"$3 loading="lazy">', $content);

    // Adicionar classe para lazy loading
    $content = preg_replace('/<img(.*?)class="(.*?)"(.*?)>/i', '<img$1class="$2 lazyload"$3>', $content);
    $content = preg_replace('/<img((?!class=).*?)>/i', '<img$1 class="lazyload">', $content);

    return $content;
}
add_filter('the_content', 'thabatta_add_lazyload_to_images', 99);
add_filter('post_thumbnail_html', 'thabatta_add_lazyload_to_images', 99);
add_filter('widget_text_content', 'thabatta_add_lazyload_to_images', 99);

/**
 * Adicionar script de lazy loading
 */
function thabatta_add_lazyload_script()
{
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var lazyImages = [].slice.call(document.querySelectorAll('img.lazyload'));
        
        if ('IntersectionObserver' in window) {
            let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        let lazyImage = entry.target;
                        if (lazyImage.dataset.src) {
                            lazyImage.src = lazyImage.dataset.src;
                            lazyImage.removeAttribute('data-src');
                        }
                        lazyImage.classList.remove('lazyload');
                        lazyImageObserver.unobserve(lazyImage);
                    }
                });
            });
            
            lazyImages.forEach(function(lazyImage) {
                lazyImageObserver.observe(lazyImage);
            });
        } else {
            // Fallback para navegadores que não suportam IntersectionObserver
            let active = false;
            
            const lazyLoad = function() {
                if (active === false) {
                    active = true;
                    
                    setTimeout(function() {
                        lazyImages.forEach(function(lazyImage) {
                            if ((lazyImage.getBoundingClientRect().top <= window.innerHeight && lazyImage.getBoundingClientRect().bottom >= 0) && getComputedStyle(lazyImage).display !== "none") {
                                if (lazyImage.dataset.src) {
                                    lazyImage.src = lazyImage.dataset.src;
                                    lazyImage.removeAttribute('data-src');
                                }
                                lazyImage.classList.remove('lazyload');
                                
                                lazyImages = lazyImages.filter(function(image) {
                                    return image !== lazyImage;
                                });
                                
                                if (lazyImages.length === 0) {
                                    document.removeEventListener('scroll', lazyLoad);
                                    window.removeEventListener('resize', lazyLoad);
                                    window.removeEventListener('orientationchange', lazyLoad);
                                }
                            }
                        });
                        
                        active = false;
                    }, 200);
                }
            };
            
            document.addEventListener('scroll', lazyLoad);
            window.addEventListener('resize', lazyLoad);
            window.addEventListener('orientationchange', lazyLoad);
            lazyLoad();
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'thabatta_add_lazyload_script');

/**
 * Adicionar preconnect para recursos externos
 */
function thabatta_add_preconnect()
{
    echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>' . "\n";

    // Adicionar preconnect para Google Analytics se estiver ativo
    if (function_exists('get_field') && get_field('google_analytics_id', 'option')) {
        echo '<link rel="preconnect" href="https://www.google-analytics.com" crossorigin>' . "\n";
    }
}
add_action('wp_head', 'thabatta_add_preconnect', 1);

/**
 * Adicionar Google Analytics
 */
function thabatta_add_google_analytics()
{
    if (function_exists('get_field') && get_field('google_analytics_id', 'option')) {
        $ga_id = get_field('google_analytics_id', 'option');
        ?>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($ga_id); ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?php echo esc_attr($ga_id); ?>', { 'anonymize_ip': true });
        </script>
        <?php
    }
}
add_action('wp_head', 'thabatta_add_google_analytics');

/**
 * Adicionar breadcrumbs
 */
function thabatta_breadcrumbs()
{
    // Não exibir em páginas de erro ou na página inicial
    if (is_404() || is_front_page()) {
        return;
    }

    $home_text = 'Início';
    $separator = '<i class="fas fa-chevron-right"></i>';

    echo '<div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">';
    echo '<span property="itemListElement" typeof="ListItem">';
    echo '<a property="item" typeof="WebPage" href="' . esc_url(home_url()) . '">';
    echo '<span property="name">' . esc_html($home_text) . '</span>';
    echo '</a>';
    echo '<meta property="position" content="1">';
    echo '</span>';

    if (is_category() || is_single() || is_tag() || is_author() || is_day() || is_month() || is_year()) {
        echo $separator;

        if (is_category()) {
            $cat = get_category(get_query_var('cat'), false);
            echo '<span property="itemListElement" typeof="ListItem">';
            echo '<span property="name">' . esc_html($cat->name) . '</span>';
            echo '<meta property="position" content="2">';
            echo '</span>';
        } elseif (is_tag()) {
            $tag = get_term_by('slug', get_query_var('tag'), 'post_tag');
            echo '<span property="itemListElement" typeof="ListItem">';
            echo '<span property="name">' . esc_html($tag->name) . '</span>';
            echo '<meta property="position" content="2">';
            echo '</span>';
        } elseif (is_author()) {
            echo '<span property="itemListElement" typeof="ListItem">';
            echo '<span property="name">' . esc_html(get_the_author()) . '</span>';
            echo '<meta property="position" content="2">';
            echo '</span>';
        } elseif (is_day()) {
            echo '<span property="itemListElement" typeof="ListItem">';
            echo '<span property="name">' . esc_html(get_the_date()) . '</span>';
            echo '<meta property="position" content="2">';
            echo '</span>';
        } elseif (is_month()) {
            echo '<span property="itemListElement" typeof="ListItem">';
            echo '<span property="name">' . esc_html(get_the_date('F Y')) . '</span>';
            echo '<meta property="position" content="2">';
            echo '</span>';
        } elseif (is_year()) {
            echo '<span property="itemListElement" typeof="ListItem">';
            echo '<span property="name">' . esc_html(get_the_date('Y')) . '</span>';
            echo '<meta property="position" content="2">';
            echo '</span>';
        } elseif (is_single()) {
            if (get_post_type() === 'post') {
                $categories = get_the_category();
                if ($categories) {
                    $cat = $categories[0];
                    echo '<span property="itemListElement" typeof="ListItem">';
                    echo '<a property="item" typeof="WebPage" href="' . esc_url(get_category_link($cat->term_id)) . '">';
                    echo '<span property="name">' . esc_html($cat->name) . '</span>';
                    echo '</a>';
                    echo '<meta property="position" content="2">';
                    echo '</span>';
                    echo $separator;
                }
            } elseif (get_post_type() === 'area_atuacao') {
                echo '<span property="itemListElement" typeof="ListItem">';
                echo '<a property="item" typeof="WebPage" href="' . esc_url(get_post_type_archive_link('area_atuacao')) . '">';
                echo '<span property="name">Áreas de Atuação</span>';
                echo '</a>';
                echo '<meta property="position" content="2">';
                echo '</span>';
                echo $separator;
            }

            echo '<span property="itemListElement" typeof="ListItem">';
            echo '<span property="name">' . esc_html(get_the_title()) . '</span>';
            echo '<meta property="position" content="3">';
            echo '</span>';
        }
    } elseif (is_page()) {
        echo $separator;

        $parent_id = wp_get_post_parent_id(get_the_ID());
        if ($parent_id) {
            echo '<span property="itemListElement" typeof="ListItem">';
            echo '<a property="item" typeof="WebPage" href="' . esc_url(get_permalink($parent_id)) . '">';
            echo '<span property="name">' . esc_html(get_the_title($parent_id)) . '</span>';
            echo '</a>';
            echo '<meta property="position" content="2">';
            echo '</span>';
            echo $separator;

            echo '<span property="itemListElement" typeof="ListItem">';
            echo '<span property="name">' . esc_html(get_the_title()) . '</span>';
            echo '<meta property="position" content="3">';
            echo '</span>';
        } else {
            echo '<span property="itemListElement" typeof="ListItem">';
            echo '<span property="name">' . esc_html(get_the_title()) . '</span>';
            echo '<meta property="position" content="2">';
            echo '</span>';
        }
    } elseif (is_search()) {
        echo $separator;
        echo '<span property="itemListElement" typeof="ListItem">';
        echo '<span property="name">Resultados da pesquisa por "' . esc_html(get_search_query()) . '"</span>';
        echo '<meta property="position" content="2">';
        echo '</span>';
    } elseif (is_post_type_archive()) {
        echo $separator;
        echo '<span property="itemListElement" typeof="ListItem">';
        echo '<span property="name">' . esc_html(post_type_archive_title('', false)) . '</span>';
        echo '<meta property="position" content="2">';
        echo '</span>';
    }

    echo '</div>';
}

/**
 * Personalizar o formulário de pesquisa
 */
function thabatta_custom_search_form($form)
{
    $form = '<form role="search" method="get" class="search-form" action="' . esc_url(home_url('/')) . '">
        <div class="input-group">
            <input type="search" class="search-field form-control" placeholder="' . esc_attr_x('Pesquisar...', 'placeholder', 'thabatta-adv') . '" value="' . get_search_query() . '" name="s" />
            <div class="input-group-append">
                <button type="submit" class="search-submit btn btn-primary"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </form>';

    return $form;
}
add_filter('get_search_form', 'thabatta_custom_search_form');

/**
 * Adicionar filtros de pesquisa avançada
 */
function thabatta_advanced_search_query($query)
{
    if ($query->is_search() && !is_admin()) {
        // Verificar se há filtros de categoria
        if (isset($_GET['category']) && !empty($_GET['category'])) {
            $category = sanitize_text_field($_GET['category']);
            $query->set('category_name', $category);
        }

        // Verificar se há filtros de tag
        if (isset($_GET['tag']) && !empty($_GET['tag'])) {
            $tag = sanitize_text_field($_GET['tag']);
            $query->set('tag', $tag);
        }

        // Verificar se há filtros de tipo de post
        if (isset($_GET['post_type']) && !empty($_GET['post_type'])) {
            $post_type = sanitize_text_field($_GET['post_type']);
            $query->set('post_type', $post_type);
        } else {
            // Por padrão, pesquisar em posts, páginas e áreas de atuação
            $query->set('post_type', array('post', 'page', 'area_atuacao'));
        }
    }

    return $query;
}
add_filter('pre_get_posts', 'thabatta_advanced_search_query');

/**
 * Exibir formulário de pesquisa avançada
 */
function thabatta_advanced_search_form()
{
    $categories = get_categories(array(
        'orderby' => 'name',
        'order' => 'ASC',
        'hide_empty' => true
    ));

    $tags = get_tags(array(
        'orderby' => 'name',
        'order' => 'ASC',
        'hide_empty' => true
    ));

    $current_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
    $current_tag = isset($_GET['tag']) ? sanitize_text_field($_GET['tag']) : '';
    $current_post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : '';
    $search_query = get_search_query();

    ?>
    <form role="search" method="get" class="advanced-search-form" action="<?php echo esc_url(home_url('/')); ?>">
        <div class="form-group">
            <label for="s"><?php _e('Pesquisar por:', 'thabatta-adv'); ?></label>
            <input type="search" id="s" name="s" class="form-control" placeholder="<?php echo esc_attr_x('Digite sua pesquisa...', 'placeholder', 'thabatta-adv'); ?>" value="<?php echo esc_attr($search_query); ?>" required />
        </div>
        
        <div class="form-group">
            <label for="category"><?php _e('Categoria:', 'thabatta-adv'); ?></label>
            <select name="category" id="category" class="form-control">
                <option value=""><?php _e('Todas as categorias', 'thabatta-adv'); ?></option>
                <?php foreach ($categories as $category) : ?>
                    <option value="<?php echo esc_attr($category->slug); ?>" <?php selected($current_category, $category->slug); ?>>
                        <?php echo esc_html($category->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="tag"><?php _e('Tag:', 'thabatta-adv'); ?></label>
            <select name="tag" id="tag" class="form-control">
                <option value=""><?php _e('Todas as tags', 'thabatta-adv'); ?></option>
                <?php foreach ($tags as $tag) : ?>
                    <option value="<?php echo esc_attr($tag->slug); ?>" <?php selected($current_tag, $tag->slug); ?>>
                        <?php echo esc_html($tag->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="post_type"><?php _e('Tipo de conteúdo:', 'thabatta-adv'); ?></label>
            <select name="post_type" id="post_type" class="form-control">
                <option value=""><?php _e('Todos os tipos', 'thabatta-adv'); ?></option>
                <option value="post" <?php selected($current_post_type, 'post'); ?>><?php _e('Artigos', 'thabatta-adv'); ?></option>
                <option value="page" <?php selected($current_post_type, 'page'); ?>><?php _e('Páginas', 'thabatta-adv'); ?></option>
                <option value="area_atuacao" <?php selected($current_post_type, 'area_atuacao'); ?>><?php _e('Áreas de Atuação', 'thabatta-adv'); ?></option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary"><?php _e('Pesquisar', 'thabatta-adv'); ?></button>
    </form>
    <?php
}

/**
 * Exibir posts relacionados
 */
function thabatta_display_related_posts($post_id = null, $title = 'Posts Relacionados', $count = 3)
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    // Obter posts relacionados
    $related_posts = thabatta_get_related_posts($post_id, $count);

    if ($related_posts) {
        echo '<div class="related-posts">';
        echo '<h3>' . esc_html($title) . '</h3>';
        echo '<div class="row">';

        foreach ($related_posts as $related_post) {
            echo '<div class="col-md-4">';
            echo '<div class="card mb-4">';

            if (has_post_thumbnail($related_post->ID)) {
                echo '<a href="' . esc_url(get_permalink($related_post->ID)) . '">';
                echo get_the_post_thumbnail($related_post->ID, 'thabatta-card', array('class' => 'card-img-top'));
                echo '</a>';
            }

            echo '<div class="card-body">';
            echo '<h5 class="card-title"><a href="' . esc_url(get_permalink($related_post->ID)) . '">' . esc_html(get_the_title($related_post->ID)) . '</a></h5>';

            // Exibir resumo
            $excerpt = has_excerpt($related_post->ID) ? get_the_excerpt($related_post->ID) : wp_trim_words(strip_shortcodes(strip_tags($related_post->post_content)), 15, '...');
            echo '<p class="card-text">' . esc_html($excerpt) . '</p>';

            echo '<a href="' . esc_url(get_permalink($related_post->ID)) . '" class="btn btn-outline-primary btn-sm">Leia mais</a>';
            echo '</div>'; // .card-body
            echo '</div>'; // .card
            echo '</div>'; // .col-md-4
        }

        echo '</div>'; // .row
        echo '</div>'; // .related-posts
    }
}

/**
 * Exibir redes sociais
 */
function thabatta_display_social_media($layout = 'horizontal', $show_labels = false)
{
    if (!function_exists('get_field')) {
        return;
    }

    $social_networks = array(
        'facebook' => array(
            'field' => 'facebook_url',
            'icon' => 'fab fa-facebook-f',
            'label' => 'Facebook'
        ),
        'instagram' => array(
            'field' => 'instagram_url',
            'icon' => 'fab fa-instagram',
            'label' => 'Instagram'
        ),
        'linkedin' => array(
            'field' => 'linkedin_url',
            'icon' => 'fab fa-linkedin-in',
            'label' => 'LinkedIn'
        ),
        'twitter' => array(
            'field' => 'twitter_url',
            'icon' => 'fab fa-twitter',
            'label' => 'Twitter'
        ),
        'youtube' => array(
            'field' => 'youtube_url',
            'icon' => 'fab fa-youtube',
            'label' => 'YouTube'
        ),
        'whatsapp' => array(
            'field' => 'whatsapp_number',
            'icon' => 'fab fa-whatsapp',
            'label' => 'WhatsApp'
        )
    );

    $has_social = false;
    foreach ($social_networks as $network) {
        if (get_field($network['field'], 'option')) {
            $has_social = true;
            break;
        }
    }

    if (!$has_social) {
        return;
    }

    $class = ($layout === 'vertical') ? 'social-media-vertical' : 'social-media-horizontal';

    echo '<div class="social-media ' . esc_attr($class) . '">';

    foreach ($social_networks as $key => $network) {
        $url = get_field($network['field'], 'option');
        if ($url) {
            // Tratar WhatsApp especialmente
            if ($key === 'whatsapp') {
                $whatsapp_number = preg_replace('/[^0-9]/', '', $url);
                $url = 'https://wa.me/' . $whatsapp_number;
            }

            echo '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer" class="social-icon ' . esc_attr($key) . '" aria-label="' . esc_attr($network['label']) . '">';
            echo '<i class="' . esc_attr($network['icon']) . '"></i>';

            if ($show_labels) {
                echo '<span class="social-label">' . esc_html($network['label']) . '</span>';
            }

            echo '</a>';
        }
    }

    echo '</div>';
}

/**
 * Exibir informações de contato
 */
function thabatta_display_contact_info($show_icons = true)
{
    if (!function_exists('get_field')) {
        return;
    }

    $telefone = get_field('telefone', 'option');
    $email = get_field('email', 'option');
    $endereco = get_field('endereco', 'option');
    $cidade_estado_cep = get_field('cidade_estado_cep', 'option');

    if (!$telefone && !$email && !$endereco) {
        return;
    }

    echo '<div class="contact-info">';

    if ($telefone) {
        echo '<div class="contact-item phone">';
        if ($show_icons) {
            echo '<i class="fas fa-phone-alt"></i> ';
        }
        echo '<a href="tel:' . esc_attr(preg_replace('/[^0-9+]/', '', $telefone)) . '">' . esc_html($telefone) . '</a>';
        echo '</div>';
    }

    if ($email) {
        echo '<div class="contact-item email">';
        if ($show_icons) {
            echo '<i class="fas fa-envelope"></i> ';
        }
        echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
        echo '</div>';
    }

    if ($endereco) {
        echo '<div class="contact-item address">';
        if ($show_icons) {
            echo '<i class="fas fa-map-marker-alt"></i> ';
        }
        echo '<address>' . esc_html($endereco);

        if ($cidade_estado_cep) {
            echo '<br>' . esc_html($cidade_estado_cep);
        }

        echo '</address>';
        echo '</div>';
    }

    echo '</div>';
}

/**
 * Exibir botão de WhatsApp flutuante
 */
function thabatta_display_whatsapp_button()
{
    if (!function_exists('get_field')) {
        return;
    }

    $whatsapp = get_field('whatsapp_number', 'option');
    if (!$whatsapp) {
        return;
    }

    $whatsapp_number = preg_replace('/[^0-9]/', '', $whatsapp);
    $whatsapp_message = get_field('whatsapp_message', 'option');

    if (!$whatsapp_message) {
        $whatsapp_message = 'Olá! Gostaria de mais informações sobre seus serviços.';
    }

    $whatsapp_url = 'https://wa.me/' . $whatsapp_number . '?text=' . urlencode($whatsapp_message);

    echo '<a href="' . esc_url($whatsapp_url) . '" class="whatsapp-button" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">';
    echo '<i class="fab fa-whatsapp"></i>';
    echo '</a>';
}

/**
 * Obter posts por categoria
 */
function thabatta_get_posts_by_category($category_slug, $count = 3, $exclude_ids = array())
{
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $count,
        'category_name' => $category_slug,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    );

    if (!empty($exclude_ids)) {
        $args['post__not_in'] = $exclude_ids;
    }

    return get_posts($args);
}

/**
 * Obter áreas de atuação
 */
function thabatta_get_areas_atuacao($count = -1, $orderby = 'title', $order = 'ASC')
{
    $args = array(
        'post_type' => 'area_atuacao',
        'posts_per_page' => $count,
        'post_status' => 'publish',
        'orderby' => $orderby,
        'order' => $order
    );

    return get_posts($args);
}

/**
 * Obter membros da equipe
 */
function thabatta_get_team_members($count = -1, $orderby = 'menu_order', $order = 'ASC')
{
    $args = array(
        'post_type' => 'equipe',
        'posts_per_page' => $count,
        'post_status' => 'publish',
        'orderby' => $orderby,
        'order' => $order
    );

    return get_posts($args);
}

/**
 * Obter depoimentos
 */
function thabatta_get_testimonials($count = 3, $orderby = 'rand', $order = 'DESC')
{
    $args = array(
        'post_type' => 'depoimento',
        'posts_per_page' => $count,
        'post_status' => 'publish',
        'orderby' => $orderby,
        'order' => $order
    );

    return get_posts($args);
}

/**
 * Exibir depoimentos
 */
function thabatta_display_testimonials($count = 3)
{
    $testimonials = thabatta_get_testimonials($count);

    if (!$testimonials) {
        return;
    }

    echo '<div class="testimonials-slider">';

    foreach ($testimonials as $testimonial) {
        $client_name = get_field('client_name', $testimonial->ID);
        $client_position = get_field('client_position', $testimonial->ID);

        echo '<div class="testimonial-item">';
        echo '<div class="testimonial-content">';
        echo '<i class="fas fa-quote-left"></i>';
        echo '<p>' . esc_html(get_the_excerpt($testimonial->ID)) . '</p>';
        echo '</div>';

        echo '<div class="testimonial-author">';
        if (has_post_thumbnail($testimonial->ID)) {
            echo get_the_post_thumbnail($testimonial->ID, 'thumbnail', array('class' => 'testimonial-avatar'));
        }

        echo '<div class="testimonial-info">';
        if ($client_name) {
            echo '<h4 class="client-name">' . esc_html($client_name) . '</h4>';
        } else {
            echo '<h4 class="client-name">' . esc_html(get_the_title($testimonial->ID)) . '</h4>';
        }

        if ($client_position) {
            echo '<p class="client-position">' . esc_html($client_position) . '</p>';
        }
        echo '</div>'; // .testimonial-info

        echo '</div>'; // .testimonial-author
        echo '</div>'; // .testimonial-item
    }

    echo '</div>'; // .testimonials-slider
}

/**
 * Exibir áreas de atuação em grid
 */
function thabatta_display_areas_grid($count = -1)
{
    $areas = thabatta_get_areas_atuacao($count);

    if (!$areas) {
        return;
    }

    echo '<div class="areas-grid row">';

    foreach ($areas as $area) {
        echo '<div class="col-md-4 mb-4">';
        echo '<div class="area-card">';

        if (has_post_thumbnail($area->ID)) {
            echo '<a href="' . esc_url(get_permalink($area->ID)) . '" class="area-image">';
            echo get_the_post_thumbnail($area->ID, 'thabatta-card', array('class' => 'img-fluid'));
            echo '</a>';
        }

        echo '<div class="area-content">';
        echo '<h3 class="area-title"><a href="' . esc_url(get_permalink($area->ID)) . '">' . esc_html(get_the_title($area->ID)) . '</a></h3>';

        $excerpt = has_excerpt($area->ID) ? get_the_excerpt($area->ID) : wp_trim_words(strip_shortcodes(strip_tags($area->post_content)), 15, '...');
        echo '<p class="area-excerpt">' . esc_html($excerpt) . '</p>';

        echo '<a href="' . esc_url(get_permalink($area->ID)) . '" class="btn btn-primary btn-sm">Saiba mais</a>';
        echo '</div>'; // .area-content

        echo '</div>'; // .area-card
        echo '</div>'; // .col-md-4
    }

    echo '</div>'; // .areas-grid
}

/**
 * Exibir membros da equipe em grid
 */
function thabatta_display_team_grid($count = -1)
{
    $members = thabatta_get_team_members($count);

    if (!$members) {
        return;
    }

    echo '<div class="team-grid row">';

    foreach ($members as $member) {
        $cargo = get_field('cargo', $member->ID);
        $email = get_field('email', $member->ID);
        $linkedin = get_field('linkedin', $member->ID);

        echo '<div class="col-md-4 mb-4">';
        echo '<div class="team-card">';

        if (has_post_thumbnail($member->ID)) {
            echo '<div class="team-image">';
            echo get_the_post_thumbnail($member->ID, 'thabatta-card', array('class' => 'img-fluid'));
            echo '</div>';
        }

        echo '<div class="team-content">';
        echo '<h3 class="team-name">' . esc_html(get_the_title($member->ID)) . '</h3>';

        if ($cargo) {
            echo '<p class="team-position">' . esc_html($cargo) . '</p>';
        }

        $excerpt = has_excerpt($member->ID) ? get_the_excerpt($member->ID) : wp_trim_words(strip_shortcodes(strip_tags($member->post_content)), 15, '...');
        echo '<p class="team-bio">' . esc_html($excerpt) . '</p>';

        echo '<div class="team-contact">';
        if ($email) {
            echo '<a href="mailto:' . esc_attr($email) . '" class="team-email"><i class="fas fa-envelope"></i></a>';
        }

        if ($linkedin) {
            echo '<a href="' . esc_url($linkedin) . '" target="_blank" rel="noopener noreferrer" class="team-linkedin"><i class="fab fa-linkedin-in"></i></a>';
        }
        echo '</div>'; // .team-contact

        echo '</div>'; // .team-content
        echo '</div>'; // .team-card
        echo '</div>'; // .col-md-4
    }

    echo '</div>'; // .team-grid
}

/**
 * Exibir formulário de contato
 */
function thabatta_display_contact_form($form_id = null)
{
    if (!function_exists('get_field')) {
        return;
    }

    if (!$form_id) {
        $form_id = get_field('contact_form_id', 'option');
    }

    if (!$form_id) {
        return;
    }

    if (function_exists('wpcf7_contact_form')) {
        $form = wpcf7_contact_form($form_id);
        if ($form) {
            echo '<div class="contact-form-container">';
            echo do_shortcode('[contact-form-7 id="' . esc_attr($form_id) . '"]');
            echo '</div>';
        }
    }
}

/**
 * Obter posts relacionados
 *
 * @param int $post_id ID do post.
 * @param int $count Número de posts para retornar.
 * @return array|bool Array de posts relacionados ou false se não encontrar.
 */
function thabatta_get_related_posts($post_id, $count = 3) {
    $post = get_post($post_id);
    
    if (!$post) {
        return false;
    }
    
    // Primeiro, verificar se existem posts relacionados selecionados manualmente
    $manual_related = get_post_meta($post_id, '_thabatta_related_posts', true);
    
    if (is_array($manual_related) && !empty($manual_related)) {
        $related_args = array(
            'post__in'       => $manual_related,
            'post_type'      => array('post', 'page', 'area_atuacao', 'equipe'),
            'posts_per_page' => $count,
            'post_status'    => 'publish',
            'orderby'        => 'post__in'
        );
        
        $related_query = new WP_Query($related_args);
        
        if ($related_query->have_posts()) {
            return $related_query->posts;
        }
    }
    
    // Se não houver posts relacionados manualmente ou se a consulta não retornar resultados,
    // buscar por taxonomia comum (categorias, tags)
    
    // Obter termos para o post atual
    $taxonomies = get_object_taxonomies($post->post_type);
    $term_ids = array();
    
    if (!empty($taxonomies)) {
        foreach ($taxonomies as $taxonomy) {
            $terms = get_the_terms($post_id, $taxonomy);
            
            if (is_array($terms) && !empty($terms)) {
                foreach ($terms as $term) {
                    $term_ids[] = $term->term_id;
                }
            }
        }
    }
    
    // Se houver termos, buscar posts relacionados
    if (!empty($term_ids)) {
        $related_args = array(
            'post_type'      => $post->post_type,
            'posts_per_page' => $count,
            'post_status'    => 'publish',
            'post__not_in'   => array($post_id),
            'tax_query'      => array(
                'relation' => 'OR'
            )
        );
        
        foreach ($taxonomies as $taxonomy) {
            $terms = get_the_terms($post_id, $taxonomy);
            
            if (is_array($terms) && !empty($terms)) {
                $term_ids = wp_list_pluck($terms, 'term_id');
                
                $related_args['tax_query'][] = array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $term_ids
                );
            }
        }
        
        $related_query = new WP_Query($related_args);
        
        if ($related_query->have_posts()) {
            return $related_query->posts;
        }
    }
    
    // Se ainda não encontrou, buscar posts recentes do mesmo tipo
    $fallback_args = array(
        'post_type'      => $post->post_type,
        'posts_per_page' => $count,
        'post_status'    => 'publish',
        'post__not_in'   => array($post_id),
        'orderby'        => 'date',
        'order'          => 'DESC'
    );
    
    $fallback_query = new WP_Query($fallback_args);
    
    if ($fallback_query->have_posts()) {
        return $fallback_query->posts;
    }
    
    return false;
}
