# Tema WordPress para Escritório de Advocacia Thabatta Apolinário

Este é um tema WordPress personalizado desenvolvido para o escritório de advocacia Thabatta Apolinário. O tema foi criado com foco em elegância, profissionalismo e usabilidade, utilizando as cores da Themis (dourado, vinho/bordô e vermelho sangue).

## Características

- Design elegante e profissional baseado nas cores da Themis
- Totalmente responsivo para todos os dispositivos
- Otimizado para SEO e Google Web Core Vitals
- Campos personalizados ACF para fácil edição via painel administrativo
- Componentes web personalizados reutilizáveis
- Integração com Jetpack para cache e otimização
- Sistema de comentários seguro com filtros anti-spam
- Busca avançada com filtros por categoria e tags
- Menu mobile hambúrguer e menu desktop rolável
- Sidebar personalizada para redes sociais e posts relacionados
- Automação com Gulp para compilação de SCSS e minificação de JS
- GitHub Actions para deploy automático no Infinity Free

## Requisitos

- WordPress 5.8 ou superior
- PHP 7.4 ou superior
- MySQL 5.6 ou superior
- Plugin Advanced Custom Fields PRO
- Plugin Jetpack (opcional, mas recomendado para recursos de cache)

## Instalação

1. Faça o download do tema
2. Descompacte o arquivo e faça upload da pasta `thabatta-adv-theme` para o diretório `/wp-content/themes/` do seu WordPress
3. Ative o tema através do menu Aparência > Temas no painel administrativo do WordPress
4. Instale e ative o plugin Advanced Custom Fields PRO
5. Instale e ative o plugin Jetpack (opcional)
6. Configure as opções do tema em Configurações do Tema

## Estrutura do Tema

```
thabatta-adv-theme/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── inc/
│   ├── acf-fields.php
│   ├── admin/
│   ├── jetpack-integration.php
│   ├── security-features.php
│   ├── template-functions.php
│   ├── template-tags.php
│   └── web-components.php
├── src/
│   ├── js/
│   └── scss/
├── template-parts/
│   ├── content.php
│   ├── content-none.php
│   ├── content-page.php
│   └── content-single.php
├── templates/
│   ├── home.php
│   └── contact.php
├── 404.php
├── archive.php
├── comments.php
├── footer.php
├── functions.php
├── gulpfile.js
├── header.php
├── index.php
├── package.json
├── page.php
├── screenshot.png
├── search.php
├── sidebar.php
├── single.php
└── style.css
```

## Personalização

### Cores

As cores principais do tema podem ser personalizadas através do arquivo `src/scss/_variables.scss`:

```scss
// Cores principais
$primary-color: #B71C1C;       // Vermelho sangue
$secondary-color: #800020;     // Vinho/bordô
$accent-color: #D4AF37;        // Dourado
$text-color: #333333;          // Texto principal
$light-text-color: #FFFFFF;    // Texto claro
$background-color: #FFFFFF;    // Fundo principal
$light-background: #F5F5F5;    // Fundo claro
$dark-background: #212121;     // Fundo escuro
```

### Campos ACF

O tema inclui vários grupos de campos ACF para personalização:

- Opções do Tema (informações de contato, redes sociais, SEO)
- Configurações da Página Inicial (seções hero, sobre, serviços, depoimentos, CTA, blog)
- Posts Relacionados (para vincular posts e páginas relacionados)
- Áreas de Atuação (informações específicas para áreas de atuação)
- Equipe (informações sobre membros da equipe)
- Página de Contato (configurações específicas para a página de contato)

## Componentes Web Personalizados

O tema inclui vários componentes web personalizados que podem ser utilizados através de shortcodes:

- `[thabatta_card]` - Cards para exibir informações
- `[thabatta_accordion]` - Acordeões para conteúdo expansível
- `[thabatta_tabs]` - Abas para organizar conteúdo
- `[thabatta_slider]` - Slider para exibir imagens ou conteúdo
- `[thabatta_testimonial]` - Depoimentos de clientes
- `[thabatta_cta]` - Call to Action
- `[thabatta_icon_box]` - Caixas com ícones
- `[thabatta_team_member]` - Membros da equipe
- `[thabatta_counter]` - Contadores numéricos
- `[thabatta_timeline]` - Linha do tempo

## Automação com Gulp

O tema utiliza Gulp para automatizar tarefas de desenvolvimento:

```bash
# Instalar dependências
npm install

# Compilar assets para desenvolvimento
npm run dev

# Compilar assets para produção
npm run build

# Observar alterações durante o desenvolvimento
npm run watch
```

## Deploy com GitHub Actions

O tema inclui configuração para deploy automático no Infinity Free através do GitHub Actions. Para configurar:

1. Acesse Configurações > GitHub Actions no painel administrativo
2. Preencha as informações do repositório e do Infinity Free
3. Gere o arquivo de workflow e adicione-o ao seu repositório no caminho `.github/workflows/deploy.yml`
4. Adicione o segredo `INFINITY_FREE_PASSWORD` nas configurações do seu repositório no GitHub

## Licença

Este tema é licenciado sob a [GNU General Public License v2.0](https://www.gnu.org/licenses/gpl-2.0.html) ou posterior.

## Créditos

- Desenvolvido por [Seu Nome/Empresa]
- Ícones por [Font Awesome](https://fontawesome.com/)
- Imagens por [Unsplash](https://unsplash.com/)
