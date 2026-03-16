

# Plano Técnico: Plugin WordPress de Integração com IA (Groq + Neuron PHP)

## 1. Arquitetura do Plugin

### 1.1 Estrutura de Diretórios e Arquivos

A arquitetura de diretórios de um plugin WordPress avançado que integra React no admin e comunicação com APIs externas de IA exige uma organização meticulosa que separe responsabilidades, facilite a manutenção e permita escalabilidade. A estrutura proposta segue as convenções modernas de desenvolvimento WordPress, incorporando práticas do ecossistema de plugins enterprise e adaptando-se às necessidades específicas de integração com inteligência artificial.

#### 1.1.1 Arquivo principal do plugin (plugin-name.php)

O arquivo principal do plugin, tipicamente nomeado com o slug do projeto (por exemplo, `ai-content-generator.php`), serve como ponto de entrada único e orquestrador de toda a funcionalidade. Este arquivo deve conter o **cabeçalho de documentação padronizado do WordPress** com campos obrigatórios: `Plugin Name`, `Plugin URI`, `Description`, `Version`, `Author`, `Author URI`, `License`, `License URI`, `Text Domain`, `Domain Path`, e requisitos mínimos `Requires at least` e `Requires PHP`. Além do cabeçalho, este arquivo é responsável por definir **constantes globais do plugin** como `AICG_VERSION` (para cache-busting de assets), `AICG_PLUGIN_DIR` (caminho absoluto no filesystem), `AICG_PLUGIN_URL` (URL para enqueue de assets), e `AICG_MIN_WP_VERSION` (para verificação de compatibilidade).

A inicialização do plugin segue o padrão de **classe Singleton** para garantir uma única instância durante o ciclo de vida da requisição. O construtor privado registra hooks de ativação, desativação e desinstalação, carrega o autoloader de classes (PSR-4 recomendado), e inicializa os componentes principais em ordem de dependência. A verificação de compatibilidade com a versão mínima do WordPress e PHP deve ocorrer antes de qualquer inicialização, exibindo notice administrativo e impedindo funcionamento em ambientes incompatíveis. O arquivo principal permanece enxuto, delegando toda a implementação específica para classes especializadas no diretório `/includes` .

#### 1.1.2 Diretório /includes para classes PHP

O diretório `/includes` concentra toda a lógica de negócio do plugin em classes PHP bem definidas, seguindo os princípios de **orientação a objetos e separação de concerns**. A organização interna em subdiretórios temáticos reflete a arquitetura em camadas do sistema:

| Subdiretório | Classes Principais | Responsabilidade |
|:---|:---|:---|
| `/includes/Core/` | `Plugin.php`, `Activator.php`, `Deactivator.php` | Inicialização, ciclo de vida do plugin |
| `/includes/API/` | `GroqClient.php`, `NeuronAdapter.php`, `ResponseParser.php` | Comunicação com API Groq via Neuron PHP |
| `/includes/Admin/` | `Menu.php`, `Assets.php`, `Settings.php` | Interface administrativa, enqueue de assets |
| `/includes/Content/` | `PostCreator.php`, `PageCreator.php`, `Scheduler.php` | Criação e agendamento de conteúdo |
| `/includes/Media/` | `ImageDownloader.php`, `AttachmentHandler.php`, `FeaturedImage.php` | Processamento de mídia e imagens destacadas |
| `/includes/SEO/` | `SEOIntegration.php`, `YoastAdapter.php`, `RankMathAdapter.php` | Integração com plugins de SEO |
| `/includes/Security/` | `Encryption.php`, `RateLimiter.php`, `Validator.php` | Criptografia, validação, proteção de dados |
| `/includes/REST/` | `GenerationController.php`, `StatusController.php`, `HistoryController.php` | Endpoints da API REST customizada |

Cada classe utiliza **namespaces PSR-4** como `AICG\API\GroqClient`, eliminando colisões de nomes e permitindo autoloading eficiente. A documentação PHPDoc é obrigatória para todos os métodos públicos, especificando `@param` tipados, `@return`, `@throws`, e `@since` para versionamento .

#### 1.1.3 Diretório /admin para páginas de administração

O diretório `/admin` abriga arquivos PHP específicos para renderização de páginas administrativas que não são implementadas em React, além de templates e partials reutilizáveis. A estrutura inclui: `/admin/partials/` para templates HTML que renderizam seções de configuração, `/admin/views/` para páginas completas (como wizard de onboarding), e `/admin/class-admin.php` como classe central de inicialização da interface administrativa. A **separação entre lógica de registro de menus e apresentação visual** permite testes unitários mais eficazes e facilita a manutenção. Para o plugin de IA, a página principal de geração de conteúdo é implementada predominantemente em React, com o PHP atuando como ponto de montagem e injeção de dados iniciais .

#### 1.1.4 Diretório /assets para CSS, JS e recursos estáticos

O diretório `/assets` centraliza recursos estáticos que **não passam pelo processo de build do React**: folhas de estilo CSS adicionais, scripts JavaScript legado para fallback, imagens, ícones, fontes, e arquivos de localização. A organização em subpastas por tipo (`/assets/css/`, `/assets/js/`, `/assets/images/`, `/assets/fonts/`) facilita o versionamento de assets e a implementação de estratégias de cache. Os arquivos neste diretório são enfileirados via `wp_enqueue_style()` e `wp_enqueue_script()` com **handles prefixados** (`aicg-admin-styles`, `aicg-vendor-scripts`) e versionamento dinâmico baseado em `AICG_VERSION` ou timestamp de modificação .

#### 1.1.5 Diretório /src para componentes React (build com @wordpress/scripts)

O diretório `/src` contém o **código-fonte da aplicação React** que constitui a interface principal de interação com a IA. A estrutura interna segue convenções modernas de desenvolvimento React:

| Subdiretório | Conteúdo | Convenções |
|:---|:---|:---|
| `/src/components/` | Componentes reutilizáveis (Button, Input, Modal, Spinner) | Functional components, props tipadas |
| `/src/features/` | Módulos de funcionalidade coesos (PromptForm, PreviewPanel, ScheduleControls) | Composição de componentes, lógica encapsulada |
| `/src/hooks/` | Custom hooks (`useApiRequest`, `useContentGeneration`, `useLocalStorage`) | Regras de hooks, dependências explícitas |
| `/src/store/` | Gerenciamento de estado global (se necessário além de hooks) | Context API ou `@wordpress/data` |
| `/src/utils/` | Funções auxiliares puras (formatting, validation, parsers) | Testáveis, sem side effects |
| `/src/styles/` | Estilos SCSS/CSS-in-JS por componente | CSS Modules ou styled-components |

O build é orquestrado pelo **`@wordpress/scripts`**, pacote npm mantido pela equipe do WordPress que abstrai a complexidade de webpack, Babel, ESLint e PostCSS. Os scripts `npm run start` (desenvolvimento com HMR) e `npm run build` (produção otimizada) geram arquivos em `/build/` com hashes de conteúdo para cache-busting, além de `index.asset.php` contendo dependências e versão detectadas automaticamente .

### 1.2 Padrões de Codificação WordPress

#### 1.2.1 Conformidade com WordPress Coding Standards (PHP e JavaScript)

A conformidade com os **WordPress Coding Standards** é imperativa para garantir qualidade de código, segurança e compatibilidade com o ecossistema. Para PHP, os padrões especificam: **tabs para indentação** (não espaços), chaves de abertura em novas linhas para classes e métodos, nomenclatura em `snake_case` para funções e variáveis, `PascalCase` para classes, e `UPPER_CASE` para constantes. A ferramenta **PHP_CodeSniffer com regras WPCS** deve ser integrada ao workflow de desenvolvimento, idealmente via pre-commit hooks .

Para JavaScript, o WordPress adota configuração ESLint baseada em Airbnb com modificações específicas. O **`@wordpress/scripts`** já inclui a configuração apropriada, simplificando a conformidade. Destaca-se: uso de ES6+ com transpilação, preferência por `const`/`let` sobre `var`, arrow functions para callbacks, e destructuring extensivo. A consistência de estilo é verificada automaticamente, impedindo que código não-conforme seja versionado .

#### 1.2.2 Uso de namespaces para evitar conflitos

O uso de **namespaces PHP** é obrigatório em plugins modernos para prevenção de colisões de nomes. O namespace raiz `AICG` (AI Content Generator) com sub-namespaces refletindo a estrutura de diretórios — `AICG\API`, `AICG\Admin`, `AICG\Content`, `AICG\Security` — cria hierarquia clara e previsível. A declaração `namespace AICG\API;` aparece na primeira linha executável do arquivo, após a verificação de `ABSPATH`. Para funções procedurais que necessitam exposição global, recomenda-se encapsulá-las em classes e registrar via array de callable `[$this, 'method_name']`, ou utilizar o namespace na declaração e referenciá-las com fully qualified name .

#### 1.2.3 Prefixação consistente de funções, classes e hooks

Apesar do uso de namespaces, a **prefixação permanece essencial** para: nomes de hooks (actions e filters), nomes de opções no banco de dados, meta keys de postmeta, e handles de scripts/estilos. O prefixo `aicg_` (AI Content Generator) é aplicado sistematicamente:

| Contexto | Exemplo | Justificativa |
|:---|:---|:---|
| Hook de ativação | `aicg_activate_plugin` | Previne colisão com outros plugins |
| Nome de opção | `aicg_groq_api_key` | Identificação clara de origem |
| Meta key de post | `_aicg_generation_prompt` | Oculta de custom fields padrão (underscore) |
| Handle de script | `aicg-admin-app` | Organização no enqueue |
| Capability custom | `aicg_generate_content` | Granularidade de permissões |

Esta disciplina de nomenclatura é crítica em ambientes multi-plugin onde a probabilidade de colisão é alta .

## 2. Integração com API Groq via Neuron PHP

### 2.1 Configuração da Comunicação API

#### 2.1.1 Armazenamento seguro da chave API (wp_options com criptografia)

A **chave de API Groq** constitui o dado mais sensível do plugin, requerendo proteção criptográfica robusta. A abordagem recomendada utiliza **criptografia bidirecional com libsodium**, disponível nativamente no PHP 7.2+. O processo envolve: derivação de chave de criptografia a partir das constantes de segurança únicas do WordPress (`AUTH_KEY`, `SECURE_AUTH_KEY`, `AUTH_SALT`, `SECURE_AUTH_SALT`), criptografia da chave API com `sodium_crypto_secretbox()` (que inclui autenticação), armazenamento do **nonce + ciphertext** na opção `aicg_api_key_encrypted`, e decriptação transparente quando necessário para requisições. Para ambientes que não suportam libsodium, fallback para `wp_hash()` com armazenamento de hash para verificação e campo separado para valor ofuscado, embora com funcionalidade reduzida .

