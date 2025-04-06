<?php
/**
 * O rodapé para o tema Thabatta Advocacia
 *
 * Contém o conteúdo do rodapé e fecha as tags html, body e div#page
 *
 * @package Thabatta_Advocacia
 */

?>

    <footer id="colophon" class="site-footer">
        <div class="container">
            <div class="footer-widgets">
                <div class="footer-widget">
                    <?php if (is_active_sidebar('footer-1')) : ?>
                        <?php dynamic_sidebar('footer-1'); ?>
                    <?php else : ?>
                        <h3 class="widget-title"><?php echo esc_html(get_bloginfo('name')); ?></h3>
                        <p><?php esc_html_e('Seu escritório de advocacia de confiança.', 'thabatta-adv'); ?></p>
                    <?php endif; ?>
                </div>

                <div class="footer-widget">
                    <?php if (is_active_sidebar('footer-2')) : ?>
                        <?php dynamic_sidebar('footer-2'); ?>
                    <?php else : ?>
                        <h3 class="widget-title"><?php echo esc_html__('Contato', 'thabatta-adv'); ?></h3>
                        <ul class="contact-info">
                            <?php if (get_theme_mod('general_phone')) : ?>
                            <li>
                                <i class="fas fa-phone"></i>
                                <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9]/', '', get_theme_mod('general_phone'))); ?>">
                                    <?php echo esc_html(get_theme_mod('general_phone')); ?>
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php if (get_theme_mod('general_email')) : ?>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:<?php echo esc_attr(get_theme_mod('general_email')); ?>">
                                    <?php echo esc_html(get_theme_mod('general_email')); ?>
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php if (get_theme_mod('social_whatsapp_number')) : ?>
                            <li>
                                <i class="fab fa-whatsapp"></i>
                                <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('social_whatsapp_number')); ?>" target="_blank">
                                    WhatsApp
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <div class="footer-widget">
                    <?php if (is_active_sidebar('footer-3')) : ?>
                        <?php dynamic_sidebar('footer-3'); ?>
                    <?php else : ?>
                        <h3 class="widget-title"><?php esc_html_e('Redes Sociais', 'thabatta-adv'); ?></h3>
                        <div class="social-links">
                            <?php if (get_theme_mod('social_facebook_url')) : ?>
                                <a href="<?php echo esc_url(get_theme_mod('social_facebook_url')); ?>" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php if (get_theme_mod('social_instagram_url')) : ?>
                                <a href="<?php echo esc_url(get_theme_mod('social_instagram_url')); ?>" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php if (get_theme_mod('social_linkedin_url')) : ?>
                                <a href="<?php echo esc_url(get_theme_mod('social_linkedin_url')); ?>" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php if (get_theme_mod('social_twitter_url')) : ?>
                                <a href="<?php echo esc_url(get_theme_mod('social_twitter_url')); ?>" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php if (get_theme_mod('social_youtube_url')) : ?>
                                <a href="<?php echo esc_url(get_theme_mod('social_youtube_url')); ?>" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php if (get_theme_mod('social_whatsapp_number')) : ?>
                                <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('social_whatsapp_number')); ?>" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="footer-widget">
                    <?php if (is_active_sidebar('footer-4')) : ?>
                        <?php dynamic_sidebar('footer-4'); ?>
                    <?php else : ?>
                        <h3 class="widget-title"><?php esc_html_e('Horário de Atendimento', 'thabatta-adv'); ?></h3>
                        <p>
                            <?php 
                            $horario_atendimento = get_theme_mod('footer_horario_atendimento', "Segunda - Sexta: 9:00 - 18:00\nSábado: Com agendamento\nDomingo: Fechado");
                            echo nl2br(esc_html($horario_atendimento));
                            ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="copyright">
                    <p>
                        &copy; <?php echo date_i18n('Y'); ?> <?php echo esc_html(get_bloginfo('name')); ?>. 
                        <?php esc_html_e('Todos os direitos reservados.', 'thabatta-adv'); ?>
                    </p>
                    <?php if (function_exists('get_field') && get_field('texto_rodape', 'option')) : ?>
                        <p><?php echo wp_kses_post(get_field('texto_rodape', 'option')); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer><!-- #colophon -->
</div><!-- #page -->

<button class="back-to-top">
    <i class="fas fa-chevron-up"></i>
</button>

<script>
    // Código para abrir o formulário de consulta
    jQuery(document).ready(function($) {
        // Função para abrir o modal do formulário de consulta
        $('.open-consultation-form').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#consultationForm').addClass('active');
            $('body').css('overflow', 'hidden');
            setTimeout(function() {
                $('.form-container').addClass('active');
            }, 100);
        });
    });
</script>

<?php wp_footer(); ?>

</body>
</html>
