<?php
/**
 * Template da página de contato
 *
 * @package Thabatta_Advocacia
 */
if (!defined('ABSPATH')) exit;
get_header();
?>
<main id="primary" class="site-main contato-page">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title"><?php the_title(); ?></h1>
            <?php if (get_the_excerpt()) : ?>
                <div class="page-description"><?php the_excerpt(); ?></div>
            <?php endif; ?>
        </header>
        <section class="contato-form-section">
            <?php get_template_part('template-parts/contact-form-multistep'); ?>
        </section>
    </div>
</main>

<?php
get_footer(); 