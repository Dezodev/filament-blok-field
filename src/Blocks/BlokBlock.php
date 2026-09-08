<?php

namespace Dezodev\FilamentBlokField\Blocks;

/**
 * Contract for a custom Blok block declared by the host app in
 * `config/blok.php`, mirroring Filament's RichContentCustomBlock.
 */
abstract class BlokBlock
{
    /**
     * Unique block type, matching the `type` used both in Blok's
     * OutputData and in the BlockTool registered client-side under the
     * same key.
     */
    abstract public static function type(): string;

    /**
     * URL of the ES module exporting this block's client-side BlockTool
     * class (e.g. `Vite::asset('resources/js/blok-blocks/section.js')`).
     * Registered as a `module` script via FilamentAsset.
     */
    abstract public static function script(): string;

    /**
     * Render this block's data to front-end HTML.
     *
     * @param  array<string, mixed>  $data
     */
    abstract public function toHtml(array $data): string;

    /**
     * Render this block's data for the editor's read-only preview.
     * Defaults to the front-end rendering.
     *
     * @param  array<string, mixed>  $data
     */
    public function toPreviewHtml(array $data): string
    {
        return $this->toHtml($data);
    }
}
