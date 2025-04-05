<?php
/**
 * Template para a página inicial
 *
 * @package Thabatta_Advocacia
 */

get_header();
?>

<main id="primary" class="site-main front-page">
    <!-- Hero Section -->
    <section class="hero-section" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/hero-bg.jpg');">
        <div class="container">
            <div class="hero-content">
                <h1><?php echo esc_html(get_theme_mod('hero_title', 'Thabatta Apolinário Advocacia')); ?></h1>
                <p><?php echo esc_html(get_theme_mod('hero_description', 'Advocacia especializada em Direito Civil, Empresarial e Trabalhista. Atendimento personalizado e soluções jurídicas eficientes.')); ?></p>
                <div class="hero-buttons">
                    <a href="<?php echo esc_url(get_theme_mod('hero_button_url', '/contato')); ?>" class="btn btn-primary"><?php echo esc_html(get_theme_mod('hero_button_text', 'Fale Conosco')); ?></a>
                    <a href="#" class="btn btn-outline-primary open-consultation-form"><?php esc_html_e('Agendar Consulta', 'thabatta-adv'); ?></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Serviços Section -->
    <section class="services-section">
        <div class="container">
            <div class="section-title">
                <h2><?php echo esc_html(get_theme_mod('services_title', 'Áreas de Atuação')); ?></h2>
                <p><?php echo esc_html(get_theme_mod('services_description', 'Conheça as principais áreas em que atuamos com excelência e compromisso.')); ?></p>
            </div>

            <div class="services-grid">
                <?php
                // Consulta para áreas de atuação
                $args = array(
                    'post_type'      => 'area_atuacao',
                    'posts_per_page' => 6,
                    'orderby'        => 'menu_order',
                    'order'          => 'ASC',
                );
                
                $areas_query = new WP_Query($args);
                
                if ($areas_query->have_posts()) :
                    while ($areas_query->have_posts()) : $areas_query->the_post();
                ?>
                    <div class="service-item">
                        <?php if (function_exists('get_field') && get_field('icone')) : ?>
                            <i class="<?php echo esc_attr(get_field('icone')); ?>"></i>
                        <?php else : ?>
                            <i class="fas fa-balance-scale"></i>
                        <?php endif; ?>
                        
                        <h3><?php the_title(); ?></h3>
                        <div class="excerpt"><?php the_excerpt(); ?></div>
                        <a href="<?php the_permalink(); ?>" class="read-more">
                            <?php esc_html_e('Saiba mais', 'thabatta-adv'); ?> 
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                ?>
                    <div class="service-item">
                        <i class="fas fa-balance-scale"></i>
                        <h3>Direito Civil</h3>
                        <div class="excerpt"><p>Assessoria jurídica completa em questões de direito civil, incluindo contratos, responsabilidade civil e direito do consumidor.</p></div>
                        <a href="#" class="read-more">
                            <?php esc_html_e('Saiba mais', 'thabatta-adv'); ?> 
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="service-item">
                        <i class="fas fa-briefcase"></i>
                        <h3>Direito Empresarial</h3>
                        <div class="excerpt"><p>Consultoria especializada para empresas, abrangendo contratos comerciais, societário e proteção patrimonial.</p></div>
                        <a href="#" class="read-more">
                            <?php esc_html_e('Saiba mais', 'thabatta-adv'); ?> 
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="service-item">
                        <i class="fas fa-gavel"></i>
                        <h3>Direito Trabalhista</h3>
                        <div class="excerpt"><p>Atendimento tanto para empregadores quanto para empregados, com foco em soluções eficientes e justas.</p></div>
                        <a href="#" class="read-more">
                            <?php esc_html_e('Saiba mais', 'thabatta-adv'); ?> 
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="section-cta">
                <a href="<?php echo esc_url(get_post_type_archive_link('area_atuacao')); ?>" class="btn btn-secondary">Ver todas as áreas de atuação</a>
            </div>
        </div>
    </section>

    <!-- Sobre Section -->
    <section class="about-section">
        <div class="container">
            <div class="about-content">
                <div class="about-image">
                    <?php 
                    $about_image = get_theme_mod('about_image');
                    if ($about_image) :
                    ?>
                        <img src="<?php echo esc_url($about_image); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="img-fluid">
                    <?php else : ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/about-placeholder.jpg" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="img-fluid">
                    <?php endif; ?>
                </div>
                <div class="about-text">
                    <h2><?php echo esc_html(get_theme_mod('about_title', 'Sobre Nosso Escritório')); ?></h2>
                    <div class="about-description">
                        <?php echo wpautop(wp_kses_post(get_theme_mod('about_description', 'Somos um escritório de advocacia comprometido com a excelência e resultados. Nossa equipe é formada por profissionais experientes, dedicados a oferecer soluções jurídicas personalizadas para cada cliente. Defendemos seus direitos com ética, competência e determinação.'))); ?>
                    </div>
                    <div class="about-features">
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo esc_html(get_theme_mod('about_feature_1', 'Atendimento Personalizado')); ?></span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo esc_html(get_theme_mod('about_feature_2', 'Profissionais Qualificados')); ?></span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo esc_html(get_theme_mod('about_feature_3', 'Soluções Eficientes')); ?></span>
                        </div>
                    </div>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('sobre'))); ?>" class="btn btn-primary">Conheça Nossa História</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Depoimentos Section -->
    <section class="testimonials-section">
        <div class="container">
            <div class="section-title">
                <h2><?php echo esc_html(get_theme_mod('testimonials_title', 'Depoimentos de Clientes')); ?></h2>
                <p><?php echo esc_html(get_theme_mod('testimonials_description', 'Veja o que nossos clientes dizem sobre nossos serviços e atendimento.')); ?></p>
            </div>

            <div class="testimonial-carousel">
                <?php
                // Consulta para depoimentos
                $args = array(
                    'post_type'      => 'depoimento',
                    'posts_per_page' => 5,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                );
                
                $testimonials_query = new WP_Query($args);
                
                if ($testimonials_query->have_posts()) :
                    while ($testimonials_query->have_posts()) : $testimonials_query->the_post();
                ?>
                    <div class="testimonial-item">
                        <div class="testimonial-content">
                            <i class="fas fa-quote-left quote-icon"></i>
                            <?php echo get_the_content(); ?>
                        </div>
                        <div class="client-info">
                            <div class="client-img">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('thumbnail'); ?>
                                <?php else : ?>
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/client-placeholder.jpg" alt="Cliente">
                                <?php endif; ?>
                            </div>
                            <div class="client-name">
                                <h4><?php the_title(); ?></h4>
                                <?php if (function_exists('get_field') && get_field('empresa_cargo')) : ?>
                                    <p><?php echo esc_html(get_field('empresa_cargo')); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                ?>
                    <div class="testimonial-item">
                        <div class="testimonial-content">
                            <i class="fas fa-quote-left quote-icon"></i>
                            <p>"Excelente escritório de advocacia, com atendimento personalizado e resultados consistentes. Recomendo fortemente o trabalho da Dra. Thabatta."</p>
                        </div>
                        <div class="client-info">
                            <div class="client-img">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/client-placeholder.jpg" alt="Cliente">
                            </div>
                            <div class="client-name">
                                <h4>Carlos Silva</h4>
                                <p>Empresário</p>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-item">
                        <div class="testimonial-content">
                            <i class="fas fa-quote-left quote-icon"></i>
                            <p>"Dra. Thabatta conduziu meu caso com profissionalismo e dedicação. O resultado superou minhas expectativas. Muito grata pelo trabalho realizado."</p>
                        </div>
                        <div class="client-info">
                            <div class="client-img">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/client-placeholder.jpg" alt="Cliente">
                            </div>
                            <div class="client-name">
                                <h4>Ana Oliveira</h4>
                                <p>Professora</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2><?php echo esc_html(get_theme_mod('cta_title', 'Precisando de Orientação Jurídica?')); ?></h2>
                <p><?php echo esc_html(get_theme_mod('cta_description', 'Entre em contato conosco para uma consulta inicial. Nossos advogados estão prontos para ajudar você.')); ?></p>
                <div class="cta-buttons">
                    <a href="<?php echo esc_url(get_theme_mod('cta_button_url', '/contato')); ?>" class="btn btn-primary"><?php echo esc_html(get_theme_mod('cta_button_text', 'Agende uma Consulta')); ?></a>
                    <a href="#" class="btn btn-outline-primary open-consultation-form"><?php esc_html_e('Formulário de Consulta', 'thabatta-adv'); ?></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Equipe Section -->
    <section class="team-section">
        <div class="container">
            <div class="section-title">
                <h2><?php echo esc_html(get_theme_mod('team_title', 'Nossa Equipe')); ?></h2>
                <p><?php echo esc_html(get_theme_mod('team_description', 'Conheça os profissionais dedicados a cuidar do seu caso com excelência.')); ?></p>
            </div>

            <div class="team-grid">
                <?php
                // Consulta para equipe
                $args = array(
                    'post_type'      => 'equipe',
                    'posts_per_page' => 4,
                    'orderby'        => 'menu_order',
                    'order'          => 'ASC',
                );
                
                $team_query = new WP_Query($args);
                
                if ($team_query->have_posts()) :
                    while ($team_query->have_posts()) : $team_query->the_post();
                ?>
                    <div class="team-member">
                        <div class="member-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium', array('class' => 'img-fluid')); ?>
                            <?php else : ?>
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/team-placeholder.jpg" alt="<?php the_title_attribute(); ?>" class="img-fluid">
                            <?php endif; ?>
                        </div>
                        <div class="member-info">
                            <h3><?php the_title(); ?></h3>
                            <?php if (function_exists('get_field')) : ?>
                                <?php if (get_field('cargo')) : ?>
                                    <p class="member-position"><?php echo esc_html(get_field('cargo')); ?></p>
                                <?php endif; ?>
                                <?php if (get_field('especialidade')) : ?>
                                    <p class="member-specialty"><?php echo esc_html(get_field('especialidade')); ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                            <div class="member-social">
                                <?php if (function_exists('get_field') && get_field('linkedin')) : ?>
                                    <a href="<?php echo esc_url(get_field('linkedin')); ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                                <?php endif; ?>
                                <?php if (function_exists('get_field') && get_field('email')) : ?>
                                    <a href="mailto:<?php echo esc_attr(get_field('email')); ?>"><i class="far fa-envelope"></i></a>
                                <?php endif; ?>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="btn btn-outline-primary btn-sm">Ver Perfil</a>
                        </div>
                    </div>
                <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
            
            <div class="section-cta">
                <a href="<?php echo esc_url(get_post_type_archive_link('equipe')); ?>" class="btn btn-secondary">Conheça toda nossa equipe</a>
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    <section class="blog-section">
        <div class="container">
            <div class="section-title">
                <h2><?php echo esc_html(get_theme_mod('blog_title', 'Últimas do Blog')); ?></h2>
                <p><?php echo esc_html(get_theme_mod('blog_description', 'Fique atualizado com nosso conteúdo jurídico e dicas relevantes.')); ?></p>
            </div>

            <div class="blog-grid">
                <?php
                // Consulta para posts recentes
                $args = array(
                    'post_type'      => 'post',
                    'posts_per_page' => 3,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                );
                
                $blog_query = new WP_Query($args);
                
                if ($blog_query->have_posts()) :
                    while ($blog_query->have_posts()) : $blog_query->the_post();
                ?>
                    <div class="blog-item">
                        <div class="blog-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium', array('class' => 'img-fluid')); ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/blog-placeholder.jpg" alt="<?php the_title_attribute(); ?>" class="img-fluid">
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="blog-content">
                            <div class="blog-meta">
                                <span class="blog-date"><i class="far fa-calendar-alt"></i> <?php echo get_the_date(); ?></span>
                                <?php
                                $categories = get_the_category();
                                if (!empty($categories)) :
                                ?>
                                <span class="blog-category"><i class="far fa-folder"></i> <?php echo esc_html($categories[0]->name); ?></span>
                                <?php endif; ?>
                            </div>
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <div class="blog-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="btn btn-outline-primary btn-sm">
                                <?php esc_html_e('Leia mais', 'thabatta-adv'); ?>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                ?>
                    <div class="blog-item">
                        <div class="blog-image">
                            <a href="#">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/blog-placeholder.jpg" alt="Blog Post" class="img-fluid">
                            </a>
                        </div>
                        <div class="blog-content">
                            <div class="blog-meta">
                                <span class="blog-date"><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y'); ?></span>
                                <span class="blog-category"><i class="far fa-folder"></i> Direito Civil</span>
                            </div>
                            <h3><a href="#">Direitos do Consumidor: O que Você Precisa Saber</a></h3>
                            <div class="blog-excerpt">
                                <p>Conheça seus direitos como consumidor e saiba como se proteger de práticas abusivas no mercado.</p>
                            </div>
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                <?php esc_html_e('Leia mais', 'thabatta-adv'); ?>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="section-cta">
                <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="btn btn-secondary">Ver Todos os Artigos</a>
            </div>
        </div>
    </section>

    <!-- Formulário de Consulta (via CTA) -->
    <section class="consultation-cta">
        <div class="container">
            <div class="cta-content text-center">
                <h2><?php echo esc_html(get_theme_mod('consultation_cta_title', __('Precisa de Orientação Jurídica?', 'thabatta-adv'))); ?></h2>
                <p><?php echo esc_html(get_theme_mod('consultation_cta_text', __('Entre em contato para uma primeira consulta e descubra como podemos ajudar no seu caso.', 'thabatta-adv'))); ?></p>
                <button class="btn btn-primary btn-lg open-consultation-form">
                    <?php esc_html_e('Agendar Consulta Online', 'thabatta-adv'); ?>
                </button>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?> 