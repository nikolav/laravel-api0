<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class AppUtils
{
  static private $DEFAULTS_TRUTHY = [true, 1, '1', 'TRUE', 'YES', 'ON', 'Y'];

  public function __construct() {}

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
}
