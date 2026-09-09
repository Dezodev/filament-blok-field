# Filament Blok Field

[Blok](https://blokeditor.com) block editor as a Filament v5 form field, with custom nested
blocks declared once via config rather than per-field.

## Install

```bash
composer require dezodev/filament-blok-field
php artisan vendor:publish --tag=filament-blok-field-config
```

## Usage

```php
use Dezodev\FilamentBlokField\Forms\Components\BlokEditor;

BlokEditor::make('content')
```

The field stores Blok's `OutputData` JSON as-is on the model attribute (cast it to `array`).

## Declaring a custom block

1. **PHP side** - extend `Dezodev\FilamentBlokField\Blocks\BlokBlock` in the host app and list it
   in `config/blok.php`:

   ```php
   // app/Blok/Blocks/SectionBlock.php
   class SectionBlock extends BlokBlock
   {
       public static function type(): string { return 'section'; }
       public static function script(): string { return Vite::asset('resources/js/blok-blocks/section.js'); }
       public function toHtml(array $data): string { return view('blok.section', $data)->render(); }
   }
   ```

   ```php
   // config/blok.php
   'blocks' => [
       \App\Blok\Blocks\SectionBlock::class,
   ],
   ```

2. **JS side** - build a `BlockTool` module with the host app's own bundler and have it
   self-register under the same type key, following the template in
   `resources/js/blocks/example.block.js`:

   ```js
   window.FilamentBlokFieldBlocks ??= {}
   window.FilamentBlokFieldBlocks.section = SectionBlockTool
   ```

The package loads every declared block's script as an ES module alongside its own bundle
whenever a `BlokEditor` field is rendered, so no `->blocks([...])` call is needed on the field
itself.

## Development

```bash
composer install
npm install
npm run build   # or: npm run dev (watch mode)
composer test
composer format
```

## Status

Early scaffold - the field, provider and custom-block contract are wired end to end. See
[_docs/TODO.md](_docs/TODO.md) for what's left open.

## License

MIT.
