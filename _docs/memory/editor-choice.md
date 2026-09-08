---
name: editor-choice
description: Why Blok was picked over Filament's Builder, existing Filament block plugins, Atelier/Fabricator, a custom TipTap extension, and GrapesJS
metadata:
  type: project
---

Blok (`@bloklabs/core`, fork of Editor.js by CodeX/JackUait) was chosen as the block editor
after eliminating, in order:

1. **Filament `Builder` (native)** — no visual preview, edits happen in a form modal. That was
   the user's main objection. Nested `Builder` also has known Livewire bugs around nested array
   state, not confirmed fixed in v5.
2. **Existing Filament block plugins** (`thiktak/filament-nested-builder-form`,
   `klisica/filament-builder-blocks`, `martin-ro/filament-page-blocks`, the various GrapesJS
   bridges) — all incompatible with Filament v5 when checked via `composer require --dry-run`.
3. **`safi/filament-atelier`, `z3d0x/filament-fabricator`** — Filament v5-compatible, but each
   owns its own page/routing model, which conflicts with the `Page` model already built in the
   YEMV project. Nesting not documented/guaranteed in Atelier.
4. **A custom TipTap extension on Filament's `RichEditor`** — Filament exposes a real extension
   point (`getTipTapJsExtensions()`) and ProseMirror can do nesting, but nobody has shipped a
   nested container block for Filament's `RichEditor` specifically. Would have been built from
   scratch.
5. **GrapesJS** — mature, true nesting, but a different paradigm (full page builder with an
   iframe canvas and style manager) — heavier than needed for block-based content editing.

Blok won on: native block JSON (no HTML to parse), generic nesting confirmed in source (not
just docs), vanilla JS (no React pivot needed since Filament is Livewire/Alpine), open source.
Trade-off accepted: young project (November 2025 fork), no existing Filament integration — the
bridge in this repo is built from scratch.

**How to apply:** don't re-litigate this comparison when extending the package — if a future
need seems better served by GrapesJS/Atelier/a Builder-based approach, that's a signal something
about Blok isn't working out, worth a fresh conversation rather than silently drifting back.

Full original research (Blok API details verified against source, OutputData shape, nesting
pattern via `mountChildBlocks`/`childTools`, Tunes API) is in `_docs/design-brief.md`, the
brief this project was scaffolded from.
