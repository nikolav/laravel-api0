<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

use App\Support\DotAccessData;

class AsDotAccessData implements CastsAttributes
{
  function get($model, string $key, $value, array $attributes): DotAccessData
  {
    return new DotAccessData(
      is_array($value)
        ? $value
        : (json_decode($value ?? '[]', true) ?: [])
    );
  }

  function set($model, string $path, $value, array $attributes): array
  {
    if ($value instanceof DotAccessData) {
      return [$path => $value->toArray()];
    }

    return [$path => $value];
  }
}
