<?php
/**
 * Template part para exibir quando não há conteúdo
 *
 * @package Thabatta_Advocacia
 */

?>

<section class="no-results not-found">
    <header class="page-header">
        <h1 class="page-title"><?php esc_html_e('Nada Encontrado', 'thabatta-adv'); ?></h1>
    </header><!-- .page-header -->

    <div class="page-content">
        <?php
        if (is_home() && current_user_can('publish_posts')) :

            printf(
                '<p>' . wp_kses(
                    /* translators: 1: link para nova postagem */
                    __('Pronto para publicar seu primeiro post? <a href="%1$s">Comece aqui</a>.', 'thabatta-adv'),
                    array(
                        'a' => array(
                            'href' => array(),
                        ),
                    )
                ) . '</p>',
                esc_url(admin_url('post-new.php'))
            );

        elseif (is_search()) :
            ?>

            <p><?php esc_html_e('Desculpe, mas nada corresponde aos seus termos de pesquisa. Por favor, tente novamente com algumas palavras-chave diferentes.', 'thabatta-adv'); ?></p>
            <?php
            get_search_form();

        else :
            ?>

            <p><?php esc_html_e('Parece que não conseguimos encontrar o que você está procurando. Talvez a pesquisa possa ajudar.', 'thabatta-adv'); ?></p>
            <?php
            get_search_form();

        endif;
        ?>
    </div><!-- .page-content -->
</section><!-- .no-results -->
