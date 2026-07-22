<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class DotAccessData implements Arrayable, JsonSerializable
{
  function __construct(protected array $items = []) {}

  function use(array $ls): static
  {
    $this->items = [...$ls];
    return $this;
  }

  function get(string $path, mixed $default = null): mixed
  {
    return data_get($this->items, $path, default: $default);
  }

  function set(string $path, mixed $value, $overwrite = true): static
  {
    data_set($this->items, $path, $value, overwrite: $overwrite);
    return $this;
  }

  function commit(array $patches, $overwrite = true): static
  {
    foreach ($patches as $path => $value) {
      $this->set($path, $value, overwrite: $overwrite);
    }
    return $this;
  }

  // path:exists
  function isset(string $path)
  {
    return data_has($this->items, $path);
  }

  function rm(string ...$paths): static
  {
    Arr::forget($this->items, $paths);
    return $this;
  }

  function all(): array
  {
    return [...$this->items];
  }

  function ls(): array
  {
    return $this->all();
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
