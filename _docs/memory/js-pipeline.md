---
name: js-pipeline
description: Custom blocks ship as one ES module per block, self-registered and loaded via FilamentAsset, not bundled into the package's own build
metadata:
  type: project
---

Decision (2026-09-08, at scaffold time): a custom block's JS `BlockTool` is built and shipped by
the **host app**, not by this package. The host app's `BlokBlock::script()` returns the built
module URL, and the package loads it via `FilamentAsset::register(..., )->module()` alongside
its own bundle (see [[editor-choice]] for the block-selection context, and
`src/FilamentBlokFieldServiceProvider.php` / `src/Support/BlokAssets.php` for the wiring).

The module self-registers on `window.FilamentBlokFieldBlocks[type]` (see
`resources/js/blocks/example.block.js`) instead of the field passing an explicit `tools: {...}`
list — consistent with the earlier decision that blocks are declared once in `config/blok.php`,
never repeated at the field call site.

**Why:** the alternative (a single JS bundle built by this package, including every custom
block) would force one shared package build to know about every site's custom blocks ahead of
time — unworkable for a package meant to be required by several independent sites with
different blocks.

**How to apply:** if a future site's custom block needs something from this package's bundle
(e.g. a shared base class, a UI helper), that's a signal to add an exported utility from
`resources/js/blok-field.js` rather than pulling `@bloklabs/core` into the host build a second
time — check what's already exported before adding a new shared dependency.
