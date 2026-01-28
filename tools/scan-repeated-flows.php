<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$themeDir = $root . '/data/wp-content/themes/thabatta-adv-theme';
$reportPath = $root . '/reports/repeated-flows.md';

$targets = [
    'functions.php',
    'front-page.php',
    'page-blog.php',
    'page.php',
    'single.php',
    'index.php',
    'inc',
    'templates',
    'template-parts',
    'partials',
    'shortcodes',
    'blocks',
    'ajax',
];

$files = [];
foreach ($targets as $target) {
    $path = $themeDir . '/' . $target;
    if (is_file($path) && str_ends_with($path, '.php')) {
        $files[] = $path;
        continue;
    }

    if (!is_dir($path)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $fileInfo) {
        if ($fileInfo->isFile() && str_ends_with($fileInfo->getFilename(), '.php')) {
            $files[] = $fileInfo->getPathname();
        }
    }
}

$queries = [];
$loopMatches = [];
$escapeClusters = [];

foreach ($files as $file) {
    $contents = file_get_contents($file);
    if ($contents === false) {
        continue;
    }

    if (preg_match_all('/new\s+WP_Query\s*\(([^;]+)\);/m', $contents, $matches, PREG_OFFSET_CAPTURE)) {
        foreach ($matches[1] as $matchIndex => $match) {
            $args = preg_replace('/\s+/', ' ', trim($match[0]));
            $offset = $matches[0][$matchIndex][1];
            $line = substr_count($contents, "\n", 0, $offset) + 1;
            $snippet = get_line_snippet($contents, $line);
            $queries[$args][] = [
                'file' => $file,
                'line' => $line,
                'snippet' => $snippet,
            ];
        }
    }

    if (preg_match_all('/if\s*\(\s*have_posts\(\)\s*\)\s*:\s*while\s*\(\s*have_posts\(\)\s*\)\s*:\s*the_post\(\)\s*;/m', $contents, $matches, PREG_OFFSET_CAPTURE)) {
        foreach ($matches[0] as $match) {
            $offset = $match[1];
            $line = substr_count($contents, "\n", 0, $offset) + 1;
            $loopMatches[] = [
                'file' => $file,
                'line' => $line,
                'snippet' => get_line_snippet($contents, $line),
            ];
        }
    }

    if (preg_match_all('/\b(esc_html|esc_attr|esc_url|wp_kses_post|sanitize_text_field|sanitize_textarea_field|sanitize_email)\s*\(/', $contents, $matches, PREG_OFFSET_CAPTURE)) {
        $count = count($matches[0]);
        if ($count >= 5) {
            $samples = [];
            foreach (array_slice($matches[0], 0, 3) as $sample) {
                $offset = $sample[1];
                $line = substr_count($contents, "\n", 0, $offset) + 1;
                $samples[] = [
                    'line' => $line,
                    'snippet' => get_line_snippet($contents, $line),
                ];
            }
            $escapeClusters[] = [
                'file' => $file,
                'count' => $count,
                'samples' => $samples,
            ];
        }
    }
}

$repeatedQueries = [];
foreach ($queries as $args => $entries) {
    if (count($entries) > 1) {
        $repeatedQueries[$args] = $entries;
    }
}

$report = [];
$report[] = '# Relatório de fluxos repetidos';
$report[] = '';
$report[] = 'Gerado em: ' . date('Y-m-d H:i:s');
$report[] = '';
$report[] = '## WP_Query similares';

if (empty($repeatedQueries)) {
    $report[] = '- Nenhum bloco de WP_Query repetido com os mesmos argumentos foi detectado.';
} else {
    foreach ($repeatedQueries as $args => $entries) {
        $report[] = '';
        $report[] = '- **Args normalizados:** `' . $args . '`';
        foreach ($entries as $entry) {
            $report[] = '  - ' . format_location($root, $entry['file'], $entry['line']) . ': `' . $entry['snippet'] . '`';
        }
        $report[] = '  - **Recomendação:** extrair a query para `src/Repository` e reutilizar.';
    }
}

$report[] = '';
$report[] = '## Loops repetidos (have_posts / the_post)';

if (empty($loopMatches)) {
    $report[] = '- Nenhum loop padrão detectado.';
} else {
    foreach ($loopMatches as $entry) {
        $report[] = '- ' . format_location($root, $entry['file'], $entry['line']) . ': `' . $entry['snippet'] . '`';
    }
    $report[] = '- **Recomendação:** mover blocos de renderização para `template-parts/`.';
}

$report[] = '';
$report[] = '## Sanitização/escape repetidos';

if (empty($escapeClusters)) {
    $report[] = '- Nenhum arquivo com uso concentrado de sanitização foi identificado.';
} else {
    foreach ($escapeClusters as $entry) {
        $report[] = '- **Arquivo:** ' . format_location($root, $entry['file'], 1) . ' (ocorrências: ' . $entry['count'] . ')';
        foreach ($entry['samples'] as $sample) {
            $report[] = '  - Linha ' . $sample['line'] . ': `' . $sample['snippet'] . '`';
        }
        $report[] = '  - **Recomendação:** criar helper em `src/Service` para sanitização padrão.';
    }
}

$report[] = '';
$report[] = '## Próximos passos sugeridos';
$report[] = '- Consolidar queries repetidas em `src/Repository`. '; 
$report[] = '- Centralizar renderizações similares em `template-parts/`. ';
$report[] = '- Criar serviços reutilizáveis em `src/Service` para regras de negócio e sanitização.';

file_put_contents($reportPath, implode(PHP_EOL, $report) . PHP_EOL);

$summary = sprintf(
    "Relatório salvo em %s (WP_Query repetidos: %d, loops: %d, arquivos com sanitização concentrada: %d).\n",
    $reportPath,
    count($repeatedQueries),
    count($loopMatches),
    count($escapeClusters)
);

echo $summary;

function get_line_snippet(string $contents, int $line): string
{
    $lines = explode("\n", $contents);
    $index = max(0, $line - 1);
    if (!isset($lines[$index])) {
        return '';
    }
    return trim($lines[$index]);
}

function format_location(string $root, string $file, int $line): string
{
    $relative = ltrim(str_replace($root . '/', '', $file), '/');
    return $relative . ':' . $line;
}
