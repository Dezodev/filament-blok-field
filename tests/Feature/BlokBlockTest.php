<?php

use Dezodev\FilamentBlokField\Blocks\BlokBlock;

it('lets a custom block declare its type, script and html rendering', function () {
    $block = new class extends BlokBlock
    {
        public static function type(): string
        {
            return 'section';
        }

        public static function script(): string
        {
            return '/blocks/section.js';
        }

        public function toHtml(array $data): string
        {
            return "<section>{$data['content']}</section>";
        }
    };

    expect($block::type())->toBe('section')
        ->and($block::script())->toBe('/blocks/section.js')
        ->and($block->toHtml(['content' => 'Hello']))->toBe('<section>Hello</section>')
        ->and($block->toPreviewHtml(['content' => 'Hello']))->toBe('<section>Hello</section>');
});

it('defaults the declared custom blocks to an empty array', function () {
    expect(config('blok.blocks'))->toBe([]);
});
