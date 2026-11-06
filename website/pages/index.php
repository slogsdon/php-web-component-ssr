<?php
/**
 * F · Frontispiece: the single entry point to this project's set.
 * It names the work, states the problem, and indexes what follows.
 * No figure: a frontispiece carries no argument.
 */
$this->layout('partials::layout', [
    'title' => 'Web Component Tools · render, hydrate, and author native web components',
    'description' => 'Three related tools for native custom elements: a PHP server-side renderer, a browser hydration library, and a work-in-progress JSX factory.',
    'path' => '/',
    'notFound' => false,
]);
?>
<section class="intro" aria-labelledby="project-title">
    <p class="t-smallcaps">shane logsdon · web components</p>
    <h1 id="project-title">Render on the server.<br>Hydrate <span class="t-accent">in the browser.</span></h1>
    <p class="t-lead">Three small tools for native custom elements: PHP that turns a component's
        template into HTML, JavaScript that turns that HTML back into a live element, and a JSX
        factory that writes the component in the first place.</p>
    <a class="btn-arrow" href="/web-component-tools/getting-started/">Choose a tool</a>
</section>

<section class="page-section" aria-labelledby="shape-title">
    <header class="index-head">
        <h2 id="shape-title" class="t-smallcaps-lg">the shape of it</h2>
        <span class="t-dateline">three pieces, one markup contract</span>
    </header>
    <div class="prose">
        <dl class="facts">
            <div class="fact">
                <dt>Render</dt>
                <dd>PHP functions in <code>slogsdon/web-component-ssr</code> read a template file,
                    fill its slots with data, and print a placeholder element the browser can
                    find again.</dd>
            </div>
            <div class="fact">
                <dt>Hydrate</dt>
                <dd><code>web-component-hydration</code> finds those placeholders, replaces each
                    one with a real custom element, and lets the element clone its template into
                    an open shadow root.</dd>
            </div>
            <div class="fact">
                <dt>Author</dt>
                <dd><code>jsx-for-web-components</code> provides the <code>h()</code> pragma
                    function and a <code>JSXCustomElement</code> base class so a component's
                    markup can be written as JSX. Its README labels it work in progress.</dd>
            </div>
        </dl>
        <p>The three were built as a set: the PHP library's output is exactly the markup the
            hydration library looks for, and both use the same two attribute names
            (<code>data-template</code> and the <code>template-</code> element id prefix). Each
            piece is also usable alone, which is how they are documented here — three pages, one
            per tool.</p>
    </div>
</section>

<section class="page-section" aria-labelledby="not-title">
    <header class="index-head">
        <h2 id="not-title" class="t-smallcaps-lg">what this is not</h2>
        <span class="t-dateline">no declarative shadow DOM</span>
    </header>
    <div class="prose">
        <p>Nothing here emits <code>&lt;template shadowrootmode&gt;</code>. Searching the three
            repositories at the revisions this site records — <code>shadowrootmode</code>,
            <code>declarative</code> — returns no matches in code, tests, or READMEs. The PHP
            renderer writes a <code>&lt;div data-template="…"&gt;</code> placeholder plus a
            separate <code>&lt;template id="template-…"&gt;</code> for the client; the shadow root
            is created by <code>attachShadow({ mode: 'open' })</code> in a constructor, in the
            browser, after the script has loaded. The JSX library has a static-markup renderer,
            but it serialises an element's shadow children inline as light DOM rather than as a
            declarative shadow root.</p>
        <p>So the server-rendered HTML is a placeholder a script replaces, not a shadow tree the
            browser adopts. If the script never runs, a reader sees the placeholder's copied
            children, not a styled component. That is the trade these tools were written to make,
            and this site does not claim more for them than that.</p>
    </div>
</section>

<section class="page-section" aria-labelledby="status-title">
    <header class="index-head">
        <h2 id="status-title" class="t-smallcaps-lg">status</h2>
        <span class="t-dateline">2018–2019 code, 2026 doc link</span>
    </header>
    <div class="prose">
        <p>The code in all three is from 2018 and 2019: the JSX factory last changed on
            2018-12-29 (1.2.1), the hydration library on 2019-08-06 (1.0.5), and the PHP library
            on 2019-08-06. Each repository has one later commit, dated 2026-05-08, which adds a
            homepage link and touches no code. Treat all three as published, unmaintained
            examples rather than as an actively developed framework, and treat the JSX library as
            the README itself labels it: <strong>WIP</strong>.</p>
        <p>The PHP package is registered on Packagist as <code>slogsdon/web-component-ssr</code>
            with exactly one version, <code>dev-master</code> — there is no tagged release. Both
            JavaScript packages are on npm: <code>web-component-hydration</code> at 1.0.5 and
            <code>jsx-for-web-components</code> at 1.2.1. The three repositories are separate;
            none of them depends on another at install time.</p>
    </div>
</section>

<section class="page-section" aria-labelledby="next-title">
    <header class="index-head">
        <h2 id="next-title" class="t-smallcaps-lg">where to go</h2>
        <span class="t-dateline">five entries</span>
    </header>
    <ul class="link-list">
        <li>
            <a href="/web-component-tools/ssr/">SSR in PHP<span aria-hidden="true"> →</span></a>
            <p>The two functions, the template directory, the demo's commands, and the markup it
                actually produced.</p>
        </li>
        <li>
            <a href="/web-component-tools/hydration/">Hydration in the browser<span aria-hidden="true"> →</span></a>
            <p><code>CustomHTMLElement</code>, <code>hydrate()</code>, <code>onReady()</code>, and
                the placeholder attributes the client half expects from the server.</p>
        </li>
        <li>
            <a href="/web-component-tools/jsx/">JSX authoring (WIP)<span aria-hidden="true"> →</span></a>
            <p>The <code>h()</code> factory, <code>JSXCustomElement</code>, and the TypeScript plus
                Rollup setup its README walks through.</p>
        </li>
        <li>
            <a href="https://github.com/slogsdon/php-web-component-ssr">Source: php-web-component-ssr<span aria-hidden="true"> ↗</span></a>
            <p>The PHP renderer, its <code>examples/simple</code> demo, and a Psalm config.</p>
        </li>
        <li>
            <a href="https://github.com/slogsdon/javascript-web-component-hydration">Source: javascript-web-component-hydration<span aria-hidden="true"> ↗</span></a>
            <p>Two ES modules and no build step. Also on
                <a href="https://www.npmjs.com/package/web-component-hydration">npm</a>, and the
                sibling <a href="https://github.com/slogsdon/jsx-for-web-components">jsx-for-web-components</a>
                is on <a href="https://www.npmjs.com/package/jsx-for-web-components">npm</a> too.</p>
        </li>
    </ul>
</section>
