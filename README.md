# php-ssr-web-components

> Demo of a server-side rendering approach for PHP + web components

### Features

- Plain PHP and JavaScript
- Native [web components](https://developer.mozilla.org/en-US/docs/Web/Web_Components)
- Zero dependencies

### Reasoning

PHP is easy to install, if not already present on you computer. PHP runs pretty much everywhere. PHP is flexible.

JavaScript is in a similar boat. It's readily available and flexible. Modern browser JavaScript is more than capable.

This project also scratches an itch to see how much PHP and JavaScript can work together to handle this problem domain.

## Requirements

- [PHP 7.1+](http://www.php.net/)
- [Composer](https://getcomposer.org/) (for PHP autoloader only)

## Running the demo

```
composer start
```

This will call the `start` Composer script defined in `composer.json` and execute the built-in PHP server.

On page load, PHP will output a template file as HTML. It will also prepare the client-side template. Once the DOM has loaded, the JavaScript will load the web component's custom element and replace all PHP created instances of the template with browser/JavaScript created instances.

### Source

Source files within [`src`](src) contain the server-side implementation, and source files within [`public/js/lib`](public/js/lib) contain the client-side implementation. Both are capable of being extracted as a reusable library.

Other source files within [`public`](public) and [`templates`](templates) demonstrate how the server- and client-side implementations can be leveraged.

## License

This project is licensed under the MIT License. See [LICENSE](LICENSE) for details.
