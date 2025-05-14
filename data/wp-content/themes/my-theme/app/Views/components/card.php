<?php
/**
 * Componente de Web Component para Card
 * 
 * @package WPFramework
 */

// Previne acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

// Obtém os atributos
$title = isset($title) ? $title : '';
$content = isset($content) ? $content : '';
$image = isset($image) ? $image : '';
$link = isset($link) ? $link : '';
$footer = isset($footer) ? $footer : '';
$class = isset($class) ? $class : '';
?>

<wp-card class="<?php echo esc_attr($class); ?>">
    <?php if (!empty($image)): ?>
        <div class="card-image">
            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
        </div>
    <?php endif; ?>
    
    <div class="card-body">
        <?php if (!empty($title)): ?>
            <h3 class="card-title">
                <?php if (!empty($link)): ?>
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                <?php else: ?>
                    <?php echo esc_html($title); ?>
                <?php endif; ?>
            </h3>
        <?php endif; ?>
        
        <?php if (!empty($content)): ?>
            <div class="card-content">
                <?php echo $content; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($footer)): ?>
        <div class="card-footer">
            <?php echo $footer; ?>
        </div>
    <?php endif; ?>
</wp-card>

<script>
// Registra o componente se ainda não estiver registrado
if (!customElements.get('wp-card')) {
    class WpCard extends HTMLElement {
        constructor() {
            super();
            
            // Cria Shadow DOM se suportado
            if (this.attachShadow) {
                this.attachShadow({ mode: 'open' });
                
                // Adiciona estilos ao Shadow DOM
                const style = document.createElement('style');
                style.textContent = `
                    :host {
                        display: block;
                        border-radius: 8px;
                        overflow: hidden;
                        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                        background-color: #fff;
                        margin-bottom: 20px;
                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                    }
                    
                    :host(:hover) {
                        transform: translateY(-5px);
                        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
                    }
                    
                    .card-image img {
                        width: 100%;
                        height: auto;
                        display: block;
                    }
                    
                    .card-body {
                        padding: 20px;
                    }
                    
                    .card-title {
                        margin-top: 0;
                        margin-bottom: 15px;
                        font-size: 1.25rem;
                        color: #333;
                    }
                    
                    .card-title a {
                        color: #333;
                        text-decoration: none;
                    }
                    
                    .card-title a:hover {
                        color: #0066cc;
                    }
                    
                    .card-content {
                        color: #666;
                        line-height: 1.5;
                    }
                    
                    .card-footer {
                        padding: 15px 20px;
                        border-top: 1px solid #eee;
                        background-color: #f9f9f9;
                    }
                `;
                
                // Adiciona o conteúdo ao Shadow DOM
                const slot = document.createElement('slot');
                
                this.shadowRoot.appendChild(style);
                this.shadowRoot.appendChild(slot);
            }
        }
    }
    
    // Registra o elemento personalizado
    customElements.define('wp-card', WpCard);
}
</script>
