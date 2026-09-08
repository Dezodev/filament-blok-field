<?php

namespace Dezodev\FilamentBlokField;

use Dezodev\FilamentBlokField\Blocks\BlokBlock;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentBlokFieldServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-blok-field';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile('blok')
            ->hasViews()
            ->hasTranslations();
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Css::make('filament-blok-field-styles', __DIR__.'/../resources/dist/blok-field.css'),
            Js::make('filament-blok-field-scripts', __DIR__.'/../resources/dist/blok-field.js'),
        ], package: 'dezodev/filament-blok-field');

        FilamentAsset::register(
            $this->customBlockScripts(),
            package: 'dezodev/filament-blok-field',
        );
    }

    /**
     * @return array<Js>
     */
    protected function customBlockScripts(): array
    {
        return collect(config('blok.blocks', []))
            ->map(function (string $blockClass) {
                /** @var class-string<BlokBlock> $blockClass */
                return Js::make($blockClass::type().'-blok-block', $blockClass::script())->module();
            })
            ->all();
    }
}
