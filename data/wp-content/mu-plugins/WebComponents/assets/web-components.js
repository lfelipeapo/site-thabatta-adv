(() => {
  const dataEl = document.getElementById('thabatta-web-components-data');
  if (!dataEl) {
    return;
  }

  let components = [];
  try {
    components = JSON.parse(dataEl.textContent || '[]');
  } catch (error) {
    console.error('Erro ao carregar web components:', error);
    return;
  }

  components.forEach((component) => {
    if (!component?.tag || customElements.get(component.tag)) {
      return;
    }

    class ThabattaWebComponent extends HTMLElement {
      constructor() {
        super();

        const template = document.getElementById(component.templateId);
        const clone = template?.content ? template.content.cloneNode(true) : null;

        let scope = this;
        if (component.useShadowDom) {
          scope = this.attachShadow({ mode: component.shadowDomMode || 'open' });
        }

        if (clone) {
          scope.append(clone);
        }

        if (component.js) {
          try {
            const runner = new Function('host', 'root', component.js);
            runner(this, scope);
          } catch (error) {
            console.error(`Erro ao executar JS do componente ${component.tag}:`, error);
          }
        }
      }
    }

    customElements.define(component.tag, ThabattaWebComponent);
  });
})();
