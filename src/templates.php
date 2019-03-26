<?php

/**
 * Wraps template files in client-side friendly `template` elements,
 * creating `slot` elements for any PHP variables.
 *
 * @param string $name Custom element name
 * @param array $slots Template variable names
 * @return string
 */
function getClientTemplate(string $name, array $slots = []): string
{
    $data = [];
    foreach ($slots as $slot) {
        $data[$slot] = sprintf('<slot name="%s"></slot>', $slot);
    }

    return sprintf(
        '<template id="template-%s">%s</template>',
        $name,
        getTemplateSource($name, $data)
    );
}

/**
 * Wraps template files in client-side friendly `div` elements. Client-side
 * code can leverage `data-` attributes to hydrate the page.
 *
 * @param string $name Custom element name
 * @param array $data Template data
 * @return string
 */
function getTemplate(string $name, array $data = []): string
{
    $initialData = '';
    if (is_array($data) && count($data) > 0) {
        $initialData = sprintf(" data-initial-data='%s'", json_encode($data));
    }

    return sprintf(
        '<div data-template="%s"%s>%s</div>',
        $name,
        $initialData,
        getTemplateSource($name, $data)
    );
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
    ob_start();
    extract($data);
    include sprintf('%s/../templates/%s.phtml', __DIR__, $name);
    return ob_get_clean();
}
