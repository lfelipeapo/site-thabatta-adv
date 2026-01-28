<?php

namespace Thabatta\WebComponents\Presentation;

use Thabatta\WebComponents\Domain\WebComponent;

class Renderer {
    /** @var WebComponent[] */
    private array $components = [];

    public function registerComponent(WebComponent $component): void {
        if (isset($this->components[$component->id])) {
            return;
        }

        $this->components[$component->id] = $component;
    }

    public function hasComponents(): bool {
        return !empty($this->components);
    }

    public function enqueueAssets(): void {
        if (!$this->hasComponents()) {
            return;
        }

        $assetPath = WPMU_PLUGIN_DIR . '/WebComponents/assets/web-components.js';
        $assetUrl = WPMU_PLUGIN_URL . '/WebComponents/assets/web-components.js';
        wp_enqueue_script(
            'thabatta-web-components',
            $assetUrl,
            [],
            file_exists($assetPath) ? filemtime($assetPath) : false,
            true
        );
    }

    public function renderTemplates(): void {
        if (!$this->hasComponents()) {
            return;
        }

        $payload = [];

        foreach ($this->components as $component) {
            $templateId = 'thabatta-web-component-' . $component->id;
            $payload[] = [
                'id' => $component->id,
                'tag' => $component->tagName,
                'templateId' => $templateId,
                'useShadowDom' => $component->useShadowDom,
                'shadowDomMode' => $component->shadowDomMode ?: 'open',
                'js' => $this->prepareInlineJs($component->js),
            ];

            echo '<template id="' . esc_attr($templateId) . '">';
            echo $component->html;
            echo '<style>' . $component->css . '</style>';
            echo '</template>';
        }

        echo '<script type="application/json" id="thabatta-web-components-data">';
        echo wp_json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        echo '</script>';
    }

    private function prepareInlineJs(string $js): string {
        $decoded = html_entity_decode($js, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return str_replace('this.querySelector', 'root.querySelector', $decoded);
    }
}