A opção é registrada com **`autoload => false`** para evitar carregamento desnecessário em todas as requisições, e o acesso é restrito a usuários com capability `manage_options`. A interface de configuração valida a chave em tempo real via requisição de teste à API Groq, fornecendo feedback imediato ao administrador .

#### 2.1.2 Classe wrapper para requisições HTTP usando WP_Http

A classe **`AICG\API\GroqClient`** encapsula toda a comunicação com a API Groq, estendendo ou compondo a classe `WP_Http` nativa do WordPress. Esta abstração garante compatibilidade cross-platform (cURL, streams, fsockopen) e permite que o WordPress gerencie proxies, SSL verification, e timeouts. A configuração interna inclui: **URL base da API** (`https://api.groq.com/openai/v1/`), headers padrão (`Authorization: Bearer {key}`, `Content-Type: application/json`), e timeouts configuráveis (30-60 segundos para operações de geração).

Métodos públicos da classe:

| Método | Parâmetros | Retorno | Descrição |
|:---|:---|:---|:---|
| `generateContent()` | `$prompt`, `$options = []` | `array\|WP_Error` | Geração completa de conteúdo |
| `getAvailableModels()` | — | `array` | Lista modelos acessíveis |
| `validateConnection()` | — | `bool` | Testa autenticação |
| `getUsageStats()` | — | `array` | Métricas de consumo |

A classe implementa **logging estruturado** de todas as requisições e respostas, com níveis apropriados (DEBUG para sucesso, WARNING para retry, ERROR para falha final), sempre **excluindo a chave API e conteúdo sensível de prompts** .

#### 2.1.3 Implementação de retry logic e timeout handling

A **estratégia de retry com backoff exponencial** é essencial para resiliência em comunicação com serviços de IA:

| Tentativa | Delay Antes | Condições de Retry |
|:---|:---|:---|
| 1ª | 0s (imediata) | Timeout de conexão, erro 5xx, 429 com retry-after |
| 2ª | 1s + jitter | Mesmas condições, exceto 401/403 (auth permanente) |
| 3ª | 2s + jitter | Última tentativa antes de falha |
| 4ª | 4s + jitter | Opcional, configurável |

O **jitter aleatório (±20%)** previne thundering herd em cenários de alta concorrência. Cada falha é logada com contexto (timestamp, endpoint, código HTTP, mensagem sanitizada), e o tempo total de retry é limitado a não exceder o timeout máximo configurado. Erros de autenticação (401, 403) **não são candidatos a retry**, resultando em falha imediata com notificação ao administrador .

#### 2.1.4 Validação de respostas e tratamento de erros

A resposta da API Groq passa por **validação em múltiplas camadas**: verificação de HTTP status code (200 esperado), validação de Content-Type (`application/json`), parsing JSON com `json_decode()` e verificação de `JSON_ERROR_NONE`, e **validação de schema** contra estrutura esperada. Erros são categorizados e tratados diferenciadamente:

| Categoria | Códigos HTTP | Ação | Mensagem ao Usuário |
|:---|:---|:---|:---|
| Autenticação | 401, 403 | Falha imediata, notificar admin | "Erro de autenticação. Verifique configurações." |
| Rate Limit | 429 | Retry com backoff, respeitar `Retry-After` | "Serviço temporariamente ocupado. Tentando..." |
| Servidor | 500, 502, 503, 504 | Retry limitado (3x) | "Erro temporário. Por favor, tente novamente." |
| Validação | 400 | Log detalhado, não retry | "Solicitação inválida. Contate suporte." |
| Timeout | — | Retry com aumento de timeout | "Processamento demorado. Aguarde..." |

Cada erro gera **exceção PHP específica** (`AICG_HTTP_Exception`, `AICG_API_Exception`, `AICG_Parse_Exception`) para tratamento adequado nas camadas superiores .

### 2.2 Estrutura do Payload e Resposta

#### 2.2.1 Formato do prompt enviado (contexto do site, tom de voz, estrutura desejada)

O **prompt estruturado para API Groq** combina múltiplas camadas de contexto para maximizar qualidade do conteúdo gerado:

```json
{
  "model": "llama-3.3-70b-versatile",
  "messages": [
    {
      "role": "system",
      "content": "Você é um assistente de redação especializado em [NICHO]. Site: [NOME_SITE]. Público-alvo: [PUBLICO]. Tom de voz: [TOM]. Diretrizes: [RESTRICOES]."
    },
    {
      "role": "user",
      "content": "Crie [TIPO_CONTEUDO] sobre: [TEMA]. Estrutura: [ESTRUTURA]. Comprimento: [COMPRIMENTO]. Inclua: [ELEMENTOS]. Responda APENAS em JSON válido conforme schema especificado."
    }
  ],
  "temperature": 0.7,
  "max_tokens": 4000,
  "response_format": { "type": "json_object" }
```

As variáveis em caixa alta são substituídas dinamicamente a partir de: **configurações globais do plugin** (tom de voz padrão, diretrizes de marca), **informações do site** (título, descrição, opções do Customizer), **seleções do usuário** no formulário React, e **contexto adicional** como posts de referência para consistência de voz .

#### 2.2.2 Parsing da resposta JSON: post (título, conteúdo HTML, status), media (URL da imagem), seo (meta descrição, keywords)

A **estrutura de resposta esperada** da IA é rigorosamente definida para parsing confiável:

| Campo JSON | Tipo | Obrigatório | Validação | Uso |
|:---|:---|:---|:---|:---|
| `post.title` | string | Sim | ≤100 chars, no HTML | `post_title` |
| `post.content` | string | Sim | HTML válido, tags permitidas | `post_content` |
| `post.excerpt` | string | Não | ≤300 chars | `post_excerpt` |
| `post.status` | string | Sim | enum: draft, future | `post_status` |
| `post.date` | string | Condicional | ISO 8601, futuro | `post_date` se future |
| `media.image_url` | string | Não | URL válida, https | Download featured image |
| `media.image_alt` | string | Não | ≤125 chars | Alt text acessibilidade |
| `seo.meta_title` | string | Não | ≤60 chars | Título SEO |
| `seo.meta_description` | string | Não | 150-160 chars | Meta description |
| `seo.focus_keyword` | string | Não | ≤10 palavras | Keyword principal |
| `seo.keywords` | array | Não | array de strings | Keywords secundárias |

O **parsing defensivo** implementa: extração de JSON de possível envoltório markdown (```json...```), validação de presença de campos obrigatórios, tipagem de valores, e aplicação de defaults para campos opcionais ausentes. Falhas em qualquer etapa resultam em `WP_Error` com código `aicg_invalid_response` e mensagem localizada .

#### 2.2.3 Sanitização de dados recebidos antes do processamento

A **sanitização contextual** aplica funções específicas por tipo de dado:

| Tipo de Dado | Função de Sanitização | Observações |
|:---|:---|:---|
| Título, excerpt | `sanitize_text_field()` | Remove tags HTML, entidades, caracteres de controle |
| Conteúdo HTML | `wp_kses_post()` | Whitelist de tags seguras; filtra atributos perigosos |
| URLs | `esc_url_raw()` + `wp_validate_redirect()` | Validação de protocolo e domínio |
| Keywords | `array_map('sanitize_text_field', $arr)` | Cada elemento individualmente |
| Datas ISO 8601 | `rest_parse_date()` | Conversão para timestamp Unix |
| Inteiros (tokens, etc.) | `absint()` | Garante valor não-negativo |

Esta camada de sanitização ocorre **imediatamente após parsing**, antes de qualquer persistência ou processamento adicional, estabelecendo defesa em profundidade contra injeção de código malicioso .

### 2.3 Otimização de Performance

#### 2.3.1 Uso de transients para cache de respostas similares

O **sistema de cache de prompts** reduz custos de API e melhora tempo de resposta:

| Aspecto | Implementação |
|:---|:---|
| Chave de cache | `sha256(prompt_normalizado)` — lowercase, trim, remoção de espaços extras |
| TTL padrão | 24 horas (configurável) |
| Invalidação | Explícita via botão "Regenerar", ou mudança de configurações globais |
| Similaridade fuzzy | Opcional: embeddings locais com `wp_hash()` para detecção de variações menores |

Antes de cada requisição à API Groq, verifica-se `get_transient('aicg_cache_' . $hash)`; em cache hit, retorna-se diretamente após validação de freshness. O **hit rate de cache** é monitorado para avaliação de eficácia da estratégia .

#### 2.3.2 Processamento assíncrono via WP Cron para geração em background

A **arquitetura de jobs assíncronos** endereça o desafio de latência em gerações complexas:

| Etapa | Ação | Responsável |
|:---|:---|:---|
| 1. Submissão | Usuário envia prompt via React | Frontend |
| 2. Registro | Cria job em tabela `aicg_jobs` com status `pending` | Endpoint REST |
| 3. Agendamento | `wp_schedule_single_event()` para execução imediata | Endpoint REST |
| 4. Resposta imediata | Retorna `job_id` ao frontend | Endpoint REST |
| 5. Polling | Frontend consulta `GET /status/{job_id}` a cada 2-5s | Frontend |
| 6. Execução | Cron processa chamada API, parsing, criação de post | WP Cron |
| 7. Atualização | Atualiza job para `completed` ou `failed` com resultado | WP Cron |

A tabela `aicg_jobs` estrutura-se com: `job_id` (UUID), `user_id`, `status`, `created_at`, `started_at`, `completed_at`, `prompt_hash`, `result_data` (JSON), `error_message`. Esta abordagem permite **gerações de qualquer duração** sem risco de timeout HTTP, além de facilitar filas de prioridade e retry automático .

#### 2.3.3 Limitação de taxa de requisições para evitar throttling

O **sistema de rate limiting em múltiplas camadas** protege contra abuso e respeita quotas da API Groq:

| Nível | Identificador | Limite | Janela |
|:---|:---|:---|:---|
| Por usuário | `user_{$user_id}` | 10 requisições | 1 minuto |
| Por usuário | `user_{$user_id}_hour` | 50 requisições | 1 hora |
| Por IP | `ip_{$ip_hash}` | 5 requisições | 1 minuto (não autenticado) |
| Global site | `site_global` | 1000 requisições | 1 hora |

A implementação utiliza **transients do WordPress** para contadores atômicos, com verificação prévia de limite e incremento em operação atômica quando disponível (Redis, Memcached). Excedido o limite, retorna-se **HTTP 429** com headers `X-RateLimit-Limit`, `X-RateLimit-Remaining` (0), `X-RateLimit-Reset` (timestamp), e `Retry-After` (segundos) .

## 3. Interface de Administração com React

### 3.1 Configuração do Ambiente React

#### 3.1.1 Configuração do @wordpress/scripts para build

