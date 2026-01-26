<?php
/**
 * Plugin Name: Thabatta Web Components CPT
 * Description: Cria um Custom Post Type para gerenciar Web Components
 * Version: 1.0.0
 * Author: Thabatta
 * Text Domain: thabatta-web-components
 */

if (!defined('ABSPATH')) {
    exit; // Saída direta se acessado diretamente
}

require_once __DIR__ . '/WebComponents/Domain/WebComponent.php';
require_once __DIR__ . '/WebComponents/Infrastructure/MetadataService.php';
require_once __DIR__ . '/WebComponents/Infrastructure/ComponentRepository.php';
require_once __DIR__ . '/WebComponents/Application/ComponentRegistrar.php';
require_once __DIR__ . '/WebComponents/Presentation/Renderer.php';
require_once __DIR__ . '/WebComponents/Presentation/ShortcodeHandler.php';
require_once __DIR__ . '/WebComponents/Presentation/MetaBox.php';
require_once __DIR__ . '/WebComponents/Presentation/Importer.php';
require_once __DIR__ . '/WebComponents/Presentation/AdminColumns.php';
require_once __DIR__ . '/WebComponents/Application/Plugin.php';

$plugin = new Thabatta\WebComponents\Application\Plugin();
$plugin->boot();
