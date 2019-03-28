// @ts-check

import { hydrate } from './lib/helpers.js';
import { XList, XListItem } from './components.js';

const components = [
  XListItem,
  XList,
];

window.addEventListener('DOMContentLoaded', () => {
  components.map((component) => {
    component.register().then(() => hydrate(component.is));
  });
});
