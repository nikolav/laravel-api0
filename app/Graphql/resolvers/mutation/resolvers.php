<?php

use App\Graphql\resolvers\mutation\DemoMutationResolver;
use App\Graphql\resolvers\mutation\doc\DocPatchResolver;
use App\Graphql\resolvers\mutation\doc\DocPathsDropResolver;

return [
  'demo'                   => [new DemoMutationResolver, 'resolve'],
  'docCacheByKeyPatch'     => [new DocPatchResolver,     'resolve'],
  'docCacheByKeyPathsDrop' => [new DocPathsDropResolver, 'resolve'],
];
