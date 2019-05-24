<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class ImportEntity
{
    protected $url;
    protected $headers = [];
    protected $itemsKey = 'data';
    protected $mapper;

    /** @var EntityMapInterface */
    protected $map;
    protected $import;
    protected $pager;

    protected $callbacks;

    public function __construct()
    {
        if ($url = $this->url()){
            $this->import = Import::fromUrl($url, $this->headers);
        }else{
            $this->import = Import::fromRequest(function(Client $client){
                return $this->request($client);
            });
        }

        if ($this->itemsKey){
            $this->import->itemsKey($this->itemsKey);
        }

        if ($this->mapper){
            $this->map = new $this->mapper;
        }

        if ($this->pager){
            /** @var Pager $pager */
            $pager = new $this->pager;
            $pager->setUrl($url);
            $this->import->withPager($pager);
        }
    }

    public function fire(): void
    {
        $this->pageRegister();
        $this->eachRegister();

        $this->import->fire();
    }

    public function each(Closure $callback): self
    {
        $this->callbacks['each'] = $callback;

        return $this;
    }

    public function page(Closure $callback): self
    {
        $this->callbacks['page'] = $callback;

        return $this;
    }

    protected function pageRegister(): void
    {
        if (isset($this->callbacks['page']) && is_callable($this->callbacks['page'])){
            $this->import->page($this->callbacks['page']);
            return;
        }

        $this->import->page(function (){
            $this->eachPage($this->import->getResponse());
        });
    }

    protected function eachRegister(): void
    {
        if (isset($this->callbacks['each']) && is_callable($this->callbacks['each'])){
            $this->import->each($this->callbacks['each']);
            return;
        }

        $this->import->each(function ($item, $response){
            if ($this->map){
                $item = $this->map->modifyFields($item);
            }

            $this->eachRow($item, $response);
        });
    }

    protected function url()
    {
        return $this->url;
    }

    protected function request(Client $client): Response
    {
        return $client->get($this->url());
    }

    protected function eachRow(array $item, array $response): void
    {

    }

    protected function eachPage(Response $response): void
    {

    }
}
