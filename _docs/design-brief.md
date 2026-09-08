# Brief : package Filament pour l'éditeur Blok

## Contexte

Ce document résume les recherches et décisions prises dans une conversation Claude Code sur le
projet YEMV (`yemv.dev.dez.ovh`), en vue de démarrer un **nouveau projet séparé** : un package
Composer/Filament réutilisable entre plusieurs sites, qui intègre l'éditeur de blocs
[Blok](https://blokeditor.com) (fork actif d'Editor.js, par CodeX/JackUait,
[github.com/JackUait/blok](https://github.com/JackUait/blok), Apache 2.0) dans un champ de
formulaire Filament v5.

Objectif d'origine : un éditeur "façon Gutenberg" pour la page d'accueil de YEMV, avec un bloc
"Section" (fond de couleur configurable) pouvant contenir d'autres blocs/contenus — donc du vrai
nesting, avec un aperçu WYSIWYG dans l'éditeur (contrairement au `Builder` natif Filament, qui
édite via des modales de formulaire sans rendu visuel).

## Décisions actées

- **Éditeur retenu : Blok**, en `@bloklabs/core` (JS/TS vanilla, agnostique de tout framework —
  pas besoin des adaptateurs React/Vue/Angular puisque Filament est Livewire/Alpine).
- **Package isolé**, dépôt git séparé de YEMV, pensé pour être requis en dépendance Composer
  par plusieurs sites (comme Media Library Pro l'est déjà dans YEMV).
- **Extensibilité par config globale**, pas par déclaration au niveau du champ : l'app hôte liste
  ses blocs custom (ex. "Section") dans un fichier de config publiable (`config/blok.php`), pas
  via un `->blocks([...])` répété à chaque instanciation du champ de formulaire. Décision
  explicite de l'utilisateur (préfère ne pas redéclarer la liste à chaque usage).

## Pourquoi Blok plutôt que les autres pistes explorées

Comparé et éliminé, dans l'ordre :

1. **`Builder` natif Filament** (flat ou imbriqué) : pas d'aperçu visuel (édition en modale de
   formulaire) — objection principale de l'utilisateur. `Builder` imbriqué dans `Builder` a des
   bugs connus côté Livewire (état des tableaux imbriqués), non confirmés corrigés en v5.
2. **Plugins Filament existants** (`thiktak/filament-nested-builder-form`,
   `klisica/filament-builder-blocks`, `martin-ro/filament-page-blocks`, bridges GrapesJS
   `dotswan`/`philippnies`/`tiepdev9x`/`vati`/`ekremogul`) : tous incompatibles avec Filament v5
   (testé via `composer require --dry-run` — exigent Filament ^3.0, ou aucune version stable
   publiée).
3. **`safi/filament-atelier`, `z3d0x/filament-fabricator`** : compatibles Filament v5, mais
   possèdent leur propre modèle de page/routing — entre en conflit avec le modèle `Page` déjà
   construit dans YEMV. Nesting non documenté/non garanti chez Atelier.
4. **TipTap "maison"** (étendre le `RichEditor` Filament avec de vrais nœuds ProseMirror
   imbriqués) : Filament expose un vrai point d'extension (`getTipTapJsExtensions()`), et
   ProseMirror sait faire du nesting — mais personne n'a documenté/livré de bloc conteneur
   imbriqué pour le `RichEditor` de Filament spécifiquement. 100 % à défricher.
5. **GrapesJS** (intégration maison, aucun bridge Filament v5 existant) : mature (10+ ans), vrai
   nesting, mais paradigme différent (constructeur de page entier avec canvas iframe, style
   manager) — plus lourd que nécessaire pour de l'édition de contenu par blocs.
6. **Blok** (retenu) : JSON de blocs natif (pas de HTML à parser), nesting générique confirmé par
   le code source (pas juste la doc), vanilla JS (pas de pivot React), open source. Contrepartie :
   projet jeune (fork de novembre 2025), aucune intégration Filament existante — tout le pont est
   à construire.

## Ce qu'on sait de l'API de Blok (vérifié dans le code source, pas seulement la doc)

Dépôt : `github.com/JackUait/blok`. Fichiers clés déjà lus :
- `src/tools/nested-blocks.ts` (fonction `mountChildBlocks`)
- `src/tools/column/index.ts` (implémentation complète d'un bloc conteneur)
- `types/block-tunes/block-tune.d.ts`, `types/tools/tool-settings.d.ts`,
  `types/tools/block-tool.d.ts`, `types/tools/menu-config.d.ts`,
  `types/utils/popover/popover-item.d.ts`

### Modèle de données (`OutputData`)

```ts
interface OutputData {
  version?: string;
  time?: number;
  blocks: OutputBlockData[];
}

interface OutputBlockData {
  id: string;
  type: string;
  data: Record<string, unknown>;
  parent?: string;       // id du bloc parent
  content?: string[];    // ids des blocs enfants, dans l'ordre
  tunes?: Record<string, unknown>;
  indent?: number;
}
```

Structure **plate** (tableau unique, DFS pré-ordre) — la hiérarchie vit dans les références
`parent`/`content` par id, pas dans des objets imbriqués. Exemple 2 colonnes dans
`/docs/output-data/`.

### Créer un bloc custom (`BlockTool`)

Méthodes principales : `static get toolbox()`, constructeur `{ data, api, block, readOnly,
origin }`, `render(): HTMLElement`, `save(el): BlockToolData`, `validate?()`,
`rendered?()`/`updated?()`/`removed?()`/`moved?()`, `setReadOnly?()`. CSS totalement libre
(classes BEM du site utilisables directement).

### Faire un bloc conteneur (nesting)

Pattern utilisé par 4 blocs natifs (`toggle`, `header`, `column`, `callout`) — **pas un cas
spécial de Colonnes**, une vraie brique réutilisable :
1. Dans `render()`, créer un élément enfant marqué `data-blok-nested-blocks` (constante
   `DATA_ATTR.nestedBlocks`) — c'est le "slot" des enfants.
2. Dans `rendered()`, appeler `mountChildBlocks(childContainer, api.blocks.getChildren(blockId))`
   (fonction exportée par `src/tools/nested-blocks.ts`) pour réconcilier le DOM avec le modèle.
3. Ajouter des enfants via `api.blocks.insertInsideParent(blockId, index)`.
4. `save()` ne renvoie QUE les données propres au bloc conteneur (ex. `{ widthRatio }`) — les
   enfants restent des entrées séparées du tableau `blocks`, liées par `parent`/`content`.

Propriétés statiques utiles pour un conteneur :
- `childTools?: { allow?: string[]; deny?: string[] }` — restreint quels types de blocs peuvent
  être enfants directs (ex. interdire une Section dans une Section).
- `ownsChildren?: boolean` — le conteneur gère ses enfants comme sa propre mécanique interne (ex.
  cellules d'un tableau), pas du contenu libre de l'utilisateur.
- `keepsChildrenOnEnter?: boolean` — Entrée sur la dernière ligne vide reste dans le conteneur
  plutôt que d'en sortir (comportement Notion callout vs comportement colonne/toggle).

### Réglage par bloc (le sélecteur de "fond") : les Tunes

```ts
interface BlockTune {
  render(context?): HTMLElement | MenuConfig;
  wrap?(pluginsContent: HTMLElement): HTMLElement;  // enveloppe le rendu du bloc
  save?(): BlockTuneData;                            // libre, `unknown`
}
```

- `wrap()` est le point d'entrée pour appliquer une classe CSS (`section--surface` / `section`)
  autour du contenu déjà rendu du bloc — sans toucher à la logique du bloc lui-même.
- `render()` peut renvoyer un `MenuConfig` (liste d'items de menu). Un item avec
  `toggle: 'fond'` (string, pas boolean) se comporte comme un **groupe radio** — plusieurs items
  partageant la même clé `toggle` s'excluent mutuellement. C'est le mécanisme pour "Fond clair" /
  "Fond surélevé".
- Un tune s'attache à un bloc précis (pas globalement) via la config d'enregistrement de l'outil :
  `tunes: ['fond']` dans `ExternalToolSettings`.

### Rendu serveur

`@bloklabs/core/view` sait rendre `OutputData` → HTML, mais **côté Node uniquement** — inutilisable
tel quel depuis PHP. Le package devra fournir son propre renderer PHP (mapping type de bloc → vue
Blade), sur le modèle de `RichContentCustomBlock::toHtml()`/`toPreviewHtml()` déjà utilisé côté
Filament dans YEMV.

## Ce qui reste à concevoir dans le nouveau projet (pas encore tranché)

1. **Nom du package / vendor / namespace** — à choisir au démarrage.
2. **Pipeline JS des blocs custom** : comment un bloc PHP custom (ex. "Section" déclaré dans
   `config/blok.php` de l'app hôte) fournit-il sa classe JS `BlockTool` ? Options à évaluer :
   - Un fichier JS par bloc, enregistré via `FilamentAsset` et chargé comme module ES au runtime,
     sur le modèle de `getTipTapJsExtensions()` de Filament (déjà un point d'extension réel et
     documenté côté Filament v5) — chaque module exporterait sa classe et s'auto-enregistrerait.
   - Ou un build JS unique du package qui doit inclure tous les blocs à l'avance (moins flexible
     pour un package partagé entre plusieurs sites aux blocs différents).
3. **Contrat PHP côté package** pour déclarer un bloc custom (interface/classe abstraite
   équivalente à `RichContentCustomBlock` : type unique, référence vers son tool JS, méthode de
   rendu front en Blade).
4. **Synchronisation Livewire** : sur quel évènement pousser l'`OutputData` JSON vers la propriété
   Livewire du champ (debounce clavier vs sauvegarde explicite) ?
5. **Sanitisation HTML du rendu front** — même sujet que `HtmlSanitizerConfig` déjà étendu dans
   YEMV pour `data-phone-reveal`/`data-phone-label` : le renderer du package produira du HTML à
   restituer côté site, donc probablement sa propre étape de sanitisation ou un point
   d'extension pour l'app hôte.
6. **Tests** : Pest côté PHP ; stratégie de test JS à définir si les blocs custom deviennent
   nombreux.

## Références

- Doc : https://blokeditor.com/docs/ (pages utiles : `custom-block-tool`, `column_list`,
  `output-data`, `tools-api`, `tunes` — cette dernière n'a pas de page dédiée, seulement les
  types TypeScript).
- Code source : https://github.com/JackUait/blok (`src/tools/`, `types/`).
