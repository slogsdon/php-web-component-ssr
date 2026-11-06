/**
 * Build one project microsite into dist/<slug>/.
 *
 * Generic on purpose: slug, project name, origin and content revision date come
 * from website/site.json, and every URL the pages emit is mounted under
 * /<slug>/. Copy this file to another project unchanged and replace site.json
 * (plus the Worker identity in cloudflare.config.ts).
 *
 * Node built-ins only. The PHP/flat-file generator renders the pages; this
 * script validates site.json, drives the generator, mounts the copied CSS at the
 * project prefix, writes the indexing files, and asserts the artifact contract.
 */
import { spawnSync } from "node:child_process";
import {
  existsSync,
  mkdirSync,
  readFileSync,
  readdirSync,
  rmSync,
  writeFileSync,
} from "node:fs";
import { join, relative } from "node:path";
import { fileURLToPath } from "node:url";

type Site = {
  slug: string;
  name: string;
  origin: string;
  description: string;
  lastmod: string;
  source: string;
};

const root = fileURLToPath(new URL("..", import.meta.url));
const site = readSite(join(root, "site.json"));
const noindex = process.env.SITE_NOINDEX ?? "1";
if (!["0", "1"].includes(noindex)) {
  throw new Error("SITE_NOINDEX must be 0 or 1");
}

const dist = join(root, "dist");
const output = join(dist, site.slug);
const base = `/${site.slug}`;
const pages = discoveredPages(join(root, "pages"));

rmSync(dist, { recursive: true, force: true });
mkdirSync(output, { recursive: true });

// The generator reads pages/ and copies public/ into the destination, so the
// project's assets land at dist/<slug>/assets/ from the start.
const build = spawnSync(
  "composer",
  ["build", "--", relative(root, output)],
  {
    cwd: root,
    env: {
      ...process.env,
      SITE_NOINDEX: noindex,
      SITE_SLUG: site.slug,
      SITE_NAME: site.name,
      SITE_ORIGIN: site.origin,
      SITE_SOURCE: site.source,
    },
    stdio: "inherit",
  },
);
if (build.status !== 0) throw new Error("PHP static build failed");

// Mount the copied CSS at the project prefix. The hub serves these files from
// /assets/; a microsite owns only /<slug>/, so a bare /assets/ URL would leave
// the namespace. Tokens also lose the Google Fonts import: fonts are local.
for (const name of ["tokens.css", "site.css"]) {
  const file = join(output, "assets", name);
  const css = readFileSync(file, "utf8")
    .replace(/^@import[^\n]+\n?/m, "")
    .replaceAll('url("/assets/', `url("${base}/assets/`)
    .replaceAll("url(/assets/", `url(${base}/assets/`);
  if (css.includes("@import") || css.includes("url(\"/assets/")) {
    throw new Error(`${name} still points outside ${base}/`);
  }
  writeFileSync(file, css);
}

// Sitemap: canonical indexable pages only. lastmod is the source revision date
// recorded in site.json, never the build date.
const sitemap = [
  '<?xml version="1.0" encoding="UTF-8"?>',
  '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
  ...pages.map(
    (path) =>
      `<url><loc>${site.origin}${base}${path}</loc><lastmod>${site.lastmod}</lastmod></url>`,
  ),
  "</urlset>",
  "",
].join("\n");
writeFileSync(join(output, "sitemap.xml"), sitemap);

if (noindex === "1") {
  writeFileSync(join(dist, "_headers"), "/*\n  X-Robots-Tag: noindex\n");
}

assertArtifacts();

console.log(
  `Microsite build: ${site.slug} — ${pages.length} page(s), ${noindex === "1" ? "noindex preview" : "indexable production"}`,
);

function readSite(path: string): Site {
  const raw: unknown = JSON.parse(readFileSync(path, "utf8"));
  const site = raw as Partial<Site>;
  if (typeof site !== "object" || site === null) throw new Error("site.json must be an object");
  for (const key of ["slug", "name", "origin", "description", "lastmod", "source"] as const) {
    if (typeof site[key] !== "string" || site[key] === "")
      throw new Error(`site.json: ${key} must be a non-empty string`);
  }
  const { slug, origin, lastmod } = site as Site;
  if (!/^[a-z0-9]+(-[a-z0-9]+)*$/.test(slug))
    throw new Error(`site.json: slug "${slug}" must be lowercase kebab-case`);
  if (!/^https:\/\/[a-z0-9.-]+$/.test(origin))
    throw new Error(`site.json: origin "${origin}" must be an https origin with no path`);
  if (!/^\d{4}-\d{2}-\d{2}$/.test(lastmod) || Number.isNaN(Date.parse(lastmod)))
    throw new Error(`site.json: lastmod "${lastmod}" must be YYYY-MM-DD`);
  if (Date.parse(lastmod) > Date.now())
    throw new Error("site.json: lastmod is in the future; use the source revision date");
  return site as Site;
}

/** Canonical paths from pages/: index.php -> "/", a/b.php -> "/a/b/". */
function discoveredPages(directory: string): string[] {
  return readdirSync(directory, { recursive: true, encoding: "utf8" })
    .filter((name) => name.endsWith(".php"))
    .map((name) => name.split("\\").join("/"))
    .map((name) => name.slice(0, -".php".length))
    .filter((name) => name !== "404.html")
    .map((name) => (name === "index" ? "/" : `/${name}/`))
    .sort((a, b) => (a === "/" ? -1 : b === "/" ? 1 : a.localeCompare(b)));
}

