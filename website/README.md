# Web Component Tools — microsite

Static microsite for the Web Component Tools family, served at
`https://projects.logsdon.io/web-component-tools/`. The family is three separate
repositories, and the site is one page per tool plus a family root:

| Page | Source repository | Revision |
| --- | --- | --- |
| `/` family root | all three, below | — |
| `/ssr/` | `slogsdon/php-web-component-ssr` | `00be84d60925830fbb02b3ee4a5e4331adb54ffb` (2026-05-08) |
| `/hydration/` | `slogsdon/javascript-web-component-hydration` | `f98d6434c3982a40a4c7d8cd5644f461674ebcd3` (tag v1.0.5, 2019-08-06) |
| `/jsx/` | `slogsdon/jsx-for-web-components` | `52c34a8beaa72216a94e696640f702415949cc54` (tag v1.2.1 code from 2018-12-29) |
| `/getting-started/` | all three | — |

All product content is derived from those revisions: the three `README.md` files,
`composer.json` and `src/` in the PHP repository, `package.json`, `helpers.js`,
`custom-html-element.js`, `render.ts`, `jsx-custom-element.ts` in the two
JavaScript repositories, and `examples/simple/` plus its `yarn.lock`.

## Build

Node 26.12.0, PHP 8.3+, and Composer are required.

```sh
cd website
composer install --no-interaction --prefer-dist
node scripts/build-site.ts          # SITE_NOINDEX=1 by default: preview, noindex
SITE_NOINDEX=0 node scripts/build-site.ts   # indexable production artifact
```

Output is a physical subtree at `website/dist/<slug>/`, plus `dist/_headers` in
preview builds. Nothing else is written at the `dist/` root: the hub owns
`/robots.txt`, `/sitemap.xml`, and the slashless `/<slug>` redirect.

Packaging (local only; neither command deploys):

```sh
npm install          # or `npm ci` against the committed lock
npx --no-install cf build --mode microsite
npx --no-install cf build --mode microsite-production
```

## One file drives the site

`site.json` holds the values the build and the pages need — `slug`, `name`,
`origin`, `lastmod`, `description` — plus the `source` URL, which the layout uses
for the header's "Source" link (it points at the PHP repository, the family's
owner). `scripts/build-site.ts` validates them, exports them to the PHP layout as
`SITE_SLUG`, `SITE_NAME`, `SITE_ORIGIN`, `SITE_SOURCE` and `SITE_NOINDEX`, and
mounts every emitted URL under `/<slug>/`. `lastmod` is the content revision date
of the family's owning repository, not the build date.

`cloudflare.config.ts` keeps the Worker name (`web-component-tools-microsite`,
with a `-preview` variant) and the fetch pattern
(`projects.logsdon.io/web-component-tools/*`) as literals on purpose: they are the
deployment identity, not site content.

## What the build asserts

`scripts/build-site.ts` fails the build unless all of the following hold:

- `index.html`, `404.html`, `sitemap.xml`, `assets/site.css`, `assets/tokens.css`,
  `assets/fonts/*` exist and are non-empty.
- No `_redirects`, `robots.txt`, `sitemap.xml`, `index.html`, or `404.html` at the
  `dist/` root; the root holds only `<slug>/` (and `_headers` when `SITE_NOINDEX=1`).
- Every HTML page has exactly one `<h1>`, a unique `<title>` containing the project
  name, a meta description, the skip link, the `main` landmark, and the primary
  `nav`.
- Canonical and `og:url` equal `https://projects.logsdon.io/<slug>/<path>`; the 404
  carries neither and is always noindex.
- The `noindex` meta matches `SITE_NOINDEX`, and the footer links both
  `https://projects.logsdon.io/` and `https://shane.logsdon.io/`.
- No `href`/`src` is root-relative outside `/<slug>/`, and every local link and
  asset target exists in the artifact.
- The sitemap lists exactly the canonical pages (five here), with `lastmod` set to
  `site.json`'s `lastmod`.

## Content and design provenance

- Pages: `pages/index.php` is F · Frontispiece — the family root names the three
  tools, states the one thing none of them does, and indexes the three tool pages.
  `pages/ssr.php`, `pages/hydration.php`, and `pages/jsx.php` are tool overviews,
  each with its own install section and one example quoted from its repository.
  `pages/getting-started.php` is D · Assembly Order, kept deliberately short: the
  tool pages carry their install steps, so it routes by task instead of repeating
  them. `pages/404.html.php` is G · Recovery.
- No CSS was added. `public/assets/site.css` and `public/assets/tokens.css` are the
  hub's files verbatim (identical to the pilot microsite's copies), and the classes
  the pages use — `.intro`, `.page-section`, `.prose`, `.facts`, `.link-list`,
  `.table-wrap`, `.assembly*`, `.harness*` — already exist in them.
- `composer.lock` pins `slogsdon/flat-file` to the hub's reference
  (`dev-code-highlight`, `3ba5285e424aa376af324db0424f53c33a2c07a5`).
  `package.json` pins the hub's `cf` 1.0.0-beta.27,
  `@cloudflare/vite-plugin` 2.0.0-beta.v.10, and `vite` 7.3.7 exactly. There is no
  client JavaScript on the site.

## Declarative shadow DOM: what the grep found

The task that produced this site forbids claiming modern declarative shadow DOM
support, so it was grepped for in all three source repositories at the recorded
revisions.

