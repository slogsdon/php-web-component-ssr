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
 * @param {string} [slotElementType] Name used when defining slot content
 *    for an element's initial data. Default is `span`.
 */
export function hydrate(name, slotElementType) {
  // skip if no custom elements support or element hasn't yet been defined
  if (!window.customElements || customElements.get(name) === undefined) {
    return;
  }

  // skip if client template isn't present
  if (!document.getElementById(`template-${name}`)) {
    return;
  }

  document.querySelectorAll(`[data-template="${name}"]`).forEach((oldChild) => {
    const newChild = document.createElement(name);
    const initialData = oldChild.getAttribute('data-initial-data');

    if (initialData) {
      hydrateInitialData(initialData, slotElementType, newChild);
    }

    if (oldChild.parentNode) {
      oldChild.parentNode.replaceChild(newChild, oldChild);
    }
  });
}

/**
 * Hydrates server-side rendered initial data for custom elements / web components.
 *
 * @param {string} initialData A custom element's initial data
 * @param {string | undefined} slotElementType Name used when defining slot content
 *    for an element's initial data.
 * @param {HTMLElement} newChild Newly created element
 */
function hydrateInitialData(initialData, slotElementType, newChild) {
  try {
    const data = JSON.parse(initialData);
    Object.keys(data).forEach((key) => {
      const el = document.createElement(slotElementType || 'span');
      el.slot = key;
      el.innerHTML = data[key];
      newChild.appendChild(el);
    });
  } catch (e) {
    // one, unnamed slot
    newChild.innerHTML = decodeURI(initialData)
      // additional replacements for PHP's `urlencode`
      .replace(/%3[dD]/g, '=')
      .replace(/\+/g, ' ')
      .replace(/%2[fF]/g, '/');
  }
}

/**
 * Helper function around the `DOMContentLoaded` event to fire
 * the event handler if the event has already been triggered
 *
 * @param {(e?: Event) => void} callback
 */
export function onReady(callback) {
  if (document.readyState === 'loading') {
    window.addEventListener('DOMContentLoaded', callback);
    return;
  }

  cb();
}
