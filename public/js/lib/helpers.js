// @ts-check

/**
 * Hydrates server-side rendered custom elements / web components.
 *
 * Server-side rendered elements should wrap generated content with a `div` element with
 * the `data-template` attribute set to the name of the corresponding custom element.
 *
 * Initial client-side data can be added to server-side rendered elements with the
 * `data-initial-data` attribute set to JSON encoded object.
 *
 * @param {string} name Custom element name
 * @param {string} slotElementType Name used when defining slot content
 *    for an element's initial data. Default is `span`.
 */
export function hydrate(name, slotElementType) {
  if (!window.customElements || customElements.get(name) === undefined) {
    return;
  }

  document.querySelectorAll(`[data-template="${name}"]`).forEach((oldChild) => {
    // skip if client template isn't present
    if (!document.getElementById(`template-${name}`)) {
      return;
    }

    const newChild = document.createElement(name);
    const initialData = oldChild.getAttribute('data-initial-data');

    if (initialData) {
      const data = JSON.parse(initialData);
      Object.keys(data).forEach((key) => {
        const el = document.createElement(slotElementType || 'span');
        el.slot = key;
        el.innerHTML = data[key];
        newChild.appendChild(el);
      });
    }

    if (oldChild.parentNode) {
      oldChild.parentNode.replaceChild(newChild, oldChild);
    }
  });
};
