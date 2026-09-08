<?php

namespace Dezodev\FilamentBlokField\Support;

class BlokAssets
{
    /**
     * FilamentAsset handles to load for a BlokEditor field: the package's
     * own bundle plus one module script per custom block declared in
     * `config/blok.php`.
     *
     * @return array<string>
     */
    public static function scriptHandles(): array
    {
        return [
            'filament-blok-field-scripts',
            ...collect(config('blok.blocks', []))
                ->map(fn (string $blockClass) => $blockClass::type().'-blok-block')
                ->all(),
        ];
    }
}
