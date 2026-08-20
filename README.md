# productredirect — 301 Redirect Suggestions for Discontinued Product Versions

A PrestaShop admin module for any catalog where the same product gets a
new version every year/season and the old one is discontinued — wine
vintages, model years, seasonal collections, anything named
`<Product Name> <Year>`. It scans your catalog for products that have
been disabled or run out of stock **and** have a newer version of the
same product that's active and in stock, and suggests the `Redirect 301`
rule you'd add to `.htaccess` to send customers straight to the current
version instead of a dead product page.

It also does the reverse: if a discontinued product comes back (re-enabled,
restocked), it flags any existing redirect for that product as **obsolete**,
since a static `.htaccess` rule has no way to know the product is sellable
again and would otherwise keep bouncing customers to the wrong product
forever.

The module is **read-only by design**. It never writes to your database
(beyond registering its own admin tab) and never touches `.htaccess`. It
recomputes suggestions live on every page load, lets you pick which ones
to accept via checkboxes, and generates the exact text for you to copy
into `.htaccess` yourself.

## Why

`.htaccess` redirects for a rotating catalog (new version every year,
old one discontinued) are usually maintained by hand, which means they
drift: new discontinuations don't get a redirect until someone remembers
to add one, and old redirects don't get removed when a product comes back
in stock. This module turns that from a manual audit into a page you can
check whenever you like.

## Features

- **Confirmados** — old product's name has an earlier year than the
  active replacement's name (`Trail Runner 2021` → `Trail Runner 2023`).
  High confidence.
- **A revisar** — the old product's name has no year in it at all (a
  non-vintage listing, an accessory, a renamed/repurposed product row).
  Shown separately, unchecked by default, for manual review.
- **Obsoletos** — an existing `Redirect 301` in `.htaccess` whose source
  product is active and in stock again. Shown with the exact line to
  remove.
- Already-covered products (already present as a `Redirect 301` source in
  your live `.htaccess`) are automatically excluded from all three lists.
- Per-row checkboxes + a "Generar documento" button that builds the
  copy-paste text client-side from whatever's checked — nothing is sent
  back to the server.

## Requirements

- PrestaShop 1.6 or 1.7 (tested on 1.7.5.2). Uses the classic
  (non-Symfony) `AdminController` API — no Symfony service wiring needed.
- PHP 7.1+ (uses scalar/nullable return type declarations). Tested on
  PHP 7.3 and 7.4.
- No third-party PHP dependencies. No JS dependencies (vanilla JS, no
  jQuery required, though PrestaShop's admin theme loads it anyway).
- Your `.htaccess` must use plain `Redirect 301 /path url` directives
  (mod_alias), not `RewriteRule`-based redirects — that's what the
  parser looks for.

## Install

Drop the `productredirect/` folder into your PrestaShop `modules/`
directory, then either install it from **Módulos → Módulos** in the
admin, or via CLI:

```bash
php -r "
require_once '/path/to/your/shop/config/config.inc.php';
require_once _PS_MODULE_DIR_ . 'productredirect/productredirect.php';
(new ProductRedirect())->install();
"
```

It adds one tab under the Catálogo menu — no configuration screen (no
`getContent()`), since the module has nothing to configure. If your
store's Catálogo parent tab isn't named `AdminParentCatalog` or
`AdminCatalog`, the tab installs at the top level of the admin menu
instead of failing.

## Usage

Open **Catálogo → Product Redirects 301**. Review the three sections,
adjust the checkboxes, click **Generar documento** (or, for the
Obsoletos section, **Generar lista para eliminar**), copy the output,
and paste it into your `.htaccess` — additions go anywhere before the
`mod_rewrite` block that dispatches to `index.php`; the obsolete lines
need to be found and deleted, not appended.

## Development / tests

There's no PHPUnit dependency — `tests/*Test.php` are plain PHP scripts
with a tiny inline assertion helper, runnable directly:

```bash
php tests/VintageMatcherTest.php
php tests/HtaccessParserTest.php
php tests/ProductUrlBuilderTest.php
```

`tests/manual_install_test.php` and `tests/manual_controller_test.php`
are functional checks that need a real PrestaShop bootstrap (they
`require` your `config/config.inc.php`), for things that can't be unit
tested in isolation — Tab registration, and the live DB query/matching
pipeline. **`manual_install_test.php` always ends with the module
uninstalled** (its last assertion is `uninstall()`) — if you run it
against a real live install, reinstall afterwards with the one-liner in
[Install](#install).

## Notes from building this

A few non-obvious things that cost real debugging time, in case they
save someone else the trouble:

- **`AdminController::setTemplate()` does not understand the
  `module:modulename/...` resource prefix.** That's a front-office-only
  Smarty resource type. In an admin controller, `createTemplate()`
  always resolves the template name by string-concatenating it onto the
  *admin theme's own* template directory — passing `'module:...'`
  produces a broken, unresolvable path and PrestaShop's own error
  handling never even sees it (the `SmartyException` is thrown deep
  inside `AdminController::display()`, well after `initContent()` has
  already returned successfully). Override `createTemplate()` to resolve
  against your module's own `views/templates/admin/` directory instead,
  and pass `setTemplate()` a bare filename.
- **Match your actual PHP-FPM pool version, not whatever `php` resolves
  to on the box.** A server can have multiple PHP versions installed
  side by side; check the vhost's PHP-FPM pool config (or a live
  response's `X-Powered-By` header) before assuming your CLI testing
  matches production.
- **Every file the module creates should be owned by the same
  user/group PHP-FPM (and your own FTP/file-manager access) runs as** —
  not whichever account you happened to create the files with. A
  mismatch doesn't always break reads (permission bits can look fine)
  but will break writes for anyone else who needs to touch the same
  files later.
- The Catálogo parent tab's `class_name` isn't guaranteed to be
  PrestaShop's documented default (`AdminParentCatalog`) — some stores
  have it as plain `AdminCatalog`. `install()` tries both.

## License

MIT.
