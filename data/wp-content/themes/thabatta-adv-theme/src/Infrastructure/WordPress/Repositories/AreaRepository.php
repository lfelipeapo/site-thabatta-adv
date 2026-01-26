<?php

namespace ThabattaAdv\Infrastructure\WordPress\Repositories;

use ThabattaAdv\Infrastructure\WordPress\HomepageCache;

class AreaRepository
{
    public function query(array $args = []): \WP_Query
    {
        $defaults = [
            'post_type'      => 'area_atuacao',
            'posts_per_page' => get_option('posts_per_page'),
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'paged'          => 1,
        ];

        $query_args = wp_parse_args($args, $defaults);
        $cached_ids = HomepageCache::getCachedIds('areas', $query_args);

        if (is_array($cached_ids)) {
            return new \WP_Query(HomepageCache::queryArgsFromIds($query_args, $cached_ids));
        }

        $query = new \WP_Query($query_args);
        HomepageCache::storeCachedIds('areas', $query_args, wp_list_pluck($query->posts, 'ID'));

        return $query;
    }
}
