// @ts-check

import { hydrate, onReady } from './lib/helpers.js';
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
