<?php

namespace App\Graphql\resolvers\query\docs;

use Throwable;

use App\Helpers\AppUtils;
use App\Models\Docs;

class CollectionByTagResolver
{
  // collectionByTag(tag: String!): JsonData!
  function resolve($root, array $args = [])
  {
    $docs  = null;
    $error = null;

    try {
      $docs = Docs::whereHas(
        'tags',
        fn($q) => $q->where('tags.tag', $args['tag'])
      )
        ->get();
    } catch (Throwable $e) {
      $error =  $e;
    }

    return AppUtils::res($docs, $error);
  }
}