O **`@wordpress/scripts`** é o toolkit oficial do WordPress para desenvolvimento JavaScript moderno, eliminando a necessidade de configuração manual de webpack, Babel, ESLint e PostCSS. A instalação via `npm install @wordpress/scripts --save-dev` seguida da configuração de scripts no `package.json`:

```json
{
  "scripts": {
    "start": "wp-scripts start",
    "build": "wp-scripts build",
    "test": "wp-scripts test-unit",
    "lint:js": "wp-scripts lint-js",
    "lint:css": "wp-scripts lint-style",
    "packages-update": "wp-scripts packages-update"
  }
}
```

O ambiente de desenvolvimento inclui **Hot Module Replacement (HMR)** para feedback imediato de alterações, enquanto o build de produção aplica: minificação de JavaScript e CSS, tree shaking para eliminação de código morto, code splitting para chunks otimizados, e geração de source maps para debugging. O arquivo `index.asset.php` gerado automaticamente contém o array de dependências detectadas (react, react-dom, wp-components, wp-api-fetch, etc.) e hash de versão para cache-busting .

#### 3.1.2 Estrutura de componentes: App, PromptForm, PreviewPanel, SettingsPanel

A **arquitetura de componentes** organiza a interface em unidades coesas de funcionalidade:

| Componente | Responsabilidade | Estado Gerenciado |
|:---|:---|:---|
| `App` | Container raiz, roteamento, providers globais | Tema, autenticação, notificações |
| `PromptForm` | Entrada de prompt, seleção de parâmetros, submissão | Valores de formulário, validação, loading |
| `PreviewPanel` | Visualização do conteúdo gerado, edição inline | Modo de visualização, conteúdo editado |
| `SettingsPanel` | Configurações de API, preferências globais | Configurações persistidas, dirty state |
| `ScheduleControls` | Seleção de data/hora de agendamento | Data selecionada, timezone |
| `StatusIndicator` | Feedback de progresso, estados de geração | Estado atual, progresso percentual |
| `ContentTypeSelector` | Toggle post/page com implicações visuais | Tipo selecionado |

A composição hierárquica mantém **unidirectional data flow**: estado elevado a `App` ou custom hooks quando compartilhado, estado local com `useState` quando isolado .

#### 3.1.3 Enfileiramento de assets com wp_enqueue_script e wp_localize_script

O **enfileiramento no PHP** integra o build React ao sistema de scripts do WordPress:

```php
function aicg_enqueue_admin_assets($hook) {
    if ('toplevel_page_ai-content-generator' !== $hook) {
        return;
    }

    $asset_file = AICG_PLUGIN_DIR . 'build/index.asset.php';
    $asset = require $asset_file;

    wp_enqueue_script(
        'aicg-admin-app',
        AICG_PLUGIN_URL . 'build/index.js',
        $asset['dependencies'],
        $asset['version'],
        true // in_footer
    );

    wp_enqueue_style(
        'aicg-admin-app',
        AICG_PLUGIN_URL . 'build/index.css',
        [],
        $asset['version']
    );

    // Dados injetados do servidor para o React
    wp_localize_script('aicg-admin-app', 'aicgData', [
        'restUrl'       => esc_url_raw(rest_url()),
        'nonce'         => wp_create_nonce('wp_rest'),
        'apiStatus'     => aicg_get_api_status(),
        'userPrefs'     => aicg_get_user_preferences(),
        'i18n'          => aicg_get_jed_locale_data(),
    ]);
}
add_action('admin_enqueue_scripts', 'aicg_enqueue_admin_assets');
```

A função `wp_localize_script()` cria a **ponte de dados essencial**: URL base da REST API para requisições `api-fetch`, nonce de autenticação, status de configuração da API, preferências do usuário, e strings traduzidas para internacionalização .

### 3.2 Componentes da Interface

#### 3.2.1 Formulário de entrada de prompt com rich text básico

O **formulário de prompt** é o ponto de interação primário, implementando:

- **Textarea expansível** com auto-resize conforme conteúdo, utilizando `react-textarea-autosize` ou lógica customizada com refs
- **Contador de caracteres/tokens** com alerta visual ao aproximar limites (tipicamente 4000 tokens para modelos Groq)
- **Templates de prompt pré-definidos** acessíveis via dropdown, categorizados por tipo de conteúdo (blog post, página de produto, descrição de serviço, etc.)
- **Histórico de prompts recentes** do usuário, persistido em `localStorage` ou user meta
- **Validação em tempo real** com feedback visual: verde para válido, amarelo para atenção (prompt curto), vermelho para inválido (vazio ou excessivo)

A experiência de rich text é implementada via **markdown leve** ou toolbar minimalista com `@wordpress/rich-text`, mantendo o valor submetido como texto plano para processamento pela IA .

#### 3.2.2 Seletor de tipo de conteúdo (post vs. page)

O **seletor de tipo** influencia campos adicionais exibidos e comportamento de inserção:

| Tipo | Campos Condicionais | Implicações |
|:---|:---|:---|
| **Post** | Categoria, tags, formato de post, sticky | Herda templates de archive, exibe em feeds |
| **Page** | Template de página, página pai, ordem | Hierarquia navegável, excluído de feeds por padrão |

A seleção é persistida em `localStorage` para conveniência entre sessões, e a interface adapta-se com animação de transição suave .

#### 3.2.3 Painel de preview do conteúdo gerado

O **painel de preview** apresenta o conteúdo em múltiplos modos:

| Modo | Implementação | Propósito |
|:---|:---|:---|
| Visual renderizado | `DangerouslySetInnerHTML` com conteúdo pré-sanitizado | Avaliação de aparência final |
| Código fonte | `<pre>` com syntax highlighting de HTML | Verificação técnica, ajustes manuais |
| SEO preview | Simulação de snippet de busca Google | Validação de título e meta description |
| Mobile preview | Iframe com viewport de 375px | Teste de responsividade |

Ações disponíveis: **editar inline** (contenteditable com sanitização), **regenerar seção** (seleção de texto + prompt de refinamento), **copiar HTML**, e **aprovar para publicação** .

#### 3.2.4 Controles de agendamento (date/time picker)

O **componente de agendamento** utiliza `DateTimePicker` do `@wordpress/components`, com:

- **Restrição de data mínima**: amanhã (não permite datas no passado)
- **Timezone handling**: exibição no timezone do usuário, conversão para UTC no envio
- **Presets rápidos**: "Amanhã de manhã", "Próxima segunda", "Em uma semana"
- **Indicador de resultado**: "Publicar imediato como rascunho" vs. "Agendar para 20/03/2026 às 14:30"

A seleção de data futura automaticamente ajusta o `post_status` para `'future'` no payload da requisição .

#### 3.2.5 Indicadores de status e progresso da geração

O **sistema de feedback visual** comunica o estado do processo:

| Estado | Indicador Visual | Mensagem | Ações Disponíveis |
|:---|:---|:---|:---|
| `idle` | — | Pronto para começar | Submeter prompt |
| `validating` | Spinner pequeno | Validando entrada... | Cancelar |
| `queued` | Fila com posição | Aguardando processamento... | Cancelar |
| `generating` | Progress bar indeterminado | Gerando conteúdo com IA... | — |
| `processing` | Progress bar determinado (etapas) | Criando post, processando imagem... | — |
| `completed` | Checkmark verde | Conteúdo criado com sucesso! | Editar, Preview, Novo |
| `failed` | X vermelho com detalhes | Erro: [mensagem específica] | Retry, Reportar, Novo |

As etapas de `processing` são: (1) Recebendo resposta da IA, (2) Criando post no WordPress, (3) Baixando e processando imagem, (4) Aplicando metadados SEO, (5) Finalizando .

### 3.3 Comunicação Frontend-Backend

#### 3.3.1 Custom REST API endpoint para submissão de prompts

O **endpoint principal** `/wp-json/aicg/v1/generate` é registrado com configuração rigorosa:

```php
register_rest_route('aicg/v1', '/generate', [
    'methods'             => 'POST',
    'callback'            => [AICG\REST\GenerationController::class, 'handleRequest'],
    'permission_callback' => function () {
        return current_user_can('edit_posts') && wp_verify_nonce($_SERVER['HTTP_X_WP_NONCE'], 'wp_rest');
    },
    'args'                => [
        'prompt'        => [
            'required'          => true,
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'validate_callback' => function ($param) {
                return strlen($param) >= 10 && strlen($param) <= 10000;
            },
        ],
        'content_type'  => [
            'required' => true,
            'type'     => 'string',
            'enum'     => ['post', 'page'],
        ],
        'schedule_date' => [
            'type'    => 'string',
            'format'  => 'date-time',
            'default' => null,
        ],
        'options'       => [
            'type'    => 'object',
            'default' => [],
        ],
    ],
]);
```

O callback orquestra: validação de rate limits, sanitização de inputs, decisão síncrono vs. assíncrono, chamada à API Groq via Neuron PHP, parsing e validação da resposta, criação de post/media/SEO, e retorno de resposta estruturada .

#### 3.3.2 Uso de @wordpress/api-fetch para requisições autenticadas

A biblioteca **`@wordpress/api-fetch`** simplifica drasticamente requisições HTTP no ecossistema WordPress:

```javascript
import apiFetch from '@wordpress/api-fetch';

// Configuração global (uma vez)
apiFetch.use(apiFetch.createRootURLMiddleware(aicgData.restUrl));
apiFetch.use(apiFetch.createNonceMiddleware(aicgData.nonce));

// Uso em componentes
const submitGeneration = async (formData) => {
    try {
        const response = await apiFetch({
            path: 'aicg/v1/generate',
            method: 'POST',
            data: formData,
        });
        return { success: true, data: response };
    } catch (error) {
        // Erro já normalizado pelo middleware
        return { 
            success: false, 
            error: error.message,
            code: error.code,
            retryAfter: error.headers?.get('Retry-After')
        };
    }
};
```

Features automáticas: injeção de nonce, parsing de JSON, tratamento de erros HTTP, e middleware extensível para logging, retry, ou transformação .

#### 3.3.3 Gerenciamento de estado com React hooks (useState, useEffect)

O **gerenciamento de estado** utiliza hooks nativos do React, mantendo simplicidade:

| Hook | Uso | Exemplo |
|:---|:---|:---|
| `useState` | Estado local de formulários, UI | `const [prompt, setPrompt] = useState('')` |
| `useEffect` | Side effects: carregamento, subscriptions, cleanup | Polling de status de job |
| `useReducer` | Estado complexo com múltiplas transições | Máquina de estados de geração |
| `useCallback` | Memoização de callbacks para performance | Handlers de submissão |
| `useMemo` | Cálculos derivados custosos | Parsing de preview HTML |

Custom hooks encapsulam lógica reutilizável:

