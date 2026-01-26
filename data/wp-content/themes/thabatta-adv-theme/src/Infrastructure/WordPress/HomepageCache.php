<?php

namespace ThabattaAdv\Infrastructure\WordPress;

class HomepageCache
{
    private const TTL = 120;
    private const OPTION_KEY = 'thabatta_homepage_cache_keys';

    public function register(): void
    {
        add_action('save_post', [$this, 'maybeFlushCache'], 10, 2);
    }

    public function maybeFlushCache(int $post_id, \WP_Post $post): void
    {
        if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
            return;
        }

        $group = $this->mapPostTypeToGroup($post->post_type);
        if (!$group) {
            return;
        }

        self::flushGroup($group);
    }

    public static function getCachedIds(string $group, array $query_args): ?array
    {
        $cache_key = self::buildCacheKey($group, $query_args);
        $cached = get_transient($cache_key);

        if ($cached === false) {
            return null;
        }

        return is_array($cached) ? $cached : null;
    }

    public static function storeCachedIds(string $group, array $query_args, array $ids): void
    {
        $cache_key = self::buildCacheKey($group, $query_args);
        set_transient($cache_key, array_values($ids), self::TTL);

        $keys = get_option(self::OPTION_KEY, []);
        if (!isset($keys[$group]) || !is_array($keys[$group])) {
            $keys[$group] = [];
        }
        $keys[$group][$cache_key] = true;
        update_option(self::OPTION_KEY, $keys, false);
    }

    public static function queryArgsFromIds(array $query_args, array $ids): array
    {
        $query_args['post__in'] = $ids ?: [0];
        $query_args['orderby'] = 'post__in';
        $query_args['posts_per_page'] = count($ids);
        $query_args['paged'] = 1;
        $query_args['no_found_rows'] = true;

        return $query_args;
    }

    public static function flushGroup(string $group): void
    {
        $keys = get_option(self::OPTION_KEY, []);
        if (empty($keys[$group]) || !is_array($keys[$group])) {
            return;
        }

        foreach (array_keys($keys[$group]) as $cache_key) {
            delete_transient($cache_key);
        }

        unset($keys[$group]);
        update_option(self::OPTION_KEY, $keys, false);
    }

    private static function buildCacheKey(string $group, array $query_args): string
    {
        $normalized = self::normalizeArgs($query_args);
        return 'thabatta_hp_' . $group . '_' . md5(wp_json_encode($normalized));
    }

    private static function normalizeArgs(array $args): array
    {
        foreach ($args as $key => $value) {
            if (is_array($value)) {
                $args[$key] = self::normalizeArgs($value);
            }
        }

        ksort($args);

        return $args;
    }

    private function mapPostTypeToGroup(string $post_type): ?string
    {
        $map = [
            'area_atuacao' => 'areas',
            'depoimento'   => 'testimonials',
            'equipe'       => 'team',
            'post'         => 'blog',
        ];

        return $map[$post_type] ?? null;
    }
}
