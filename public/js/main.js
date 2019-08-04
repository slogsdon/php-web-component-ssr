// @ts-check

import { hydrate, onReady } from './node_modules/web-component-hydration/helpers.js';
import { XList, XListItem } from './components.js';

const components = [
  XListItem,
  XList,
];

onReady(() => {
  components.map(async (component) => {
    await component.register();
    hydrate(component.is)
  });
});
