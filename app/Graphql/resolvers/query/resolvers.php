<?php

use App\Graphql\resolvers\query\doc\DocQueryResolver;
use App\Graphql\resolvers\query\StatusQueryResolver;

return [
  'status'         => [new StatusQueryResolver, 'resolve'],
  'docCacheByKey'  => [new DocQueryResolver,    'resolve'],
];
