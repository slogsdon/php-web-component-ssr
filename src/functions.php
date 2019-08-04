<?php

use Slogsdon\SSRWebComponents\CustomElement;

/**
 * Wraps template files in client-side friendly `template` elements,
 * creating `slot` elements for any PHP variables.
 *
 * @param string $name Custom element name
 * @param string|string[] $data Template data
 * @return string
 */
function getClientTemplate(string $name, $data = null): string
{
    return (new CustomElement($name))
        ->withSource(getTemplateSource($name))
        ->withData($data)
        ->asTemplate();
}

/**
 * Wraps template files in client-side friendly `div` elements. Client-side
 * code can leverage `data-` attributes to hydrate the page.
 *
 * @param string $name Custom element name
 * @param string|string[] $data Template data
 * @return string
 */
function getTemplate(string $name, $data = null): string
{
    return (new CustomElement($name))
        ->withSource(getTemplateSource($name))
        ->withData($data)
        ->asHTML();
}

/**
 * Gets source for a custom element template
 *
 * @param string $name Custom element name
 * @param array $data Template data
 * @return string
 */
function getTemplateSource(string $name, array $data = []): string
{
    $templatePath = realpath(sprintf('%s/../public/js/templates/%s.html', __DIR__, $name));

    if (!is_file($templatePath)) {
        return '';
    }

    ob_start();
    extract($data);
    /** @psalm-suppress UnresolvableInclude */
    include $templatePath;
    return ob_get_clean();
}
