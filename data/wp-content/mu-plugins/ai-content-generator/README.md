# AI Content Generator

Plugin WordPress para geração de conteúdo com Inteligência Artificial via API Groq.

## Requisitos

- WordPress 6.0+
- PHP 8.0+
- Extensão libsodium (recomendada, mas não obrigatória)
- Chave API Groq (obtenha em https://console.groq.com/)

## Instalação

1. Copie a pasta `ai-content-generator` para `/wp-content/mu-plugins/` ou `/wp-content/plugins/`
2. Ative o plugin no painel administrativo
3. Siga o wizard de configuração inicial
4. Insira sua chave API Groq

## Desenvolvimento

### Instalação de dependências

```bash
cd ai-content-generator
npm install
```

### Build de desenvolvimento

```bash
npm run start
```

### Build de produção

```bash
npm run build
```

### Linting

```bash
npm run lint:js
npm run lint:css
```

## Estrutura de Arquivos

```
ai-content-generator/
├── ai-content-generator.php      # Arquivo principal
├── package.json                  # Dependências npm
├── README.md                     # Esta documentação
├── includes/
│   ├── Core/                     # Classes core
│   │   ├── Autoloader.php
│   │   ├── Plugin.php
│   │   ├── Activator.php
│   │   ├── Deactivator.php
│   │   └── Logger.php
│   ├── API/                      # Integração com API
│   │   ├── GroqClient.php
│   │   └── ResponseParser.php
│   ├── Admin/                    # Admin
│   │   ├── Menu.php
│   │   └── Assets.php
│   ├── Content/                  # Gerenciamento de conteúdo
│   │   ├── PostCreator.php
│   │   └── Scheduler.php
│   ├── Media/                    # Processamento de mídia
│   │   └── ImageHandler.php
│   ├── SEO/                      # Integração SEO
│   │   └── SEOIntegration.php
│   ├── Security/                 # Segurança
│   │   ├── Encryption.php
│   │   └── RateLimiter.php
│   ├── REST/                     # API REST
│   │   ├── Routes.php
│   │   ├── GenerationController.php
│   │   ├── StatusController.php
│   │   ├── HistoryController.php
│   │   ├── SettingsController.php
│   │   ├── ContentController.php
│   │   └── StatsController.php
│   └── Database/
│       └── Migrations.php
├── admin/
│   ├── partials/
│   └── views/
│       ├── settings.php
│       ├── history.php
│       └── onboarding.php
├── assets/
│   ├── css/
│   │   └── admin.css
│   ├── js/
│   │   └── admin.js
│   ├── images/
│   └── fonts/
├── src/                          # Aplicação React
│   ├── index.js
│   ├── components/
│   │   ├── PromptForm.js
│   │   ├── PreviewPanel.js
│   │   ├── StatusIndicator.js
│   │   └── SettingsPanel.js
│   ├── hooks/
│   │   └── useGeneration.js
│   └── styles/
│       └── app.css
├── build/                        # Assets compilados (gerado)
├── languages/                    # Arquivos de tradução
└── tests/                        # Testes
```

## Endpoints REST API

### Gerar Conteúdo

```
POST /wp-json/aicg/v1/generate
```

Parâmetros:
- `prompt` (string, obrigatório) - Descrição do conteúdo
- `content_type` (string, obrigatório) - 'post' ou 'page'
- `schedule_date` (string, opcional) - Data ISO 8601
- `options` (object, opcional) - Opções adicionais

### Verificar Status

```
GET /wp-json/aicg/v1/status/{job_id}
```

### Histórico

```
GET /wp-json/aicg/v1/history?page=1&per_page=20
```

### Configurações

```
GET /wp-json/aicg/v1/settings
POST /wp-json/aicg/v1/settings
```

## Segurança

- Criptografia de chaves API usando libsodium ou OpenSSL
- Rate limiting em múltiplas camadas
- Sanitização de todos os inputs
- Nonces em requisições
- Capability checks em todas as operações

## Licença

GPL v2 ou posterior
