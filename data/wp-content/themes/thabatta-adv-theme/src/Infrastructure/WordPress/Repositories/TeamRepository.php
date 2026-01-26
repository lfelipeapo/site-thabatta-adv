<?php

namespace ThabattaAdv\Infrastructure\WordPress\Repositories;

class TeamRepository
{
    public function query(array $args = []): \WP_Query
    {
        $defaults = [
            'post_type'      => 'equipe',
            'posts_per_page' => get_option('posts_per_page'),
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'paged'          => 1,
        ];

        return new \WP_Query(wp_parse_args($args, $defaults));
    }
}
