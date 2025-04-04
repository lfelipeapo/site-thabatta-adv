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
                        <h3 class="widget-title"><?php esc_html_e('Contato', 'thabatta-adv'); ?></h3>
                        <?php if (function_exists('get_field')) : ?>
                            <p>
                                <?php echo esc_html(get_field('endereco', 'option')); ?><br>
                                <?php echo esc_html(get_field('cidade_estado_cep', 'option')); ?>
                            </p>
                            <p>
                                <?php esc_html_e('Telefone:', 'thabatta-adv'); ?> <?php echo esc_html(get_field('telefone', 'option')); ?><br>
                                <?php esc_html_e('Email:', 'thabatta-adv'); ?> <?php echo esc_html(get_field('email', 'option')); ?>
                            </p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="footer-widget">
                    <?php if (is_active_sidebar('footer-3')) : ?>
                        <?php dynamic_sidebar('footer-3'); ?>
                    <?php else : ?>
                        <h3 class="widget-title"><?php esc_html_e('Redes Sociais', 'thabatta-adv'); ?></h3>
                        <?php if (function_exists('get_field')) : ?>
                            <div class="social-links">
                                <?php if (get_field('facebook', 'option')) : ?>
                                    <a href="<?php echo esc_url(get_field('facebook', 'option')); ?>" target="_blank" rel="noopener noreferrer">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (get_field('instagram', 'option')) : ?>
                                    <a href="<?php echo esc_url(get_field('instagram', 'option')); ?>" target="_blank" rel="noopener noreferrer">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (get_field('linkedin', 'option')) : ?>
                                    <a href="<?php echo esc_url(get_field('linkedin', 'option')); ?>" target="_blank" rel="noopener noreferrer">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (get_field('twitter', 'option')) : ?>
                                    <a href="<?php echo esc_url(get_field('twitter', 'option')); ?>" target="_blank" rel="noopener noreferrer">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (get_field('youtube', 'option')) : ?>
                                    <a href="<?php echo esc_url(get_field('youtube', 'option')); ?>" target="_blank" rel="noopener noreferrer">
                                        <i class="fab fa-youtube"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="footer-widget">
                    <?php if (is_active_sidebar('footer-4')) : ?>
                        <?php dynamic_sidebar('footer-4'); ?>
                    <?php else : ?>
                        <h3 class="widget-title"><?php esc_html_e('Horário de Atendimento', 'thabatta-adv'); ?></h3>
                        <?php if (function_exists('get_field')) : ?>
                            <p>
                                <?php echo wp_kses_post(get_field('horario_atendimento', 'option')); ?>
                            </p>
                        <?php else : ?>
                            <p>
                                <?php esc_html_e('Segunda - Sexta: 9:00 - 18:00', 'thabatta-adv'); ?><br>
                                <?php esc_html_e('Sábado: Com agendamento', 'thabatta-adv'); ?><br>
                                <?php esc_html_e('Domingo: Fechado', 'thabatta-adv'); ?>
                            </p>
                        <?php endif; ?>
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

<?php wp_footer(); ?>

</body>
</html>