- `grep -rn shadowrootmode` and `grep -rni 'declarative'` over `README.md`, `src/`,
  `examples/`, and `composer.json` in `php-web-component-ssr`: **no matches**.
- The same search over `src/` and `tests/` in `jsx-for-web-components` and over the
  three `.js` files in `javascript-web-component-hydration`: **no matches** for
  either term.
- What the code does instead: the PHP renderer prints `<div data-template="…">`
  and a separate `<template id="template-…">`; both JavaScript libraries call
  `attachShadow({ mode: 'open' })` inside a constructor and clone a template into
  the resulting shadow root in the browser. `jsx-for-web-components`'
  `renderToStaticMarkup()` serialises an element's shadow children *inline* as
  light DOM.

The three pages say this explicitly, and none of them claims a declarative shadow
root.

## Verified here

- `composer install`, `node scripts/build-site.ts` in both modes, and every
  assertion listed above.
- `npm install` (lock regenerated by npm, resolved versions unchanged), then
  `npx --no-install cf build --mode microsite` and
  `npx --no-install cf build --mode microsite-production`; the packaged asset
  trees were inspected.
- **The SSR example was executed on the server side.** With
  `php -r 'require "src/CustomHTMLElement.php"; require "src/functions.php"; …'`
  on PHP 8.5.11, `templateLocation()` pointed at `examples/simple/js/templates`
  and the demo's two `getTemplate()` calls plus `getClientTemplate()` were run. The
  markup on `/ssr/` is that run's output, verbatim apart from a trailing newline.
  The run also printed
  `mb_convert_encoding(): Handling HTML entities via mbstring is deprecated`
  naming `src/CustomHTMLElement.php` line 38; the site reports that notice rather
  than hiding it.
- **The PHP demo ran end to end, hydration included (2026-11-09).** In a scratch copy of
  `origin/master` (`00be84d`): `composer install --no-dev`, then `npm pack
  web-component-hydration@1.0.5`. The tarball's sha512 matches
  `examples/simple/js/yarn.lock`, and it was unpacked as that directory's `node_modules`
  in place of `yarn install`. The copy was served with `php -S 127.0.0.1:5077 -t
  examples/simple` (the `composer start` command, bound to loopback) and opened with
  Playwright in Chromium, Firefox and WebKit. In each engine, zero `[data-template]`
  placeholders remained, `x-list` and both `x-list-item`s had shadow roots holding their
  templates, the server-rendered item sat inside `x-list`, and the page reported no errors
  or console errors. The unmatched `foo` datum became a `<span slot="foo">` that is never
  projected. The page also shows the `mb_convert_encoding` deprecation, because `php -S`
  displays errors.
- Package metadata over plain HTTPS reads (no installs): npm reports
  `web-component-hydration` 1.0.5 as latest (published 2019-08-06) and
  `jsx-for-web-components` 1.2.1 as latest (published 2018-12-29); Packagist
  reports `slogsdon/web-component-ssr` with exactly one version, `dev-master`, at
  source reference `00be84d` — the same commit quoted on the page. This is why the
  site states that there is no tagged PHP release.
- Commit dates and tag history via `git log`, which is where the "code from
  2018–2019, docs-only commit 2026-05-08" framing comes from.
- Absence of out-of-namespace root-relative URLs, and the preview/production
  indexing difference.

## Not verified here (Unverified)

Apart from the demo run above, nothing client-side was executed. Anything below is
quoted from the source and marked as such on the page.

- `cd examples/simple && yarn install` and `cd ../.. && composer start` — from
  `php-web-component-ssr` `README.md` (Running the demo). Not run literally: the demo
  run above used the npm tarball that matches the lockfile instead of `yarn install`,
  and served it with `php -S` on loopback.
- `composer test` (Psalm) — from `php-web-component-ssr` `composer.json`. Not run.
- The PHP 7.1+ requirement — read from `README.md`. Only PHP 8.5.11 was exercised
  here, and only through the two render functions, not through the built-in server.
- Hydration beyond the demo page: other markup and other initial-data shapes, and
  the README's stand-alone snippets — read from `helpers.js` and
  `custom-html-element.js` and quoted from the README's examples. Only the demo page
  was run.
- `hydrate()`'s early-return ordering requirement — read from `helpers.js`; the
  explicit `onReady` + `await register()` sequencing quoted in the demo comes from
  `php-web-component-ssr` `examples/simple/js/main.js`. Not run.
- `yarn add jsx-for-web-components`, `yarn add --dev typescript rollup
  rollup-plugin-node-resolve rollup-plugin-typescript2`, and
  `rollup -c rollup.config.js` — from `jsx-for-web-components` `README.md`. Not
  run.
- The JSX example's expected browser result ("you should be able to load your HTML
  in a browser to see your work") — from the same README. Not reproduced.
- The JSX repository's Jest and TSLint suites — from `package.json`. Not run.
- The label "WIP" is the README's own word, kept on the page and in the page title.

## Seed note

`website/composer.json` (seeded with the pilot skeleton, and not editable under
this task's rules) still carries the description `"Microsite for Deferred Skill
Catalog"`. The package name and build script are correct; only that description
string is stale. It affects nothing the build emits.

## Deployment

Not performed from this repository: no `cf deploy`, no `cf previews deploy`, no
Cloudflare API call. This repository only produces the artifact and the Worker
configuration. The hub owns the hostname (`projects.logsdon.io`), the sitemap
index, and the slashless redirect; this Worker claims only
`projects.logsdon.io/web-component-tools/*`.
