<?php
declare(strict_types=1);

return [
    'modifiers' => [
        \sidigi\LaravelApiImport\Modifiers\Arr\FieldsArrModifier::class,
        \sidigi\LaravelApiImport\Modifiers\Arr\TypeConversationArrModifier::class,
        \sidigi\LaravelApiImport\Modifiers\Arr\FieldConversionArrModifier::class,
    ],

    'entities' => [

    ]
];