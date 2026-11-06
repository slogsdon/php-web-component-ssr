<?php
/**
 * G · Recovery: an error state offers one route back. No canonical, always noindex.
 */
$this->layout('partials::layout', [
    'title' => 'Page not found · Web Component Tools',
    'description' => 'This address is not part of the Web Component Tools microsite.',
    'notFound' => true,
]);
?>
<section class="not-found">
    <p class="t-smallcaps">404 · page not found</p>
    <h1>This page isn’t here.</h1>
    <p class="t-lead">The microsite has an introduction, a short getting-started router, and one
        page per tool: SSR in PHP, hydration, and JSX. Start from the introduction to find the
        documentation you need.</p>
    <a class="btn-arrow" href="/web-component-tools/">Back to the introduction</a>
</section>
