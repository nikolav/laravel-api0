<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class StoreMain extends Facade
{
  protected static function getFacadeAccessor(): string
  {
    return 'store:main';
  }
}
