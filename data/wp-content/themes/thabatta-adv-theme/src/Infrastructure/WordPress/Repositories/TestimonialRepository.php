<?php

namespace ThabattaAdv\Infrastructure\WordPress\Repositories;

class TestimonialRepository
{
    public function query(array $args = []): \WP_Query
    {
        $defaults = [
            'post_type'      => 'depoimento',
            'posts_per_page' => get_option('posts_per_page'),
            'orderby'        => 'date',
            'order'          => 'DESC',
            'paged'          => 1,
        ];

        return new \WP_Query(wp_parse_args($args, $defaults));
    }
}
