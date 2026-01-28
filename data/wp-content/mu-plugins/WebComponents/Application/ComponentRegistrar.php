<?php

namespace Thabatta\WebComponents\Application;

use Thabatta\WebComponents\Infrastructure\ComponentRepository;
use Thabatta\WebComponents\Presentation\Renderer;

class ComponentRegistrar {
    public function __construct(
        private ComponentRepository $repository,
        private Renderer $renderer
    ) {
    }

    public function registerComponentsFromContent(?\WP_Post $post): void {
        if (!$post) {
            return;
        }

        $content = $post->post_content;
        $components = $this->repository->findAll();

        foreach ($components as $component) {
            if ($this->componentInContent($content, $component->tagName)) {
                $this->renderer->registerComponent($component);
            }
        }
    }

    private function componentInContent(string $content, string $tagName): bool {
        if ($tagName === '') {
            return false;
        }

        return str_contains($content, "<{$tagName}")
            || str_contains($content, '[thabatta_web_component');
    }
}
