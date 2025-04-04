<?php
/**
 * Script para gerar o arquivo principal de estilos SCSS
 */

// Definir caminho para o arquivo de saída
$output_file = __DIR__ . '/../src/scss/main.scss';

// Conteúdo do arquivo
$scss_content = <<<'EOT'
/**
 * Estilos principais para o tema Thabatta Advocacia
 * 
 * @package Thabatta_Advocacia
 */

// Importar variáveis
@import 'variables';

// Importar mixins
@import 'mixins';

// Importar reset
@import 'reset';

// Importar tipografia
@import 'typography';

// Importar layout
@import 'layout';

// Importar componentes
@import 'components/header';
@import 'components/footer';
@import 'components/navigation';
@import 'components/buttons';
@import 'components/forms';
@import 'components/cards';
@import 'components/accordion';
@import 'components/tabs';
@import 'components/slider';
@import 'components/testimonials';
@import 'components/cta';
@import 'components/icon-box';
@import 'components/team';
@import 'components/counter';
@import 'components/timeline';
@import 'components/sidebar';
@import 'components/pagination';
@import 'components/comments';
@import 'components/search';
@import 'components/social';
@import 'components/preloader';

// Importar páginas
@import 'pages/home';
@import 'pages/about';
@import 'pages/services';
@import 'pages/blog';
@import 'pages/single';
@import 'pages/contact';
@import 'pages/404';

// Importar utilitários
@import 'utilities';

// Importar responsividade
@import 'responsive';
EOT;

// Criar diretório se não existir
if (!file_exists(dirname($output_file))) {
    mkdir(dirname($output_file), 0755, true);
}

// Escrever arquivo
file_put_contents($output_file, $scss_content);

echo "Arquivo SCSS principal gerado com sucesso em: $output_file\n";

// Gerar arquivo de variáveis SCSS
$variables_file = __DIR__ . '/../src/scss/_variables.scss';
$variables_content = <<<'EOT'
/**
 * Variáveis SCSS para o tema Thabatta Advocacia
 * 
 * @package Thabatta_Advocacia
 */

// Cores principais
$primary-color: #8B0000; // Vinho/Bordô
$secondary-color: #D4AF37; // Dourado
$accent-color: #4A0404; // Vermelho sangue escuro
$text-color: #333333;
$light-text-color: #ffffff;
$link-color: $secondary-color;
$link-hover-color: darken($secondary-color, 10%);

// Cores de fundo
$background-color: #ffffff;
$light-background: #f9f9f9;
$dark-background: #1a1a1a;

// Cores de estado
$success-color: #46b450;
$error-color: #dc3232;
$warning-color: #ffb900;
$info-color: #00a0d2;

// Tipografia
$heading-font: 'Playfair Display', serif;
$body-font: 'Roboto', sans-serif;
$base-font-size: 16px;
$base-line-height: 1.6;
$heading-line-height: 1.3;

// Espaçamento
$spacing-xs: 5px;
$spacing-sm: 10px;
$spacing-md: 20px;
$spacing-lg: 30px;
$spacing-xl: 50px;

// Bordas
$border-radius: 4px;
$border-color: #dddddd;

// Sombras
$box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
$text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);

// Breakpoints
$breakpoint-xs: 480px;
$breakpoint-sm: 768px;
$breakpoint-md: 992px;
$breakpoint-lg: 1200px;

// Contêiner
$container-width: 1170px;
$container-padding: 15px;

// Header
$header-height: 80px;
$header-mobile-height: 60px;

// Footer
$footer-background: $dark-background;
$footer-text-color: #cccccc;

// Navegação
$nav-item-spacing: 20px;
$nav-dropdown-width: 220px;

// Botões
$button-padding: 12px 25px;
$button-border-radius: $border-radius;

// Formulários
$input-height: 45px;
$input-padding: 10px 15px;
$input-border-radius: $border-radius;
$input-border-color: $border-color;
$input-focus-border-color: $secondary-color;

// Transições
$transition-speed: 0.3s;
$transition-timing: ease;

// Z-index
$z-index-dropdown: 100;
$z-index-sticky: 200;
$z-index-fixed: 300;
$z-index-modal-backdrop: 400;
$z-index-modal: 500;
$z-index-popover: 600;
$z-index-tooltip: 700;
EOT;