```javascript
// useGeneration.js — lógica completa de geração
function useGeneration() {
    const [status, setStatus] = useState('idle');
    const [result, setResult] = useState(null);
    const [error, setError] = useState(null);

    const generate = useCallback(async (params) => {
        setStatus('validating');
        // ... validação client-side
        
        setStatus('submitting');
        const response = await submitGeneration(params);
        
        if (response.success) {
            setResult(response.data);
            setStatus('completed');
        } else {
            setError(response.error);
            setStatus('failed');
        }
    }, []);

    const reset = useCallback(() => {
        setStatus('idle');
        setResult(null);
        setError(null);
    }, []);

    return { status, result, error, generate, reset };
}
```

Para aplicações de maior escala, **Context API** ou **`@wordpress/data`** (implementação de Redux pelo WordPress) podem ser introduzidos .

## 4. Criação e Gerenciamento de Conteúdo

### 4.1 Inserção Programática de Posts/Páginas

#### 4.1.1 Uso de wp_insert_post com array de argumentos completo

A função **`wp_insert_post()`** é a primitiva central do WordPress para criação programática de conteúdo, aceitando um array associativo com mais de 20 parâmetros configuráveis. Para o plugin de IA, o array é construído dinamicamente a partir da resposta processada:

```php
$post_data = [
    // Campos obrigatórios
    'post_title'   => sanitize_text_field($ai_response['post']['title']),
    'post_content' => wp_kses_post($ai_response['post']['content']),
    'post_status'  => $schedule_date ? 'future' : 'draft',
    'post_type'    => $content_type, // 'post' ou 'page'
    
    // Campos condicionais e opcionais
    'post_excerpt' => sanitize_text_field($ai_response['post']['excerpt'] ?? ''),
    'post_date'    => $schedule_date ? gmdate('Y-m-d H:i:s', strtotime($schedule_date)) : null,
    'post_date_gmt'=> $schedule_date ? get_gmt_from_date(gmdate('Y-m-d H:i:s', strtotime($schedule_date))) : null,
    'post_author'  => get_current_user_id(),
    'post_name'    => sanitize_title($ai_response['post']['title']),
    'post_category'=> array_map('intval', $selected_categories),
    'tags_input'   => array_map('sanitize_text_field', $ai_response['seo']['keywords'] ?? []),
    'meta_input'   => [
        '_aicg_generated'       => '1',
        '_aicg_generation_time' => current_time('mysql'),
        '_aicg_model_used'      => sanitize_text_field($model),
    ],
    
    // Configurações de interação
    'comment_status' => get_default_comment_status($content_type),
    'ping_status'    => get_option('default_ping_status'),
];

$post_id = wp_insert_post($post_data, true); // WP_Error em caso de falha

if (is_wp_error($post_id)) {
    return new WP_Error('insert_failed', $post_id->get_error_message());
}
```

O parâmetro `$wp_error = true` garante que falhas retornem objeto `WP_Error` com mensagem diagnóstica, em vez de simplesmente `0` .

#### 4.1.2 Mapeamento de campos: post_title, post_content, post_excerpt, post_status, post_date, post_author

O **mapeamento sistemático** de campos da resposta da IA para parâmetros de `wp_insert_post()`:

| Campo IA | Parâmetro wp_insert_post | Transformação | Validação |
|:---|:---|:---|:---|
| `post.title` | `post_title` | `sanitize_text_field()` | ≤100 chars, não vazio |
| `post.content` | `post_content` | `wp_kses_post()` | HTML válido, tags permitidas |
| `post.excerpt` | `post_excerpt` | `sanitize_text_field()`, truncamento | ≤300 chars |
| `post.status` | `post_status` | whitelist: draft/future | Condicional a `schedule_date` |
| `post.date` | `post_date` / `post_date_gmt` | `gmdate()`, `get_gmt_from_date()` | Data futura obrigatória se future |
| — | `post_author` | `get_current_user_id()` | Capability check prévio |
| `post.slug` | `post_name` | `sanitize_title()` | Único via suffixo automático |

A especificação explícita de `post_author` é **crítica para operação via API REST ou Cron**, onde não há usuário logado no contexto tradicional. O valor 0 (padrão quando omitido) causa "vários bugs" em funcionalidades dependentes de autoria .

#### 4.1.3 Definição de post_status como 'draft' ou 'future' para agendamento

A **lógica de determinação de status** implementa o requisito de agendamento:

| Condição | `post_status` | `post_date` | Comportamento |
|:---|:---|:---|:---|
| Nenhuma data selecionada | `'draft'` | `null` (hora atual) | Rascunho para revisão imediata |
| Data/hora futura (> now + 1 min) | `'future'` | Data selecionada | Agendado para publicação automática |
| Data/hora presente (± 1 min) | `'draft'` | `null` | Rascunho (data no passado ignorada) |
| Data/hora passada | `'draft'` | `null` | Rascunho, com aviso ao usuário |

O status `'future'` aciona o **mecanismo nativo de publicação agendada do WordPress**: o WP Cron verifica periodicamente posts com `post_date` no futuro e transiciona para `'publish'` no momento exato. A precisão depende da configuração do cron — em produção, recomenda-se **cron de sistema real** em vez do pseudo-cron baseado em tráfego .

### 4.2 Processamento de Mídia e Featured Image

#### 4.2.1 Download de imagem da URL retornada via wp_remote_get

O **pipeline de download** implementa validações de segurança antes do processamento:

```php
function aicg_download_image($image_url) {
    // Validação inicial de URL
    $parsed = wp_parse_url($image_url);
    if (!$parsed || !in_array($parsed['scheme'], ['https', 'http'])) {
        return new WP_Error('invalid_url', 'URL de imagem inválida');
    }
    
    // Verificação de tipo via HEAD request
    $head = wp_remote_head($image_url, [
        'timeout'     => 10,
        'sslverify'   => true,
        'user-agent'  => 'WordPress/' . get_bloginfo('version'),
    ]);
    
    if (is_wp_error($head)) {
        return $head;
    }
    
    $content_type = wp_remote_retrieve_header($head, 'content-type');
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    
    if (!in_array($content_type, $allowed_types)) {
        return new WP_Error('invalid_type', 'Tipo de imagem não permitido: ' . $content_type);
    }
    
    // Download do conteúdo binário
    $response = wp_remote_get($image_url, [
        'timeout'    => 30,
        'limit_response_size' => 5 * 1024 * 1024, // 5MB max
    ]);
    
    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
        return new WP_Error('download_failed', 'Falha ao baixar imagem');
    }
    
    return [
        'content' => wp_remote_retrieve_body($response),
        'type'    => $content_type,
        'name'    => basename($parsed['path']) ?: 'ai-generated-' . time() . '.jpg',
    ];
}
```

A validação de **Content-Type via HEAD request** evita downloads desnecessários de recursos não-imagem, e o limite de tamanho protege contra negação de serviço .

#### 4.2.2 Upload para biblioteca de mídia com wp_upload_bits e wp_insert_attachment

O **processo de upload programático** simula o comportamento de upload de arquivo:

```php
function aicg_upload_to_media_library($image_data, $post_id = 0) {
    $upload = wp_upload_bits(
        sanitize_file_name($image_data['name']),
        null,
        $image_data['content']
    );
    
    if (!empty($upload['error'])) {
        return new WP_Error('upload_failed', $upload['error']);
    }
    
    $file_path = $upload['file'];
    $file_url  = $upload['url'];
    $file_type = $image_data['type'];
    
    // Criação do attachment
    $attachment = [
        'post_mime_type' => $file_type,
        'post_title'     => sanitize_file_name(pathinfo($image_data['name'], PATHINFO_FILENAME)),
        'post_content'   => '',
        'post_status'    => 'inherit',
        'guid'           => $file_url,
    ];
    
    $attach_id = wp_insert_attachment($attachment, $file_path, $post_id);
    
    if (is_wp_error($attach_id)) {
        return $attach_id;
    }
    
    // Geração de metadados e thumbnails
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
    wp_update_attachment_metadata($attach_id, $attach_data);
    
    // Alt text para acessibilidade
    update_post_meta($attach_id, '_wp_attachment_image_alt', 
        sanitize_text_field($image_data['alt'] ?? 'Imagem gerada por IA'));
    
    return $attach_id;
}
```

A função `wp_upload_bits()` lida automaticamente com: geração de nome único se conflito, criação de subdiretórios por data (`/2026/03/`), e aplicação de permissões de arquivo seguras .

#### 4.2.3 Geração de metadados com wp_generate_attachment_metadata

A função **`wp_generate_attachment_metadata()`** processa a imagem para:

- **Extração de dimensões originais** (largura, altura)
- **Geração de thumbnails** nos tamanhos registrados: thumbnail (150x150), medium (300x300), large (1024x1024), e tamanhos customizados do tema
- **Criação de srcset** para imagens responsivas
- **Extração de metadados EXIF/IPTC** quando presentes (raro em imagens de IA)
- **Otimização opcional** via filtros do WordPress

O resultado é serializado e armazenado em `wp_postmeta` com chave `_wp_attachment_metadata`, essencial para funcionalidades de edição e exibição responsiva .

#### 4.2.4 Vinculação como featured image via set_post_thumbnail

A **vinculação final** é realizada por:

```php
set_post_thumbnail($post_id, $attach_id);
// Ou equivalente:
update_post_meta($post_id, '_thumbnail_id', $attach_id);
```

A função `set_post_thumbnail()` verifica automaticamente: existência do post, existência do attachment, e permissões do usuário atual. Em caso de sucesso, dispara action `set_post_thumbnail` para integrações de terceiros. A verificação posterior com `has_post_thumbnail($post_id)` confirma a operação .

### 4.3 Armazenamento de Dados SEO

#### 4.3.1 Detecção de plugin SEO ativo (Yoast, Rank Math, SEOPress)

A **detecção de plugin SEO** verifica múltiplos indicadores:

| Plugin | Função/Classe de Detecção | Prioridade |
|:---|:---|:---|
| Yoast SEO | `defined('WPSEO_VERSION')` ou `class_exists('Yoast\WP\SEO\Main')` | 1 |
| Rank Math | `class_exists('RankMath')` ou `function_exists('rank_math')` | 2 |
| SEOPress | `defined('SEOPRESS_VERSION')` ou `function_exists('seopress_init')` | 3 |
| All in One SEO | `class_exists('AIOSEO\Plugin\AIOSEO')` | 4 |
| The SEO Framework | `defined('THE_SEO_FRAMEWORK_VERSION')` | 5 |

A detecção ocorre em tempo de execução, permitindo que o usuário mude de plugin SEO sem reconfiguração do plugin de IA .

#### 4.3.2 Mapeamento para meta keys específicas de cada plugin

O **mapeamento de campos SEO** para convenções de cada plugin:

