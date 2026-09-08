<?php

namespace Dezodev\FilamentBlokField\Forms\Components;

use Filament\Forms\Components\Field;

class BlokEditor extends Field
{
    protected string $view = 'filament-blok-field::forms.components.blok-editor';

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(['time' => null, 'blocks' => []]);
    }
}
