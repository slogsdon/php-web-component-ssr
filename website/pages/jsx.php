<?php
/**
 * Tool page · JSX authoring. The README labels this library WIP; that label is
 * kept on the page. Nothing here was built, tested, or bundled.
 */
$this->layout('partials::layout', [
    'title' => 'JSX authoring (WIP) · Web Component Tools',
    'description' => 'The work-in-progress jsx-for-web-components factory: the h() pragma, the JSXCustomElement base class, and the TypeScript and Rollup setup its README walks through.',
    'path' => '/jsx/',
    'notFound' => false,
]);
?>
<section class="intro intro--compact" aria-labelledby="tool-title">
    <p class="t-smallcaps">jsx-for-web-components · npm 1.2.1 · work in progress</p>
    <h1 id="tool-title">Components in TSX. <span class="t-accent">Work in progress.</span></h1>
    <p class="t-lead">A small JSX factory and a component base class, so a custom element's
        markup can be written as JSX and compiled to plain DOM calls. Its README labels it
        <strong>WIP</strong> and this page keeps that label.</p>
    <p class="t-meta guide-source">Quoted from <code>slogsdon/jsx-for-web-components</code> at
        commit <code>52c34a8</code> (2026-05-08, tag v1.2.1 from 2018-12-29). Not built, tested,
        or bundled while writing this page.</p>
</section>

<section class="page-section" aria-labelledby="wip-title">
    <header class="index-head">
        <h2 id="wip-title" class="t-smallcaps-lg">read this first</h2>
        <span class="t-dateline">wip, by its own description</span>
    </header>
    <div class="prose">
        <p>The README's own summary is “WIP: A basic JSX factory for use in projects leveraging
            web components”. The last commit that changes code is the v1.2.1 tag, 2018-12-29; the
            newest commit, 2026-05-08, only adds a homepage link. The package is still on npm at
            1.2.1, and the repository carries five Jest test files, but the toolchain it pins is
            from the same period — Jest 23, ts-jest 23, TSLint 5, TypeScript 3.2.</p>
        <p>Treat it as a working sketch of an idea, not as a maintained framework. Nothing on
            this page has been compiled or run; the commands and files below are quoted from the
            README so you can judge the idea from its own example.</p>
    </div>
</section>

<section class="page-section" aria-labelledby="api-title">
    <header class="index-head">
        <h2 id="api-title" class="t-smallcaps-lg">the surface</h2>
        <span class="t-dateline">a factory and a base class</span>
    </header>
    <div class="prose">
        <dl class="facts">
            <div class="fact">
                <dt><code>h(type, props, ...children)</code></dt>
                <dd>The pragma function the compiler calls for every JSX element. It returns a
                    plain object — <code>{ children, props, type }</code> — and does no DOM work
                    itself.</dd>
            </div>
            <div class="fact">
                <dt><code>JSXCustomElement</code></dt>
                <dd>Extends <code>HTMLElement</code>. A subclass sets
                    <code>static elementName</code>, overrides <code>render()</code>, and calls
                    the inherited <code>static register()</code>. The constructor builds a
                    <code>template</code> from <code>render()</code> and calls
                    <code>attachShadow({ mode: 'open' })</code>;
                    <code>connectedCallback()</code> clones the template into the shadow root.</dd>
            </div>
            <div class="fact">
                <dt><code>createElement(node)</code> / <code>createTemplate(node)</code></dt>
                <dd>Turn that object tree into DOM. <code>node.type</code> is either a tag name or
                    a function: tags become <code>document.createElement()</code>, functions are
                    constructed with <code>Reflect.construct()</code> and rendered — the
                    <code>StatelessComponent</code> path. A comment in
                    <code>src/types.ts</code> records the gap: “TODO: Support classes for
                    components”.</dd>
            </div>
            <div class="fact">
                <dt><code>renderToStaticMarkup(root)</code></dt>
                <dd>Serialises an element to an HTML string: its tag, its attributes, then its
                    shadow children, then its light children. It writes both sets inline; it does
                    not emit a declarative shadow root.</dd>
            </div>
        </dl>
        <p><code>src/register.ts</code> mirrors the hydration library's helper: reject when
            <code>elementName</code> is unset or <code>window.customElements</code> is missing,
            otherwise define once and return <code>customElements.whenDefined()</code>. The
            repository also vendors Polymer's Custom Elements v1 <code>Reflect</code> shim as
            <code>src/native-shim.ts</code>, BSD licensed, with its original header intact.</p>
    </div>
</section>

