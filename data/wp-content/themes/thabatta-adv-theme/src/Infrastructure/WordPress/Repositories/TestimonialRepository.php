<?php

namespace ThabattaAdv\Infrastructure\WordPress\Repositories;

use ThabattaAdv\Infrastructure\WordPress\HomepageCache;

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

        $query_args = wp_parse_args($args, $defaults);
        $cached_ids = HomepageCache::getCachedIds('testimonials', $query_args);

        if (is_array($cached_ids)) {
            return new \WP_Query(HomepageCache::queryArgsFromIds($query_args, $cached_ids));
        }

        $query = new \WP_Query($query_args);
        HomepageCache::storeCachedIds('testimonials', $query_args, wp_list_pluck($query->posts, 'ID'));

        return $query;
    }
}
