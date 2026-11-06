<?php
/**
 * Tool page · SSR: the PHP renderer, its documented commands, and the markup it
 * produces. The output shown was produced by running the package's own
 * functions; see website/README.md for what was and was not executed.
 */
$this->layout('partials::layout', [
    'title' => 'SSR in PHP · Web Component Tools',
    'description' => 'How php-web-component-ssr renders a custom element template into HTML: getTemplate, getClientTemplate, the template directory, and the markup the demo actually produces.',
    'path' => '/ssr/',
    'notFound' => false,
]);
?>
<section class="intro intro--compact" aria-labelledby="tool-title">
    <p class="t-smallcaps">php-web-component-ssr · packagist slogsdon/web-component-ssr</p>
    <h1 id="tool-title">Template in. <span class="t-accent">HTML out.</span></h1>
    <p class="t-lead">One PHP file of functions. It reads a custom element's template from disk,
        fills the template's slots with whatever data you pass, and prints two things: a
        placeholder for the browser to find, and the template the client half will clone.</p>
    <p class="t-meta guide-source">Quoted from <code>slogsdon/php-web-component-ssr</code> at
        commit <code>00be84d</code> (2026-05-08). The output in the example was produced by
        running the package's functions on PHP 8.5.11 while building this page; the demo commands
        were not run.</p>
</section>

<section class="page-section" aria-labelledby="api-title">
    <header class="index-head">
        <h2 id="api-title" class="t-smallcaps-lg">the two functions you call</h2>
        <span class="t-dateline">plus one setter</span>
    </header>
    <div class="prose">
        <dl class="facts">
            <div class="fact">
                <dt><code>getTemplate($name, $data = null)</code></dt>
                <dd>Prints the <em>server-side</em> half: a <code>div</code> carrying
                    <code>data-template="$name"</code>, the template's children moved into it, and
                    — when you pass data — a URL-encoded <code>data-initial-data</code> attribute.</dd>
            </div>
            <div class="fact">
                <dt><code>getClientTemplate($name, $data = null)</code></dt>
                <dd>Prints the <em>client-side</em> half: the raw template file as a
                    <code>template</code> element with <code>id="template-$name"</code>. The
                    hydration library looks the element up by that id.</dd>
            </div>
            <div class="fact">
                <dt><code>templateLocation($directory = null)</code></dt>
                <dd>Tells the package where template files live. It keeps the path in a static
                    variable, so call it once before rendering; with no argument it returns the
                    current directory.</dd>
            </div>
        </dl>
        <p>Both readers funnel through <code>getTemplateSource($name, $data = [])</code>, which
            includes <code>&lt;template-location&gt;/&lt;name&gt;.html</code> with <code>$data</code>
            extracted into scope, so a template file is ordinary PHP-flavoured HTML. A missing
            template file returns an empty string rather than failing.</p>
    </div>
</section>

<section class="page-section" aria-labelledby="install-title">
    <header class="index-head">
        <h2 id="install-title" class="t-smallcaps-lg">install</h2>
        <span class="t-dateline">composer + one npm package</span>
    </header>
    <div class="prose">
        <p>The README's requirements list is short: PHP 7.1+, Composer for the autoloader only,
            and <code>web-component-hydration</code> for the client half.
            <code>composer.json</code> requires <code>ext-dom</code> and <code>ext-json</code> —
            no runtime libraries — and PSR-4 autoloads <code>WebComponentSSR\</code> from
            <code>src/</code>, loading <code>src/functions.php</code> eagerly.</p>
        <p><strong>There is no tagged release.</strong> Packagist lists the package as
            <code>slogsdon/web-component-ssr</code> with exactly one version, <code>dev-master</code>,
            pointing at the same commit this page quotes. The README does not give a
            <code>composer require</code> line, so none is printed here: a stable constraint would
            not resolve.</p>
        <p><strong>What the README does document</strong> is running the demo. Both lines are
            quoted exactly:</p>
        <pre><code>cd examples/simple &amp;&amp; yarn install
cd ../.. &amp;&amp; composer start</code></pre>
        <p>The second line runs the package's Composer script, which is <code>@php -S 0.0.0.0:5000
            -t examples/simple</code>: PHP's built-in server on port 5000, serving the demo
            directory. <code>composer test</code> runs Psalm, the only dev dependency
            (<code>vimeo/psalm ^3.4</code>).</p>
        <p>The <code>yarn install</code> is for the demo's own
            <code>examples/simple/js/package.json</code>, which depends on
            <code>web-component-hydration@^1.0.0</code>; its lockfile resolves that to 1.0.5. The
            demo imports the package by relative path —
            <code>./node_modules/web-component-hydration/helpers.js</code> — so there is no bundler
            and no build step.</p>
    </div>
</section>

<section class="page-section" aria-labelledby="example-title">
    <header class="index-head">
        <h2 id="example-title" class="t-smallcaps-lg">the demo, end to end</h2>
        <span class="t-dateline">two elements, one nesting</span>
    </header>
    <div class="prose">
        <p><code>examples/simple/index.php</code> sets the template directory and then renders
            <code>x-list-item</code> inside <code>x-list</code>, hands the page a client template
            for each, and loads the module script. The two calls, quoted:</p>
        <pre><code>&lt;?php
WebComponentSSR\templateLocation(__DIR__ . '/js/templates');
?&gt;

