<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class AppUtils
{
  public function __construct() {}

  static function csv_list(string $value): Collection
  {
    return collect(explode(',', $value))
      ->map(fn($v) => trim($v))
      ->filter();
  }
}