| Campo Genérico | Yoast SEO | Rank Math | SEOPress |
|:---|:---|:---|:---|
| Título SEO | `_yoast_wpseo_title` | `rank_math_title` | `_seopress_titles_title` |
| Meta description | `_yoast_wpseo_metadesc` | `rank_math_description` | `_seopress_titles_desc` |
| Focus keyword | `_yoast_wpseo_focuskw` | `rank_math_focus_keyword` | `_seopress_analysis_target_kw` |
| Keywords adicionais | `_yoast_wpseo_keywordsynonyms` | `rank_math_focus_keywords` (array) | — |
| URL canônica | `_yoast_wpseo_canonical` | `rank_math_canonical_url` | `_seopress_robots_canonical` |
| Robots meta | `_yoast_wpseo_meta-robots` | `rank_math_robots` | `_seopress_robots_index` |

A classe `AICG\SEO\SEOIntegration` abstrai este mapeamento, expondo método unificado `update_seo_meta($post_id, $seo_data)` que detecta plugin ativo e delega para adapter específico .

#### 4.3.3 Fallback para armazenamento em postmeta customizado

Quando **nenhum plugin SEO reconhecido está ativo**, ou como camada adicional de persistência:

```php
$fallback_meta = [
    '_aicg_seo_title'       => sanitize_text_field($seo_data['meta_title']),
    '_aicg_seo_description' => sanitize_textarea_field($seo_data['meta_description']),
    '_aicg_seo_keywords'    => implode(',', array_map('sanitize_text_field', $seo_data['keywords'])),
    '_aicg_seo_canonical'   => esc_url_raw($seo_data['canonical_url']),
];

foreach ($fallback_meta as $key => $value) {
    if (!empty($value)) {
        update_post_meta($post_id, $key, $value);
    }
}
```

Estes campos prefixados com `_` (underscore) são **ocultos da interface padrão de custom fields**, mas permanecem disponíveis para: futura migração para plugin SEO, consumo por templates de tema customizados, ou processamento por outras extensões. A convenção de nomenclatura segue o padrão WordPress de meta keys privadas .

## 5. Extensão do Core e Customização

### 5.1 Custom Post Types (Opcional)

#### 5.1.1 Registro de CPT 'ai_content' para organização de conteúdo gerado

O **Custom Post Type dedicado** oferece separação semântica para sites com alto volume de produção assistida:

```php
register_post_type('ai_content', [
    'labels' => [
        'name'          => 'Conteúdo IA',
        'singular_name' => 'Conteúdo IA',
        'add_new'       => 'Adicionar Novo',
        'add_new_item'  => 'Adicionar Novo Conteúdo IA',
        'edit_item'     => 'Editar Conteúdo IA',
        'new_item'      => 'Novo Conteúdo IA',
        'view_item'     => 'Ver Conteúdo IA',
        'search_items'  => 'Buscar Conteúdo IA',
        'not_found'     => 'Nenhum conteúdo IA encontrado',
    ],
    'description'         => 'Conteúdo gerado automaticamente por inteligência artificial',
    'public'              => false,        // Não acessível diretamente no frontend
    'publicly_queryable'  => false,        // Não aparece em queries públicas
    'show_ui'             => true,         // Interface de admin visível
    'show_in_menu'        => true,         // Menu próprio no admin
    'menu_position'       => 20,           // Após Posts
    'menu_icon'           => 'dashicons-art', // Ou ícone customizado SVG
    'capability_type'     => 'post',
    'map_meta_cap'        => true,
    'supports'            => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'revisions', 'custom-fields'],
    'has_archive'         => false,
    'exclude_from_search' => true,
    'show_in_rest'        => true,         // Compatibilidade com Gutenberg
    'rest_base'           => 'ai-content',
]);
```

A configuração `public => false` com `show_ui => true` cria um **"backend-only" post type**: gerenciável no admin, mas nunca exposto no frontend sem intervenção explícita do tema ou shortcodes .

#### 5.1.2 Taxonomias customizadas para categorização (tópicos, status de revisão)

Taxonomias que enriquecem a organização do conteúdo IA:

| Taxonomia | Tipo | Terms Padrão | Propósito |
|:---|:---|:---|:---|
| `ai_topic` | Hierárquica (como categorias) | Tecnologia, Saúde, Negócios, Entretenimento, Educação | Categorização temática do conteúdo |
| `ai_review_status` | Não-hierárquica (como tags) | pending_review, approved, needs_revision, rejected, published | Workflow de revisão editorial |
| `ai_model` | Não-hierárquica | llama-3.3-70b, mixtral-8x7b, gemma-7b | Rastreamento de qualidade por modelo |
| `ai_generation_type` | Não-hierárquica | blog_post, landing_page, product_description, social_media | Tipo de conteúdo gerado |

O registro via `register_taxonomy()` especifica `object_type => ['ai_content', 'post', 'page']` para permitir uso flexível em múltiplos post types .

#### 5.1.3 Metaboxes adicionais para metadados de geração (prompt original, modelo IA usado)

Metaboxes implementados via **Secure Custom Fields (SCF)** ou código nativo:

| Campo | Tipo | Configuração | Exibição |
|:---|:---|:---|:---|
| `aicg_prompt_original` | textarea | readonly, rows: 8 | Prompt completo enviado à IA |
| `aicg_model_used` | select | opções dinâmicas dos modelos disponíveis | Modelo específico da Groq |
| `aicg_generation_timestamp` | date_time_picker | readonly, formato: d/m/Y H:i | Quando a geração ocorreu |
| `aicg_tokens_input` | number | readonly, suffix: tokens | Consumo de tokens de prompt |
| `aicg_tokens_output` | number | readonly, suffix: tokens | Consumo de tokens de resposta |
| `aicg_generation_duration` | number | readonly, suffix: segundos | Tempo total de processamento |
| `aicg_cost_estimate` | number | readonly, prefix: $, precisão: 4 | Custo estimado em USD |

A configuração `readonly` para campos automáticos previne modificação acidental, preservando integridade de auditoria .

### 5.2 Integração com Gutenberg

#### 5.2.1 Bloco customizado para inserção de conteúdo IA diretamente no editor

O **bloco `aicg/content-generator`** registra-se via `registerBlockType()`:

```javascript
// block.json
{
  "$schema": "https://schemas.wp.org/trunk/block.json",
  "apiVersion": 3,
  "name": "aicg/content-generator",
  "title": "Gerador de Conteúdo IA",
  "category": "common",
  "icon": "art",
  "description": "Gere conteúdo com inteligência artificial diretamente no editor",
  "supports": {
    "html": false,
    "multiple": false,
    "reusable": false
  },
  "attributes": {
    "generatedContent": { "type": "string", "default": "" },
    "generationId": { "type": "string", "default": "" }
  },
  "editorScript": "file:./index.js",
  "editorStyle": "file:./index.css"
}
```

O bloco renderiza: **placeholder inicial** com botão "Gerar conteúdo com IA", **modal de prompt** ao clicar, **inserção de blocos nativos** (paragraph, heading, image, list) após geração, e **controles de regeneração** no toolbar do bloco. A transformação de HTML da IA em blocos Gutenberg utiliza `rawHandler` do `@wordpress/blocks` .

#### 5.2.2 Sidebar plugin com controles de geração no contexto do editor

O **PluginDocumentSettingPanel** adiciona painel na sidebar direita:

```javascript
import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

const AICGSidebar = () => (
    <PluginDocumentSettingPanel
        name="aicg-sidebar"
        title="Assistente de IA"
        icon="art"
    >
        <AICGGenerateButton />
        <AICGToneSelector />
        <AICGImproveSection />
        <AICGSEOScore />
    </PluginDocumentSettingPanel>
);

registerPlugin('aicg-sidebar', { render: AICGSidebar });
```

Funcionalidades: **"Melhorar este conteúdo"** (envia texto atual para refinamento), **"Gerar excerpt otimizado"**, **"Sugerir categorias/tags"**, e **preview de score SEO** em tempo real .

#### 5.2.3 Patterns de bloco pré-configurados para layouts comuns

**Block patterns** registrados via `register_block_pattern()`:

| Pattern | Descrição | Blocos Incluídos |
|:---|:---|:---|
| `aicg/hero-article` | Artigo com hero image e destaques | Cover, Heading, Paragraph, Quote, Separator |
| `aicg/product-showcase` | Página de produto com especificações | Columns (imagem + detalhes), Heading, List, Button |
| `aicg/faq-section` | Seção de perguntas frequentes | Heading, Details (accordion), Paragraph |
| `aicg/comparison-table` | Tabela comparativa de produtos | Table, Heading, Paragraph |
| `aicg/timeline` | Linha do tempo de eventos | Group (repetido), Heading, Paragraph, Separator |

Cada pattern inclui: título, descrição, categorias, keywords, viewportWidth para preview, e template de blocos aninhados com atributos preenchidos .

### 5.3 Customizer API (Para Configurações Globais)

#### 5.3.1 Seção para configurações padrão de tom de voz e estilo

A **seção no Customizer** centraliza preferências que afetam todas as gerações:

```php
add_action('customize_register', function ($wp_customize) {
    $wp_customize->add_section('aicg_defaults', [
        'title'    => 'Gerador de Conteúdo IA',
        'priority' => 200,
    ]);
    
    // Tom de voz padrão
    $wp_customize->add_setting('aicg_default_tone', [
        'default'           => 'professional',
        'sanitize_callback' => 'sanitize_key',
        'transport'         => 'postMessage',
    ]);
    
    $wp_customize->add_control('aicg_default_tone', [
        'section' => 'aicg_defaults',
        'label'   => 'Tom de voz padrão',
        'type'    => 'select',
        'choices' => [
            'professional' => 'Profissional',
            'casual'       => 'Casual/Conversacional',
            'technical'    => 'Técnico/Especializado',
            'persuasive'   => 'Persuasivo/Vendas',
            'narrative'    => 'Narrativo/Storytelling',
        ],
    ]);
    
    // Comprimento típico de conteúdo
    $wp_customize->add_setting('aicg_default_length', [
        'default'           => 'medium',
        'sanitize_callback' => 'sanitize_key',
    ]);
    
    $wp_customize->add_control('aicg_default_length', [
        'section' => 'aicg_defaults',
        'label'   => 'Comprimento padrão',
        'type'    => 'radio',
        'choices' => [
            'short'  => 'Curto (300-500 palavras)',
            'medium' => 'Médio (800-1200 palavras)',
            'long'   => 'Longo (1500-2500 palavras)',
        ],
    ]);
});
```

O transport `postMessage` habilita **preview ao vivo** sem recarregamento de página .

#### 5.3.2 Preview ao vivo de configurações de exibição

O **preview dinâmico** implementa-se via JavaScript no Customizer:

