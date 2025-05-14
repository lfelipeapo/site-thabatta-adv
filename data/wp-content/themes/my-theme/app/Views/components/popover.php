<?php
/**
 * Componente de Web Component para Popover
 * 
 * @package WPFramework
 */

// Previne acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

// Obtém os atributos
$id = isset($id) ? $id : 'popover-' . uniqid();
$title = isset($title) ? $title : '';
$content = isset($content) ? $content : '';
$trigger_text = isset($trigger_text) ? $trigger_text : __('Mostrar Popover', 'wpframework');
$trigger_class = isset($trigger_class) ? $trigger_class : 'button';
$position = isset($position) ? $position : 'bottom'; // top, right, bottom, left
?>

<wp-popover id="<?php echo esc_attr($id); ?>" position="<?php echo esc_attr($position); ?>">
    <button slot="trigger" class="<?php echo esc_attr($trigger_class); ?>" popovertarget="<?php echo esc_attr($id); ?>-native" popover="auto">
        <?php echo esc_html($trigger_text); ?>
    </button>
    
    <div slot="content">
        <?php if (!empty($title)): ?>
            <h3 class="popover-title"><?php echo esc_html($title); ?></h3>
        <?php endif; ?>
        
        <div class="popover-body">
            <?php echo $content; ?>
        </div>
    </div>
    
    <!-- Fallback para navegadores que suportam popover nativo -->
    <div id="<?php echo esc_attr($id); ?>-native" popover="auto" class="native-popover">
        <?php if (!empty($title)): ?>
            <h3 class="popover-title"><?php echo esc_html($title); ?></h3>
        <?php endif; ?>
        
        <div class="popover-body">
            <?php echo $content; ?>
        </div>
    </div>
</wp-popover>

