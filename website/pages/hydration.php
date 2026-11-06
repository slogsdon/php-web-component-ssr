<?php
/**
 * Tool page · Hydration: the client-side half. Everything here is read from the
 * repository and quoted from its README; nothing was executed in a browser.
 */
$this->layout('partials::layout', [
    'title' => 'Hydration in the browser · Web Component Tools',
    'description' => 'How web-component-hydration replaces server-rendered placeholders with live custom elements: CustomHTMLElement, hydrate, onReady, and the attributes it expects.',
    'path' => '/hydration/',
    'notFound' => false,
]);
?>
<section class="intro intro--compact" aria-labelledby="tool-title">
    <p class="t-smallcaps">javascript-web-component-hydration · npm web-component-hydration 1.0.5</p>
    <h1 id="tool-title">Placeholder in. <span class="t-accent">Live element out.</span></h1>
    <p class="t-lead">Two ES modules. <code>CustomHTMLElement</code> is an <code>HTMLElement</code>
        subclass that clones a template into an open shadow root;
        <code>hydrate()</code> is the function that swaps server-rendered placeholders for
        instances of it.</p>
    <p class="t-meta guide-source">Quoted from
        <code>slogsdon/javascript-web-component-hydration</code> at commit
        <code>f98d643</code> (2019-08-06, tag v1.0.5). Nothing on this page was run in a browser
        while building the site.</p>
</section>

<section class="page-section" aria-labelledby="api-title">
    <header class="index-head">
        <h2 id="api-title" class="t-smallcaps-lg">the surface</h2>
        <span class="t-dateline">three exports</span>
    </header>
    <div class="prose">
        <dl class="facts">
            <div class="fact">
                <dt><code>CustomHTMLElement</code></dt>
                <dd>Extends <code>HTMLElement</code>. Subclasses declare a tag name with a static
                    <code>is</code> getter and are defined once per page with the static
                    <code>register()</code>, which wraps
                    <code>customElements.whenDefined()</code> and rejects when no name is set or
                    <code>window.customElements</code> is missing.</dd>
            </div>
            <div class="fact">
                <dt><code>hydrate(name, slotElementType?)</code></dt>
                <dd>Finds every <code>[data-template="name"]</code> in the document and replaces it
                    with a freshly created <code>&lt;name&gt;</code> element, carrying over the
                    initial data. The optional second argument is the tag used for slot content
                    created from that data — <code>span</code> by default.</dd>
            </div>
            <div class="fact">
                <dt><code>onReady(callback)</code></dt>
                <dd>A wrapper around <code>DOMContentLoaded</code> that calls back immediately if
                    the document has already finished loading.</dd>
            </div>
        </dl>
        <p>The constructor does three things in order: it looks up
            <code>document.getElementById('template-' + is)</code>, calls
            <code>this.attachShadow({ mode: 'open' })</code>, and initialises a list of child
            element names it has already hydrated. On <code>connectedCallback</code> it appends a
            clone of that template's content to the shadow root, then hydrates any
            <code>[data-template]</code> descendants — in the light DOM and in the shadow root both
            — so nested components resolve too.</p>
    </div>
</section>

<section class="page-section" aria-labelledby="contract-title">
    <header class="index-head">
        <h2 id="contract-title" class="t-smallcaps-lg">the markup contract</h2>
        <span class="t-dateline">two attributes, one id</span>
    </header>
    <div class="prose">
        <p>The library does no rendering on the server itself; it is written to pair with a
            service that does. It needs two things in the page: a client template whose
            <code>id</code> is <code>template-</code> plus the element name, and a server-rendered
            placeholder whose <code>data-template</code> attribute is that same name. The README's
            example template and its rendered form, quoted:</p>
        <pre><code>&lt;template id="template-x-list-item"&gt;
  &lt;li&gt;
    &lt;slot&gt;&lt;/slot&gt;
  &lt;/li&gt;
&lt;/template&gt;</code></pre>
        <pre><code>&lt;div data-template="x-list-item" data-initial-data="content"&gt;
  &lt;li&gt;
    &lt;slot&gt;content&lt;/slot&gt;
  &lt;/li&gt;
&lt;/div&gt;</code></pre>
        <p>The data (<code>content</code>) becomes the <code>data-initial-data</code> value and is
            inserted into the unnamed slot as a child. Named slots require the value to be
            URL-encoded JSON keyed by slot name; the README's named-slot variant:</p>
        <pre><code>&lt;div data-template="x-list-item" data-initial-data="%7B%22label%22%3A%22content%22%2C%22foo%22%3A%22bar%22%7D"&gt;
  &lt;li&gt;
    &lt;slot name="label"&gt;content&lt;/slot&gt;
    &lt;slot name="foo"&gt;bar&lt;/slot&gt;
  &lt;/li&gt;
&lt;/div&gt;</code></pre>
        <p><strong>How the data is decoded.</strong> <code>hydrate()</code> runs
            <code>decodeURI()</code> and then the substitutions PHP's <code>urlencode()</code>
            needs — <code>%3D</code> to <code>=</code>, <code>+</code> to space,
            <code>%2F</code> to <code>/</code>, <code>%3A</code> to <code>:</code>,
            <code>%2C</code> to <code>,</code> — and tries
            <code>JSON.parse()</code>. On success each key becomes an element with
            <code>slot = key</code> and the value as <code>innerHTML</code>. On failure the whole
            decoded string is treated as one unnamed slot's HTML. Note that this writes slot
            content with <code>innerHTML</code>: the initial data is treated as markup, not as
            inert text.</p>
    </div>
