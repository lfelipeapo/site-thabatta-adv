# Pipeline de qualidade (Theme Thabatta Advocacia)

## Objetivo
Garantir padronização, detectar duplicações e prevenir regressões no tema customizado.

## Pré-requisitos
- PHP 8.x
- Composer
- Node.js + npm

## Instalação
```bash
cd data
composer install

cd ../data/wp-content/themes/thabatta-adv-theme
npm install
```

## Comandos principais (Composer)
> Executar a partir de `data/`.

- **Lint (PHPCS/WPCS)**
  ```bash
  composer lint
  ```
- **Lint com correção automática**
  ```bash
  composer lint:fix
  ```
- **Análise estática (PHPStan)**
  ```bash
  composer stan
  ```
- **Detecção de duplicação (PHP)**
  ```bash
  composer cpd
  ```
- **Detecção de duplicação (templates/JS/CSS)**
  ```bash
  composer cpd:js
  ```
- **Scan de fluxos repetidos (convenções do tema)**
  ```bash
  composer flows:scan
  ```
- **Pipeline completa**
  ```bash
  composer quality
  ```

## Baseline (PHPStan)
- `data/phpstan-baseline.neon` guarda o baseline inicial.
- Se surgirem falsos positivos, gere um novo baseline e revise o arquivo gradualmente.

## Duplicação
- PHP: `phpcpd.xml`
- Templates/JS/CSS: `.jscpd.json`

Se o relatório apontar repetição real:
1. **Extrair para** `src/Repository/` quando for consulta (`WP_Query`).
2. **Extrair para** `template-parts/` quando for renderização repetida.
3. **Extrair para** `src/Service/` quando for regra de negócio/sanitização.

## Pre-commit
Instale o pre-commit (uma vez):
```bash
pip install pre-commit
pre-commit install
```

O hook roda PHPCS apenas nos arquivos PHP do tema alterados.

## CI (GitHub Actions)
O workflow `quality.yml` executa `composer quality` em PRs.

## Estrutura recomendada (convenções internas)
- `src/Repository/` → Queries reutilizáveis (WP_Query)
- `template-parts/` → Renderização repetida
- `src/Service/` → Regras de negócio e sanitização
