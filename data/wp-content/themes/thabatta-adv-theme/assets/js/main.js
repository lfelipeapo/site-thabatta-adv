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

        // FORMULÁRIO DE CONSULTA
        // Elementos do formulário
        const consultationForm = document.getElementById('consultationForm');
        const multiStepForm = document.getElementById('multiStepForm');
        const steps = document.querySelectorAll('.step');
        const stepIndicators = document.querySelectorAll('.step-indicator');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        const formSuccess = document.getElementById('formSuccess');
        
        // IMPORTANTE: Gerenciar eventos de botões de abertura do formulário
        $('.open-consultation-form').on('click', function(e) {
            e.preventDefault();
            if (consultationForm) {
                $(consultationForm).addClass('active');
                $('body').css('overflow', 'hidden');
                console.log('Formulário de consulta aberto');
            } else {
                console.error('Elemento do formulário de consulta não encontrado');
            }
        });
        
        // Fechar o formulário
        $('.close-form, .form-overlay').on('click', function() {
            $(consultationForm).removeClass('active');
            $('body').css('overflow', '');
            resetFormState();
        });
        
        // Fechar mensagem de sucesso
        $('.close-success').on('click', function() {
            $(consultationForm).removeClass('active');
            $('body').css('overflow', '');
            resetFormState();
        });
        
        // Resetar estado do formulário
        function resetFormState() {
            if (multiStepForm) {
                multiStepForm.reset();
                showFormStep(1);
                
                // Ocultar mensagem de sucesso
                $(formSuccess).addClass('hidden');
                
                // Mostrar formulário
                $(multiStepForm).show();
            }
        }
        
        // Mostrar etapa específica
        function showFormStep(stepNumber) {
            if (!steps.length) return;
            
            const currentStep = stepNumber;
            
            // Atualizar visibilidade dos botões
            $(prevBtn).toggleClass('hidden', currentStep === 1);
            $(nextBtn).toggleClass('hidden', currentStep === steps.length);
            $(submitBtn).toggleClass('hidden', currentStep !== steps.length);
            
            // Atualizar classe ativa para a etapa atual
            $(steps).removeClass('active');
            $(steps[currentStep - 1]).addClass('active');
            
            // Atualizar indicadores de etapa
            $(stepIndicators).each(function(index) {
                if (index + 1 < currentStep) {
                    $(this).addClass('completed').removeClass('active');
                } else if (index + 1 === currentStep) {
                    $(this).addClass('active').removeClass('completed');
                } else {
                    $(this).removeClass('active completed');
                }
            });
        }
        
        // Validar etapa atual
        function validateFormStep(stepIndex) {
            const currentStepEl = steps[stepIndex - 1];
            const requiredFields = currentStepEl.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                $(field).removeClass('invalid');
                $('.error-message').remove();
                
                if (!field.value.trim()) {
                    isValid = false;
                    $(field).addClass('invalid');
                    
                    const errorMsg = $('<span class="error-message" style="color: red; font-size: 12px;">Este campo é obrigatório</span>');
                    $(field).after(errorMsg);
                }
            });
            
            return isValid;
        }
        
        // Botão Próximo
        $(nextBtn).on('click', function() {
            const currentStep = $(steps).index($(steps).filter('.active')) + 1;
            if (validateFormStep(currentStep)) {
                showFormStep(currentStep + 1);
            }
        });
        
        // Botão Anterior
        $(prevBtn).on('click', function() {
            const currentStep = $(steps).index($(steps).filter('.active')) + 1;
            showFormStep(currentStep - 1);
        });
        
        // Envio do formulário
        $(multiStepForm).on('submit', function(e) {
            e.preventDefault();
            
            const currentStep = $(steps).index($(steps).filter('.active')) + 1;
            if (!validateFormStep(currentStep)) {
                return;
            }
            
            // Coleta dos dados do formulário
            const formData = new FormData(multiStepForm);
            formData.append('action', 'thabatta_submit_consultation');
            
            // Desabilita o botão de envio e mostra carregamento
            $(submitBtn).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
            
            // Enviar dados via AJAX
            $.ajax({
                url: thabattaData.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $(submitBtn).prop('disabled', false).text('Enviar');
                    
                    if (response.success) {
                        // Mostrar mensagem de sucesso
                        $(multiStepForm).hide();
                        $(formSuccess).removeClass('hidden');
                        multiStepForm.reset();
                    } else {
                        // Mostrar erro
                        alert(response.data || 'Ocorreu um erro ao enviar sua consulta. Por favor, tente novamente.');
                    }
                },
                error: function() {
                    $(submitBtn).prop('disabled', false).text('Enviar');
                    alert('Ocorreu um erro ao enviar sua consulta. Por favor, tente novamente.');
                }
            });
        });
        
        // Aplicar máscaras aos campos se jQuery.mask existe
        if ($.fn.mask) {
            $('.phone-mask').mask('(00) 00000-0000');
            $('.cpfcnpj-mask').mask('000.000.000-000', {
                onKeyPress: function(cpf, e, field, options) {
                    let masks = ['000.000.000-000', '00.000.000/0000-00'];
                    let mask = (cpf.length > 14) ? masks[1] : masks[0];
                    $('.cpfcnpj-mask').mask(mask, options);
                }
            });
        }
        
        // Inicializar formulário se existir
        if (consultationForm && multiStepForm) {
            // Verificar se os botões do formulário estão presentes
            console.log('Formulário de consulta encontrado, inicializando...');
            showFormStep(1);
        }
    });

})(jQuery); 