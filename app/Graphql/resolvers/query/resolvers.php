<?php

use App\Graphql\resolvers\query\doc\DocQueryResolver;
use App\Graphql\resolvers\query\docs\CollectionByTagResolver;
use App\Graphql\resolvers\query\docs\CollectionsByTagCount;
use App\Graphql\resolvers\query\StatusQueryResolver;

return [
  'status'               => [new StatusQueryResolver,     'resolve'],
  'docCacheByKey'        => [new DocQueryResolver,        'resolve'],
  'collectionByTag'      => [new CollectionByTagResolver, 'resolve'],
  'collectionByTagCount' => [new CollectionsByTagCount,   'resolve'],
];
