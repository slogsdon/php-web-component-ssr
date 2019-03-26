// @ts-check

/**
 * Requires a `template` element to be present on page with the custom element's
 * client-side template. The `template` element should have an `id` of the custom
 * element's name prefixed with `template-`, e.g. `template-my-custom-element`.
 */
export class CustomHTMLElement extends HTMLElement {
  /**
   * Custom element's name
   * @type {string}
   */
  static get is() { return undefined; }

  /**
   * Ensure's custom elements are only defined once per page load and pass along
   * the promise created with `customElements.whenDefined`.
   *
   * When custom elements are not available for the current browser, a rejected
   * promise is returned.
   */
  static register() {
    if (this.is === undefined) {
      return Promise.reject(new Error('No defined name'));
    }

    if (!window.customElements) {
      return Promise.reject(new Error('window.customElements not available'));
    }

    const elementName = this.is;
    const promise = window.customElements.whenDefined(elementName);
    const isDefined = window.customElements.get(elementName) !== undefined;

    if (!isDefined) {
      window.customElements.define(elementName, this);
    }

    return promise;
  }

  constructor() {
      super();

      /** @type {HTMLTemplateElement} */
      // @ts-ignore
      this.template = document.getElementById(`template-${this.constructor.is}`);;
      this.attachShadow({ mode: 'open' });
  }

  connectedCallback() {
    if (!this.shadowRoot || !this.isConnected || !this.template) {
      return;
    }

    this.shadowRoot.appendChild(
      this.template.content.cloneNode(true),
    );
  }
}