```javascript
// Escuta alterações e atualiza preview
wp.customize('aicg_default_tone', function (value) {
    value.bind(function (newTone) {
        // Requisição AJAX para gerar exemplo com novo tom
        wp.ajax.post('aicg_preview_tone', { tone: newTone })
            .done(function (response) {
                // Atualiza iframe de preview com novo conteúdo
                document.getElementById('aicg-preview-frame')
                    .contentDocument.body.innerHTML = response.html;
            });
    });
});
```

O preview mostra: **exemplo de parágrafo** gerado com configurações atuais, **comparação lado a lado** de diferentes tons (se selecionado), e **estimativa de tokens/custo** para configuração atual .

## 6. Segurança e Boas Práticas

### 6.1 Validação e Sanitização

#### 6.1.1 Nonces em todas as requisições de formulário e AJAX

A **proteção CSRF** implementa-se em múltiplas camadas:

| Contexto | Geração de Nonce | Verificação |
|:---|:---|:---|
| Formulários tradicionais | `wp_nonce_field('aicg_action')` | `check_admin_referer('aicg_action')` |
| AJAX legado | `wp_create_nonce('aicg_ajax_nonce')` | `check_ajax_referer('aicg_ajax_nonce')` |
| REST API | `wp_create_nonce('wp_rest')` | Automática via `rest_cookie_check_errors()` |
| Links de ação | `wp_nonce_url($url, 'aicg_delete', 'aicg_nonce')` | `wp_verify_nonce($_GET['aicg_nonce'], 'aicg_delete')` |

O nonce da REST API é injetado via `wp_localize_script()` e automaticamente incluído pelo `@wordpress/api-fetch` no header `X-WP-Nonce` .

#### 6.1.2 Capability checks (manage_options, edit_posts) para acesso à funcionalidade

A **hierarquia de permissões** granular:

| Funcionalidade | Capability Requerida | Justificativa |
|:---|:---|:---|
| Configurações de API, logs de sistema | `manage_options` | Acesso administrativo completo |
| Geração de conteúdo, acesso à interface | `edit_posts` | Criação de posts como autor |
| Agendamento de publicação | `publish_posts` | Publicação de conteúdo próprio |
| Edição de conteúdo de outros | `edit_others_posts` | Moderação editorial |
| Exclusão de conteúdo gerado | `delete_posts` / `delete_others_posts` | Segundo autor do conteúdo |

A verificação `current_user_can()` precede qualquer operação sensível, nunca assumindo que acesso a página admin implica autorização .

#### 6.1.3 Sanitização de input com sanitize_text_field, wp_kses_post para HTML

A **sanitização contextual por tipo de dado**:

| Tipo | Função | Contexto de Uso |
|:---|:---|:---|
| String simples | `sanitize_text_field()` | Títulos, keywords, nomes |
| Texto multilinha | `sanitize_textarea_field()` | Prompts, descrições |
| HTML de conteúdo | `wp_kses_post()` | `post_content`, campos rich text |
| HTML de atributo | `esc_attr()` | Valores em atributos HTML |
| URL | `esc_url_raw()` / `esc_url()` | URLs para armazenamento / exibição |
| Email | `sanitize_email()` | Endereços de notificação |
| Inteiro | `absint()` | IDs, contagens |
| Array | `map_deep($arr, 'sanitize_text_field')` | Coleções de strings |
| JSON | `wp_kses_post()` antes de `json_decode()` | Respostas de API externa |

A função `wp_kses_post()` aplica a **whitelist de tags seguras** do WordPress: `p`, `br`, `em`, `strong`, `a` (com `href`, `title`, `target`), `blockquote`, `cite`, `code`, `del`, `ins`, `img` (com `src`, `alt`, `width`, `height`, `class`), `ul`, `ol`, `li`, `h1`-`h6`, `hr`, e outras tags semânticas, removendo atributos event (`onclick`, `onload`, etc.) e `javascript:` URLs .

#### 6.1.4 Escape de output com esc_html, esc_attr, esc_url

O **escape contextual na saída** complementa a sanitização de entrada:

| Contexto | Função | Exemplo |
|:---|:---|:---|
| Texto em HTML | `esc_html()` | `echo esc_html($title)` |
| Atributo HTML | `esc_attr()` | `<input value="<?php echo esc_attr($value); ?>"` |
| URL em atributo | `esc_url()` | `<a href="<?php echo esc_url($url); ?>"` |
| JavaScript | `esc_js()` | `var data = '<?php echo esc_js($string); ?>'` |
| JSON em script | `wp_json_encode()` | `var config = <?php echo wp_json_encode($data); ?>` |

A consistência na aplicação de escape, **mesmo para dados previamente sanitizados**, estabelece defesa em profundidade contra vetores de ataque emergentes .

### 6.2 Proteção de Dados Sensíveis

#### 6.2.1 Criptografia de chaves API com wp_hash ou libsodium

A **arquitetura de criptografia** em duas camadas:

| Método | Algoritmo | Caso de Uso | Implementação |
|:---|:---|:---|:---|
| `wp_hash()` | HMAC-SHA256 | Verificação de integridade, tokens | `wp_hash($data, 'auth')` |
| libsodium | AES-256-GCM | Criptografia bidirecional de chaves API | `sodium_crypto_secretbox()` |

Para libsodium, o processo completo:

```php
function aicg_encrypt_api_key($api_key) {
    $key = sodium_base642bin(AICG_ENCRYPTION_KEY, SODIUM_BASE64_VARIANT_ORIGINAL);
    $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    $ciphertext = sodium_crypto_secretbox($api_key, $nonce, $key);
    
    return base64_encode($nonce . $ciphertext);
}

function aicg_decrypt_api_key($encrypted) {
    $key = sodium_base642bin(AICG_ENCRYPTION_KEY, SODIUM_BASE64_VARIANT_ORIGINAL);
    $decoded = base64_decode($encrypted);
    $nonce = substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    $ciphertext = substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    
    return sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
}
```

A constante `AICG_ENCRYPTION_KEY` é derivada de `AUTH_KEY` e `SECURE_AUTH_SALT` do `wp-config.php`, garantindo **unicidade por instalação** .

#### 6.2.2 Logging seguro de erros sem exposição de dados sensíveis

O **sistema de logging estruturado** discrimina níveis e redige dados sensíveis:

| Nível | Uso | Retenção |
|:---|:---|:---|
| `DEBUG` | Desenvolvimento, fluxo detalhado | 7 dias |
| `INFO` | Operações bem-sucedidas, métricas | 30 dias |
| `WARNING` | Retry, degradação graceful | 90 dias |
| `ERROR` | Falhas recuperáveis | 1 ano |
| `CRITICAL` | Falhas de segurança, indisponibilidade | 2 anos |

**Redação automática** em todos os níveis: chaves API (substituídas por `***REDACTED***`), tokens de autenticação, conteúdo de prompts (truncado ou hash), e PII detectada via regex. Logs de `DEBUG` e `INFO` nunca incluem dados sensíveis mesmo com redação .

#### 6.2.3 Política de retenção e exclusão de dados de prompts

A **gestão de ciclo de vida de dados**:

| Fase | Ação | Gatilho |
|:---|:---|:---|
| Coleta | Consentimento explícito para retenção | Opt-in na primeira utilização |
| Processamento | Anonimização de identificadores | Imediatamente após geração |
| Retenção ativa | Armazenamento completo com associação a usuário | Período configurável (padrão: 90 dias) |
| Arquivamento | Remoção de associação a usuário, retenção de métricas agregadas | Após período de retenção ativa |
| Exclusão | Remoção completa do banco de dados | Solicitação do usuário ou política automática |

A interface de admin permite ao usuário: **exportar seu histórico de prompts** (formato JSON/CSV), **solicitar exclusão completa** (com confirmação de irreversibilidade), e **configurar retenção mínima** para seus dados. A desativação do plugin oferece opção de **limpeza completa** de todas as tabelas e opções customizadas .

### 6.3 Segurança da API REST Customizada

#### 6.3.1 Registro de endpoint com permission_callback adequado

O **`permission_callback`** é obrigatório e rigoroso:

```php
'permission_callback' => function ($request) {
    // Verificação de autenticação
    if (!is_user_logged_in()) {
        return new WP_Error('rest_not_logged_in', 'Autenticação requerida', ['status' => 401]);
    }
    
    // Verificação de nonce para CSRF
    $nonce = $request->get_header('X_WP_Nonce');
    if (!wp_verify_nonce($nonce, 'wp_rest')) {
        return new WP_Error('rest_invalid_nonce', 'Nonce inválido', ['status' => 403]);
    }
    
    // Verificação de capability específica
    $required_cap = $request->get_method() === 'POST' ? 'edit_posts' : 'read';
    if (!current_user_can($required_cap)) {
        return new WP_Error('rest_forbidden', 'Permissão insuficiente', ['status' => 403]);
    }
    
    // Verificação de origem (opcional, reforço adicional)
    $origin = $request->get_header('Origin');
    if ($origin && !str_starts_with($origin, get_site_url())) {
        return new WP_Error('rest_invalid_origin', 'Origem não permitida', ['status' => 403]);
    }
    
    return true;
}
```

A omissão de `permission_callback` é **deprecated desde WordPress 5.5** e insegura .

#### 6.3.2 Rate limiting por usuário/IP

A **implementação em múltiplas camadas**:

```php
function aicg_check_rate_limit($request) {
    $user_id = get_current_user_id();
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    // Limites por usuário autenticado
    if ($user_id) {
        $user_key = "aicg_rate_user_{$user_id}";
        $user_count = (int) get_transient($user_key);
        
        if ($user_count >= 50) { // 50/hora
            return new WP_Error('rate_limit_exceeded', 'Limite por usuário excedido', [
                'status' => 429,
                'retry_after' => HOUR_IN_SECONDS
            ]);
        }
        set_transient($user_key, $user_count + 1, HOUR_IN_SECONDS);
    }
    
    // Limites por IP (fallback para não autenticado)
    $ip_key = "aicg_rate_ip_" . md5($ip);
    $ip_count = (int) get_transient($ip_key);
    
    if ($ip_count >= 10) { // 10/hora por IP
        return new WP_Error('rate_limit_exceeded', 'Limite por IP excedido', [
            'status' => 429,
            'retry_after' => HOUR_IN_SECONDS
        ]);
    }
    set_transient($ip_key, $ip_count + 1, HOUR_IN_SECONDS);
    
    return true;
}
```

Headers de resposta incluem: `X-RateLimit-Limit`, `X-RateLimit-Remaining`, `X-RateLimit-Reset` .

#### 6.3.3 Validação de origem da requisição

A **validação de origem complementar**:

