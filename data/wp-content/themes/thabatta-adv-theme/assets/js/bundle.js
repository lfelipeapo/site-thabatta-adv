"use strict";

/**
 * Main JavaScript file for Thabatta Advocacia theme
 */
(function ($) {
  'use strict';

  // Document ready
  $(document).ready(function () {
    // Smooth scrolling for anchor links
    $('a[href*="#"]:not([href="#"]):not([href="#0"])').click(function () {
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
    var $header = $('.site-header');
    var headerHeight = $header.outerHeight();
    var $body = $('body');
    $(window).scroll(function () {
      if ($(this).scrollTop() > 100) {
        $header.addClass('sticky');
        $body.css('padding-top', headerHeight + 'px');
      } else {
        $header.removeClass('sticky');
        $body.css('padding-top', '0');
      }
    });

    // Mobile menu toggle
    $('.menu-toggle').on('click', function (e) {
      e.preventDefault();
      $('.main-navigation').toggleClass('toggled');
      $('body').toggleClass('menu-open');
    });

    // Dropdown menus for mobile
    $('.menu-item-has-children > a, .page_item_has_children > a').append('<span class="dropdown-toggle"><i class="fas fa-chevron-down"></i></span>');
    $('.dropdown-toggle').on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      $(this).toggleClass('toggle-on');
      $(this).parent().next('.sub-menu, .children').toggleClass('toggled-on');
    });

    // Adicionar funcionalidade de Accordion
    $('.accordion-header').on('click', function () {
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
        responsive: [{
          breakpoint: 992,
          settings: {
            slidesToShow: 2
          }
        }, {
          breakpoint: 768,
          settings: {
            slidesToShow: 1
          }
        }]
      });
    }

    // Inicializar contador de números
    function startCounter() {
      $('.counter').each(function () {
        var $this = $(this);
        var countTo = $this.attr('data-count');
        $({
          countNum: $this.text()
        }).animate({
          countNum: countTo
        }, {
          duration: 2000,
          easing: 'swing',
          step: function step() {
            $this.text(Math.floor(this.countNum));
          },
          complete: function complete() {
            $this.text(this.countNum);
          }
        });
      });
    }

    // Iniciar contador quando visível
    var $counters = $('.counter');
    if ($counters.length) {
      $(window).on('scroll', function () {
        var windowHeight = $(window).height();
        var scrollTop = $(window).scrollTop();
        $counters.each(function () {
          var $this = $(this);
          var offsetTop = $this.offset().top;
          if (scrollTop + windowHeight > offsetTop && !$this.hasClass('counted')) {
            $this.addClass('counted');
            startCounter();
          }
        });
      });
    }

    // Formulário de contato - animação de labels
    $('.contact-form .form-control').on('focus blur', function (e) {
      $(this).parents('.form-group').toggleClass('focused', e.type === 'focus' || this.value !== '');
    }).trigger('blur');

    // Botão de voltar ao topo
    var $backToTop = $('.back-to-top');
    $(window).scroll(function () {
      if ($(window).scrollTop() > 300) {
        $backToTop.addClass('show');
      } else {
        $backToTop.removeClass('show');
      }
    });
    $backToTop.on('click', function (e) {
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
  });
})(jQuery);

/**
 * Formulário de Consulta Multi-etapas
 */
(function ($) {
  $(document).ready(function () {
    // Elementos DOM
    var $consultationForm = $('#consultationForm');
    var $formOverlay = $('.form-overlay');
    var $formContainer = $('.form-container');
    var $multiStepForm = $('#multiStepForm');
    var $steps = $('.step');
    var $stepIndicators = $('.step-indicator');
    var $nextBtn = $('#nextBtn');
    var $prevBtn = $('#prevBtn');
    var $submitBtn = $('#submitBtn');
    var $closeFormBtn = $('.close-form');
    var $formSuccess = $('#formSuccess');
    var $closeSuccessBtn = $('.close-success');

    // Estado atual
    var currentStep = 1;
    var totalSteps = $steps.length;

    // Verificar se o formulário existe na página
    if (!$consultationForm.length) return;

    // Configurar máscaras de input se a biblioteca estiver disponível
    setupInputMasks();

    // Configurar event listeners
    setupEventListeners();

    // Configurar máscaras de input
    function setupInputMasks() {
      // Verificar se a biblioteca jQuery.mask está disponível
      if (typeof $.fn.mask === 'function') {
        $('.phone-mask').mask('(00) 00000-0000');
        $('.cpfcnpj-mask').mask('000.000.000-000', {
          onKeyPress: function onKeyPress(cpf, e, field, options) {
            var masks = ['000.000.000-000', '00.000.000/0000-00'];
            var mask = cpf.length > 14 ? masks[1] : masks[0];
            $('.cpfcnpj-mask').mask(mask, options);
          }
        });

        // Validação em tempo real para email
        $('input[type="email"]').on('blur', function () {
          var $field = $(this);
          var email = $field.val().trim();
          if (email && !isValidEmail(email)) {
            showFieldError($field, 'Por favor, insira um email válido');
          } else {
            $field.removeClass('is-invalid');
            $field.siblings('.invalid-feedback').removeClass('visible');
          }
        });
      } else {
        // Se a biblioteca não estiver disponível, configurar validação básica
        $('input[type="tel"]').on('input', function () {
          // Remover caracteres não numéricos
          $(this).val($(this).val().replace(/[^0-9]/g, ''));
        });

        // Validação básica para email
        $('input[type="email"]').on('blur', function () {
          var $field = $(this);
          var email = $field.val().trim();
          if (email && !isValidEmail(email)) {
            $field.addClass('is-invalid');
            var $feedback = $field.siblings('.invalid-feedback');
            if ($feedback.length === 0) {
              $('<div class="invalid-feedback">Por favor, insira um email válido</div>').insertAfter($field);
              $feedback = $field.siblings('.invalid-feedback');
            }
            $feedback.addClass('visible');
          } else {
            $field.removeClass('is-invalid');
            $field.siblings('.invalid-feedback').removeClass('visible');
          }
        });
      }
    }

    // Configurar event listeners
    function setupEventListeners() {
      // Abrir formulário
      $('.open-consultation-form').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        openForm();
      });

      // Evento delegado para botões adicionados dinamicamente
      $(document).on('click', '.open-consultation-form', function (e) {
        e.preventDefault();
        e.stopPropagation();
        openForm();
      });

      // Fechar formulário com clique no botão ou overlay
      $closeFormBtn.on('click', function (e) {
        e.preventDefault();
        closeForm();
      });
      $formOverlay.on('click', function (e) {
        e.preventDefault();
        closeForm();
      });

      // Fechar formulário com tecla ESC
      $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $consultationForm.hasClass('active')) {
          closeForm();
        }
      });

      // Navegação entre etapas
      $nextBtn.on('click', function (e) {
        e.preventDefault();
        goToNextStep();
      });
      $prevBtn.on('click', function (e) {
        e.preventDefault();
        goToPrevStep();
      });
      $submitBtn.on('click', function (e) {
        e.preventDefault();
        if (validateFormStep(currentStep)) {
          submitForm();
        }
      });

      // Fechar mensagem de sucesso
      $closeSuccessBtn.on('click', function (e) {
        e.preventDefault();
        resetForm();
      });
    }

    // Abrir formulário
    function openForm() {
      $consultationForm.addClass('active');
      $('body').css('overflow', 'hidden');
      setTimeout(function () {
        $formContainer.addClass('active');
      }, 100);
    }

    // Fechar formulário
    function closeForm() {
      $formContainer.removeClass('active');
      setTimeout(function () {
        $consultationForm.removeClass('active');
        $('body').css('overflow', '');
        resetFormState();
      }, 300);
    }

    // Reset do formulário
    function resetForm() {
      // Resetar para o primeiro passo
      currentStep = 1;
      showFormStep(currentStep);

      // Limpar formulário e mensagens de erro
      resetFormState();

      // Ocultar mensagem de sucesso se visível
      $formSuccess.removeClass('active');

      // Fechar o formulário
      closeForm();
    }

    // Limpar estado do formulário
    function resetFormState() {
      $multiStepForm[0].reset();
      // Remover classes de erro e mensagens
      $('.is-invalid').removeClass('is-invalid');
      $('.invalid-feedback').removeClass('visible').text('');
    }

    // Ir para o próximo passo
    function goToNextStep() {
      if (currentStep < totalSteps) {
        if (validateFormStep(currentStep)) {
          currentStep++;
          showFormStep(currentStep);
        }
      }
    }

    // Voltar ao passo anterior
    function goToPrevStep() {
      if (currentStep > 1) {
        currentStep--;
        showFormStep(currentStep);
      }
    }

    // Mostrar passo específico
    function showFormStep(stepNumber) {
      // Ocultar todos os passos
      $steps.removeClass('active');

      // Exibir o passo atual com uma pequena animação
      setTimeout(function () {
        $steps.filter('[data-step="' + stepNumber + '"]').addClass('active');
      }, 150);

      // Atualizar indicadores de passo
      $stepIndicators.removeClass('active completed');

      // Marcar passos anteriores como concluídos e o atual como ativo
      $stepIndicators.each(function () {
        var stepIndex = $(this).data('step');
        if (stepIndex < stepNumber) {
          $(this).addClass('completed');
        } else if (stepIndex === stepNumber) {
          $(this).addClass('active');
        }
      });

      // Atualizar exibição dos botões de navegação
      updateNavigationButtons();
    }

    // Atualizar botões de navegação
    function updateNavigationButtons() {
      if (currentStep === 1) {
        $prevBtn.addClass('hidden');
      } else {
        $prevBtn.removeClass('hidden');
      }
      if (currentStep === totalSteps) {
        $nextBtn.addClass('hidden');
        $submitBtn.removeClass('hidden');
      } else {
        $nextBtn.removeClass('hidden');
        $submitBtn.addClass('hidden');
      }
    }

    // Validar passo atual
    function validateFormStep(stepNumber) {
      var isValid = true;
      var $currentStep = $steps.filter('[data-step="' + stepNumber + '"]');
      var $requiredFields = $currentStep.find('[required]');

      // Remover todas as mensagens de erro existentes
      $currentStep.find('.is-invalid').removeClass('is-invalid');
      $currentStep.find('.invalid-feedback').removeClass('visible').text('');

      // Validar cada campo requerido
      $requiredFields.each(function () {
        var $field = $(this);
        var errorMessage = '';

        // Obter mensagem de erro personalizada se existir
        if ($field.data('error')) {
          errorMessage = $field.data('error');
        }

        // Verificar se o campo está vazio
        if (!$field.val().trim()) {
          isValid = false;
          showFieldError($field, errorMessage || 'Este campo é obrigatório');
        }
        // Validação específica para email
        else if ($field.attr('type') === 'email' && !isValidEmail($field.val())) {
          isValid = false;
          showFieldError($field, errorMessage || 'Por favor, insira um email válido');
        }
        // Validação específica para checkbox (ex: termos de privacidade)
        else if ($field.attr('type') === 'checkbox' && !$field.is(':checked')) {
          isValid = false;
          showFieldError($field, errorMessage || 'Você precisa concordar com este item');
        }
      });

      // Se não for válido, animar o container para feedback visual
      if (!isValid) {
        $currentStep.addClass('shake');
        setTimeout(function () {
          $currentStep.removeClass('shake');
        }, 600);
      }
      return isValid;
    }

    // Exibir erro em um campo
    function showFieldError($field, message) {
      $field.addClass('is-invalid');
      var $feedback = $field.siblings('.invalid-feedback');
      if ($feedback.length > 0) {
        $feedback.text(message).addClass('visible');
      } else {
        // Se não existir um elemento de feedback, criar um
        $('<div class="invalid-feedback visible">' + message + '</div>').insertAfter($field);
      }
    }

    // Validar formato de email
    function isValidEmail(email) {
      var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailPattern.test(email);
    }

    // Enviar formulário
    function submitForm() {
      // Mostrar indicador de carregamento
      $('#loadingIndicator').show();

      // Desativar botão de envio
      $('#submitBtn').prop('disabled', true);

      // Coletar dados do formulário
      var formData = new FormData($multiStepForm[0]);

      // Adicionar ação do WordPress e nonce de segurança
      formData.append('action', 'thabatta_submit_consultation');
      formData.append('security', thabattaData.nonce);

      // Fazer requisição AJAX
      $.ajax({
        type: 'POST',
        url: thabattaData.ajaxUrl,
        data: formData,
        processData: false,
        contentType: false,
        success: function success(response) {
          // Esconder indicador de carregamento
          $('#loadingIndicator').hide();

          // Verificar resposta
          if (response.success) {
            // Resetar formulário
            $multiStepForm[0].reset();

            // Ocultar o formulário, mas manter o modal aberto
            $multiStepForm.hide();
            $('.step-indicators').hide();

            // Mostrar mensagem de sucesso imediatamente
            $formSuccess.removeClass('hidden').addClass('active');
          } else {
            // Mostrar mensagem de erro
            alert(response.data.message || 'Ocorreu um erro ao enviar o formulário. Por favor, tente novamente.');
            $('#submitBtn').prop('disabled', false);
          }
        },
        error: function error() {
          // Esconder indicador de carregamento
          $('#loadingIndicator').hide();

          // Mostrar mensagem de erro
          alert('Ocorreu um erro ao enviar o formulário. Por favor, tente novamente.');
          $('#submitBtn').prop('disabled', false);
        }
      });
    }

    // Inicializar o primeiro passo
    showFormStep(1);
  });
})(jQuery);
//# sourceMappingURL=bundle.js.map
