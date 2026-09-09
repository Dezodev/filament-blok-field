---
name: livewire-sync
description: Decision to sync OutputData to Livewire via a debounced .live() entangle, not on explicit save only
metadata:
  type: project
---

Decision (2026-09-09): the field pushes `OutputData` to the Livewire property on a debounced
keystroke event, not only on explicit save. Implemented: `resources/views/forms/components/blok-editor.blade.php`
entangles with `.live()`, and `resources/js/blok-field.js` sets `state` from Blok's `onSave(data, api)`
handler (fires with the full serialized `OutputData`, on the trailing edge of Blok's internal
change-batching window — no extra debounce layer added).

**Why:** matches how Filament's own `RichEditor` behaves, and avoids losing content if the user
navigates away before an explicit save. The user chose this over "explicit save only" (fewer
requests, but data-loss risk) when asked directly.

**How to apply:** if Blok's internal batching proves too chatty in practice, add an explicit
Livewire debounce modifier (`.live(debounce: '500ms')`) rather than reverting to explicit-save-only
— that fallback would reopen a decision already made. See [[js-pipeline]] for why `onSave` (not
`onChange`) is the right hook here.