// Escrever arquivo de variáveis
file_put_contents($variables_file, $variables_content);
echo "Arquivo de variáveis SCSS gerado com sucesso em: $variables_file\n";

// Gerar arquivo de mixins SCSS
$mixins_file = __DIR__ . '/../src/scss/_mixins.scss';
$mixins_content = <<<'EOT'
/**
 * Mixins SCSS para o tema Thabatta Advocacia
 * 
 * @package Thabatta_Advocacia
 */

// Flexbox
@mixin flex($direction: row, $wrap: nowrap, $justify: flex-start, $align: stretch) {
    display: flex;
    flex-direction: $direction;
    flex-wrap: $wrap;
    justify-content: $justify;
    align-items: $align;
}

// Centralizar com flexbox
@mixin flex-center {
    display: flex;
    justify-content: center;
    align-items: center;
}

// Centralizar absolutamente
@mixin absolute-center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

// Transição
@mixin transition($property: all, $duration: $transition-speed, $timing: $transition-timing) {
    transition: $property $duration $timing;
}

// Sombra de caixa
@mixin box-shadow($shadow: $box-shadow) {
    box-shadow: $shadow;
}

// Sombra de texto
@mixin text-shadow($shadow: $text-shadow) {
    text-shadow: $shadow;
}

// Gradiente
@mixin gradient($start-color, $end-color, $direction: to bottom) {
    background: $start-color;
    background: linear-gradient($direction, $start-color, $end-color);
}

// Truncar texto
@mixin text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

