<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use sidigi\LaravelApiImport\Exceptions\InvalidFormatterClassException;
use sidigi\LaravelApiImport\Formatters\FormatterInterface;

class ImportEntity extends Import
{
    protected $url;
    protected $formatters = [];

    public function __construct()
    {
        parent::__construct();

        $this->formatters = $this->formatters();

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
            $item = $this->formatItem($item);
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
     * @return array|null|boolean
     */
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
     */
    protected function formatItem(array $item): array
    {
        foreach ($this->formatters as $formatter){
            /** @var FormatterInterface $formatterObj */
            $formatterObj = new $formatter($item);

            if (! $formatterObj instanceof FormatterInterface){
                throw new InvalidFormatterClassException('Formatter class must be of the type ' . FormatterInterface::class . ', '.get_class($formatterObj).' given');
            }

            $item = $formatterObj->get();
        }

        return $item;
    }

    protected function formatters()
    {
        return array_merge(
            config('laravel-api-import.formatters') ?? [],
            $this->formatters,
            config('laravel-api-import.entities.' . static::class . '.formatters') ?? []
        );
    }
}
