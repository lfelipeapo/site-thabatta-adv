/**
 * Scripts administrativos
 *
 * @package AICG
 */

(function($) {
    'use strict';

    // Namespace
    window.AICG = window.AICG || {};

    /**
     * Inicialização
     */
    AICG.init = function() {
        this.bindEvents();
        this.initMetaBoxes();
    };

    /**
     * Eventos
     */
    AICG.bindEvents = function() {
        // Confirmação de deleção
        $(document).on('click', '.aicg-delete-confirm', function(e) {
            if (!confirm(aicgData.i18n?.confirmDelete || 'Tem certeza?')) {
                e.preventDefault();
            }
        });

        // Toggle de campos
        $(document).on('change', '.aicg-toggle-field', function() {
            const target = $(this).data('toggle-target');
            if (target) {
                $(target).toggle($(this).is(':checked'));
            }
        });
    };

    /**
     * Meta boxes
     */
    AICG.initMetaBoxes = function() {
        // Campos readonly
        $('.aicg-readonly-field').each(function() {
            $(this).on('keydown paste', function(e) {
                e.preventDefault();
            });
        });
    };

    /**
     * AJAX helpers
     */
    AICG.ajax = function(action, data, callback) {
        $.ajax({
            url: aicgData.ajaxUrl || ajaxurl,
            type: 'POST',
            data: {
                action: 'aicg_' + action,
                nonce: aicgData.nonce,
                data: data,
            },
            success: function(response) {
                if (callback) {
                    callback(response.success, response.data);
                }
            },
            error: function() {
                if (callback) {
                    callback(false, null);
                }
            },
        });
    };

    // Inicializa quando DOM estiver pronto
    $(document).ready(function() {
        AICG.init();
    });

})(jQuery);
