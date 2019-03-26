// @ts-check

import { hydrate } from './lib/helpers.js';
import { XListItem } from './components.js';

window.addEventListener('DOMContentLoaded', () => {
    XListItem.register().then(() => hydrate(XListItem.is, 'div'));
});
