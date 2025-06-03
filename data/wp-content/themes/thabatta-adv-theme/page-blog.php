<?php
/**
 * Template Name: Página de Blog
 * Template Post Type: page
 *
 * @package Thabatta_Advocacia
 */

get_header();
?>

<main id="primary" class="site-main blog-page">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title"><?php the_title(); ?></h1>
            <?php if (get_the_content()) : ?>
                <div class="page-description">
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>
        </header>

        <div class="blog-grid">
            <?php
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $args = array(
                'post_type'      => 'post',
                'posts_per_page' => 10,
                'paged'          => $paged,
                'orderby'        => 'date',
                'order'          => 'DESC',
            );
            
            $blog_query = new WP_Query($args);
            
            if ($blog_query->have_posts()) :
                while ($blog_query->have_posts()) : $blog_query->the_post();
            ?>
                <article class="post">
                    <div class="entry-header">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="entry-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium', array('class' => 'img-fluid')); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="entry-content">
                        <h2 class="entry-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>

                        <div class="entry-meta">
                            <span class="posted-on">
                                <i class="far fa-calendar-alt"></i>
                                <?php echo get_the_date(); ?>
                            </span>
                            <?php
                            $categories = get_the_category();
                            if (!empty($categories)) :
                            ?>
                            <span class="cat-links">
                                <i class="far fa-folder"></i>
                                <?php echo esc_html($categories[0]->name); ?>
                            </span>
                            <?php endif; ?>
                        </div>

                        <div class="entry-summary">
                            <?php the_excerpt(); ?>
                        </div>

                        <div class="entry-footer">
                            <a href="<?php the_permalink(); ?>" class="read-more">
                                <?php esc_html_e('Leia mais', 'thabatta-adv'); ?>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>
            <?php
                endwhile;
                
                // Paginação
                echo '<div class="pagination">';
                the_posts_pagination(array(
                    'mid_size'  => 2,
                    'prev_text' => '<i class="fas fa-arrow-left"></i> ' . esc_html__('Anterior', 'thabatta-adv'),
                    'next_text' => esc_html__('Próximo', 'thabatta-adv') . ' <i class="fas fa-arrow-right"></i>',
                    'screen_reader_text' => ' ',
                    'aria_label' => __('Navegação de posts', 'thabatta-adv'),
                ));
                echo '</div>';
                
                wp_reset_postdata();
            else :
            ?>
                <div class="no-posts">
                    <p><?php esc_html_e('Nenhum post encontrado.', 'thabatta-adv'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
get_footer(); 