<?php

namespace App\Support;

use Hidehalo\Nanoid\Client;

class Nanoid
{
  protected Client $client;

  function __construct(?Client $client = null)
  {
    $this->client = $client ?? new Client();
  }

  function __invoke(int $length = 21, ?string $alphabet = null): string
  {
    return $this->client->generateId($length, $alphabet);
  }
}
