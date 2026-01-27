<?php

use App\Graphql\resolvers\query\StatusQueryResolver;

return [
  'status'  => [new StatusQueryResolver, 'resolve'],
];