&lt;!-- server rendered --&gt;
&lt;?= getTemplate('x-list', getTemplate('x-list-item', ['label' =&gt; 'content', 'foo' =&gt; 'bar'])); ?&gt;

&lt;!-- client template --&gt;
&lt;?= getClientTemplate('x-list'); ?&gt;
&lt;?= getClientTemplate('x-list-item'); ?&gt;</code></pre>
        <p>The templates are plain HTML files in <code>examples/simple/js/templates/</code>,
            <code>x-list.html</code> holding <code>&lt;ul&gt;&lt;slot&gt;&lt;/slot&gt;&lt;/ul&gt;</code>
            and <code>x-list-item.html</code> holding
            <code>&lt;li&gt;&lt;slot name="label"&gt;&lt;/slot&gt;&lt;/li&gt;</code>, each wrapped in a
            <code>&lt;template&gt;</code>.</p>
        <p><strong>What it produced.</strong> Running those two calls on PHP 8.5.11, with
            <code>templateLocation()</code> pointed at the demo's templates, printed the following.
            It is verbatim apart from a trailing newline; the long
            <code>data-initial-data</code> value is one line in the output.</p>
        <pre><code>&lt;div data-template="x-list" data-initial-data="%3Cdiv+data-template%3D%22x-list-item%22+data-initial-data%3D%22%257B%2522label%2522%253A%2522content%2522%252C%2522foo%2522%253A%2522bar%2522%257D%22%3E%0A++%3Cli%3E%0A++++%3Cslot+name%3D%22label%22%3Econtent%3C%2Fslot%3E%0A++%3C%2Fli%3E%0A%3C%2Fdiv%3E%0A"&gt;
  &lt;ul&gt;
    &lt;slot&gt;&lt;div data-template="x-list-item" data-initial-data="%7B%22label%22%3A%22content%22%2C%22foo%22%3A%22bar%22%7D"&gt;
  &lt;li&gt;
    &lt;slot name="label"&gt;content&lt;/slot&gt;
  &lt;/li&gt;
&lt;/div&gt;
&lt;/slot&gt;
  &lt;/ul&gt;
&lt;/div&gt;</code></pre>
        <p>And the client template for <code>x-list-item</code>:</p>
        <pre><code>&lt;template id="template-x-list-item"&gt;
  &lt;li&gt;
    &lt;slot name="label"&gt;&lt;/slot&gt;
  &lt;/li&gt;
&lt;/template&gt;</code></pre>
    </div>
</section>

<section class="page-section" aria-labelledby="notes-title">
    <header class="index-head">
        <h2 id="notes-title" class="t-smallcaps-lg">reading the output</h2>
        <span class="t-dateline">worth knowing before you use it</span>
    </header>
    <div class="prose">
        <p><strong>The data shape decides which slots fill.</strong> A string fills only the
            unnamed slot, inserted as an XML fragment — that is why the outer
            <code>x-list</code> slot holds the child element's markup. An array fills only
            <em>named</em> slots, one text node each, and leaves an unnamed slot empty. The demo
            passes <code>foo =&gt; 'bar'</code> to <code>x-list-item</code>, whose template declares
            only <code>slot name="label"</code>, so that key goes unused here; the hydration
            library's README shows a template that declares both.</p>
        <p><strong>The placeholder copies the template, minus its id.</strong>
            <code>getTemplate()</code> moves the template's children into the new
            <code>div</code> and copies every attribute except <code>id</code>. So the content is
            real, visible light DOM until a script replaces it.</p>
        <p><strong>Encoding.</strong> String data is passed to <code>urlencode()</code> before it
            becomes <code>data-initial-data</code>; arrays go through <code>json_encode()</code>
            first. The client half undoes both.</p>
        <p><strong>A deprecation notice on current PHP.</strong> The same run printed
            <code>mb_convert_encoding(): Handling HTML entities via mbstring is deprecated</code>,
            naming <code>src/CustomHTMLElement.php</code> line 38. The output above was still
            produced. The README's stated floor is PHP 7.1; this was PHP 8.5.11, and nothing in
            the repository was changed to silence the notice.</p>
        <p><strong>No declarative shadow DOM.</strong> The printed markup contains no
            <code>&lt;template shadowrootmode&gt;</code>; the shadow root arrives later, when the
            client half constructs the element.</p>
    </div>
</section>

<section class="page-section" aria-labelledby="next-title">
    <header class="index-head">
        <h2 id="next-title" class="t-smallcaps-lg">where to go</h2>
        <span class="t-dateline">the other half</span>
    </header>
    <ul class="link-list">
        <li>
            <a href="/web-component-tools/hydration/">Hydration in the browser<span aria-hidden="true"> →</span></a>
            <p>What consumes the <code>div</code> and the <code>template</code> printed above.</p>
        </li>
        <li>
            <a href="/web-component-tools/">The three tools<span aria-hidden="true"> →</span></a>
            <p>How the pieces fit, and what none of them claims.</p>
        </li>
        <li>
            <a href="https://github.com/slogsdon/php-web-component-ssr">Source on GitHub<span aria-hidden="true"> ↗</span></a>
            <p><code>src/functions.php</code>,
                <code>src/CustomHTMLElement.php</code>, and <code>examples/simple</code>.</p>
        </li>
        <li>
            <a href="https://packagist.org/packages/slogsdon/web-component-ssr">Packagist<span aria-hidden="true"> ↗</span></a>
            <p>One version: <code>dev-master</code>.</p>
        </li>
    </ul>
</section>
