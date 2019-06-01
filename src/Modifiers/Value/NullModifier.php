<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport\Modifiers\Value;

class NullModifier extends TypeModifier
{
    protected $types = [
        'null' => null,
    ];
}