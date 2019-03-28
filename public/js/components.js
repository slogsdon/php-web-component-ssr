// @ts-check

import { CustomHTMLElement } from './lib/custom-html-element.js';

export class XList extends CustomHTMLElement {
  static get is() { return 'x-list'; }
}
export class XListItem extends CustomHTMLElement {
  static get is() { return 'x-list-item'; }
}
