<?php

namespace App\Data;

/**
 * @property \App\Data\FooData[] $foos
 */
class DemoData extends DD
{
  public function __construct(
    public ?string $title = null,
    public array $foos = [],
  ) {}
}
