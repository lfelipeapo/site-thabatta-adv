<?php
/**
 * Layout principal
 * 
 * Layout padrão para as páginas do site
 * 
 * @package WPFramework
 */

// Previne acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

// Obtém o conteúdo da view
$content = isset($content) ? $content : '';
?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title"><?php echo isset($title) ? esc_html($title) : ''; ?></h1>
    </div>
    
    <div class="page-content">
        <?php echo $content; ?>
    </div>
</div>
