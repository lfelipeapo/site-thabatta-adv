jQuery(($) => {
  const dataEl = document.getElementById('thabatta-import-data');
  if (!dataEl) {
    return;
  }

  const ajaxUrl = dataEl.dataset.ajaxUrl;
  const nonce = dataEl.dataset.nonce;

  $('#thabatta-import-button').on('click', () => {
    const components = [];
    $('input[name="components[]"]:checked').each((_, input) => {
      components.push(input.value);
    });

    if (components.length === 0) {
      alert('Por favor, selecione pelo menos um componente para importar.');
      return;
    }

    const $button = $('#thabatta-import-button');
    $button.prop('disabled', true).text('Importando...');

    $.ajax({
      url: ajaxUrl,
      type: 'POST',
      data: {
        action: 'thabatta_import_web_component',
        components,
        nonce,
      },
      success(response) {
        const html = response?.success === false ? response.data : response;
        $('#thabatta-import-results').show();
        $('#thabatta-import-messages').html(html);
        $button.prop('disabled', false).text('Importar Componentes Selecionados');
      },
      error() {
        $('#thabatta-import-results').show();
        $('#thabatta-import-messages').html(
          '<div class="notice notice-error"><p>Ocorreu um erro durante a importação. Por favor, tente novamente.</p></div>'
        );
        $button.prop('disabled', false).text('Importar Componentes Selecionados');
      },
    });
  });
});
