<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use sidigi\LaravelApiImport\Exceptions\InvalidFormatterClassException;
use sidigi\LaravelApiImport\Modifiers\ModifierInterface;

class ImportEntity extends Import
{
    protected $url;
    protected $modifiers = [];

    public function __construct()
    {
        parent::__construct();

        $this->modifiers = $this->modifiers();

        /*$this->requestCallback = function(Client $client){
            return $this->request($client);
        };*/

        if ($url = $this->url()){
            $this->pager->setUrl($url);
        }
    }

    public function fire(): void
    {
        $this->pageRegister();
        $this->eachRegister();

        parent::fire();
    }

    protected function pageRegister(): void
    {
        $this->page(function(){
            $this->eachPage($this->getResponse());
        });
    }

    protected function eachRegister(): void
    {
        $this->each(function ($item){
            $item = $this->modifyItem($item);
            $this->eachItem($item);
        });
    }

    protected function url(): string
    {
        return $this->url;
    }

    protected function request(Client $client): Response
    {
        return $client->get($this->url());
    }

    protected function eachItem(array $item)
    {

    }

    protected function eachPage(Response $response): void
    {

    }

    /**
     * @param array $item
     * @return array
     * @throws InvalidFormatterClassException
     * @throws BindingResolutionException
     */
    protected function modifyItem(array $item): array
    {
        foreach ($this->modifiers as $modifier){
            /** @var ModifierInterface $formatterObj */
            $formatterObj = app()->make($modifier);

            if (! $formatterObj instanceof ModifierInterface){
                throw new InvalidFormatterClassException('Formatter class must be of the type ' . ModifierInterface::class . ', '.get_class($formatterObj).' given');
            }

            $item = $formatterObj->modify($item);
        }

        return $item;
    }

    protected function modifiers()
    {
        if ($this->modifiers) {
            return $this->modifiers;
        }

        if ($entityModifiers = config('laravel-api-import.entities.' . static::class . '.modifiers') ?? []) {
            return $entityModifiers;
        }

        if ($commonModifiers = config('laravel-api-import.modifiers') ?? []) {
            return $entityModifiers;
        }
    }
}
