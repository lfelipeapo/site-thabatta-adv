<?php

namespace Thabatta\WebComponents\Presentation;

use Thabatta\WebComponents\Infrastructure\MetadataService;

class AdminColumns {
    public function __construct(private MetadataService $metadataService) {
    }

    public function register(): void {
        add_filter('manage_web_component_posts_columns', [$this, 'addColumns']);
        add_action('manage_web_component_posts_custom_column', [$this, 'renderColumns'], 10, 2);
        add_filter('manage_edit-web_component_sortable_columns', [$this, 'sortableColumns']);
        add_action('pre_get_posts', [$this, 'orderBy']);
    }

    public function addColumns(array $columns): array {
        $newColumns = [];
        foreach ($columns as $key => $value) {
            if ($key === 'title') {
                $newColumns[$key] = $value;
                $newColumns['tag_name'] = 'Tag HTML';
                $newColumns['shortcode'] = 'Shortcode';
            } else {
                $newColumns[$key] = $value;
            }
        }
        return $newColumns;
    }

    public function renderColumns(string $column, int $postId): void {
        if ($column === 'tag_name') {
            $tagName = $this->metadataService->get($postId, 'tag_name');
            if ($tagName) {
                echo '<code>&lt;' . esc_html($tagName) . '&gt;&lt;/' . esc_html($tagName) . '&gt;</code>';
            }
            return;
        }

        if ($column === 'shortcode') {
            echo '<code>[thabatta_web_component id="' . esc_attr((string) $postId) . '"]</code><br>';
            $tagName = $this->metadataService->get($postId, 'tag_name');
            if ($tagName) {
                echo '<code>[thabatta_web_component tag="' . esc_html($tagName) . '"]</code>';
            }
        }
    }

    public function sortableColumns(array $columns): array {
        $columns['tag_name'] = 'tag_name';
        return $columns;
    }

    public function orderBy(\WP_Query $query): void {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        if ($query->get('post_type') === 'web_component') {
            $orderby = $query->get('orderby');

            if ($orderby === 'tag_name') {
                $query->set('meta_key', 'tag_name');
                $query->set('orderby', 'meta_value');
            }
        }
    }
}
