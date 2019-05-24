<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport;

interface EntityMapInterface
{
    public function only(): ?array;
    public function except(): ?array;
    public function replace(): ?array;
    public function modifyFields(array $fields): array;
}
