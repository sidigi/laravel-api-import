<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport\Modifiers;

use Illuminate\Container\Container;

class FieldsModifier implements ModifierInterface
{
    protected $only;
    protected $except;
    protected $replace;

    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function only(): ?array
    {
        return $this->only;
    }

    public function except(): ?array
    {
        return $this->except;
    }

    public function replace(): ?array
    {
        return $this->replace;
    }

    public function modify(array $data): array
    {

        if ($only = $this->only()){
            $data = $this->container->make(OnlyModifier::class, ['keys' => $only])->modify($data);
        }

        if ($except = $this->except()){
            $data = $this->container->make(ExceptModifier::class, ['keys' => $except])->modify($data);
        }

        if ($replace = $this->replace()){
            $data = $this->container->make(ReplaceModifier::class, ['keys' => $replace])->modify($data);
        }

        return $data;
    }
}
