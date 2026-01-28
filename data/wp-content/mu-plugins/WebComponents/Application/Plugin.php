<?php

namespace Thabatta\WebComponents\Application;

use Thabatta\WebComponents\Infrastructure\ComponentRepository;
use Thabatta\WebComponents\Infrastructure\MetadataService;
use Thabatta\WebComponents\Presentation\AdminColumns;
use Thabatta\WebComponents\Presentation\Importer;
use Thabatta\WebComponents\Presentation\MetaBox;
use Thabatta\WebComponents\Presentation\Renderer;
use Thabatta\WebComponents\Presentation\ShortcodeHandler;

class Plugin {
    private MetadataService $metadataService;
    private ComponentRepository $componentRepository;
    private Renderer $renderer;
    private ComponentRegistrar $componentRegistrar;
    private MetaBox $metaBox;
    private Importer $importer;
    private ShortcodeHandler $shortcodeHandler;
    private AdminColumns $adminColumns;

    public function __construct() {
        $this->metadataService = new MetadataService();
        $this->componentRepository = new ComponentRepository($this->metadataService);
        $this->renderer = new Renderer();
        $this->componentRegistrar = new ComponentRegistrar($this->componentRepository, $this->renderer);
        $this->metaBox = new MetaBox($this->metadataService);
        $this->importer = new Importer($this->metadataService);
        $this->shortcodeHandler = new ShortcodeHandler($this->componentRepository, $this->renderer);
        $this->adminColumns = new AdminColumns($this->metadataService);
    }

    public function boot(): void {
        add_action('init', [$this, 'registerPostType']);
        add_action('add_meta_boxes', [$this->metaBox, 'register']);
        add_action('save_post', [$this, 'saveMeta']);
        add_action('wp', [$this, 'detectComponents']);
        add_action('wp_enqueue_scripts', [$this->renderer, 'enqueueAssets']);
        add_action('wp_footer', [$this->renderer, 'renderTemplates'], 20);

        $this->importer->register();
        $this->adminColumns->register();

        add_shortcode('thabatta_web_component', [$this->shortcodeHandler, 'handle']);

        add_filter('wp_kses_allowed_html', [$this, 'allowWebComponentTags'], 10, 2);
        add_action('save_post', [$this, 'disableKsesForWebComponents'], 1, 1);
    }

    public function registerPostType(): void {
        register_post_type('web_component', [
            'labels' => [
                'name' => 'Web Components',
                'singular_name' => 'Web Component',
            ],
            'public' => true,
            'has_archive' => false,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-editor-code',
            'supports' => ['title'],
        ]);
    }

    public function saveMeta(int $postId): void {
        if (get_post_type($postId) !== 'web_component') {
            return;
        }

        $this->metadataService->saveMeta($postId, $_POST);
    }

    public function detectComponents(): void {
        global $post;
        $this->componentRegistrar->registerComponentsFromContent($post);
    }

    public function allowWebComponentTags(array $allowed, string $context): array {
        if ($context !== 'post' && $context !== 'pre_user_description') {
            return $allowed;
        }

        $components = $this->componentRepository->findAll();
        foreach ($components as $component) {
            if (!$component->tagName) {
                continue;
            }
            $allowed[$component->tagName] = array_merge(
                $allowed[$component->tagName] ?? [],
                ['data-*' => true]
            );
        }

        return $allowed;
    }

    public function disableKsesForWebComponents(int $postId): void {
        if (get_post_type($postId) !== 'web_component') {
            return;
        }

        remove_filter('content_save_pre', 'wp_filter_post_kses');
    }
}
