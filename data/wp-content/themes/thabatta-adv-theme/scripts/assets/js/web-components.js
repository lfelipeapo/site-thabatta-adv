/**
 * Componentes Web para o tema Thabatta Advocacia
 * 
 * @package Thabatta_Advocacia
 */

(function() {
    'use strict';

    /**
     * Componente de Acordeão
     */
    class ThabattaAccordion extends HTMLElement {
        constructor() {
            super();
            this.init();
        }

        init() {
            // Adicionar manipuladores de eventos aos botões do acordeão
            const buttons = this.querySelectorAll('.thabatta-accordion-button');
            
            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    const isExpanded = button.getAttribute('aria-expanded') === 'true';
                    const controlsId = button.getAttribute('aria-controls');
                    const content = document.getElementById(controlsId);
                    
                    // Alternar estado
                    button.setAttribute('aria-expanded', !isExpanded);
                    
                    if (isExpanded) {
                        content.setAttribute('hidden', '');
                    } else {
                        content.removeAttribute('hidden');
                    }
                });
            });
        }
    }

    /**
     * Componente de Abas
     */
    class ThabattaTabs extends HTMLElement {
        constructor() {
            super();
            this.init();
        }

        init() {
            // Adicionar manipuladores de eventos aos botões das abas
            const buttons = this.querySelectorAll('.thabatta-tab-button');
            const panels = this.querySelectorAll('.thabatta-tab-panel');
            
            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    const controlsId = button.getAttribute('aria-controls');
                    
                    // Desativar todas as abas
                    buttons.forEach(btn => {
                        btn.classList.remove('active');
                        btn.setAttribute('aria-selected', 'false');
                    });
                    
                    panels.forEach(panel => {
                        panel.classList.remove('active');
                        panel.setAttribute('hidden', '');
                    });
                    
                    // Ativar aba selecionada
                    button.classList.add('active');
                    button.setAttribute('aria-selected', 'true');
                    
                    const panel = document.getElementById(controlsId);
                    panel.classList.add('active');
                    panel.removeAttribute('hidden');
                });
            });
        }
    }

    /**
     * Componente de Slider
     */
    class ThabattaSlider extends HTMLElement {
        constructor() {
            super();
            this.currentSlide = 0;
            this.autoplayInterval = null;
            this.init();
        }

        init() {
            // Obter configurações
            this.autoplay = this.getAttribute('data-autoplay') === 'true';
            this.interval = parseInt(this.getAttribute('data-interval')) || 5000;
            this.showArrows = this.getAttribute('data-arrows') === 'true';
            this.showDots = this.getAttribute('data-dots') === 'true';
            
            // Obter elementos
            this.slides = this.querySelectorAll('.thabatta-slide');
            this.prevButton = this.querySelector('.thabatta-slider-prev');
            this.nextButton = this.querySelector('.thabatta-slider-next');
            this.dots = this.querySelectorAll('.thabatta-slider-dot');
            
            // Configurar manipuladores de eventos
            if (this.showArrows && this.prevButton && this.nextButton) {
                this.prevButton.addEventListener('click', () => this.prevSlide());
                this.nextButton.addEventListener('click', () => this.nextSlide());
            }
            
            if (this.showDots && this.dots.length > 0) {
                this.dots.forEach((dot, index) => {
                    dot.addEventListener('click', () => this.goToSlide(index));
                });
            }
            
            // Iniciar autoplay se necessário
            if (this.autoplay) {
                this.startAutoplay();
                
                // Pausar autoplay ao passar o mouse
                this.addEventListener('mouseenter', () => this.stopAutoplay());
                this.addEventListener('mouseleave', () => this.startAutoplay());
            }
            
            // Mostrar primeiro slide
            this.showSlide(0);
        }

        showSlide(index) {
            // Ocultar todos os slides
            this.slides.forEach(slide => {
                slide.style.display = 'none';
            });
            
            // Desativar todos os pontos
            if (this.dots.length > 0) {
                this.dots.forEach(dot => {
                    dot.classList.remove('active');
                });
            }
            
            // Mostrar slide atual
            this.slides[index].style.display = 'block';
            
            // Ativar ponto atual
            if (this.dots.length > 0) {
                this.dots[index].classList.add('active');
            }
            
            // Atualizar índice atual
            this.currentSlide = index;
        }

        nextSlide() {
            const newIndex = (this.currentSlide + 1) % this.slides.length;
            this.showSlide(newIndex);
        }

        prevSlide() {
            const newIndex = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
            this.showSlide(newIndex);
        }

        goToSlide(index) {
            this.showSlide(index);
        }

        startAutoplay() {
            if (this.autoplay && !this.autoplayInterval) {
                this.autoplayInterval = setInterval(() => {
                    this.nextSlide();
                }, this.interval);
            }
        }

        stopAutoplay() {
            if (this.autoplayInterval) {
                clearInterval(this.autoplayInterval);
                this.autoplayInterval = null;
            }
        }
    }

    /**
     * Componente de Contador
     */
    class ThabattaCounter extends HTMLElement {
        constructor() {
            super();
            this.init();
        }

        init() {
            // Obter configurações
            this.targetNumber = parseInt(this.getAttribute('data-number')) || 0;
            this.counterElement = this.querySelector('.thabatta-counter-value');
            
            // Configurar observador de interseção para iniciar contagem quando visível
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.startCounting();
                        observer.unobserve(this);
                    }
                });
            }, { threshold: 0.1 });
            
            observer.observe(this);
        }

        startCounting() {
            const duration = 2000; // 2 segundos
            const frameDuration = 1000 / 60; // 60 fps
            const totalFrames = Math.round(duration / frameDuration);
            const increment = this.targetNumber / totalFrames;
            
            let currentNumber = 0;
            let frame = 0;
            
            const counter = setInterval(() => {
                frame++;
                currentNumber += increment;
                
                if (frame === totalFrames) {
                    clearInterval(counter);
                    currentNumber = this.targetNumber;
                }
                
                this.counterElement.textContent = Math.floor(currentNumber);
                
                if (frame === totalFrames) {
                    this.counterElement.textContent = this.targetNumber;
                }
            }, frameDuration);
        }
    }

    // Registrar componentes personalizados
    customElements.define('thabatta-accordion', ThabattaAccordion);
    customElements.define('thabatta-tabs', ThabattaTabs);
    customElements.define('thabatta-slider', ThabattaSlider);
    customElements.define('thabatta-counter', ThabattaCounter);
    
    // Inicializar componentes que não usam Custom Elements API
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips
        const tooltips = document.querySelectorAll('[data-tooltip]');
        tooltips.forEach(tooltip => {
            tooltip.addEventListener('mouseenter', function() {
                const tooltipText = this.getAttribute('data-tooltip');
                const tooltipElement = document.createElement('div');
                tooltipElement.className = 'thabatta-tooltip';
                tooltipElement.textContent = tooltipText;
                document.body.appendChild(tooltipElement);
                
                const rect = this.getBoundingClientRect();
                tooltipElement.style.top = rect.top - tooltipElement.offsetHeight - 10 + 'px';
                tooltipElement.style.left = rect.left + (rect.width / 2) - (tooltipElement.offsetWidth / 2) + 'px';
                tooltipElement.style.opacity = '1';
                
                this.addEventListener('mouseleave', function() {
                    tooltipElement.remove();
                });
            });
        });
        
        // Inicializar botão voltar ao topo
        const backToTopButton = document.querySelector('.thabatta-back-to-top');
        if (backToTopButton) {
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTopButton.classList.add('show');
                } else {
                    backToTopButton.classList.remove('show');
                }
            });
            
            backToTopButton.addEventListener('click', function(e) {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
        
        // Inicializar menu mobile
        const menuToggle = document.querySelector('.thabatta-menu-toggle');
        const mobileMenu = document.querySelector('.thabatta-mobile-menu');
        
        if (menuToggle && mobileMenu) {
            menuToggle.addEventListener('click', function() {
                menuToggle.classList.toggle('active');
                mobileMenu.classList.toggle('active');
                document.body.classList.toggle('thabatta-menu-open');
            });
        }
    });
})();