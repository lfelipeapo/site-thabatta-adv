<?php

namespace ThabattaAdv\Infrastructure\WordPress;

class PostTypes
{
    public function register(): void
    {
        add_action('init', [$this, 'register_post_types']);
        add_action('init', [$this, 'register_taxonomies']);
    }

    public function register_post_types(): void
    {
        register_post_type('area_atuacao', array(
            'labels' => array(
                'name'               => esc_html__('Áreas de Atuação', 'thabatta-adv'),
                'singular_name'      => esc_html__('Área de Atuação', 'thabatta-adv'),
                'add_new'            => esc_html__('Adicionar Nova', 'thabatta-adv'),
                'add_new_item'       => esc_html__('Adicionar Nova Área', 'thabatta-adv'),
                'edit_item'          => esc_html__('Editar Área', 'thabatta-adv'),
                'new_item'           => esc_html__('Nova Área', 'thabatta-adv'),
                'view_item'          => esc_html__('Ver Área', 'thabatta-adv'),
                'search_items'       => esc_html__('Buscar Áreas', 'thabatta-adv'),
                'not_found'          => esc_html__('Nenhuma área encontrada', 'thabatta-adv'),
                'not_found_in_trash' => esc_html__('Nenhuma área encontrada na lixeira', 'thabatta-adv'),
                'menu_name'          => esc_html__('Áreas de Atuação', 'thabatta-adv'),
            ),
            'public'              => true,
            'hierarchical'        => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-portfolio',
            'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
            'has_archive'         => true,
            'rewrite'             => array('slug' => 'areas-de-atuacao'),
            'show_in_rest'        => true,
        ));

        register_post_type('equipe', array(
            'labels' => array(
                'name'               => esc_html__('Equipe', 'thabatta-adv'),
                'singular_name'      => esc_html__('Membro da Equipe', 'thabatta-adv'),
                'add_new'            => esc_html__('Adicionar Novo', 'thabatta-adv'),
                'add_new_item'       => esc_html__('Adicionar Novo Membro', 'thabatta-adv'),
                'edit_item'          => esc_html__('Editar Membro', 'thabatta-adv'),
                'new_item'           => esc_html__('Novo Membro', 'thabatta-adv'),
                'view_item'          => esc_html__('Ver Membro', 'thabatta-adv'),
                'search_items'       => esc_html__('Buscar Membros', 'thabatta-adv'),
                'not_found'          => esc_html__('Nenhum membro encontrado', 'thabatta-adv'),
                'not_found_in_trash' => esc_html__('Nenhum membro encontrado na lixeira', 'thabatta-adv'),
                'menu_name'          => esc_html__('Equipe', 'thabatta-adv'),
            ),
            'public'              => true,
            'hierarchical'        => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 6,
            'menu_icon'           => 'dashicons-groups',
            'supports'            => array('title', 'editor', 'thumbnail'),
            'has_archive'         => true,
            'rewrite'             => array('slug' => 'equipe'),
            'show_in_rest'        => true,
        ));

        register_post_type('depoimento', array(
            'labels' => array(
                'name'               => esc_html__('Depoimentos', 'thabatta-adv'),
                'singular_name'      => esc_html__('Depoimento', 'thabatta-adv'),
                'add_new'            => esc_html__('Adicionar Novo', 'thabatta-adv'),
                'add_new_item'       => esc_html__('Adicionar Novo Depoimento', 'thabatta-adv'),
                'edit_item'          => esc_html__('Editar Depoimento', 'thabatta-adv'),
                'new_item'           => esc_html__('Novo Depoimento', 'thabatta-adv'),
                'view_item'          => esc_html__('Ver Depoimento', 'thabatta-adv'),
                'search_items'       => esc_html__('Buscar Depoimentos', 'thabatta-adv'),
                'not_found'          => esc_html__('Nenhum depoimento encontrado', 'thabatta-adv'),
                'not_found_in_trash' => esc_html__('Nenhum depoimento encontrado na lixeira', 'thabatta-adv'),
                'menu_name'          => esc_html__('Depoimentos', 'thabatta-adv'),
            ),
            'public'              => true,
            'hierarchical'        => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 7,
            'menu_icon'           => 'dashicons-format-quote',
            'supports'            => array('title', 'editor', 'thumbnail'),
            'has_archive'         => false,
            'rewrite'             => array('slug' => 'depoimentos'),
            'show_in_rest'        => true,
        ));
    }

    public function register_taxonomies(): void
    {
        register_taxonomy('categoria_area', 'area_atuacao', array(
            'labels' => array(
                'name'              => esc_html__('Categorias de Áreas', 'thabatta-adv'),
                'singular_name'     => esc_html__('Categoria de Área', 'thabatta-adv'),
                'search_items'      => esc_html__('Buscar Categorias', 'thabatta-adv'),
                'all_items'         => esc_html__('Todas as Categorias', 'thabatta-adv'),
                'parent_item'       => esc_html__('Categoria Pai', 'thabatta-adv'),
                'parent_item_colon' => esc_html__('Categoria Pai:', 'thabatta-adv'),
                'edit_item'         => esc_html__('Editar Categoria', 'thabatta-adv'),
                'update_item'       => esc_html__('Atualizar Categoria', 'thabatta-adv'),
                'add_new_item'      => esc_html__('Adicionar Nova Categoria', 'thabatta-adv'),
                'new_item_name'     => esc_html__('Nova Categoria', 'thabatta-adv'),
                'menu_name'         => esc_html__('Categorias', 'thabatta-adv'),
            ),
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'categoria-area'),
            'show_in_rest'      => true,
        ));
    }
}
