---
name: livewire-sync
description: Decision to sync OutputData to Livewire via a debounced .live() entangle, not on explicit save only
metadata:
  type: project
---

Decision (2026-09-09): the field pushes `OutputData` to the Livewire property on a debounced
keystroke event, not only on explicit save. Concretely: `resources/views/forms/components/blok-editor.blade.php`
should entangle with `.live()` (currently a plain deferred `$wire.entangle(...)`), relying on
Blok/Editor.js's own internal `onChange` debounce (~450ms) rather than adding a second debounce
layer, unless that turns out insufficient in practice.

**Why:** matches how Filament's own `RichEditor` behaves, and avoids losing content if the user
navigates away before an explicit save. The user chose this over "explicit save only" (fewer
requests, but data-loss risk) when asked directly.

**How to apply:** when implementing the JS/Blade wiring (see the "Implémentation" list in
`_docs/TODO.md`), wire the debounced live sync rather than a deferred one. If the internal Blok
debounce proves too chatty (e.g. nested text blocks firing `onChange` rapidly), add an explicit
Livewire debounce modifier rather than reverting to explicit-save-only — that fallback would
reopen a decision already made.
