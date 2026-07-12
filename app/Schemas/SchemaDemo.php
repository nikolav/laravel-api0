<?php

namespace App\Schemas;

use Spatie\LaravelData\Attributes\Validation\Email;
// use Spatie\LaravelData\Attributes\Validation\Required;

class SchemaDemo extends Schema
{
  function __construct(
    #[Email]
    public string $email,
  ) {}
}
