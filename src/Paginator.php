<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport;

class Paginator
{
    protected $page;
    protected $start;
    protected $limit;

    private $url;

    public function setUrl(string $url): void
    {
        $this->url = $this->prepare($url);
    }

    public function next(): void
    {
        $this->url = $this->nextUrl();
    }

    public static function fromUrl(string $url): self
    {
        $obj = new static();
        $obj->setUrl($url);

        return $obj;
    }

    public function hasNextPage(): bool
    {
        return ! (! $this->page && ! $this->start);
    }

    private function prepare(string $url): string
    {
        $parts = explode('?', $url);
        $params = [];

        if (isset($parts[1])){
            parse_str($parts[1], $params);
        }

        if ($this->start && $this->limit){
            $params = $params + $this->start + $this->limit;
        }

        if ($this->page){
            $params += $this->page;
        }

        return implode('?', [$parts[0], http_build_query($params)]);
    }

    public function url(): string
    {
        return $this->url;
    }

    public function nextUrl(): ?string
    {
        $parts = explode('?', $this->url);
        $params = [];

        if (isset($parts[1])){
            parse_str($parts[1], $params);
        }

        return implode('?', [$parts[0], http_build_query($this->increment($params))]);
    }

    private function increment(array $params): array
    {
        if ($this->page){
            $key = $this->getKey($this->page);
            $params[$key]++;
        }

        if ($this->start){
            $key = $this->getKey($this->start);
            $params[$key] += $this->limit[$this->getKey($this->limit)] + 1;
        }

        return $params;
    }

    private function getKey(array $val): ?string
    {
        if (! $val){
            return null;
        }

        return head(array_keys($val));
    }
}
