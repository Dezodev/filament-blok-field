# TODO

## Implémentation

- Installer `@bloklabs/core` (`npm install`) et lancer un premier `npm run build` réel —
  `resources/dist/blok-field.{js,css}` ne contiennent pour l'instant que des placeholders vides.
- Vérifier l'API réelle du constructeur `new Editor({ holder, data, tools, onChange })` de Blok
  dans le code source : `resources/js/blok-field.js` calque pour l'instant le pattern Editor.js
  standard, non confirmé contre le code de Blok.
- Passer l'entangle Alpine en `.live()` (`resources/views/forms/components/blok-editor.blade.php`)
  pour pousser l'`OutputData` vers la propriété Livewire au fil de la frappe, avec un debounce —
  celui de Blok en interne sur `onChange` (~450ms) suffit peut-être, à vérifier à l'usage.
- Tester le rendu du champ `BlokEditor` dans un vrai panel Filament (Livewire) — seuls le
  contrat `BlokBlock` et la config par défaut sont couverts par les tests actuels.
- Écrire le renderer PHP `OutputData` → HTML (mapping type de bloc → vue Blade), sur le modèle
  de `RichContentCustomBlock::toHtml()`/`toPreviewHtml()` déjà utilisé côté Filament sur un site
  consommateur. Sanitiser par défaut avec Symfony HtmlSanitizer (même mécanisme que le
  `RichEditor` de Filament) et exposer un point d'extension (`sanitizeHtmlUsing()`) pour que
  l'app hôte étende la config, comme déjà fait pour `data-phone-reveal`/`data-phone-label` sur
  un site consommateur.
- Définir une stratégie de test JS une fois les blocs custom plus nombreux.

## Plus tard

- CI (au minimum `composer test` + `vendor/bin/pint --test` sur push/PR).
- Premier tag de version (`v0.1.0`) une fois le pont validé sur un site consommateur.
- Implémenter le premier bloc réel sur un site consommateur : "Section" (fond de couleur
  configurable via une Tune, voir `_docs/design-brief.md`) — sert aussi de validation
  end-to-end du package.