| Técnica | Implementação | Eficácia |
|:---|:---|:---|
| CORS restritivo | `Access-Control-Allow-Origin: https://site.com` | Alta contra requisições cross-origin não intencionais |
| Validação de Referer | `$_SERVER['HTTP_REFERER']` vs `site_url()` | Média, spoofável |
| Nonce de sessão | `wp_verify_nonce()` | Alta contra replay de requisições |
| Assinatura de requisição | HMAC-SHA256 de timestamp + payload | Muito alta, overhead de implementação |

Para integrações críticas, **tokens JWT de curta duração** (5 minutos) com assinatura de requisição oferecem máxima segurança .

## 7. WP REST API e Endpoints Customizados

### 7.1 Endpoint Principal de Geração

#### 7.1.1 Rota: /wp-json/aicg/v1/generate

A **especificação completa do endpoint**:

| Aspecto | Configuração |
|:---|:---|
| Namespace | `aicg/v1` (versionado para evolução) |
| Rota | `/generate` |
| Métodos | `POST` |
| Autenticação | Cookie + nonce (padrão WP) ou Application Password |
| Content-Type | `application/json` |
| Timeout recomendado | 60-120 segundos (operação de IA) |

#### 7.1.2 Métodos: POST para submissão de prompt

O **corpo da requisição** esperado:

```json
{
  "prompt": "Artigo completo sobre desenvolvimento sustentável na indústria têxtil",
  "content_type": "post",
  "schedule_date": "2026-03-25T09:00:00-03:00",
  "options": {
    "tone": "professional",
    "target_length": 1200,
    "include_images": true,
    "seo_focus_keyword": "desenvolvimento sustentável têxtil",
    "language": "pt-BR",
    "model_override": "llama-3.3-70b-versatile"
  }
}
```

#### 7.1.3 Parâmetros: prompt, content_type, schedule_date, options

| Parâmetro | Tipo | Obrigatório | Validação | Padrão |
|:---|:---|:---|:---|:---|
| `prompt` | string | Sim | 10-10000 caracteres | — |
| `content_type` | string | Sim | enum: `post`, `page` | `post` |
| `schedule_date` | string (ISO 8601) | Não | Data/hora futura | `null` (rascunho) |
| `options` | object | Não | Schema validado | `{}` |

Schema de `options`:

| Propriedade | Tipo | Valores | Descrição |
|:---|:---|:---|:---|
| `tone` | string | `professional`, `casual`, `technical`, `persuasive`, `narrative` | Tom de voz desejado |
| `target_length` | integer | 300-5000 | Comprimento alvo em palavras |
| `include_images` | boolean | `true`, `false` | Gerar/buscar imagem destacada |
| `seo_focus_keyword` | string | — | Palavra-chave principal para SEO |
| `language` | string | Códigos ISO 639-1 | Idioma do conteúdo gerado |
| `model_override` | string | Modelos disponíveis na Groq | Modelo específico (ignora padrão) |

A **resposta de sucesso** (HTTP 201):

```json
{
  "success": true,
  "data": {
    "job_id": "a1b2c3d4e5f6",
    "post_id": 12345,
    "post_type": "post",
    "status": "future",
    "scheduled_date": "2026-03-25T12:00:00+00:00",
    "edit_link": "https://site.com/wp-admin/post.php?post=12345&action=edit",
    "preview_link": "https://site.com/?p=12345&preview=true",
    "generation_metadata": {
      "model_used": "llama-3.3-70b-versatile",
      "tokens_input": 245,
      "tokens_output": 1847,
      "generation_time_ms": 12450,
      "cost_estimate_usd": 0.0032
    }
  }
}
```

### 7.2 Endpoints de Gerenciamento

#### 7.2.1 GET /wp-json/aicg/v1/status/{job_id} para acompanhamento de geração

Acompanhamento de jobs assíncronos:

| Status | Descrição | Progresso | Ação do Frontend |
|:---|:---|:---|:---|
| `queued` | Aguardando início do processamento | 0% | Polling a cada 5s |
| `validating` | Validando prompt e parâmetros | 5% | — |
| `generating` | Chamada à API Groq em andamento | 10-60% | — |
| `processing` | Parsing, criação de post, mídia | 60-95% | — |
| `completed` | Geração finalizada com sucesso | 100% | Redirecionar para edição |
| `failed` | Erro em qualquer etapa | — | Exibir erro, oferecer retry |

Resposta para job em processamento:

```json
{
  "job_id": "a1b2c3d4e5f6",
  "status": "generating",
  "progress_percent": 45,
  "stage": "awaiting_api_response",
  "estimated_completion": "2026-03-17T14:32:15Z",
  "created_at": "2026-03-17T14:30:00Z",
  "started_at": "2026-03-17T14:30:02Z"
}
```

#### 7.2.2 GET /wp-json/aicg/v1/history para listar conteúdo gerado

Parâmetros de query: `page`, `per_page` (max 100), `content_type`, `status`, `date_from`, `date_to`, `search` (em títulos e prompts).

Resposta paginada com headers: `X-WP-Total`, `X-WP-TotalPages`, `Link` (para navegação HATEOAS).

#### 7.2.3 DELETE /wp-json/aicg/v1/content/{post_id} para remoção

Restrições de segurança: verificação de `delete_post` capability para o post específico, verificação de meta `_aicg_generated` para prevenir exclusão acidental de conteúdo manual, e parâmetro opcional `force=true` para bypass de lixeira (requer `delete_others_posts`).

### 7.3 Integração com WP-CLI (Opcional)

#### 7.3.1 Comando wp ai generate para geração via linha de comando

```bash
# Geração síncrona básica
wp ai generate --prompt="Artigo sobre energia solar residencial" --type=post

# Com todas as opções
wp ai generate \
  --prompt="Guia completo de SEO para e-commerce" \
  --type=page \
  --author=5 \
  --schedule="2026-04-01 09:00" \
  --tone=technical \
  --length=2000 \
  --keyword="SEO e-commerce" \
  --output=json

# Leitura de prompt de arquivo
cat prompt.txt | wp ai generate --type=post --author=$(wp user get admin --field=ID)
```

Flags disponíveis: `--dry-run` (simulação), `--verbose` (output detalhado), `--timeout` (override), `--model` (modelo específico).

#### 7.3.2 Comando wp ai schedule para agendamento em massa

```bash
# Agendamento a partir de CSV
wp ai schedule --file=/caminho/prompts.csv --type=post --spread="7d"

# Estrutura do CSV: prompt,scheduled_date,content_type,options_json
# "Artigo 1","2026-03-20 09:00","post","{\"tone\":\"professional\"}"
# "Artigo 2","2026-03-27 09:00","post","{\"tone\":\"casual\"}"

# Agendamento recorrente (diário às 9h)
wp ai schedule --prompt-file=/caminho/templates/ --interval="daily 09:00" --count=30
```

Comandos adicionais: `wp ai status` (fila de jobs), `wp ai retry` (reprocessar falhas), `wp ai config` (visualizar/alterar configurações), `wp ai stats` (métricas de uso).

## 8. Campos Customizados com SCF (Secure Custom Fields)

### 8.1 Configuração de Campos para Metadados de IA

#### 8.1.1 Grupo de campos para rastreamento de geração (prompt original, timestamp, modelo)

Registro via código PHP (recomendado para versionamento):

```php
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;
    
    acf_add_local_field_group([
        'key' => 'group_aicg_tracking',
        'title' => 'Rastreamento de Geração IA',
        'fields' => [
            [
                'key' => 'field_aicg_prompt',
                'label' => 'Prompt Original',
                'name' => 'aicg_prompt_original',
                'type' => 'textarea',
                'readonly' => true,
                'rows' => 8,
            ],
            [
                'key' => 'field_aicg_model',
                'label' => 'Modelo Utilizado',
                'name' => 'aicg_model_used',
                'type' => 'select',
                'readonly' => true,
                'choices' => aicg_get_available_models(), // Dinâmico
            ],
            [
                'key' => 'field_aicg_timestamp',
                'label' => 'Timestamp de Geração',
                'name' => 'aicg_generation_timestamp',
                'type' => 'date_time_picker',
                'readonly' => true,
                'display_format' => 'd/m/Y H:i:s',
                'return_format' => 'Y-m-d H:i:s',
            ],
            [
                'key' => 'field_aicg_tokens',
                'label' => 'Tokens Consumidos',
                'name' => 'aicg_tokens',
                'type' => 'group',
                'sub_fields' => [
                    ['key' => 'field_tokens_input', 'label' => 'Input', 'name' => 'input', 'type' => 'number', 'readonly' => true],
                    ['key' => 'field_tokens_output', 'label' => 'Output', 'name' => 'output', 'type' => 'number', 'readonly' => true],
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'post',
                ],
                [
                    'param' => 'post_status',
                    'operator' => '!=',
                    'value' => 'auto-draft',
                ],
            ],
            // Repetir para 'page' e 'ai_content' se CPT habilitado
        ],
        'position' => 'side',
        'style' => 'default',
    ]);
});
```

#### 8.1.2 Campos para métricas de qualidade (score de SEO, legibilidade)

| Campo | Tipo | Fonte | Uso |
|:---|:---|:---|:---|
| `aicg_seo_score` | range (0-100) | Integração com plugin SEO | Benchmark de otimização |
| `aicg_readability_score` | number | Cálculo Flesch-Kincaid | Avaliação de complexidade textual |
| `aicg_originality_score` | number (0-100) | Integração com detector de plágio | Garantia de conteúdo único |
| `aicg_engagement_prediction` | number (0-100) | Modelo de ML próprio (futuro) | Estimativa de performance |

#### 8.1.3 Campos para revisão humana (status de aprovação, notas do editor)

| Campo | Tipo | Opções/Configuração |
|:---|:---|:---|
| `aicg_review_status` | select | `pending_review`, `approved`, `needs_revision`, `rejected` |
| `aicg_reviewer_id` | user | Capacidade `edit_others_posts` |
| `aicg_review_notes` | textarea | Notas qualitativas do revisor |
| `aicg_review_date` | date_time_picker | Timestamp da decisão de revisão |

### 8.2 Exibição Condicional e Validação

#### 8.2.1 Regras de localização para exibir campos apenas em conteúdo gerado por IA

A **condição composta** para exibição:

```php
'location' => [
    [
        // Condição 1: Post type permitido
        ['param' => 'post_type', 'operator' => '==', 'value' => 'post'],
        // Condição 2: Meta de geração presente OU taxonomia de IA
        [
            'param' => 'post_taxonomy',
            'operator' => '==',
            'value' => 'ai_review_status:pending_review',
        ],
        // OU (lógica alternativa via múltiplos grupos)
    ],
    [
        ['param' => 'post_type', 'operator' => '==', 'value' => 'post'],
        ['param' => 'post_meta', 'operator' => '==', 'value' => '_aicg_generated:1'],
    ],
],
```

