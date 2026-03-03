<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class DotAccessData implements Arrayable, JsonSerializable
{
  function __construct(protected array $items = []) {}

  function get(string $path, mixed $default = null): mixed
  {
    return data_get($this->items, $path, $default);
  }

  function set(string $path, mixed $value): static
  {
    data_set($this->items, $path, $value);
    return $this;
  }

  function has(string $path): bool
  {
    return null != data_get($this->items, $path);
  }

  function rm(string $path): static
  {
    Arr::forget($this->items, $path);
    return $this;
  }

  function all(): array
  {
    return [...$this->items];
  }

  function toArray(): array
  {
    return [...$this->items];
  }

  function jsonSerialize(): array
  {
    return $this->items;
  }
}
