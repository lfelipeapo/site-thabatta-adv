<?php

namespace Thabatta\WebComponents\Domain;

class WebComponent {
    public function __construct(
        public int $id,
        public string $tagName,
        public string $html,
        public string $css,
        public string $js,
        public bool $useShadowDom,
        public string $shadowDomMode
    ) {
    }
}
