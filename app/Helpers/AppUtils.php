<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class AppUtils
{
  static private $DEFAULTS_TRUTHY = [true, 1, '1', 'TRUE', 'YES', 'ON', 'Y'];

  function __construct() {}

  static function csv_list(string $value): Collection
  {
    return collect(explode(',', $value))
      ->map(fn($v) => trim($v))
      ->filter();
  }

  static function parse_boolean($value): bool
  {
    // Normalize input if it's a string
    if (is_string($value)) {
      $value = mb_strtoupper(trim($value));
    }

    return in_array($value, self::$DEFAULTS_TRUTHY, true);
  }

  static function res(mixed $result = null, ?\Throwable  $error = null): Result
  {
    return new Result($result, $error);
  }
}

final class Result implements \JsonSerializable
{
  function __construct(
    public readonly mixed        $result = null,
    public readonly ?\Throwable  $error  = null
  ) {}

  // json
  function jsonSerialize(): array
  {
    return [
      'ok'     => null === $this->error,
      'error'  => $this->error?->getMessage(),
      'result' => $this->normalized($this->result),
    ];
  }

  private function normalized(mixed $value): mixed
  {
    return $value instanceof \JsonSerializable
      ? $value->jsonSerialize()
      : $value;
  }
}
