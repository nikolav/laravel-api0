<?php

namespace App\Helpers;

use stdClass;

use Illuminate\Support\Collection;

use App\Support\Nanoid;


class AppUtils
{
  static private $DEFAULTS_TRUTHY = [true, 1, '1', 'TRUE', 'YES', 'ON', 'Y'];

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

  static function deepToObject(mixed $value): mixed
  {
    if (!is_array($value)) {
      return $value;
    }

    if ($value === []) {
      return (object)[];
    }

    foreach ($value as $k => $v) {
      $value[$k] = self::deepToObject($v);
    }

    return $value;
  }

  static function encodeJson($json)
  {
    return is_string($json)
      ? $json
      : json_encode(
        $json ?? new stdClass(),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
      );
  }

  static function merge_deep($base, $override)
  {
    return array_replace_recursive(
      json_decode(json_encode($base), true),
      json_decode(json_encode($override), true),
    );
  }

  static function nanoid(int $length = 21, ?string $alphabet = null): string
  {
    return app()->make(Nanoid::class)($length, $alphabet);
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
