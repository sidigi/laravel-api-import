<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class Import
{
    private $headers = [];
    private $response;
    private $callbacks = [];
    private $client;
    private $itemsKey = 'data';

    /** @var Pager */
    private $pager;

    public function __construct()
    {
        $this->client = new Client();
        $this->pager = new Pager();
    }

    public static function fromRequest(Closure $callback): self
    {
        $obj = new self();
        $obj->callbacks['request'] = $callback;

        return $obj;
    }

    public static function fromUrl(string $url, array $headers = []): self
    {
        $obj = new self();
        $obj->pager->setUrl($url);
        $obj->headers += $headers;

        return $obj;
    }

    public function itemsKey(?string $key): self
    {
        $this->itemsKey = $key;

        return $this;
    }

    public function withPager(Pager $pager): self
    {
        $this->pager = $pager;

        return $this;
    }

    public function fire(): void
    {
        $this->makeRequest();

        do {
            $response = $this->getParsedResponse();

            $items = $this->getItemsFromResponseByKey($response, $this->itemsKey);

            if (isset($this->callbacks['page']) && is_callable($this->callbacks['page'])){
                $this->callbacks['page']($this->getResponse());
            }

            if (isset($this->callbacks['each']) && is_callable($this->callbacks['each'])){
                collect($items)->each(function ($item) use ($response) {
                    $this->callbacks['each']($item, $response);
                });
            }

            $this->makeNextRequest();

        } while($this->hasNextPage());
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

    public function makeRequest(): void
    {
        if (isset($this->callbacks['request']) && is_callable($this->callbacks['request'])){
            $this->response = call_user_func($this->callbacks['request'], $this->client);
            return;
        }

        $this->response = $this->client->get($this->pager->url(), $this->headers);
    }

    public function makeNextRequest(): void
    {
        if ($this->pager->nextUrl()){
            $this->pager->next();
            $this->makeRequest();
        }
    }

    public function getParsedResponse(): ?array
    {
        return json_decode((string)$this->getResponse()->getBody(), true);
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getItemsFromResponseByKey(array $response, ?string $key)
    {
        if (count($response) !== count($response, COUNT_RECURSIVE)){
            return $response[$key] ?? $response;
        };

        return [$response];
    }

    public function hasNextPage(): bool
    {
        return (bool) $this->pager->nextUrl() && (bool) $this->getParsedResponse();
    }
}