<section class="page-section" aria-labelledby="install-title">
    <header class="index-head">
        <h2 id="install-title" class="t-smallcaps-lg">install</h2>
        <span class="t-dateline">yarn, then a compiler</span>
    </header>
    <div class="prose">
        <p>The README's requirement is a single line: a compiler that can turn JSX into factory
            calls. Its install line, quoted:</p>
        <pre><code>yarn add jsx-for-web-components</code></pre>
        <p>Its worked example builds for the browser with TypeScript and Rollup, so it also
            installs the toolchain:</p>
        <pre><code>yarn add --dev typescript rollup rollup-plugin-node-resolve rollup-plugin-typescript2</code></pre>
        <p>The package itself declares one runtime dependency, <code>csstype ^2.6.0</code>, for
            the CSS property types in its JSX attribute definitions. It publishes
            <code>lib/index.js</code> and <code>types/index.d.ts</code>; <code>npm run build</code>
            is <code>tsc</code>, and the repository's own <code>tsconfig.json</code> compiles
            <code>src/**/*</code> to <code>lib/</code> with <code>strict</code> on and DOM plus
            ESNext libs.</p>
        <p>Note what that <code>tsconfig.json</code> does <em>not</em> set: <code>jsx</code> and
            <code>jsxFactory</code>. Those belong to the project consuming the library, and the
            README's example supplies them itself.</p>
    </div>
</section>

<section class="page-section" aria-labelledby="example-title">
    <header class="index-head">
        <h2 id="example-title" class="t-smallcaps-lg">one example, quoted</h2>
        <span class="t-dateline">tsconfig, component, page</span>
    </header>
    <div class="prose">
        <p>The README's walkthrough, in three files. First the two compiler options that make the
            factory work — classic JSX, with <code>h</code> as the factory:</p>
        <pre><code>{
  "compilerOptions": {
    "jsx": "react",
    "jsxFactory": "h",
    "moduleResolution": "node",
    "target": "es2015"
  }
}</code></pre>
        <p>Then the component, in <code>src/index.tsx</code>. The <code>h</code> import is
            unused in the source but is what the compiled output calls:</p>
        <pre><code>import { h, JSXCustomElement } from "jsx-for-web-components";

class ExampleElement extends JSXCustomElement {
  static elementName = "example-element";

  public render() {
    return &lt;div id="testing"&gt;hello&lt;/div&gt;;
  }
}

ExampleElement.register();</code></pre>
        <p>And the page that uses it, in <code>index.html</code>:</p>
        <pre><code>&lt;!DOCTYPE html&gt;
&lt;script src=&quot;/dist/index.js&quot; defer&gt;&lt;/script&gt;
&lt;example-element&gt;&lt;/example-element&gt;</code></pre>
        <p>After <code>rollup -c rollup.config.js</code> — the README also prints the Rollup config
            it expects, an IIFE bundle of <code>./src/index.tsx</code> into
            <code>./dist/index.js</code> — the README says the page should load in a browser and
            show the rendered work. That claim is the README's, not this site's: it was not
            reproduced here.</p>
    </div>
</section>

<section class="page-section" aria-labelledby="notes-title">
    <header class="index-head">
        <h2 id="notes-title" class="t-smallcaps-lg">how it differs</h2>
        <span class="t-dateline">same pattern, no server</span>
    </header>
    <div class="prose">
        <p>The runtime pattern is the one the other two tools use, minus the server: a
            <code>template</code> is filled from a render result, an open shadow root is attached in
            the constructor, and the clone is appended on connect. There is no naming convention
            shared with hydration — no <code>template-</code> id lookup and no
            <code>data-template</code> placeholder — because this library renders its own elements
            directly rather than adopting server markup.</p>
        <p>Its <code>renderToStaticMarkup()</code> suggests a server-rendering path, but it
            serialises an element's shadow children into the element's own light DOM. If you need
            server output that a browser renders before JavaScript runs, nothing here produces it:
            no file in this repository emits <code>&lt;template shadowrootmode&gt;</code>.</p>
        <p>If you need a super call, the README's note is explicit: override
            <code>connectedCallback()</code> and call <code>super.connectedCallback()</code>, or the
            template never gets cloned into the shadow root.</p>
    </div>
</section>

<section class="page-section" aria-labelledby="next-title">
    <header class="index-head">
        <h2 id="next-title" class="t-smallcaps-lg">where to go</h2>
        <span class="t-dateline">the rest of the set</span>
    </header>
    <ul class="link-list">
        <li>
            <a href="/web-component-tools/">The three tools<span aria-hidden="true"> →</span></a>
            <p>What the set does, and the one thing none of the three does.</p>
        </li>
        <li>
            <a href="/web-component-tools/hydration/">Hydration in the browser<span aria-hidden="true"> →</span></a>
            <p>The maintained-in-2019 cousin of this library's base class.</p>
        </li>
        <li>
            <a href="https://github.com/slogsdon/jsx-for-web-components">Source on GitHub<span aria-hidden="true"> ↗</span></a>
            <p><code>src/</code>, five Jest test files, and the Polymer shim.</p>
        </li>
        <li>
            <a href="https://www.npmjs.com/package/jsx-for-web-components">npm<span aria-hidden="true"> ↗</span></a>
            <p><code>jsx-for-web-components</code>, latest 1.2.1.</p>
        </li>
    </ul>
</section>
