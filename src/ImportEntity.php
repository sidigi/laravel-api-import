<?php

declare(strict_types=1);

namespace sidigi\LaravelApiImport;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class ImportEntity extends Import
{
    protected $url;

    public function __construct()
    {
        parent::__construct();

        /*$this->requestCallback = function(Client $client){
            return $this->request($client);
        };*/

        if ($url = $this->url()) {
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
        $this->page(function () {
            $this->eachPage($this->getResponse());
        });
    }

    protected function eachRegister(): void
    {
        $this->each(function ($item) {
            if ($this->mapper) {
                $item = (new $this->mapper($item))->get();
            }

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

    /**
     * @param array $item
     *
     * @return array|null|bool
     */
    protected function eachItem(array $item)
    {
    }

    protected function eachPage(Response $response): void
    {
    }
}
