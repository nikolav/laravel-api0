<?php

namespace App\Graphql\resolvers\query;

use App\Helpers\AppUtils;

class StatusQueryResolver
{
  function resolve($root, array $args = [])
  {
    return AppUtils::res([
      'status' => 'ok',
      'args'   => $args,
    ]);
  }
}
