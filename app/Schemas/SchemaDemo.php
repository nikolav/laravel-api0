<?php

namespace App\Schemas;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Email;
// use Spatie\LaravelData\Attributes\Validation\Required;

class SchemaDemo extends Data
{
  function __construct(
    #[Email]
    public string $email,
  ) {}
}
