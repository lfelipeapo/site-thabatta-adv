<?php
/**
 * Script para gerar o arquivo CSS de administração
 */

// Definir caminho para o arquivo de saída
$output_file = __DIR__ . '/assets/css/admin.css';

// Conteúdo do arquivo
$css_content = <<<'EOT'
/**
 * Estilos de administração para o tema Thabatta Advocacia
 * 
 * @package Thabatta_Advocacia
 */

/* Estilos gerais */
.thabatta-admin-header {
    margin: 20px 0;
    padding-bottom: 20px;
    border-bottom: 1px solid #ddd;
}

.thabatta-admin-header h1 {
    margin-bottom: 10px;
    color: #8B0000;
}

.thabatta-admin-header p {
    font-size: 14px;
    color: #666;
}

.thabatta-admin-section {
    margin: 30px 0;
    padding: 20px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.thabatta-admin-section h2 {
    margin-top: 0;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
    color: #8B0000;
}

.thabatta-admin-footer {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
    color: #666;
    font-size: 13px;
}

/* Seletor de mídia */
.thabatta-media-uploader {
    margin-bottom: 10px;
}

.thabatta-media-preview {
    margin-top: 10px;
    max-width: 300px;
}

.thabatta-media-preview img {
    max-width: 100%;
    height: auto;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 3px;
    background: #f9f9f9;
}

/* Tabs de administração */
.thabatta-admin-tabs-nav {
    margin-bottom: 20px;
    border-bottom: 1px solid #ccc;
}

.thabatta-admin-tab-content {
    display: none;
}

.thabatta-admin-tab-content.active {
    display: block;
}

/* Preview de cores */
.thabatta-color-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin: 20px 0;
}

.color-sample {
    width: 120px;
    height: 80px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: bold;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.thabatta-typography-preview {
    margin: 20px 0;
    padding: 20px;
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.thabatta-typography-preview h3 {
    margin-top: 0;
    color: #8B0000;
}

/* SEO Preview */
.thabatta-seo-preview {
    margin: 20px 0;
    padding: 20px;
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.thabatta-seo-preview-content {
    max-width: 600px;
    padding: 15px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: Arial, sans-serif;
}

.thabatta-seo-preview-title {
    color: #1a0dab;
    font-size: 18px;
    margin-bottom: 5px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.thabatta-seo-preview-url {
    color: #006621;
    font-size: 14px;
    margin-bottom: 5px;
}

.thabatta-seo-preview-description {
    color: #545454;
    font-size: 13px;
    line-height: 1.4;
}

.thabatta-seo-counter {
    display: inline-block;
    margin-left: 10px;
    font-size: 12px;
    color: #666;
}

.thabatta-seo-counter.thabatta-counter-warning {
    color: #d63638;
}

/* Posts relacionados */
.thabatta-related-posts-container {
    margin: 15px 0;
}

.thabatta-related-posts-list {
    margin-top: 15px;
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    max-height: 200px;
    overflow-y: auto;
}

.thabatta-related-posts-list li {
    padding: 8px 10px;
    margin-bottom: 5px;
    background: #fff;
    border: 1px solid #eee;
    border-radius: 3px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.thabatta-related-posts-list li .title {
    font-weight: bold;
}

.thabatta-related-posts-list li .type {
    color: #666;
    font-size: 12px;
}

.thabatta-related-posts-list li .thabatta-remove-related-post {
    color: #d63638;
    text-decoration: none;
}

/* Status de SEO */
.thabatta-seo-status {
    display: flex;
    align-items: center;
}

.thabatta-seo-indicator {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 5px;
}

.thabatta-seo-status.good .thabatta-seo-indicator {
    background-color: #46b450;
}

.thabatta-seo-status.ok .thabatta-seo-indicator {
    background-color: #ffb900;
}

.thabatta-seo-status.poor .thabatta-seo-indicator {
    background-color: #dc3232;
}

.thabatta-seo-status.warning .thabatta-seo-indicator {
    background-color: #00a0d2;
}

/* Dashboard Widget */
.thabatta-dashboard-widget {
    padding: 0 12px;
}

.thabatta-dashboard-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 20px;
}

.thabatta-stat-item {
    flex: 1;
    min-width: 100px;
    padding: 15px;
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-align: center;
}

.thabatta-stat-number {
    display: block;
    font-size: 24px;
    font-weight: bold;
    color: #8B0000;
    margin-bottom: 5px;
}

.thabatta-stat-label {
    display: block;
    font-size: 13px;
    color: #666;
}

.thabatta-dashboard-recent {
    margin-bottom: 20px;
}

.thabatta-dashboard-recent h3 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
    color: #8B0000;
}

.thabatta-dashboard-recent ul {
    margin: 0;
}

.thabatta-dashboard-recent li {
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.thabatta-dashboard-recent li:last-child {
    border-bottom: none;
}

.thabatta-dashboard-recent .post-date {
    color: #666;
    font-size: 12px;
    margin-left: 10px;
}

.thabatta-dashboard-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 15px;
}

/* Jetpack Cache */
.thabatta-jetpack-cache-section {
    margin-bottom: 30px;
}

.thabatta-jetpack-cache-section h3 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.thabatta-cache-status {
    margin: 15px 0;
    min-height: 30px;
}

.thabatta-cache-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

/* Sortable */
.thabatta-sortable-list {
    margin: 15px 0;
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
}

.thabatta-sortable-item {
    padding: 10px;
    margin-bottom: 10px;
    background: #fff;
    border: 1px solid #eee;
    border-radius: 3px;
    display: flex;
    align-items: center;
}

.thabatta-sortable-handle {
    cursor: move;
    margin-right: 10px;
    color: #666;
}

.thabatta-sortable-content {
    flex: 1;
}

.thabatta-sortable-actions {
    margin-left: 10px;
}

/* Responsividade */
@media screen and (max-width: 782px) {
    .thabatta-dashboard-stats {
        flex-direction: column;
    }
    
    .thabatta-stat-item {
        width: 100%;
    }
    
    .thabatta-cache-actions {
        flex-direction: column;
    }
    
    .thabatta-cache-actions .button {
        margin-bottom: 10px;
    }
}
EOT;

// Criar diretório se não existir
if (!file_exists(dirname($output_file))) {
    mkdir(dirname($output_file), 0755, true);
}

// Escrever arquivo
file_put_contents($output_file, $css_content);

echo "Arquivo CSS de administração gerado com sucesso em: $output_file\n";
