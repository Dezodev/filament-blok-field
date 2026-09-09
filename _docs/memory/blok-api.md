---
name: blok-api
description: Real @bloklabs/core API confirmed against installed package v1.13.0 — class name, constructor shape, onSave vs onChange
metadata:
  type: project
---

Confirmed (2026-09-09) against `node_modules/@bloklabs/core` v1.13.0 (there was never a `0.1.0` —
that was a scaffold-time placeholder version that doesn't exist on npm; the package.json dependency
is now pinned `^1.13.0`):

- The exported class is **`Blok`**, not `Editor`. `import { Blok } from '@bloklabs/core'` (a
  default export and an `EditorJS` alias also exist, kept only for Editor.js-migration compat via
  the bundled `migrate-from-editorjs` codemod — don't use those names in this package's own code).
- Constructor: `new Blok({ holder, data, tools, onSave, ... })` — `holder`, `data`, `tools` behave
  as expected from the Editor.js-like pattern the scaffold assumed.
- **Use `onSave(data, api)`, not `onChange(api, event)`**, to get the full `OutputData` on change.
  `onSave` fires with the already-serialized document on the trailing edge of Blok's internal
  change-batching window; `onChange` only gives mutation events and would require manually calling
  `api.saver.save()`. Each handler's mere *presence* arms its own pipeline (documented as
  "load-bearing" in Blok's types) — setting both would serialize twice per change batch, so only
  `onSave` is wired in `resources/js/blok-field.js`.
- The `BlockTool` contract (constructor `{data, api, block}`, `render()`, `save(el)`, static
  `toolbox`) matches what `resources/js/blocks/example.block.js` already had — no scaffold
  assumption needed correcting there.
- No `.codex-editor` class exists anywhere in Blok (grepped the whole package) — it's a full rename
  away from Editor.js's DOM classes. The package's own CSS classes are Tailwind-utility-style, not
  semantic; don't assume Editor.js class names when styling around the editor.

**Why:** the scaffold (`_docs/design-brief.md` era) guessed at Blok's API by analogy with
Editor.js before the real package was ever installed. Once installed, several guesses turned out
wrong (class name, onChange signature, CSS class), which is expected — it's a from-scratch API, not
an Editor.js fork.

**How to apply:** treat anything in this package's JS that still smells like "assumed from
Editor.js" as unverified until cross-checked against `node_modules/@bloklabs/core/types/`. If Blok
publishes a new major version, re-check `types/index.d.ts` (`export class Blok`) and
`types/configs/blok-config.d.ts` (`BlokMountOptions`/`BlokState`) before assuming this memory still
holds. See [[js-pipeline]] and [[livewire-sync]] for how this shapes the field's JS/Livewire wiring.
