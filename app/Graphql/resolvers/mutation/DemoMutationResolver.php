<?php

namespace App\Graphql\resolvers\mutation;

use App\Helpers\AppUtils;

class DemoMutationResolver
{
  function resolve($root, array $args = [])
  {
    return AppUtils::res([
      'status' => 'demo:ok',
      'args'   => $args,
    ]);
  }
}
