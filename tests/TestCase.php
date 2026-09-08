<?php

namespace Dezodev\FilamentBlokField\Tests;

use Dezodev\FilamentBlokField\FilamentBlokFieldServiceProvider;
use Filament\Support\SupportServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            SupportServiceProvider::class,
            FilamentBlokFieldServiceProvider::class,
        ];
    }
}
