/**
 * Arquivo JavaScript principal do tema Thabatta Advocacia
 */

(function($) {
    'use strict';

    // Menu Mobile
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNavigation = document.querySelector('.main-navigation');
    const body = document.querySelector('body');

    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            mainNavigation.classList.toggle('active');
            body.classList.toggle('menu-open');
        });
    }

    // Fechar menu ao clicar fora
    document.addEventListener('click', function(event) {
        if (mainNavigation && mainNavigation.classList.contains('active') && !mainNavigation.contains(event.target) && !menuToggle.contains(event.target)) {
            mainNavigation.classList.remove('active');
            body.classList.remove('menu-open');
        }
    });

    // Rolagem suave para links de âncora
    $(document).on('click', 'a[href^="#"]', function(event) {
        event.preventDefault();
        
        const target = $(this.getAttribute('href'));
        
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 800);
            
            // Fechar menu mobile se estiver aberto
            if (mainNavigation && mainNavigation.classList.contains('active')) {
                mainNavigation.classList.remove('active');
                body.classList.remove('menu-open');
            }
        }
    });

    // Formulário Multi-etapas
    const multiStepForm = {
        init: function() {
            this.form = $('.multi-step-form');
            
            if (!this.form.length) return;
            
            this.steps = this.form.find('.step-content');
            this.stepIndicators = this.form.find('.step-item');
            this.nextBtn = this.form.find('.btn-next');
            this.prevBtn = this.form.find('.btn-prev');
            this.submitBtn = this.form.find('.btn-submit');
            this.currentStep = 0;
            
            this.showStep(this.currentStep);
            this.bindEvents();
        },
        
        bindEvents: function() {
            const self = this;
            
            this.nextBtn.on('click', function() {
                self.nextStep();
            });
            
            this.prevBtn.on('click', function() {
                self.prevStep();
            });
            
            this.form.on('submit', function(e) {
                e.preventDefault();
                
                if (self.validateStep(self.currentStep)) {
                    // Aqui você pode adicionar o código para enviar o formulário via AJAX
                    alert('Formulário enviado com sucesso!');
                    self.resetForm();
                }
            });
        },
        
        showStep: function(stepIndex) {
            this.steps.removeClass('active');
            this.steps.eq(stepIndex).addClass('active');
            
            this.stepIndicators.removeClass('active completed');
            
            // Atualizar indicadores de etapa
            this.stepIndicators.each(function(index) {
                if (index < stepIndex) {
                    $(this).addClass('completed');
                } else if (index === stepIndex) {
                    $(this).addClass('active');
                }
            });
            
            // Atualizar botões
            if (stepIndex === 0) {
                this.prevBtn.hide();
            } else {
                this.prevBtn.show();
            }
            
            if (stepIndex === this.steps.length - 1) {
                this.nextBtn.hide();
                this.submitBtn.show();
            } else {
                this.nextBtn.show();
                this.submitBtn.hide();
            }
        },
        
        nextStep: function() {
            if (this.validateStep(this.currentStep)) {
                this.currentStep++;
                if (this.currentStep < this.steps.length) {
                    this.showStep(this.currentStep);
                }
            }
        },
        
        prevStep: function() {
            this.currentStep--;
            if (this.currentStep >= 0) {
                this.showStep(this.currentStep);
            }
        },
        
        validateStep: function(stepIndex) {
            const inputs = this.steps.eq(stepIndex).find('input, select, textarea');
            let isValid = true;
            
            inputs.each(function() {
                if ($(this).prop('required') && !$(this).val().trim()) {
                    isValid = false;
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                }
            });
            
            return isValid;
        },
        
        resetForm: function() {
            this.form[0].reset();
            this.currentStep = 0;
            this.showStep(this.currentStep);
        }
    };

    // Inicializar componentes quando o DOM estiver pronto
    $(document).ready(function() {
        // Inicializar formulário multi-etapas
        multiStepForm.init();
        
        // Inicializar carrossel de depoimentos se o Slick estiver disponível
        if ($.fn.slick) {
            $('.testimonials-carousel').slick({
                dots: true,
                arrows: false,
                infinite: true,
                speed: 500,
                slidesToShow: 3,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 5000,
                responsive: [
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 2
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 1
                        }
                    }
                ]
            });
        }
        
        // Adicionar classe ao header ao rolar a página
        $(window).scroll(function() {
            if ($(this).scrollTop() > 100) {
                $('.site-header').addClass('scrolled');
            } else {
                $('.site-header').removeClass('scrolled');
            }
        });
    });

})(jQuery);
