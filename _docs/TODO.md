# TODO

## Implémentation

- ~~Installer `@bloklabs/core` (`npm install`) et lancer un premier `npm run build` réel~~ — fait :
  le paquet réel est en `^1.13.0` (pas de version `0.1.0`, c'était un pin de scaffold jamais
  publié), `npm run build` produit un vrai bundle (`resources/dist/blok-field.js`, ~5.2 Mo non
  découpé — à surveiller si la taille devient un problème).
- ~~Vérifier l'API réelle du constructeur~~ — fait : la classe exportée s'appelle `Blok` (pas
  `Editor`, alias `EditorJS` fourni pour la compat Editor.js), `resources/js/blok-field.js` importe
  et instancie `new Blok({ holder, data, tools, onSave })` désormais. `onSave(data, api)` renvoie
  directement l'`OutputData` complet (pas besoin de rappeler `api.saver.save()` comme avec
  `onChange`), et sa présence seule arme la sérialisation debouncée en interne — c'est le hook à
  utiliser pour la sync Livewire, `onChange` ne sert que si on veut les événements de mutation bruts.
  `resources/js/blocks/example.block.js` (constructeur `{data, api, block}`, `render()`,
  `save(el)`, `static toolbox`) correspond déjà au contrat réel `BlockTool` — aucun changement
  nécessaire côté template de bloc custom. Le sélecteur CSS `.codex-editor` dans
  `resources/css/blok-field.css` était un reliquat du pattern Editor.js (classe inexistante chez
  Blok) et a été supprimé ; pas encore vérifié si l'éditeur a besoin d'un ajustement de hauteur une
  fois testé dans un vrai panel (voir point ci-dessous).
- ~~Passer l'entangle Alpine en `.live()`~~ — fait dans
  `resources/views/forms/components/blok-editor.blade.php`, en s'appuyant sur le debounce interne
  de Blok (`onSave`) sans couche de debounce supplémentaire, à ajuster si ça s'avère trop bavard en
  usage réel.
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
