<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use sidigi\LaravelApiImport\Modifiers\Arr\ArrModifierInterface;
use sidigi\LaravelApiImport\Modifiers\Value\ValueModifierInterface;

class ImportEntity extends Import
{
    protected $url;

    /** @var Modifier  */
    protected $modifier;

    public function __construct()
    {
        parent::__construct();

        /*$this->requestCallback = function(Client $client){
            return $this->request($client);
        };*/

        if ($url = $this->url()){
            $this->pager->setUrl($url);
        }

        $this->modifier = new Modifier();
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

    protected function modifyItem(array $item): array
    {
        foreach ($this->modifier->availableModifiers($this,ArrModifierInterface::class) as $modifier){
            $item = (new $modifier)->modify($item);

            foreach ($item as $key => $value){
                $item[$key] = $this->modifyValue($value);
            }
        }

        return $item;
    }

    protected function modifyValue($item)
    {
        foreach ($this->modifier->availableModifiers($this, ValueModifierInterface::class) as $modifier){
            $item = (new $modifier)->modify($item);
        }

        return $item;
    }
}
