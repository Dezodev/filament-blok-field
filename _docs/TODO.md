# TODO

## À trancher

- **Déclencheur de synchronisation Livewire** : sur quel évènement pousser l'`OutputData` JSON
  vers la propriété Livewire du champ (debounce clavier vs sauvegarde explicite) ?
- **Sanitisation HTML du rendu front** : le renderer PHP (type de bloc → HTML) produira du HTML
  à restituer côté site — définir si le package porte sa propre étape de sanitisation ou expose
  un point d'extension pour l'app hôte (même sujet qu'un `HtmlSanitizerConfig` déjà étendu sur
  un site consommateur).

## Implémentation

- Installer `@bloklabs/core` (`npm install`) et lancer un premier `npm run build` réel —
  `resources/dist/blok-field.{js,css}` ne contiennent pour l'instant que des placeholders vides.
- Vérifier l'API réelle du constructeur `new Editor({ holder, data, tools, onChange })` de Blok
  dans le code source : `resources/js/blok-field.js` calque pour l'instant le pattern Editor.js
  standard, non confirmé contre le code de Blok.
- Tester le rendu du champ `BlokEditor` dans un vrai panel Filament (Livewire) — seuls le
  contrat `BlokBlock` et la config par défaut sont couverts par les tests actuels.
- Écrire le renderer PHP `OutputData` → HTML (mapping type de bloc → vue Blade), sur le modèle
  de `RichContentCustomBlock::toHtml()`/`toPreviewHtml()` déjà utilisé côté Filament sur un site
  consommateur.
- Définir une stratégie de test JS une fois les blocs custom plus nombreux.

## Plus tard

- CI (au minimum `composer test` + `vendor/bin/pint --test` sur push/PR).
- Premier tag de version (`v0.1.0`) une fois le pont validé sur un site consommateur.
- Implémenter le premier bloc réel sur un site consommateur : "Section" (fond de couleur
  configurable via une Tune, voir `_docs/design-brief.md`) — sert aussi de validation
  end-to-end du package.
