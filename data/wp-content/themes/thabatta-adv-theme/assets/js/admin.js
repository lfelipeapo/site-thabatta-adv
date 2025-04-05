/**
 * Admin JavaScript file for Thabatta Advocacia theme
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Inicializar color picker
        if ($.fn.wpColorPicker) {
            $('.color-picker').wpColorPicker();
        }
        
        // Seletor de mídia para campos de imagem
        $('.image-upload-button').on('click', function(e) {
            e.preventDefault();
            
            const button = $(this);
            const imageIdField = button.siblings('.image-id');
            const imagePreview = button.siblings('.image-preview');
            
            // Criar frame de mídia
            const frame = wp.media({
                title: thabattaAdmin.mediaTitle,
                button: {
                    text: thabattaAdmin.mediaButton
                },
                multiple: false
            });
            
            // Quando uma imagem for selecionada
            frame.on('select', function() {
                const attachment = frame.state().get('selection').first().toJSON();
                
                // Atualizar campo com ID da imagem
                imageIdField.val(attachment.id);
                
                // Atualizar preview
                imagePreview.html('<img src="' + attachment.url + '" style="max-width: 100%; height: auto;" />');
                
                // Mostrar botão de remoção
                button.siblings('.image-remove-button').show();
            });
            
            // Abrir frame de mídia
            frame.open();
        });
        
        // Botão de remoção de imagem
        $('.image-remove-button').on('click', function(e) {
            e.preventDefault();
            
            const button = $(this);
            const imageIdField = button.siblings('.image-id');
            const imagePreview = button.siblings('.image-preview');
            
            // Limpar campo
            imageIdField.val('');
            
            // Limpar preview
            imagePreview.html('');
            
            // Esconder botão de remoção
            button.hide();
        });
        
        // Tabs em metaboxes
        $('.thabatta-tabs-nav a').on('click', function(e) {
            e.preventDefault();
            
            const tabId = $(this).attr('href');
            
            // Ativar tab
            $(this).parent().addClass('active').siblings().removeClass('active');
            
            // Mostrar conteúdo da tab
            $(tabId).show().siblings('.thabatta-tab-content').hide();
        });
        
        // Ativar primeira tab por padrão
        $('.thabatta-tabs-nav li:first-child a').click();
        
        // Repetidor de campos (para grupos de campos repetíveis)
        $('.add-row-button').on('click', function(e) {
            e.preventDefault();
            
            const container = $(this).prev('.repeatable-fields-container');
            const template = container.data('template');
            const nextIndex = container.children().length;
            
            // Substituir índice no template
            const newRow = template.replace(/\{index\}/g, nextIndex);
            
            // Adicionar nova linha
            container.append(newRow);
            
            // Inicializar color picker em novos campos
            container.find('.color-picker').wpColorPicker();
        });
        
        // Remover linha do repetidor
        $(document).on('click', '.remove-row-button', function(e) {
            e.preventDefault();
            $(this).closest('.repeatable-field-row').remove();
        });
        
        // Ordenar campos repetíveis
        if ($.fn.sortable) {
            $('.repeatable-fields-container').sortable({
                handle: '.sort-handle',
                update: function(event, ui) {
                    // Reindexar campos se necessário
                }
            });
        }
    });
    
})(jQuery); 