<?php

namespace App\Data;

class DemoData extends DD
{
  public function __construct(

    public ?string $title = null,

    /** @var \App\Data\FooData[] */
    public array $foos = [],
  ) {}
}
