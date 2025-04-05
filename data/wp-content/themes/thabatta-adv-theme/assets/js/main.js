/**
 * Main JavaScript file for Thabatta Advocacia theme
 */
(function($) {
    'use strict';

    // Document ready
    $(document).ready(function() {
        // Smooth scrolling for anchor links
        $('a[href*="#"]:not([href="#"]):not([href="#0"])').click(function() {
            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 1000);
                    return false;
                }
            }
        });

        // Adicionar classe ao header quando rolar a página
        const $header = $('.site-header');
        const headerHeight = $header.outerHeight();
        const $body = $('body');

        $(window).scroll(function() {
            if ($(this).scrollTop() > 100) {
                $header.addClass('sticky');
                $body.css('padding-top', headerHeight + 'px');
            } else {
                $header.removeClass('sticky');
                $body.css('padding-top', '0');
            }
        });

        // Mobile menu toggle
        $('.menu-toggle').on('click', function(e) {
            e.preventDefault();
            $('.main-navigation').toggleClass('toggled');
            $('body').toggleClass('menu-open');
        });

        // Dropdown menus for mobile
        $('.menu-item-has-children > a, .page_item_has_children > a').append('<span class="dropdown-toggle"><i class="fas fa-chevron-down"></i></span>');

        $('.dropdown-toggle').on('click', function(e) {
            e.preventDefault();
            $(this).toggleClass('toggle-on');
            $(this).parent().next('.sub-menu, .children').toggleClass('toggled-on');
        });

        // Adicionar funcionalidade de Accordion
        $('.accordion-header').on('click', function() {
            $(this).toggleClass('active');
            $(this).next('.accordion-content').slideToggle(300);
        });

        // Inicializar sliders se o plugin slick estiver carregado
        if (typeof $.fn.slick !== 'undefined') {
            // Slider de Depoimentos
            $('.testimonial-slider').slick({
                dots: true,
                infinite: true,
                speed: 300,
                slidesToShow: 1,
                adaptiveHeight: true,
                autoplay: true,
                autoplaySpeed: 5000
            });

            // Slider de Áreas de Atuação
            $('.areas-slider').slick({
                dots: true,
                infinite: true,
                speed: 300,
                slidesToShow: 3,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 4000,
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

        // Inicializar contador de números
        function startCounter() {
            $('.counter').each(function() {
                const $this = $(this);
                const countTo = $this.attr('data-count');
                
                $({ countNum: $this.text() }).animate({
                    countNum: countTo
                }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function() {
                        $this.text(Math.floor(this.countNum));
                    },
                    complete: function() {
                        $this.text(this.countNum);
                    }
                });
            });
        }

        // Iniciar contador quando visível
        const $counters = $('.counter');
        if ($counters.length) {
            $(window).on('scroll', function() {
                const windowHeight = $(window).height();
                const scrollTop = $(window).scrollTop();
                
                $counters.each(function() {
                    const $this = $(this);
                    const offsetTop = $this.offset().top;
                    
                    if (scrollTop + windowHeight > offsetTop && !$this.hasClass('counted')) {
                        $this.addClass('counted');
                        startCounter();
                    }
                });
            });
        }

        // Formulário de contato - animação de labels
        $('.contact-form .form-control').on('focus blur', function(e) {
            $(this).parents('.form-group').toggleClass('focused', (e.type === 'focus' || this.value !== ''));
        }).trigger('blur');

        // Botão de voltar ao topo
        const $backToTop = $('.back-to-top');
        $(window).scroll(function() {
            if ($(window).scrollTop() > 300) {
                $backToTop.addClass('show');
            } else {
                $backToTop.removeClass('show');
            }
        });

        $backToTop.on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: 0
            }, 800);
        });

        // Animação AOS (se existir)
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true
            });
        }

        // Multi-step form functionality
        const multiStepForm = {
            init: function() {
                // Form container and controls
                this.consultationForm = $('#consultationForm');
                this.form = $('#multiStepForm');
                this.steps = $('.step');
                this.stepIndicators = $('.step-indicator');
                this.prevBtn = $('#prevBtn');
                this.nextBtn = $('#nextBtn');
                this.submitBtn = $('#submitBtn');
                this.formSuccess = $('#formSuccess');
                
                // Open/Close form triggers
                this.openFormBtn = $('.open-consultation-form');
                this.closeFormBtn = $('.close-form');
                this.formOverlay = $('.form-overlay');
                this.closeSuccessBtn = $('.close-success');
                
                // Current step
                this.currentStep = 0;
                
                // Initialize steps and events
                this.showStep(this.currentStep);
                this.bindEvents();
            },
            
            bindEvents: function() {
                const self = this;
                
                // Open form event
                this.openFormBtn.on('click', function(e) {
                    e.preventDefault();
                    self.openForm();
                });
                
                // Close form events
                this.closeFormBtn.on('click', this.closeForm.bind(this));
                this.formOverlay.on('click', this.closeForm.bind(this));
                this.closeSuccessBtn.on('click', this.closeForm.bind(this));
                
                // Form navigation
                this.prevBtn.on('click', this.prevStep.bind(this));
                this.nextBtn.on('click', this.nextStep.bind(this));
                
                // Form submission
                this.form.on('submit', function(e) {
                    e.preventDefault();
                    
                    if (self.validateStep(self.currentStep)) {
                        // Show loading state
                        self.submitBtn.prop('disabled', true).text('Enviando...');
                        
                        // Simulate AJAX form submission (replace with actual AJAX)
                        setTimeout(function() {
                            self.showSuccess();
                            self.submitBtn.prop('disabled', false).text('Enviar');
                            self.resetForm();
                        }, 1500);
                    }
                });
                
                // Input mask for phone and CPF/CNPJ if mask plugin is available
                if ($.fn.mask) {
                    $('.phone-mask').mask('(00) 00000-0000');
                    $('.cpfcnpj-mask').mask('000.000.000-00', {
                        onKeyPress: function(cpf, e, field, options) {
                            const masks = ['000.000.000-00', '00.000.000/0000-00'];
                            const mask = (cpf.length > 14) ? masks[1] : masks[0];
                            $('.cpfcnpj-mask').mask(mask, options);
                        }
                    });
                }
            },
            
            openForm: function() {
                this.consultationForm.addClass('active');
                $('body').addClass('form-open');
            },
            
            closeForm: function() {
                this.consultationForm.removeClass('active');
                this.formSuccess.addClass('hidden');
                $('body').removeClass('form-open');
            },
            
            showStep: function(stepIndex) {
                // Hide all steps
                this.steps.removeClass('active');
                
                // Show current step
                this.steps.eq(stepIndex).addClass('active');
                
                // Update step indicators
                this.stepIndicators.removeClass('active');
                this.stepIndicators.eq(stepIndex).addClass('active');
                
                // Update navigation buttons
                if (stepIndex === 0) {
                    this.prevBtn.addClass('hidden');
                } else {
                    this.prevBtn.removeClass('hidden');
                }
                
                if (stepIndex === this.steps.length - 1) {
                    this.nextBtn.addClass('hidden');
                    this.submitBtn.removeClass('hidden');
                } else {
                    this.nextBtn.removeClass('hidden');
                    this.submitBtn.addClass('hidden');
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
                const inputs = this.steps.eq(stepIndex).find('input[required], select[required], textarea[required]');
                let isValid = true;
                
                inputs.removeClass('error');
                
                inputs.each(function() {
                    if (!$(this).val()) {
                        $(this).addClass('error');
                        isValid = false;
                    }
                });
                
                return isValid;
            },
            
            showSuccess: function() {
                this.form.addClass('hidden');
                this.formSuccess.removeClass('hidden');
            },
            
            resetForm: function() {
                this.form[0].reset();
                this.currentStep = 0;
                this.showStep(this.currentStep);
                this.form.removeClass('hidden');
            }
        };

        // Initialize components when DOM is ready
        if ($('#multiStepForm').length) {
            multiStepForm.init();
        }
    });

})(jQuery); 