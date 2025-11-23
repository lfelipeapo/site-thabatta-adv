<?php
/**
 * Script para gerar o arquivo JavaScript de administração
 */

// Definir caminho para o arquivo de saída
$output_file = __DIR__ . '/assets/js/admin.js';

// Conteúdo do arquivo
$js_content = <<<'EOT'
/**
 * Scripts de administração para o tema Thabatta Advocacia
 * 
 * @package Thabatta_Advocacia
 */

(function($) {
    'use strict';

    /**
     * Inicializar seletor de mídia
     */
    function initMediaUploader() {
        $('.thabatta-media-button').on('click', function(e) {
            e.preventDefault();
            
            const button = $(this);
            const targetInput = $(button.data('target'));
            const previewContainer = button.closest('.thabatta-media-uploader').find('.thabatta-media-preview');
            
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
                
                // Atualizar valor do input
                targetInput.val(attachment.url);
                
                // Atualizar preview
                if (previewContainer.length) {
                    previewContainer.html('<img src="' + attachment.url + '" alt="Preview">');
                } else {
                    button.after('<div class="thabatta-media-preview"><img src="' + attachment.url + '" alt="Preview"></div>');
                }
            });
            
            // Abrir frame de mídia
            frame.open();
        });
    }

    /**
     * Inicializar seletor de cores
     */
    function initColorPicker() {
        $('.thabatta-color-picker').wpColorPicker({
            change: function(event, ui) {
                // Atualizar preview de cores
                updateColorPreview();
            }
        });
    }

    /**
     * Atualizar preview de cores
     */
    function updateColorPreview() {
        const primaryColor = $('#thabatta_primary_color').val();
        const secondaryColor = $('#thabatta_secondary_color').val();
        const accentColor = $('#thabatta_accent_color').val();
        const textColor = $('#thabatta_text_color').val();
        
        $('.primary-color').css('background-color', primaryColor);
        $('.secondary-color').css('background-color', secondaryColor);
        $('.accent-color').css('background-color', accentColor);
        $('.text-color').css('background-color', textColor);
    }

    /**
     * Inicializar preview de tipografia
     */
    function initTypographyPreview() {
        $('#thabatta_heading_font, #thabatta_body_font').on('change', function() {
            updateTypographyPreview();
        });
    }

    /**
     * Atualizar preview de tipografia
     */
    function updateTypographyPreview() {
        const headingFont = $('#thabatta_heading_font').val();
        const bodyFont = $('#thabatta_body_font').val();
        
        $('.thabatta-typography-preview h3').css('font-family', "'" + headingFont + "', serif");
        $('.thabatta-typography-preview p').css('font-family', "'" + bodyFont + "', sans-serif");
        
        $('.thabatta-typography-preview h3').text('Exemplo de Título com ' + headingFont);
    }

    /**
     * Inicializar contador de caracteres para SEO
     */
    function initSeoCounter() {
        $('#thabatta_meta_title').on('input', function() {
            const length = $(this).val().length;
            $('#thabatta-title-counter').text(length + '/60');
            
            if (length > 60) {
                $('#thabatta-title-counter').addClass('thabatta-counter-warning');
            } else {
                $('#thabatta-title-counter').removeClass('thabatta-counter-warning');
            }
            
            // Atualizar preview
            $('#thabatta-seo-preview-title').text($(this).val() || $('.post-title').text());
        });
        
        $('#thabatta_meta_description').on('input', function() {
            const length = $(this).val().length;
            $('#thabatta-description-counter').text(length + '/160');
            
            if (length > 160) {
                $('#thabatta-description-counter').addClass('thabatta-counter-warning');
            } else {
                $('#thabatta-description-counter').removeClass('thabatta-counter-warning');
            }
            
            // Atualizar preview
            $('#thabatta-seo-preview-description').text($(this).val());
        });
        
        // Inicializar contadores
        $('#thabatta_meta_title').trigger('input');
        $('#thabatta_meta_description').trigger('input');
    }

    /**
     * Inicializar gerenciamento de posts relacionados
     */
    function initRelatedPosts() {
        // Adicionar post relacionado
        $('#thabatta-add-related-post').on('click', function() {
            const select = $('#thabatta-related-posts-select');
            const postId = select.val();
            
            if (!postId) {
                return;
            }
            
            // Verificar se já existe
            if ($('#thabatta-related-posts-list li[data-id="' + postId + '"]').length) {
                return;
            }
            
            const postTitle = select.find('option:selected').text();
            const postType = postTitle.match(/\((.*?)\)$/)[1];
            const title = postTitle.replace(/\s*\(.*?\)$/, '');
            
            // Adicionar à lista
            $('#thabatta-related-posts-list').append(
                '<li data-id="' + postId + '">' +
                '<input type="hidden" name="thabatta_related_posts[]" value="' + postId + '">' +
                '<span class="title">' + title + '</span> ' +
                '<span class="type">(' + postType + ')</span> ' +
                '<a href="#" class="thabatta-remove-related-post" title="Remover">' +
                '<span class="dashicons dashicons-no-alt"></span>' +
                '</a>' +
                '</li>'
            );
            
            // Resetar select
            select.val('');
        });
        
        // Remover post relacionado
        $(document).on('click', '.thabatta-remove-related-post', function(e) {
            e.preventDefault();
            $(this).closest('li').remove();
        });
    }

    /**
     * Inicializar limpeza de cache do Jetpack
     */
    function initJetpackCache() {
        $('.thabatta-clear-cache-button').on('click', function(e) {
            e.preventDefault();
            
            const button = $(this);
            const originalText = button.text();
            const statusContainer = $('.thabatta-cache-status');
            
            // Desabilitar botão
            button.prop('disabled', true).text('Limpando cache...');
            
            // Enviar requisição AJAX
            $.ajax({
                url: thabattaAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'thabatta_clear_jetpack_cache',
                    nonce: thabattaAdmin.nonce,
                    cache_type: button.data('cache-type')
                },
                success: function(response) {
                    if (response.success) {
                        statusContainer.html('<div class="notice notice-success inline"><p>' + response.data.message + '</p></div>');
                    } else {
                        statusContainer.html('<div class="notice notice-error inline"><p>' + response.data.message + '</p></div>');
                    }
                },
                error: function() {
                    statusContainer.html('<div class="notice notice-error inline"><p>Erro ao limpar cache. Tente novamente.</p></div>');
                },
                complete: function() {
                    // Reabilitar botão
                    button.prop('disabled', false).text(originalText);
                    
                    // Esconder mensagem após 5 segundos
                    setTimeout(function() {
                        statusContainer.find('.notice').fadeOut(500, function() {
                            $(this).remove();
                        });
                    }, 5000);
                }
            });
        });
    }

    /**
     * Inicializar ordenação de itens
     */
    function initSortable() {
        if ($.fn.sortable) {
            $('.thabatta-sortable-list').sortable({
                handle: '.thabatta-sortable-handle',
                update: function() {
                    // Atualizar ordem dos itens
                    $(this).find('.thabatta-sortable-order').each(function(index) {
                        $(this).val(index);
                    });
                }
            });
        }
    }

    /**
     * Inicializar tabs de administração
     */
    function initAdminTabs() {
        $('.thabatta-admin-tabs-nav a').on('click', function(e) {
            e.preventDefault();
            
            const tabId = $(this).attr('href');
            
            // Desativar todas as tabs
            $('.thabatta-admin-tabs-nav a').removeClass('nav-tab-active');
            $('.thabatta-admin-tab-content').removeClass('active');
            
            // Ativar tab selecionada
            $(this).addClass('nav-tab-active');
            $(tabId).addClass('active');
            
            // Salvar preferência do usuário
            if (window.localStorage) {
                const pageId = window.location.pathname.replace(/^.*[\\\/]/, '');
                localStorage.setItem('thabatta_active_tab_' + pageId, tabId);
            }
        });
        
        // Verificar se há tab salva
        if (window.localStorage) {
            const pageId = window.location.pathname.replace(/^.*[\\\/]/, '');
            const savedTab = localStorage.getItem('thabatta_active_tab_' + pageId);
            
            if (savedTab && $(savedTab).length) {
                $('.thabatta-admin-tabs-nav a[href="' + savedTab + '"]').trigger('click');
            } else {
                // Ativar primeira tab
                $('.thabatta-admin-tabs-nav a:first').trigger('click');
            }
        } else {
            // Ativar primeira tab
            $('.thabatta-admin-tabs-nav a:first').trigger('click');
        }
    }

    /**
     * Inicializar ao carregar documento
     */
    $(document).ready(function() {
        initMediaUploader();
        initColorPicker();
        initTypographyPreview();
        initSeoCounter();
        initRelatedPosts();
        initJetpackCache();
        initSortable();
        initAdminTabs();
        
        // Inicializar previews
        updateColorPreview();
        updateTypographyPreview();
    });
})(jQuery);
EOT;

// Criar diretório se não existir
if (!file_exists(dirname($output_file))) {
    mkdir(dirname($output_file), 0755, true);
}

// Escrever arquivo
file_put_contents($output_file, $js_content);

echo "Arquivo de administração gerado com sucesso em: $output_file\n";
