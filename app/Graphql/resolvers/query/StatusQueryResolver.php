<?php

namespace App\Graphql\resolvers\query;

class StatusQueryResolver
{
  function resolve($root, array $args = [])
  {
    return [
      'status' => 'ok',
      'args'   => $args,
    ];
  }
}
