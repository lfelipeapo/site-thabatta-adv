<?php

namespace Thabatta\WebComponents\Presentation;

use Thabatta\WebComponents\Infrastructure\MetadataService;

class MetaBox {
    public function __construct(private MetadataService $metadataService) {
    }

    public function register(): void {
        add_meta_box(
            'thabatta_web_component_meta',
            'Configuração do Web Component',
            [$this, 'render'],
            'web_component',
            'normal',
            'default'
        );
    }

    public function render(\WP_Post $post): void {
        $fields = [
            'tag_name' => 'Tag Name',
            'html_code' => 'HTML',
            'css_code' => 'CSS',
            'js_code' => 'JS',
            'use_shadow_dom' => 'Usar Shadow DOM?',
            'shadow_dom_mode' => 'Modo do Shadow DOM (open/closed)',
        ];

        foreach ($fields as $key => $label) {
            $value = $this->metadataService->get($post->ID, $key);
            echo "<p><label for='{$key}'><strong>{$label}</strong></label><br>";

            if ($key === 'use_shadow_dom') {
                echo "<input type='checkbox' name='{$key}' id='{$key}' value='1' "
                    . checked($value, '1', false)
                    . " />";
            } elseif ($key === 'shadow_dom_mode') {
                echo "<select name='{$key}' id='{$key}'>
                    <option value='open' " . selected($value, 'open', false) . ">open</option>
                    <option value='closed' " . selected($value, 'closed', false) . ">closed</option>
                </select>";
            } elseif (str_ends_with($key, '_code')) {
                echo "<textarea name='{$key}' id='{$key}' rows='5' style='width:100%'>"
                    . esc_textarea($value)
                    . '</textarea>';
            } else {
                echo "<input type='text' name='{$key}' id='{$key}' value='" . esc_attr($value) . "' style='width:100%' />";
            }

            echo '</p>';
        }
    }
}
