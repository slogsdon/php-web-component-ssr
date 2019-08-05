# web-component-ssr

> Provides server-side rendering support for native web components / custom elements

### Features

- Plain PHP and JavaScript
- Native [web components](https://developer.mozilla.org/en-US/docs/Web/Web_Components)
- Minimal dependencies

### Reasoning

PHP is easy to install, if not already present on you computer. PHP runs pretty much everywhere. PHP is flexible.

JavaScript is in a similar boat. It's readily available and flexible. Modern browser JavaScript is more than capable.

This project also scratches an itch to see how much PHP and JavaScript can work together to handle this problem domain.

## Requirements

- [PHP 7.1+](http://www.php.net/)
- [Composer](https://getcomposer.org/) (for PHP autoloader only)
- [`web-component-hydration`](https://www.npmjs.com/package/web-component-hydration)

## Running the demo

```
cd examples/simple && yarn install
cd ../.. && composer start
```

This will install a local copy of `web-component-hydration` and call the `start` Composer script defined in `composer.json` and execute the built-in PHP server.

On page load, PHP will output a template file as HTML. It will also prepare the client-side template. Once the DOM has loaded, the JavaScript will load the web component's custom element and replace all PHP created instances of the template with browser/JavaScript created instances.

## License

This project is licensed under the MIT License. See [LICENSE](LICENSE) for details.
