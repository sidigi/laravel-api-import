<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport\Formatters;

interface FormatterInterface
{
    public function get(): array;
}