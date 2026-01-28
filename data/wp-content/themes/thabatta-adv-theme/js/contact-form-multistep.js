/**
 * Formulário de contato multi-etapas
 */
(function($) {
    $(document).ready(function() {
        // Elementos DOM
        const $contactForm = $('#contactMultiStepForm');
        const $steps = $contactForm.find('.step');
        const $stepIndicators = $('.step-indicators .step-indicator');
        const $nextBtn = $('#contactNextBtn');
        const $prevBtn = $('#contactPrevBtn');
        const $submitBtn = $('#contactSubmitBtn');
        const $formSuccess = $('#contactFormSuccess');
        
        // Estado atual
        let currentStep = 1;
        const totalSteps = $steps.length;
        
        // Verificar se o formulário existe na página
        if (!$contactForm.length) return;
        
        // Configurar máscaras de input
        if (typeof IMask !== 'undefined') {
            IMask($contactForm.find('.phone-mask')[0], {
                mask: '(00) 00000-0000'
            });

            IMask($contactForm.find('.cpfcnpj-mask')[0], {
                mask: [
                    { mask: '000.000.000-00' },
                    { mask: '00.000.000/0000-00' }
                ]
            });
        }
        
        // Navegação entre etapas
        $nextBtn.on('click', function(e) {
            e.preventDefault();
            if (validateFormStep(currentStep)) {
                currentStep++;
                showFormStep(currentStep);
            }
        });
        
        $prevBtn.on('click', function(e) {
            e.preventDefault();
            if (currentStep > 1) {
                currentStep--;
                showFormStep(currentStep);
            }
        });
        
        $submitBtn.on('click', function(e) {
            e.preventDefault();
            if (validateFormStep(currentStep)) {
                submitForm();
            }
        });
        
        // Mostrar passo específico
        function showFormStep(stepNumber) {
            // Ocultar todos os passos
            $steps.removeClass('active');
            
            // Exibir o passo atual com uma pequena animação
            setTimeout(() => {
                $steps.filter('[data-step="' + stepNumber + '"]').addClass('active');
            }, 150);
            
            // Atualizar indicadores de passo
            $stepIndicators.removeClass('active completed');
            
            // Marcar passos anteriores como concluídos e o atual como ativo
            $stepIndicators.each(function() {
                const stepIndex = $(this).data('step');
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
            let isValid = true;
            const $currentStep = $steps.filter('[data-step="' + stepNumber + '"]');
            const $requiredFields = $currentStep.find('[required]');
            
            // Remover todas as mensagens de erro existentes
            $currentStep.find('.is-invalid').removeClass('is-invalid');
            $currentStep.find('.invalid-feedback').removeClass('visible').text('');
            
            // Validar cada campo requerido
            $requiredFields.each(function() {
                const $field = $(this);
                let errorMessage = '';
                
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
                setTimeout(() => {
                    $currentStep.removeClass('shake');
                }, 600);
            }
            
            return isValid;
        }
        
        // Exibir erro em um campo
        function showFieldError($field, message) {
            $field.addClass('is-invalid');
            const $feedback = $field.siblings('.invalid-feedback');
            if ($feedback.length > 0) {
                $feedback.text(message).addClass('visible');
            } else {
                // Se não existir um elemento de feedback, criar um
                $('<div class="invalid-feedback visible">' + message + '</div>').insertAfter($field);
            }
        }
        
        // Validar formato de email
        function isValidEmail(email) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailPattern.test(email);
        }
        
        // Enviar formulário
        function submitForm() {
            // Mostrar indicador de carregamento
            $('#loadingIndicator').show();
            
            // Desativar botão de envio
            $submitBtn.prop('disabled', true);
            
            // Coletar dados do formulário
            const formData = new FormData($contactForm[0]);
            
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
                success: function(response) {
                    // Esconder indicador de carregamento
                    $('#loadingIndicator').hide();
                    
                    // Verificar resposta
                    if (response.success) {
                        // Resetar formulário
                        $contactForm[0].reset();
                        
                        // Ocultar o formulário
                        $contactForm.hide();
                        $('.step-indicators').hide();
                        
                        // Mostrar mensagem de sucesso
                        $formSuccess.removeClass('hidden').addClass('active');
                    } else {
                        // Mostrar mensagem de erro
                        alert(response.data.message || 'Ocorreu um erro ao enviar o formulário. Por favor, tente novamente.');
                        $submitBtn.prop('disabled', false);
                    }
                },
                error: function() {
                    // Esconder indicador de carregamento
                    $('#loadingIndicator').hide();
                    
                    // Mostrar mensagem de erro
                    alert('Ocorreu um erro ao enviar o formulário. Por favor, tente novamente.');
                    $submitBtn.prop('disabled', false);
                }
            });
        }

        // Inicializar o primeiro passo
        showFormStep(1);
    });
})(jQuery); 