</section>

<section class="page-section" aria-labelledby="install-title">
    <header class="index-head">
        <h2 id="install-title" class="t-smallcaps-lg">install</h2>
        <span class="t-dateline">npm, no build step</span>
    </header>
    <div class="prose">
        <p>The package is <code>web-component-hydration</code> on npm; 1.0.5 is the latest
            version and was published on 2019-08-06. It has no dependencies,
            <code>module</code> points at <code>index.js</code>, and the source is three ES
            modules — <code>custom-html-element.js</code>, <code>helpers.js</code>, and an
            <code>index.js</code> that re-exports both.</p>
        <p>The repository's README documents usage, not installation, so no install command is
            printed here. Its own examples import by relative path from
            <code>./node_modules/web-component-hydration/…</code>, which is what the sibling PHP
            demo does: its <code>examples/simple/js/package.json</code> depends on
            <code>web-component-hydration@^1.0.0</code> and its README runs
            <code>cd examples/simple &amp;&amp; yarn install</code>. The lockfile there resolves the
            dependency to exactly 1.0.5.</p>
    </div>
</section>

<section class="page-section" aria-labelledby="example-title">
    <header class="index-head">
        <h2 id="example-title" class="t-smallcaps-lg">one example, quoted</h2>
        <span class="t-dateline">register, then hydrate</span>
    </header>
    <div class="prose">
        <p>The whole client side of a component, from the README. First the element:</p>
        <pre><code>// x-list-item.js
import { CustomHTMLElement } from './node_modules/web-component-hydration/custom-html-element.js';

export class XListItem extends CustomHTMLElement {
  // define `XListItem`'s tag name
  static get is() { return 'x-list-item'; }
}</code></pre>
        <p>Then the entry point — register the element, then hydrate the placeholders that carry
            its name:</p>
        <pre><code>// main.js
import { hydrate } from './node_modules/web-component-hydration/helpers.js';
import { XListItem } from './x-list-item.js';

// register `XListItem` as a custom element
await XListItem.register();
// hydrate any server-side rendered `XListIem`s
hydrate(XListItem.is);</code></pre>
        <p>Order matters: <code>hydrate()</code> returns early if the element is not defined yet
            or if <code>template-&lt;name&gt;</code> is absent, so a hydrate call that runs before
            <code>register()</code> resolves does nothing at all. The PHP demo wraps the whole
            sequence in <code>onReady()</code> and awaits each <code>register()</code> before
            calling <code>hydrate()</code> for that component.</p>
        <p>Both snippets are quoted as written, including the README's <code>XListIem</code>
            typo in the trailing comment. Nothing in this section was executed while building
            this page: it needs a browser, and a browser is not part of this build.</p>
    </div>
</section>

<section class="page-section" aria-labelledby="status-title">
    <header class="index-head">
        <h2 id="status-title" class="t-smallcaps-lg">status and limits</h2>
        <span class="t-dateline">2019 code</span>
    </header>
    <div class="prose">
        <p>Version 1.0.5, MIT licensed. The last commit that changes behaviour is 2019-08-06 (the
            v1.0.5 tag); the repository's newest commit, 2026-05-08, only adds a homepage link. It
            is published and unmaintained, not deprecated.</p>
        <p>Two consequences follow from how it works, not from a defect.</p>
        <p>Hydration needs JavaScript. The browser receives a <code>div</code> with copied
            children; the shadow root and the real custom element only exist after the module runs.
            That is the same trade the PHP page describes, seen from the other side.</p>
        <p>It is not declarative shadow DOM. Neither this repository's code nor its README mentions
            <code>&lt;template shadowrootmode&gt;</code>; the shadow root here is created
            imperatively with <code>attachShadow()</code> in the constructor, in the browser.</p>
    </div>
</section>

<section class="page-section" aria-labelledby="next-title">
    <header class="index-head">
        <h2 id="next-title" class="t-smallcaps-lg">where to go</h2>
        <span class="t-dateline">the other halves</span>
    </header>
    <ul class="link-list">
        <li>
            <a href="/web-component-tools/ssr/">SSR in PHP<span aria-hidden="true"> →</span></a>
            <p>The server side of this contract, and the markup it produced when it was run.</p>
        </li>
        <li>
            <a href="/web-component-tools/jsx/">Authoring in JSX (WIP)<span aria-hidden="true"> →</span></a>
            <p>A different base class, with the same constructor-and-shadow-root pattern.</p>
        </li>
        <li>
            <a href="https://github.com/slogsdon/javascript-web-component-hydration">Source on GitHub<span aria-hidden="true"> ↗</span></a>
            <p>Three files, 178 lines including comments, with <code>@ts-check</code>
                annotations.</p>
        </li>
        <li>
            <a href="https://www.npmjs.com/package/web-component-hydration">npm<span aria-hidden="true"> ↗</span></a>
            <p><code>web-component-hydration</code>, latest 1.0.5.</p>
        </li>
    </ul>
</section>
