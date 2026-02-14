<?php

use App\Graphql\resolvers\mutation\DemoMutationResolver;
use App\Graphql\resolvers\mutation\doc\DocPatchResolver;
use App\Graphql\resolvers\mutation\doc\DocPathsDropResolver;
use App\Graphql\resolvers\mutation\docs\CollectionBatchUpsertResolver;
use App\Graphql\resolvers\mutation\docs\CollectionDropIds;

return [
  'demo'                   => [new DemoMutationResolver,          'resolve'],
  'docCacheByKeyPatch'     => [new DocPatchResolver,              'resolve'],
  'docCacheByKeyPathsDrop' => [new DocPathsDropResolver,          'resolve'],
  'collectionBatchUpsert'  => [new CollectionBatchUpsertResolver, 'resolve'],
  'collectionDropIds'      => [new CollectionDropIds,             'resolve'],
];
