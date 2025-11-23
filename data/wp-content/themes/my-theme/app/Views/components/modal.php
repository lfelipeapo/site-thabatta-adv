<?php
/**
 * Componente de Web Component para Modal
 * 
 * @package WPFramework
 */

// Previne acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

// Obtém os atributos
$id = isset($id) ? $id : 'modal-' . uniqid();
$title = isset($title) ? $title : '';
$content = isset($content) ? $content : '';
$footer = isset($footer) ? $footer : '';
$size = isset($size) ? $size : 'medium'; // small, medium, large
$trigger_text = isset($trigger_text) ? $trigger_text : __('Abrir Modal', 'wpframework');
$trigger_class = isset($trigger_class) ? $trigger_class : 'button';
$close_text = isset($close_text) ? $close_text : __('Fechar', 'wpframework');
?>

<wp-modal id="<?php echo esc_attr($id); ?>" size="<?php echo esc_attr($size); ?>">
    <button slot="trigger" class="<?php echo esc_attr($trigger_class); ?>"><?php echo esc_html($trigger_text); ?></button>
    
    <div slot="header">
        <?php if (!empty($title)): ?>
            <h3 class="modal-title"><?php echo esc_html($title); ?></h3>
        <?php endif; ?>
    </div>
    
    <div slot="content">
        <?php echo $content; ?>
    </div>
    
    <div slot="footer">
        <?php if (!empty($footer)): ?>
            <?php echo $footer; ?>
        <?php else: ?>
            <button class="button close-modal"><?php echo esc_html($close_text); ?></button>
        <?php endif; ?>
    </div>
</wp-modal>

<script>
// Registra o componente se ainda não estiver registrado
if (!customElements.get('wp-modal')) {
    class WpModal extends HTMLElement {
        constructor() {
            super();
            
            // Cria Shadow DOM
            this.attachShadow({ mode: 'open' });
            
            // Adiciona estilos ao Shadow DOM
            const style = document.createElement('style');
            style.textContent = `
                :host {
                    display: block;
                }
                
                .modal-container {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.5);
                    z-index: 1000;
                    justify-content: center;
                    align-items: center;
                }
                
                .modal-container.open {
                    display: flex;
                }
                
                .modal-dialog {
                    background-color: #fff;
                    border-radius: 8px;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
                    max-width: 90%;
                    max-height: 90%;
                    overflow: hidden;
                    display: flex;
                    flex-direction: column;
                }
                
                .modal-dialog.small {
                    width: 400px;
                }
                
                .modal-dialog.medium {
                    width: 600px;
                }
                
                .modal-dialog.large {
                    width: 800px;
                }
                
                .modal-header {
                    padding: 15px 20px;
                    border-bottom: 1px solid #eee;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                
                .modal-title {
                    margin: 0;
                    font-size: 1.25rem;
                    color: #333;
                }
                
                .modal-close {
                    background: none;
                    border: none;
                    font-size: 1.5rem;
                    cursor: pointer;
                    color: #999;
                }
                
                .modal-close:hover {
                    color: #333;
                }
                
                .modal-body {
                    padding: 20px;
                    overflow-y: auto;
                    flex: 1;
                }
                
                .modal-footer {
                    padding: 15px 20px;
                    border-top: 1px solid #eee;
                    display: flex;
                    justify-content: flex-end;
                    gap: 10px;
                }
                
                button {
                    cursor: pointer;
                }
                
                @media (max-width: 768px) {
                    .modal-dialog.small,
                    .modal-dialog.medium,
                    .modal-dialog.large {
                        width: 95%;
                    }
                }
            `;
            
            // Cria a estrutura do modal
            const template = document.createElement('template');
            template.innerHTML = `
                <slot name="trigger"></slot>
                
                <div class="modal-container">
                    <div class="modal-dialog">
                        <div class="modal-header">
                            <slot name="header"></slot>
                            <button class="modal-close">&times;</button>
                        </div>
                        <div class="modal-body">
                            <slot name="content"></slot>
                        </div>
                        <div class="modal-footer">
                            <slot name="footer"></slot>
                        </div>
                    </div>
                </div>
            `;
            
            // Adiciona ao Shadow DOM
            this.shadowRoot.appendChild(style);
            this.shadowRoot.appendChild(template.content.cloneNode(true));
            
            // Referências aos elementos
            this.modalContainer = this.shadowRoot.querySelector('.modal-container');
            this.modalDialog = this.shadowRoot.querySelector('.modal-dialog');
            this.closeButton = this.shadowRoot.querySelector('.modal-close');
            
            // Adiciona event listeners
            this.addEventListeners();
        }
        
        connectedCallback() {
            // Define o tamanho do modal
            const size = this.getAttribute('size') || 'medium';
            this.modalDialog.classList.add(size);
            
            // Verifica se o modal deve ser aberto automaticamente
            if (this.hasAttribute('auto-open')) {
                setTimeout(() => this.open(), 100);
            }
        }
        
        addEventListeners() {
            // Botão de trigger
            const triggerSlot = this.shadowRoot.querySelector('slot[name="trigger"]');
            triggerSlot.addEventListener('slotchange', () => {
                const trigger = triggerSlot.assignedElements()[0];
                if (trigger) {
                    trigger.addEventListener('click', () => this.open());
                }
            });
            
            // Botão de fechar
            this.closeButton.addEventListener('click', () => this.close());
            
            // Fechar ao clicar fora do modal
            this.modalContainer.addEventListener('click', (e) => {
                if (e.target === this.modalContainer) {
                    this.close();
                }
            });
            
            // Botões de fechar no footer
            const footerSlot = this.shadowRoot.querySelector('slot[name="footer"]');
            footerSlot.addEventListener('slotchange', () => {
                const footerElements = footerSlot.assignedElements();
                footerElements.forEach(element => {
                    const closeButtons = element.querySelectorAll('.close-modal');
                    closeButtons.forEach(button => {
                        button.addEventListener('click', () => this.close());
                    });
                });
            });
            
            // Tecla ESC para fechar
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isOpen()) {
                    this.close();
                }
            });
        }
        
        open() {
            this.modalContainer.classList.add('open');
            document.body.style.overflow = 'hidden';
            
            // Dispara evento
            this.dispatchEvent(new CustomEvent('modal-open', {
                bubbles: true,
                composed: true
            }));
        }
        
        close() {
            this.modalContainer.classList.remove('open');
            document.body.style.overflow = '';
            
            // Dispara evento
            this.dispatchEvent(new CustomEvent('modal-close', {
                bubbles: true,
                composed: true
            }));
        }
        
        isOpen() {
            return this.modalContainer.classList.contains('open');
        }
        
        // Observa mudanças nos atributos
        static get observedAttributes() {
            return ['size', 'auto-open'];
        }
        
        attributeChangedCallback(name, oldValue, newValue) {
            if (name === 'size' && this.modalDialog) {
                // Remove classes de tamanho anteriores
                this.modalDialog.classList.remove('small', 'medium', 'large');
                // Adiciona a nova classe de tamanho
                this.modalDialog.classList.add(newValue || 'medium');
            }
        }
    }
    
    // Registra o elemento personalizado
    customElements.define('wp-modal', WpModal);
    
    // Fallback para navegadores que não suportam dialog
    if (typeof HTMLDialogElement !== 'function') {
        console.log('Este navegador não suporta o elemento dialog nativo. Usando wp-modal como fallback.');
    }
}
</script>
