<?php

namespace Thabatta\WebComponents\Presentation;

use Thabatta\WebComponents\Infrastructure\ComponentRepository;

class ShortcodeHandler {
    public function __construct(
        private ComponentRepository $repository,
        private Renderer $renderer
    ) {
    }

    public function handle(array $atts): string {
        $atts = shortcode_atts([
            'id' => 0,
            'tag' => '',
        ], $atts);

        if (empty($atts['id']) && empty($atts['tag'])) {
            return '<p>Erro: ID ou tag do componente não especificado.</p>';
        }

        $component = null;
        if (!empty($atts['id'])) {
            $component = $this->repository->findById((int) $atts['id']);
        } elseif (!empty($atts['tag'])) {
            $component = $this->repository->findByTag($atts['tag']);
        }

        if (!$component) {
            return '<p>Erro: Componente não encontrado.</p>';
        }

        $this->renderer->registerComponent($component);

        $extra = '';
        foreach ($atts as $key => $value) {
            if (str_starts_with($key, 'data-') || str_starts_with($key, 'data_')) {
                $attr = str_replace('_', '-', $key);
                $extra .= ' ' . esc_attr($attr) . '="' . esc_attr($value) . '"';
            }
        }

        return '<' . esc_html($component->tagName) . $extra . '></' . esc_html($component->tagName) . '>';
    }
}
