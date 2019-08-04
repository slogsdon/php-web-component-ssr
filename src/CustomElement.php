<?php declare(strict_types=1);

namespace Slogsdon\SSRWebComponents;

use DOMDocument;

class CustomElement
{
    /** @var string */
    const MBSTRING_ENCODING_HTML = 'HTML-ENTITIES';

    /** @var string */
    const MBSTRING_ENCODING_UTF8 = 'UTF-8';

    /** @var string|string[]|null */
    protected $data = null;

    /** @var DOMDocument */
    protected $dom;

    /** @var string */
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->dom = new DOMDocument();
    }

    /**
     * Imports HTML source for a web component
     *
     * @param string $html
     * @return CustomElement
     */
    public function withSource($html): self
    {
        $this->dom->loadXML(mb_convert_encoding(
            $html,
            self::MBSTRING_ENCODING_HTML,
            self::MBSTRING_ENCODING_UTF8
        ));

        // automatically set `id` attribute for use on client-side
        $id = $this->dom->createAttribute('id');
        $id->appendChild($this->dom->createTextNode(sprintf('template-%s', $this->name)));
        $this->dom->firstChild->appendChild($id);

        return $this;
    }

    /**
     * Adds data for template slots
     *
     * @param string|string[] $data
     * @return CustomElement
     */
    public function withData($data = null): self
    {
        $this->data = $data;

        if ($this->data) {
            $this->injectSlotData();
        }

        return $this;
    }

    /**
     * Exports `CustomElement` as server-side string
     *
     * @return string
     */
    public function asHTML(): string
    {
        $initialData = '';

        if (is_array($this->data) && count($this->data) > 0) {
            $initialData = json_encode($this->data);
        }

        if (is_string($this->data) && !empty($this->data)) {
            $initialData = $this->data;
        }

        $div = $this->dom->createElement('div');

        $div->setAttribute('data-template', $this->name);

        // set initial data for client side
        if (!empty($initialData)) {
            $div->setAttribute('data-initial-data', urlencode($initialData));
        }

        // rest of method expects the `template` node to be first and only child
        // of the document (`$this->dom`)

        $firstChild = $this->dom->firstChild;

        /**
         * copy `template` attributes
         * @var \DOMNode $attribute
         */
        foreach ($firstChild->attributes as $attribute) {
            if ($attribute->localName === 'id') {
                continue;
            }
            $div->setAttribute($attribute->nodeName, $attribute->nodeValue);
        }

        // copy `template` children
        while ($firstChild->hasChildNodes()) {
            $div->appendChild($firstChild->firstChild);
        }

        // replace `template` with `div`
        $this->dom->replaceChild($div, $firstChild);

        return $this->dom->saveHTML();
    }

    /**
     * Exports `CustomElement` as client-side string
     *
     * @return string
     */
    public function asTemplate(): string
    {
        return $this->dom->saveHTML();
    }

    protected function injectSlotData(): void
    {
        if (!$this->data) {
            return;
        }

        $slots = $this->dom->getElementsByTagName('slot');

        foreach ($slots as $slot) {
            if (is_string($this->data) && !empty($this->data) && !$slot->hasAttribute('name')) {
                $frag = $this->dom->createDocumentFragment();
                $frag->appendXML($this->data);
                $slot->appendChild($frag);
                return;
            }

            if (!is_array($this->data) || empty($this->data)) {
                continue;
            }

            if (isset($this->data[$slot->getAttribute('name')])) {
                $slot->appendChild($this->dom->createTextNode($this->data[$slot->getAttribute('name')]));
            }
        }
    }
}
