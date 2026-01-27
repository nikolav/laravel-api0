<?php

use App\Graphql\resolvers\mutation\DemoMutationResolver;

return [
  'demo' => [new DemoMutationResolver, 'resolve'],
];
