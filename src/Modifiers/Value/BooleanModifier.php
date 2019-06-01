<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport\Modifiers\Value;

class BooleanModifier extends TypeModifier
{
    protected $types = [
        'true' => true,
        'false' => false,
    ];
}