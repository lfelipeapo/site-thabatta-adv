<?php

namespace Thabatta\WebComponents\Infrastructure;

use Thabatta\WebComponents\Domain\WebComponent;

class ComponentRepository {
    public function __construct(private MetadataService $metadataService) {
    }

    /**
     * @return WebComponent[]
     */
    public function findAll(): array {
        $components = get_posts([
            'post_type' => 'web_component',
            'posts_per_page' => -1,
        ]);

        return array_map(fn($component) => $this->metadataService->hydrateComponent($component->ID), $components);
    }

    public function findById(int $postId): ?WebComponent {
        $post = get_post($postId);
        if (!$post || $post->post_type !== 'web_component') {
            return null;
        }

        return $this->metadataService->hydrateComponent($postId);
    }

    public function findByTag(string $tag): ?WebComponent {
        $query = new \WP_Query([
            'post_type' => 'web_component',
            'posts_per_page' => 1,
            'meta_query' => [
                [
                    'key' => 'tag_name',
                    'value' => $tag,
                    'compare' => '=',
                ],
            ],
        ]);

        if (!$query->have_posts()) {
            return null;
        }

        $query->the_post();
        $postId = get_the_ID();
        wp_reset_postdata();

        return $this->metadataService->hydrateComponent($postId);
    }
}
