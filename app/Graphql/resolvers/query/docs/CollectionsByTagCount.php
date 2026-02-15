<?php

namespace App\Graphql\resolvers\query\docs;

use App\Helpers\AppUtils;
use App\Models\Docs;
use Throwable;

// collectionByTagCount(tag: String!): JsonData!
class CollectionsByTagCount
{
  function resolve($root, array $input = [])
  {
    $result = 0;
    $error  = null;

    try {
      $result = Docs::whereHas(
        'tags',
        function ($q) use ($input) {
          $q->where('tag', $input['tag']);
        }
      )->count();
    } catch (Throwable $e) {
      $error = $e;
    }

    return AppUtils::res($result, $error);
  }
}
