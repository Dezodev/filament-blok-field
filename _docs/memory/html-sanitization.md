---
name: html-sanitization
description: The PHP OutputData-to-HTML renderer sanitizes by default (Symfony HtmlSanitizer) and exposes a sanitizeHtmlUsing() extension point for host apps
metadata:
  type: project
---

Decision (2026-09-09): the package's own PHP renderer (block type → HTML, not yet written — see
`_docs/TODO.md`) sanitizes its output by default, using Symfony's HtmlSanitizer — the same
mechanism Filament's own `RichEditor` uses — and exposes an extension point (a
`sanitizeHtmlUsing()`-style hook) so a host app can extend the `HtmlSanitizerConfig` rather than
being stuck with the package's default.

**Why:** the user chose "default sanitization + extension point" over "fully delegated to the
host app" when asked directly. A consumer site has already needed to extend an
`HtmlSanitizerConfig` for custom attributes (`data-phone-reveal`/`data-phone-label`) — a fully
unsanitized package output would just push that same need onto every consumer site from scratch,
while a fixed non-extensible sanitizer would block that known real case.

**How to apply:** when writing the renderer, ship a safe default config out of the box (safe to
use with zero host configuration) and make it extensible/overridable at the config or field
level, following the same shape as Filament's `RichEditor::sanitizeHtmlUsing()` API rather than
inventing a new pattern.
