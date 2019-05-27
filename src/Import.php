<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\DB;

class Import
{
    protected $headers = [];
    protected $requestCallback;
    protected $client;
    protected $itemsKey = 'data';
    protected $mapper;
    protected $sleep;

    /** @var Paginator */
    protected $pager;

    private $response;
    private $pageCallbacks = [];
    private $eachCallbacks = [];

    public function __construct()
    {
        $this->client = new Client();
        $this->pager = new Paginator();
    }

    public static function fromRequest(Closure $callback): self
    {
        $obj = new self();
        $obj->requestCallback = $callback;

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

    public function withPager(Paginator $pager): self
    {
        $this->pager = $pager;

        return $this;
    }

    public function fire(): void
    {
        DB::beginTransaction();

            do {
                $this->makeRequest();

                $response = $this->getParsedResponse();

                $items = $this->getItemsFromResponseByKey($response, $this->itemsKey);

                foreach ($this->pageCallbacks as $callback){
                    if (is_callable($callback)){
                        $callback($this->getResponse());
                    }
                }

                foreach ($this->eachCallbacks as $callback){
                    if (is_callable($callback)){
                        collect($items)->each(static function ($item) use ($callback) {
                            $callback($item);
                        });
                    }
                }

                if ($this->sleep){
                    sleep($this->sleep);
                }

            } while ( $this->hasNextPage() );

        DB::commit();
    }

    public function each(Closure $callback): self
    {
        $this->eachCallbacks[] = $callback;

        return $this;
    }

    public function page(Closure $callback): self
    {
        $this->pageCallbacks[] = $callback;

        return $this;
    }

    public function makeRequest(): void
    {
        if (is_callable($this->requestCallback)){
            $this->response = call_user_func($this->requestCallback, $this->client);
            return;
        }

        $this->response = $this->client->get($this->pager->url(), $this->headers);

        if ($this->pager->hasNextPage()){
            $this->pager->next();
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
        return $this->pager->hasNextPage() && (bool) $this->getParsedResponse();
    }
}