// Limitar linhas de texto
@mixin line-clamp($lines) {
    display: -webkit-box;
    -webkit-line-clamp: $lines;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

// Clearfix
@mixin clearfix {
    &::after {
        content: "";
        display: table;
        clear: both;
    }
}

// Responsividade
@mixin respond-to($breakpoint) {
    @if $breakpoint == xs {
        @media (max-width: $breakpoint-xs) { @content; }
    } @else if $breakpoint == sm {
        @media (max-width: $breakpoint-sm) { @content; }
    } @else if $breakpoint == md {
        @media (max-width: $breakpoint-md) { @content; }
    } @else if $breakpoint == lg {
        @media (max-width: $breakpoint-lg) { @content; }
    } @else if $breakpoint == xl {
        @media (min-width: $breakpoint-lg + 1) { @content; }
    }
}

// Botão
@mixin button($bg-color: $primary-color, $text-color: $light-text-color, $hover-bg-color: darken($bg-color, 10%)) {
    display: inline-block;
    padding: $button-padding;
    background-color: $bg-color;
    color: $text-color;
    border: none;
    border-radius: $button-border-radius;
    font-family: $body-font;
    font-weight: 500;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
    @include transition;
    
    &:hover, &:focus {
        background-color: $hover-bg-color;
        color: $text-color;
        text-decoration: none;
    }
}

// Botão outline
@mixin button-outline($color: $primary-color, $hover-bg-color: $color, $hover-text-color: $light-text-color) {
    display: inline-block;
    padding: $button-padding;
    background-color: transparent;
    color: $color;
    border: 2px solid $color;
    border-radius: $button-border-radius;
    font-family: $body-font;
    font-weight: 500;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
    @include transition;
    
    &:hover, &:focus {
        background-color: $hover-bg-color;
        color: $hover-text-color;
        text-decoration: none;
    }
}

// Overlay
@mixin overlay($bg-color: rgba(0, 0, 0, 0.5), $z-index: 1) {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: $bg-color;
    z-index: $z-index;
}

// Aspect ratio
@mixin aspect-ratio($width, $height) {
    position: relative;
    
    &::before {
        content: "";
        display: block;
        padding-top: ($height / $width) * 100%;
    }
    
    > * {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
}

// Scrollbar personalizada
@mixin custom-scrollbar($width: 8px, $track-color: $light-background, $thumb-color: $primary-color) {
    &::-webkit-scrollbar {
        width: $width;
        height: $width;
    }
    
    &::-webkit-scrollbar-track {
        background: $track-color;
    }
    
    &::-webkit-scrollbar-thumb {
        background: $thumb-color;
        border-radius: $width / 2;
    }
    
    &::-webkit-scrollbar-thumb:hover {
        background: darken($thumb-color, 10%);
    }
}

// Efeito de brilho
@mixin shine-effect($duration: 1.5s) {
    position: relative;
    overflow: hidden;
    
    &::after {
        content: "";
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(
            to right,
            rgba(255, 255, 255, 0) 0%,
            rgba(255, 255, 255, 0.3) 50%,
            rgba(255, 255, 255, 0) 100%
        );
        transform: rotate(30deg);
        animation: shine $duration infinite;
    }
    
    @keyframes shine {
        0% {
            transform: translateX(-100%) rotate(30deg);
        }
        100% {
            transform: translateX(100%) rotate(30deg);
        }
    }
}

// Efeito de vidro (glassmorphism)
@mixin glass-effect($bg-opacity: 0.1, $blur: 10px, $border-opacity: 0.2) {
    background-color: rgba(255, 255, 255, $bg-opacity);
    backdrop-filter: blur($blur);
    -webkit-backdrop-filter: blur($blur);
    border: 1px solid rgba(255, 255, 255, $border-opacity);
}

// Efeito de sombra interna
@mixin inner-shadow($color: rgba(0, 0, 0, 0.2), $size: 10px) {
    box-shadow: inset 0 0 $size $color;
}

// Texto com gradiente
@mixin gradient-text($gradient) {
    background: $gradient;
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    text-fill-color: transparent;
}

// Animação de fade-in
@mixin fade-in($duration: 0.5s, $delay: 0s) {
    opacity: 0;
    animation: fadeIn $duration ease-in-out forwards;
    animation-delay: $delay;
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
}

// Animação de slide-in
@mixin slide-in($direction: 'up', $distance: 20px, $duration: 0.5s, $delay: 0s) {
    opacity: 0;
    
    @if $direction == 'up' {
        transform: translateY($distance);
        animation: slideUp $duration ease-in-out forwards;
    } @else if $direction == 'down' {
        transform: translateY(-$distance);
        animation: slideDown $duration ease-in-out forwards;
    } @else if $direction == 'left' {
        transform: translateX($distance);
        animation: slideLeft $duration ease-in-out forwards;
    } @else if $direction == 'right' {
        transform: translateX(-$distance);
        animation: slideRight $duration ease-in-out forwards;
    }
    
    animation-delay: $delay;
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY($distance);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-$distance);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideLeft {
        from {
            opacity: 0;
            transform: translateX($distance);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideRight {
        from {
            opacity: 0;
            transform: translateX(-$distance);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
}

// Efeito de hover com sublinhado
@mixin hover-underline($color: $secondary-color, $height: 2px, $offset: 4px) {
    position: relative;
    
    &::after {
        content: '';
        position: absolute;
        width: 100%;
        height: $height;
        bottom: -$offset;
        left: 0;
        background-color: $color;
        transform: scaleX(0);
        transform-origin: bottom right;
        transition: transform 0.3s ease-out;
    }
    
    &:hover::after {
        transform: scaleX(1);
        transform-origin: bottom left;
    }
}

// Efeito de borda animada
@mixin animated-border($color: $secondary-color, $duration: 0.3s) {
    position: relative;
    
    &::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border: 2px solid transparent;
        transition: border-color $duration ease;
    }
    
    &:hover::before {
        border-color: $color;
    }
}
EOT;

// Escrever arquivo de mixins
file_put_contents($mixins_file, $mixins_content);
echo "Arquivo de mixins SCSS gerado com sucesso em: $mixins_file\n";

// Gerar arquivo de reset SCSS
$reset_file = __DIR__ . '/../src/scss/_reset.scss';
$reset_content = <<<'EOT'
/**
 * Reset CSS para o tema Thabatta Advocacia
 * 
 * @package Thabatta_Advocacia
 */

*, *::before, *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

html {
    font-size: $base-font-size;
    -webkit-text-size-adjust: 100%;
    -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
    scroll-behavior: smooth;
}

body {
    font-family: $body-font;
    font-size: 1rem;
    line-height: $base-line-height;
    color: $text-color;
    background-color: $background-color;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    overflow-x: hidden;
}

article, aside, figcaption, figure, footer, header, hgroup, main, nav, section {
    display: block;
}

a {
    color: $link-color;
    text-decoration: none;
    background-color: transparent;
    transition: color $transition-speed $transition-timing;
    
    &:hover {
        color: $link-hover-color;
        text-decoration: underline;
    }
    
    &:focus {
        outline: thin dotted;
    }
    
    &:not([href]):not([tabindex]) {
        color: inherit;
        text-decoration: none;
        
        &:hover, &:focus {
            color: inherit;
            text-decoration: none;
        }
        
        &:focus {
            outline: 0;
        }
    }
}

img {
    max-width: 100%;
    height: auto;
    vertical-align: middle;
    border-style: none;
}

svg {
    overflow: hidden;
    vertical-align: middle;
}

table {
    border-collapse: collapse;
}

button, input, optgroup, select, textarea {
    margin: 0;
    font-family: inherit;
    font-size: inherit;
    line-height: inherit;
}

button, input {
    overflow: visible;
}

button, select {
    text-transform: none;
}

button, [type="button"], [type="reset"], [type="submit"] {
    -webkit-appearance: button;
    
    &:not(:disabled) {
        cursor: pointer;
    }
}

button::-moz-focus-inner, [type="button"]::-moz-focus-inner, [type="reset"]::-moz-focus-inner, [type="submit"]::-moz-focus-inner {
    padding: 0;
    border-style: none;
}

textarea {
    overflow: auto;
    resize: vertical;
}

fieldset {
    min-width: 0;
    padding: 0;
    margin: 0;
    border: 0;
}

legend {
    display: block;
    width: 100%;
    max-width: 100%;
    padding: 0;
    margin-bottom: 0.5rem;
    font-size: 1.5rem;
    line-height: inherit;
    color: inherit;
    white-space: normal;
}

progress {
    vertical-align: baseline;
}

[type="checkbox"], [type="radio"] {
    box-sizing: border-box;
    padding: 0;
}

[type="number"]::-webkit-inner-spin-button, [type="number"]::-webkit-outer-spin-button {
    height: auto;
}

[type="search"] {
    outline-offset: -2px;
    -webkit-appearance: none;
}

[type="search"]::-webkit-search-decoration {
    -webkit-appearance: none;
}

::-webkit-file-upload-button {
    font: inherit;
    -webkit-appearance: button;
}

[hidden] {
    display: none !important;
}

h1, h2, h3, h4, h5, h6 {
    margin-top: 0;
    margin-bottom: 0.5rem;
    font-family: $heading-font;
    font-weight: 700;
    line-height: $heading-line-height;
    color: inherit;
}

p {
    margin-top: 0;
    margin-bottom: 1rem;
}

ol, ul, dl {
    margin-top: 0;
    margin-bottom: 1rem;
    padding-left: 2rem;
}

ol ol, ul ul, ol ul, ul ol {
    margin-bottom: 0;
}

blockquote {
    margin: 0 0 1rem;
}

small {
    font-size: 80%;
}

sub, sup {
    position: relative;
    font-size: 75%;
    line-height: 0;
    vertical-align: baseline;
}

sub {
    bottom: -0.25em;
}

sup {
    top: -0.5em;
}

code, kbd, pre, samp {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size: 1em;
}

pre {
    margin-top: 0;
    margin-bottom: 1rem;
    overflow: auto;
}

figure {
    margin: 0 0 1rem;
}

address {
    margin-bottom: 1rem;
    font-style: normal;
    line-height: inherit;
}

hr {
    box-sizing: content-box;
    height: 0;
    overflow: visible;
    margin-top: 1rem;
    margin-bottom: 1rem;
    border: 0;
    border-top: 1px solid $border-color;
}

EOT;

// Escrever arquivo de reset
file_put_contents($reset_file, $reset_content);
echo "Arquivo de reset SCSS gerado com sucesso em: $reset_file\n";

// Criar diretórios para componentes e páginas
$components_dir = __DIR__ . '/../src/scss/components';
$pages_dir = __DIR__ . '/../src/scss/pages';

if (!file_exists($components_dir)) {
    mkdir($components_dir, 0755, true);
    echo "Diretório de componentes SCSS criado com sucesso em: $components_dir\n";
}

if (!file_exists($pages_dir)) {
    mkdir($pages_dir, 0755, true);
    echo "Diretório de páginas SCSS criado com sucesso em: $pages_dir\n";
}

echo "Geração de arquivos SCSS concluída com sucesso!\n";
