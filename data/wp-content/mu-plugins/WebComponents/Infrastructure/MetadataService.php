<?php

namespace Thabatta\WebComponents\Infrastructure;

use Thabatta\WebComponents\Domain\WebComponent;

class MetadataService {
    public const META_KEYS = [
        'tag_name',
        'html_code',
        'css_code',
        'js_code',
        'use_shadow_dom',
        'shadow_dom_mode',
    ];

    public function get(int $postId, string $key, string $default = ''): string {
        $value = get_post_meta($postId, $key, true);
        if ($value === '') {
            return $default;
        }

        return (string) $value;
    }

    public function set(int $postId, string $key, string $value): void {
        update_post_meta($postId, $key, $value);
    }

    public function hydrateComponent(int $postId): WebComponent {
        $tagName = $this->get($postId, 'tag_name');
        $html = $this->get($postId, 'html_code');
        $css = $this->get($postId, 'css_code');
        $js = $this->get($postId, 'js_code');
        $useShadowDom = $this->get($postId, 'use_shadow_dom') === '1';
        $shadowDomMode = $this->get($postId, 'shadow_dom_mode', 'open');

        return new WebComponent(
            $postId,
            $tagName,
            $html,
            $css,
            $js,
            $useShadowDom,
            $shadowDomMode
        );
    }

    public function saveMeta(int $postId, array $input): void {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $postId)) {
            return;
        }

        $allowedHtml = $this->allowedHtml();

        foreach (self::META_KEYS as $key) {
            if (isset($input[$key])) {
                $raw = wp_unslash($input[$key]);
                if ($key === 'js_code') {
                    $value = wp_slash($raw);
                } elseif (in_array($key, ['html_code', 'css_code'], true)) {
                    $value = wp_kses($raw, $allowedHtml);
                } else {
                    $value = sanitize_text_field($raw);
                }
                update_post_meta($postId, $key, $value);
            } elseif ($key === 'use_shadow_dom') {
                update_post_meta($postId, $key, '0');
            }
        }
    }

    private function allowedHtml(): array {
        $allowedHtml = wp_kses_allowed_html('post');
        $allowedHtml['template'] = ['id' => true];
        $allowedHtml['style'] = ['type' => true, 'media' => true];
        $allowedHtml['script'] = ['type' => true, 'src' => true, 'defer' => true];
        $allowedHtml['dialog'] = ['open' => true, 'id' => true, 'class' => true, 'style' => true];
        $allowedHtml['details'] = ['open' => true, 'id' => true, 'class' => true, 'style' => true];
        $allowedHtml['summary'] = ['id' => true, 'class' => true, 'style' => true];
        $allowedHtml['time'] = ['datetime' => true, 'id' => true, 'class' => true];
        $allowedHtml['mark'] = ['id' => true, 'class' => true, 'style' => true];
        $allowedHtml['slot'] = ['name' => true, 'id' => true, 'class' => true, 'style' => true];

        foreach ($allowedHtml as $tag => $attrs) {
            $allowedHtml[$tag]['slot'] = true;
            $allowedHtml[$tag]['data-*'] = true;
        }

        $basicElements = ['div', 'span', 'p', 'button', 'a', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'li'];
        foreach ($basicElements as $tag) {
            if (!isset($allowedHtml[$tag])) {
                $allowedHtml[$tag] = [];
            }
            $allowedHtml[$tag]['slot'] = true;
            $allowedHtml[$tag]['class'] = true;
            $allowedHtml[$tag]['id'] = true;
            $allowedHtml[$tag]['style'] = true;
        }

        return $allowedHtml;
    }
}