A lógica de **"OU" entre grupos** permite múltiplos caminhos para ativação dos campos .

#### 8.2.2 Validação customizada para garantir integridade dos metadados

```php
add_filter('acf/validate_value/key=field_aicg_prompt', function ($valid, $value, $field, $input) {
    if (!$valid) return $valid;
    
    // Validação: prompt não vazio se post marcado como gerado
    $is_generated = get_post_meta(get_the_ID(), '_aicg_generated', true);
    if ($is_generated && empty($value)) {
        $valid = 'Prompt original é obrigatório para conteúdo gerado por IA';
    }
    
    return $valid;
}, 10, 4);
```

## 9. Fluxo Completo de Funcionamento

### 9.1 Inicialização e Configuração

#### 9.1.1 Ativação do plugin: criação de tabelas customizadas e opções padrão

A **função de ativação** executa:

| Etapa | Ação | Verificação |
|:---|:---|:---|
| 1 | Criação de tabela `aicg_jobs` para fila assíncrona | `dbDelta()` com charset utf8mb4 |
| 2 | Criação de tabela `aicg_logs` para auditoria estruturada | Índices em timestamp, user_id, level |
| 3 | Registro de opções padrão | `add_option()` com autoload=false |
| 4 | Registro de capabilities customizadas | `map_meta_cap` para granularidade |
| 5 | Criação de diretório de uploads dedicado | `wp_mkdir_p()` com .htaccess proteção |
| 6 | Agendamento de eventos WP Cron recorrentes | Limpeza de logs, sincronização de modelos |

#### 9.1.2 Configuração inicial de chave API e preferências globais

O **wizard de onboarding** apresenta:

1. **Bem-vindo**: visão geral do plugin e termos de uso
2. **Configuração de API**: campo para chave Groq, com validação em tempo real
3. **Preferências padrão**: tom de voz, comprimento, opções de SEO
4. **Teste de geração**: prompt de exemplo para validação completa
5. **Conclusão**: links para documentação, suporte, e página principal

A conclusão do wizard define `aicg_onboarding_completed = true`, desativando o redirect automático.

### 9.2 Geração de Conteúdo

#### 9.2.1 Usuário acessa página customizada no admin (AI Content Generator)

A página é registrada via `add_menu_page()`:

```php
add_menu_page(
    'Gerador de Conteúdo IA',           // page_title
    'Conteúdo IA',                       // menu_title
    'edit_posts',                        // capability
    'ai-content-generator',              // menu_slug
    'aicg_render_generator_page',        // callback
    'dashicons-art',                     // icon_url
    25                                   // position (após Posts)
);
```

O callback renderiza container mínimo para React:

```php
function aicg_render_generator_page() {
    echo '<div id="aicg-root" class="wrap"></div>';
}
```

#### 9.2.2 Insere prompt e seleciona parâmetros (tipo, agendamento)

A interface React apresenta: formulário de prompt com rich text básico, seletor de tipo (post/page) com implicações visuais, controles de tom e estilo, seletor de categoria/tags (se post), controles de agendamento com date/time picker, e preview de configuração resultante.

#### 9.2.3 Submissão via React para endpoint REST customizado

O `apiFetch` envia POST para `/wp-json/aicg/v1/generate` com payload JSON. O estado local transita para `submitting`, desabilitando o formulário e exibindo indicador de progresso.

#### 9.2.4 Processamento PHP: validação, chamada à API Groq via Neuron PHP

O **fluxo no servidor**:

| Etapa | Duração Típica | Ação |
|:---|:---|:---|
| Validação de entrada | < 10ms | Schema, sanitização, rate limiting |
| Verificação de cache | < 50ms | Transient lookup |
| Chamada API Groq | 5-30s | Neuron PHP, retry com backoff |
| Parsing de resposta | < 100ms | JSON validation, schema check |
| Decisão síncrono/assíncrono | < 10ms | Baseado em configuração e timeout |

#### 9.2.5 Recebimento e parsing da resposta estruturada

A resposta JSON da IA é parseada para array PHP, com validação de presença de campos obrigatórios e sanitização de cada valor.

### 9.3 Criação e Finalização

#### 9.3.1 Inserção do post com wp_insert_post (status draft ou future)

A função `wp_insert_post()` é invocada com array completo de argumentos. Em caso de sucesso, retorna `$post_id`; em caso de falha, `WP_Error` é propagado ao cliente.

#### 9.3.2 Processamento e anexação da imagem destacada

Se `media.image_url` presente na resposta: download via `wp_remote_get()`, upload via `wp_upload_bits()`, criação de attachment via `wp_insert_attachment()`, geração de metadados via `wp_generate_attachment_metadata()`, e vinculação via `set_post_thumbnail()`.

#### 9.3.3 Preenchimento de metadados SEO em postmeta

Detecção de plugin SEO ativo, mapeamento para meta keys específicas, e `update_post_meta()` para cada campo. Fallback para `_aicg_seo_*` em todos os casos.

#### 9.3.4 Registro de metadados de rastreamento via SCF

Atualização de campos ACF/SCF: `aicg_prompt_original`, `aicg_model_used`, `aicg_generation_timestamp`, `aicg_tokens_input`, `aicg_tokens_output`, `aicg_generation_duration`.

#### 9.3.5 Retorno de confirmação com link para edição do rascunho

Resposta JSON com: `success: true`, `data` contendo `post_id`, `edit_link`, `preview_link`, `scheduled_date` (se aplicável), e `generation_metadata` completo.

### 9.4 Agendamento e Publicação

#### 9.4.1 Uso nativo do WP Cron para publicação agendada

Posts com `post_status = 'future'` e `post_date` futuro são automaticamente gerenciados pelo WordPress. O hook `publish_future_post` dispara na transição para `'publish'`.

#### 9.4.2 Notificações opcionais por email na publicação

```php
add_action('publish_future_post', function ($post_id) {
    if (!get_post_meta($post_id, '_aicg_generated', true)) return;
    
    $author = get_userdata(get_post_field('post_author', $post_id));
    $edit_link = get_edit_post_link($post_id, 'raw');
    
    wp_mail(
        $author->user_email,
        'Seu conteúdo IA foi publicado',
        "O post \"" . get_the_title($post_id) . "\" agendado foi publicado automaticamente.\n\nEditar: $edit_link"
    );
});
```

#### 9.4.3 Log de publicações automáticas para auditoria

Inserção em `aicg_logs` com: `event_type: 'auto_publish'`, `post_id`, `user_id` (autor original), `timestamp`, `details` (JSON com dados da publicação).

## 10. Testes e Manutenção

### 10.1 Estratégia de Testes

#### 10.1.1 Testes unitários para classes de integração API

Framework: **PHPUnit** com **WP_Mock** para mocking de funções WordPress. Cobertura alvo: 80%+ para classes em `/includes/API/`.

| Classe | Métodos Críticos | Casos de Teste |
|:---|:---|:---|
| `GroqClient` | `generateContent()`, `validateConnection()` | Sucesso, timeout, rate limit, auth failure, malformed response |
| `ResponseParser` | `parse()`, `validateSchema()` | JSON válido, JSON inválido, campos ausentes, tipos incorretos |
| `RateLimiter` | `check()`, `increment()` | Dentro do limite, no limite, excedido, janela de reset |

#### 10.1.2 Testes de integração para fluxo completo de geração

Ambiente: **WordPress test install** com `WP_UnitTestCase`. Cenários: geração síncrona completa, geração assíncrona com polling, falha e retry, agendamento e publicação automática, e integração com cada plugin SEO suportado.

#### 10.1.3 Testes de segurança: SQL injection, XSS, CSRF

| Vetor | Teste | Ferramenta/Método |
|:---|:---|:---|
| SQL injection | Injeção em parâmetros de query | `sqlmap`, análise manual de prepared statements |
| XSS | Scripts em prompts e conteúdo gerado | `wp_kses_post()` validation, DOMPurify no frontend |
| CSRF | Requisições sem nonce | Burp Suite, verificação de `permission_callback` |
| IDOR | Acesso a posts de outros usuários | Testes com múltiplos usuários, verificação de capabilities |

### 10.2 Monitoramento e Logging

#### 10.2.1 Sistema de logging estruturado para requisições API

Estrutura de log em JSON:

```json
{
  "timestamp": "2026-03-17T14:30:00.123Z",
  "level": "INFO",
  "component": "GroqClient",
  "event": "api_request",
  "request_id": "req_a1b2c3",
  "user_id": 5,
  "method": "POST",
  "endpoint": "/v1/chat/completions",
  "duration_ms": 12450,
  "status_code": 200,
  "tokens_input": 245,
  "tokens_output": 1847,
  "error": null
}
```

#### 10.2.2 Dashboard de métricas: volume de geração, taxas de sucesso/erro

Métricas calculadas em tempo real ou agregadas periodicamente: gerações por hora/dia/semana, taxa de sucesso (200 / total), distribuição de erros por categoria, tempo médio de geração, custo estimado acumulado, e satisfação do usuário (feedback explícito).

#### 10.2.3 Alertas para falhas críticas ou limites de API

| Condição | Canal | Escalonamento |
|:---|:---|:---|
| Taxa de erro > 10% em 5 min | Slack, email | Equipe técnica imediata |
| Rate limit da Groq próximo | Email | Administrador em 1h |
| Falha de autenticação API | SMS, telefone | Administrador imediato |
| Espaço em disco < 10% | Email, Slack | Operações em 4h |

### 10.3 Atualização e Compatibilidade

#### 10.3.1 Versionamento semântico do plugin

Formato: `MAJOR.MINOR.PATCH` (ex: `2.3.1`). **MAJOR**: breaking changes, migração de dados necessária. **MINOR**: novas features, backward compatible. **PATCH**: bug fixes, security updates.

#### 10.3.2 Testes de compatibilidade com novas versões do WordPress

Matriz de teste: WordPress 6.4, 6.5, 6.6 (últimas 3 versões principais), PHP 8.0, 8.1, 8.2, 8.3, e combinações de plugins SEO populares. Automação via GitHub Actions ou similar.

#### 10.3.3 Migrações de banco de dados em atualizações

Classe `AICG\Database\Migrations` com métodos versionados:

```php
public function migrate_2_0_0() {
    // Adiciona coluna 'cost_estimate' à tabela aicg_jobs
    $this->db->query("ALTER TABLE {$this->prefix}aicg_jobs ADD COLUMN cost_estimate DECIMAL(10,6) NULL");
}

public function migrate_2_1_0() {
    // Cria nova tabela para métricas agregadas
    $this->db->delta("CREATE TABLE ...");
}
```

Sistema de tracking de versão aplicada em `aicg_db_version`, com execução automática de migrações pendentes em `admin_init`.

