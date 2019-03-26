// @ts-check

import { CustomHTMLElement } from './lib/custom-html-element.js';

export class XListItem extends CustomHTMLElement {
  static get is() { return 'x-list-item'; }
}
