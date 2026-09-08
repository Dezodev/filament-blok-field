@php
    use Dezodev\FilamentBlokField\Support\BlokAssets;
    use Filament\Support\Facades\FilamentAsset;

    $statePath = $getStatePath();
    $scriptSources = collect(BlokAssets::scriptHandles())
        ->map(fn (string $handle) => FilamentAsset::getScriptSrc($handle, package: 'dezodev/filament-blok-field'))
        ->all();
    $styleHref = FilamentAsset::getStyleHref('filament-blok-field-styles', package: 'dezodev/filament-blok-field');
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        wire:ignore
        x-load
        x-load-css="[@js($styleHref)]"
        x-load-js="[@foreach ($scriptSources as $source) @js($source), @endforeach]"
        x-data="blokEditor({ state: $wire.entangle('{{ $statePath }}') })"
        class="blok-editor"
    ></div>
</x-dynamic-component>
