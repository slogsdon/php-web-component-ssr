<?php
/**
 * Shared layout for a project microsite served at <origin>/<slug>/.
 *
 * Everything site-specific comes from website/site.json, which scripts/build-site.ts
 * validates and passes in as environment variables. No origin, slug, or project
 * name is hard-coded here, so the skeleton can be copied to another project by
 * replacing site.json (and the Worker identity in cloudflare.config.ts).
 */
$slug = getenv('SITE_SLUG');
$name = getenv('SITE_NAME');
$origin = getenv('SITE_ORIGIN');
if (!is_string($slug) || $slug === '' || !is_string($name) || !is_string($origin)) {
    throw new RuntimeException('SITE_SLUG, SITE_NAME and SITE_ORIGIN must be set by scripts/build-site.ts');
}
$base = '/' . $slug;
$origin = rtrim($origin, '/');
$path = $path ?? '/';
$canonical = $origin . $base . $path;
$preview = getenv('SITE_NOINDEX') !== '0';
$noindex = $preview || $notFound;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title><?= $this->e($title) ?></title>
    <meta name="description" content="<?= $this->e($description) ?>">
<?php if ($noindex): ?>
    <meta name="robots" content="noindex">
<?php endif; ?>
<?php if (!$notFound): ?>
    <link rel="canonical" href="<?= $this->e($canonical) ?>">
    <meta property="og:url" content="<?= $this->e($canonical) ?>">
<?php endif; ?>
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= $this->e($title) ?>">
    <meta property="og:description" content="<?= $this->e($description) ?>">
    <meta name="author" content="Shane Logsdon">
    <link rel="preload" href="<?= $base ?>/assets/fonts/fraunces-var.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?= $base ?>/assets/tokens.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/site.css">
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to content</a>
    <header class="site-header frame">
        <a href="<?= $base ?>/" class="wordmark"><?= $this->e($name) ?></a>
        <nav aria-label="Primary">
            <a href="<?= $base ?>/getting-started/">Getting started</a>
            <a href="<?= $this->e(getenv('SITE_SOURCE')) ?>">Source</a>
            <a href="https://projects.logsdon.io/">Projects</a>
        </nav>
    </header>
    <main id="main-content" class="frame" tabindex="-1"><?= $this->section('content') ?></main>
    <footer class="site-footer frame">
        <p class="t-smallcaps">a project by <a href="https://shane.logsdon.io/about/">shane logsdon</a></p>
        <div>
            <a href="https://projects.logsdon.io/">Project collection</a>
            <a href="https://shane.logsdon.io/">shane.logsdon.io</a>
        </div>
    </footer>
</body>
</html>
