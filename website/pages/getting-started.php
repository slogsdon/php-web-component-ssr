<?php
/**
 * D · Assembly Order: a short router. The three tool pages carry their own
 * install steps and examples, so this page only establishes the order and the
 * dependency between choosing and running one.
 */
$this->layout('partials::layout', [
    'title' => 'Getting started · Web Component Tools',
    'description' => 'Pick which of the three web component tools you need — PHP server rendering, browser hydration, or JSX authoring — and follow that tool page for install steps.',
    'path' => '/getting-started/',
    'notFound' => false,
]);
?>
<section class="intro intro--compact" aria-labelledby="guide-title">
    <p class="t-smallcaps">web component tools · assembly order</p>
    <h1 id="guide-title">Getting <span class="t-accent">started.</span></h1>
    <p class="t-lead">Three tools, three separate installs. The one thing that is genuinely
        sequential is step 1: decide which problem you have. After that, each tool page carries
        its own steps and its own example.</p>
    <p class="t-meta guide-source">Commands and output on the tool pages are quoted from the
        three repositories at the revisions those pages record. The PHP output was produced by
        running the package; the two JavaScript examples were not executed.</p>
</section>

<div class="assembly">
    <section class="assembly__step" id="step-1" aria-labelledby="step-1-title">
        <span class="assembly__num" aria-hidden="true">01</span>
        <h2 id="step-1-title" class="t-headline-sm">Work out which problem you have</h2>
        <div class="prose">
            <p>The three tools are not stages of one pipeline, and none of them installs another.
                Pick by the thing you are trying to do:</p>
        </div>
        <div class="table-wrap">
            <table>
                <caption class="t-smallcaps">choose by task</caption>
                <thead>
                    <tr><th scope="col">If you need to…</th><th scope="col">Use</th><th scope="col">Needs</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">Print a component's markup from PHP</th>
                        <td><a href="/web-component-tools/ssr/">SSR in PHP</a></td>
                        <td>PHP 7.1+ and a template file; the client half is separate</td>
                    </tr>
                    <tr>
                        <th scope="row">Turn server-rendered placeholders into live elements</th>
                        <td><a href="/web-component-tools/hydration/">Hydration in the browser</a></td>
                        <td>native custom elements in a browser, plus server markup to hydrate</td>
                    </tr>
                    <tr>
                        <th scope="row">Write a component as JSX</th>
                        <td><a href="/web-component-tools/jsx/">JSX authoring (WIP)</a></td>
                        <td>a JSX compiler, then a bundler</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="prose">
            <p>Two of them pair: the PHP renderer prints exactly the attributes
                <code>hydrate()</code> looks for, so using both is a normal setup — one install on
                the server, one in the browser. The JSX library stands apart: it renders its own
                elements and shares no placeholder contract with the other two.</p>
        </div>
    </section>

    <section class="assembly__step" id="step-2" aria-labelledby="step-2-title">
        <span class="assembly__num" aria-hidden="true">02</span>
        <h2 id="step-2-title" class="t-headline-sm">Install only what step 1 named</h2>
        <div class="prose">
            <p>Each tool page repeats its own install section in full, because the requirements
                differ and a summary here would go stale first. What is worth knowing before you
                start:</p>
            <dl class="facts">
                <div class="fact">
                    <dt>SSR in PHP</dt>
                    <dd>Composer for the autoloader, <code>ext-dom</code> and
                        <code>ext-json</code>, plus <code>web-component-hydration</code> for the
                        client half. Packagist lists one version, <code>dev-master</code> — there
                        is no tagged release, so a stable constraint will not resolve.</dd>
                </div>
                <div class="fact">
                    <dt>Hydration</dt>
                    <dd><code>web-component-hydration</code> from npm at 1.0.5, last published
                        2019-08-06. No dependencies, no build step: three ES modules imported by
                        relative path.</dd>
                </div>
                <div class="fact">
                    <dt>JSX (WIP)</dt>
                    <dd><code>jsx-for-web-components</code> from npm at 1.2.1, last published
                        2018-12-29, plus the TypeScript and Rollup toolchain its README pins.</dd>
                </div>
            </dl>
        </div>
    </section>

    <section class="assembly__step" id="step-3" aria-labelledby="step-3-title">
        <span class="assembly__num" aria-hidden="true">03</span>
        <h2 id="step-3-title" class="t-headline-sm">Set the template directory, then render</h2>
        <div class="prose">
            <p>For the PHP path only. The package needs to know where template files live before it
                renders anything; after that, a call to <code>getTemplate()</code> prints the
                server half and <code>getClientTemplate()</code> prints the client half. The
                <a href="/web-component-tools/ssr/">SSR page</a> shows both calls together with the
                markup they produced when the package was run.</p>
        </div>
    </section>

    <section class="assembly__step" id="step-4" aria-labelledby="step-4-title">
        <span class="assembly__num" aria-hidden="true">04</span>
        <h2 id="step-4-title" class="t-headline-sm">Register in the browser, then hydrate</h2>
        <div class="prose">
            <p>For the hydration path. The order is not optional: <code>hydrate(name)</code>
                returns early when the element is not yet defined or the
                <code>template-&lt;name&gt;</code> element is missing, so every
                <code>await register()</code> has to settle before the matching
                <code>hydrate()</code> call runs. The
                <a href="/web-component-tools/hydration/">hydration page</a> quotes the two files
                that do it.</p>
        </div>
    </section>
</div>

<section class="page-section" aria-labelledby="next-title">
    <header class="index-head">
        <h2 id="next-title" class="t-smallcaps-lg">where to go</h2>
        <span class="t-dateline">after you pick</span>
    </header>
    <ul class="link-list">
        <li>
            <a href="/web-component-tools/ssr/">SSR in PHP<span aria-hidden="true"> →</span></a>
            <p>Two functions, the template directory, the demo's commands, and its real output.</p>
        </li>
        <li>
            <a href="/web-component-tools/hydration/">Hydration in the browser<span aria-hidden="true"> →</span></a>
            <p>The placeholder contract, the decoder, and the register-then-hydrate order.</p>
        </li>
        <li>
            <a href="/web-component-tools/jsx/">JSX authoring (WIP)<span aria-hidden="true"> →</span></a>
            <p>The <code>h()</code> factory and the TSX example, with the WIP label intact.</p>
        </li>
        <li>
            <a href="/web-component-tools/">The three tools<span aria-hidden="true"> →</span></a>
            <p>What the set is, and what none of it claims.</p>
        </li>
    </ul>
</section>