<script>
// Registra o componente se ainda não estiver registrado
if (!customElements.get('wp-popover')) {
    class WpPopover extends HTMLElement {
        constructor() {
            super();
            
            // Verifica se o navegador suporta popover nativo
            this.supportsNativePopover = HTMLElement.prototype.hasOwnProperty('popover');
            
            if (!this.supportsNativePopover) {
                // Cria Shadow DOM para navegadores sem suporte nativo
                this.attachShadow({ mode: 'open' });
                
                // Adiciona estilos ao Shadow DOM
                const style = document.createElement('style');
                style.textContent = `
                    :host {
                        display: inline-block;
                        position: relative;
                    }
                    
                    .popover-container {
                        display: none;
                        position: absolute;
                        background-color: #fff;
                        border-radius: 4px;
                        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                        padding: 10px;
                        z-index: 1000;
                        min-width: 200px;
                        max-width: 300px;
                    }
                    
                    .popover-container.open {
                        display: block;
                    }
                    
                    .popover-container.top {
                        bottom: 100%;
                        left: 50%;
                        transform: translateX(-50%) translateY(-10px);
                    }
                    
                    .popover-container.right {
                        left: 100%;
                        top: 50%;
                        transform: translateY(-50%) translateX(10px);
                    }
                    
                    .popover-container.bottom {
                        top: 100%;
                        left: 50%;
                        transform: translateX(-50%) translateY(10px);
                    }
                    
                    .popover-container.left {
                        right: 100%;
                        top: 50%;
                        transform: translateY(-50%) translateX(-10px);
                    }
                    
                    .popover-arrow {
                        position: absolute;
                        width: 10px;
                        height: 10px;
                        background-color: #fff;
                        transform: rotate(45deg);
                    }
                    
                    .popover-container.top .popover-arrow {
                        bottom: -5px;
                        left: 50%;
                        margin-left: -5px;
                        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                        border-right: 1px solid rgba(0, 0, 0, 0.1);
                    }
                    
                    .popover-container.right .popover-arrow {
                        left: -5px;
                        top: 50%;
                        margin-top: -5px;
                        border-left: 1px solid rgba(0, 0, 0, 0.1);
                        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                    }
                    
                    .popover-container.bottom .popover-arrow {
                        top: -5px;
                        left: 50%;
                        margin-left: -5px;
                        border-top: 1px solid rgba(0, 0, 0, 0.1);
                        border-left: 1px solid rgba(0, 0, 0, 0.1);
                    }
                    
                    .popover-container.left .popover-arrow {
                        right: -5px;
                        top: 50%;
                        margin-top: -5px;
                        border-top: 1px solid rgba(0, 0, 0, 0.1);
                        border-right: 1px solid rgba(0, 0, 0, 0.1);
                    }
                    
                    .popover-title {
                        margin-top: 0;
                        margin-bottom: 8px;
                        font-size: 1rem;
                        font-weight: bold;
                        color: #333;
                    }
                    
                    .popover-body {
                        font-size: 0.9rem;
                        color: #666;
                    }
                `;
                
                // Cria a estrutura do popover
                const template = document.createElement('template');
                template.innerHTML = `
                    <slot name="trigger"></slot>
                    
                    <div class="popover-container">
                        <div class="popover-arrow"></div>
                        <div class="popover-content">
                            <slot name="content"></slot>
                        </div>
                    </div>
                `;
                
                // Adiciona ao Shadow DOM
                this.shadowRoot.appendChild(style);
                this.shadowRoot.appendChild(template.content.cloneNode(true));
                
                // Referências aos elementos
                this.popoverContainer = this.shadowRoot.querySelector('.popover-container');
                
                // Adiciona event listeners
                this.addEventListeners();
            } else {
                // Se o navegador suporta popover nativo, esconde o componente customizado
                this.style.display = 'none';
            }
        }
        
        connectedCallback() {
            if (!this.supportsNativePopover) {
                // Define a posição do popover
                const position = this.getAttribute('position') || 'bottom';
                this.popoverContainer.classList.add(position);
            } else {
                // Adiciona estilos para o popover nativo
                const style = document.createElement('style');
                style.textContent = `
                    .native-popover {
                        padding: 10px;
                        border-radius: 4px;
                        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                        max-width: 300px;
                    }
                    
                    .native-popover .popover-title {
                        margin-top: 0;
                        margin-bottom: 8px;
                        font-size: 1rem;
                        font-weight: bold;
                        color: #333;
                    }
                    
                    .native-popover .popover-body {
                        font-size: 0.9rem;
                        color: #666;
                    }
                `;
                document.head.appendChild(style);
            }
        }
        
        addEventListeners() {
            if (!this.supportsNativePopover) {
                // Botão de trigger
                const triggerSlot = this.shadowRoot.querySelector('slot[name="trigger"]');
                triggerSlot.addEventListener('slotchange', () => {
                    const trigger = triggerSlot.assignedElements()[0];
                    if (trigger) {
                        trigger.addEventListener('click', (e) => {
                            e.preventDefault();
                            this.toggle();
                        });
                    }
                });
                
                // Fechar ao clicar fora do popover
                document.addEventListener('click', (e) => {
                    if (this.isOpen() && !this.contains(e.target)) {
                        this.close();
                    }
                });
                
                // Tecla ESC para fechar
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.isOpen()) {
                        this.close();
                    }
                });
            }
        }
        
        toggle() {
            if (this.isOpen()) {
                this.close();
            } else {
                this.open();
            }
        }
        
        open() {
            if (!this.supportsNativePopover) {
                this.popoverContainer.classList.add('open');
                
                // Dispara evento
                this.dispatchEvent(new CustomEvent('popover-open', {
                    bubbles: true,
                    composed: true
                }));
            }
        }
        
        close() {
            if (!this.supportsNativePopover) {
                this.popoverContainer.classList.remove('open');
                
                // Dispara evento
                this.dispatchEvent(new CustomEvent('popover-close', {
                    bubbles: true,
                    composed: true
                }));
            }
        }
        
        isOpen() {
            return this.popoverContainer && this.popoverContainer.classList.contains('open');
        }
        
        // Observa mudanças nos atributos
        static get observedAttributes() {
            return ['position'];
        }
        
        attributeChangedCallback(name, oldValue, newValue) {
            if (name === 'position' && this.popoverContainer && !this.supportsNativePopover) {
                // Remove classes de posição anteriores
                this.popoverContainer.classList.remove('top', 'right', 'bottom', 'left');
                // Adiciona a nova classe de posição
                this.popoverContainer.classList.add(newValue || 'bottom');
            }
        }
    }
    
    // Registra o elemento personalizado
    customElements.define('wp-popover', WpPopover);
}
</script>
