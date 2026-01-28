# Relatório inicial de duplicação

## Resumo
- **WP_Query com argumentos reutilizados:** `front-page.php`, `page-blog.php` e `inc/acf-fields.php` usam `$args` repetidamente.
- **WP_Query com argumentos similares:** `inc/template-functions.php` contém `$related_args` duplicados.
- **Loops de renderização semelhantes:** há múltiplos blocos de renderização de listas no `front-page.php` (áreas, depoimentos, equipe e posts).

> Observação: este relatório inicial foi gerado com base no scan de fluxos repetidos (`composer flows:scan`) e inspeção rápida. Use `composer cpd` e `composer cpd:js` para obter o relatório completo de duplicação em PHP e templates/JS/CSS.

## Recomendações imediatas
1. Centralizar queries repetidas em `src/Repository/`.
2. Extrair blocos de listagens repetidas para `template-parts/`.
3. Consolidar helpers de sanitização/escape em `src/Service/`.
