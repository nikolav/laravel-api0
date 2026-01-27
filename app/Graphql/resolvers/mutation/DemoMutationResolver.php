<?php

namespace App\Graphql\resolvers\mutation;

class DemoMutationResolver
{
  function resolve($root, array $args = [])
  {
    return [
      'status' => 'demo:ok',
      'args'   => $args,
    ];
  }
}