function assertArtifacts(): void {
  const required = [
    join(output, "index.html"),
    join(output, "404.html"),
    join(output, "sitemap.xml"),
    join(output, "assets", "site.css"),
    join(output, "assets", "tokens.css"),
    join(output, "assets", "fonts", "fraunces-var.woff2"),
    join(output, "assets", "fonts", "LICENSES.txt"),
  ];
  for (const file of required) {
    if (!existsSync(file) || readFileSync(file).length === 0)
      throw new Error(`Missing or empty artifact: ${relative(root, file)}`);
  }
  for (const forbidden of ["_redirects", "robots.txt", "sitemap.xml", "index.html", "404.html"]) {
    if (existsSync(join(dist, forbidden)))
      throw new Error(`dist/${forbidden} must not exist: the hub owns it`);
  }
  const atRoot = readdirSync(dist).sort();
  const allowed = noindex === "1" ? [site.slug, "_headers"].sort() : [site.slug];
  if (atRoot.join(",") !== allowed.join(","))
    throw new Error(`Unexpected dist/ root entries: ${atRoot.join(", ")}`);

  // Plates escapes the title (htmlspecialchars, ENT_QUOTES), so compare like with like.
  const escapeHtml = (text: string) =>
    text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
  const titles = new Set<string>();
  const htmlFiles = walk(output).filter((file) => file.endsWith(".html"));
  if (htmlFiles.length !== pages.length + 1)
    throw new Error(`Expected ${pages.length + 1} HTML files, found ${htmlFiles.length}`);
  for (const file of htmlFiles) {
    const html = readFileSync(file, "utf8");
    const name = relative(output, file);
    const notFound = name === "404.html";
    const h1 = html.match(/<h1\b/g) ?? [];
    if (h1.length !== 1) throw new Error(`${name}: expected exactly one <h1>, found ${h1.length}`);
    const title = html.match(/<title>([^<]+)<\/title>/)?.[1];
    if (!title) throw new Error(`${name}: missing <title>`);
    if (!notFound && !title.includes(escapeHtml(site.name)))
      throw new Error(`${name}: <title> must contain the project name`);
    if (titles.has(title)) throw new Error(`Duplicate <title>: ${title}`);
    titles.add(title);
    if (!/<meta name="description" content="[^"]+">/.test(html))
      throw new Error(`${name}: missing meta description`);
    if (!html.includes('class="skip-link"')) throw new Error(`${name}: missing skip link`);
    if (!html.includes('<main id="main-content"')) throw new Error(`${name}: missing main landmark`);
    if (!/<nav aria-label="Primary">/.test(html)) throw new Error(`${name}: missing primary nav`);
    const wantsNoindex = notFound || noindex === "1";
    if (html.includes('<meta name="robots" content="noindex">') !== wantsNoindex)
      throw new Error(`${name}: wrong robots policy`);
    if (notFound) {
      if (html.includes('rel="canonical"')) throw new Error(`${name}: 404 must not canonicalize`);
    } else {
      const path = name === "index.html" ? "/" : `/${name.replace(/index\.html$/, "")}`;
      const canonical = `${site.origin}${base}${path}`;
      if (!html.includes(`<link rel="canonical" href="${canonical}">`))
        throw new Error(`${name}: wrong canonical, expected ${canonical}`);
      if (!html.includes(`<meta property="og:url" content="${canonical}">`))
        throw new Error(`${name}: wrong og:url, expected ${canonical}`);
    }
    if (!html.includes("https://projects.logsdon.io/")) {
      throw new Error(`${name}: footer must attribute the project collection`);
    }
    if (!html.includes("https://shane.logsdon.io/")) {
      throw new Error(`${name}: footer must attribute shane.logsdon.io`);
    }
    for (const match of html.matchAll(/(?:href|src)="(\/[^"]*)"/g)) {
      if (match[1] !== base && !match[1].startsWith(`${base}/`))
        throw new Error(`${name}: root-relative URL escapes ${base}/: ${match[1]}`);
    }
    for (const src of html.matchAll(/(?:href|src)="([^"#]+)"/g)) {
      const target = src[1];
      if (!target.startsWith("/")) continue;
      const candidate = join(output, target.slice(base.length));
      const isDirectory = target.endsWith("/");
      const file = isDirectory ? join(candidate, "index.html") : candidate;
      if (!existsSync(file)) throw new Error(`${name}: broken local link ${target}`);
    }
  }

  const sitemapXml = readFileSync(join(output, "sitemap.xml"), "utf8");
  const locs = [...sitemapXml.matchAll(/<loc>([^<]+)<\/loc>/g)].map((m) => m[1]);
  const expected = pages.map((path) => `${site.origin}${base}${path}`);
  if (locs.join(",") !== expected.join(","))
    throw new Error(`Sitemap mismatch: ${locs.join(", ")}`);
  if (!sitemapXml.includes(`<lastmod>${site.lastmod}</lastmod>`))
    throw new Error("Sitemap is missing the source revision date");
}

function walk(directory: string): string[] {
  return readdirSync(directory, { withFileTypes: true }).flatMap((entry) =>
    entry.isDirectory() ? walk(join(directory, entry.name)) : [join(directory, entry.name)],
  );
}
