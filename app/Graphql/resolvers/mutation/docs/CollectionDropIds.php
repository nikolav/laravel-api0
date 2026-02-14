<?php

namespace App\Graphql\resolvers\mutation\docs;

use Throwable;
use Exception;

use App\Models\Docs;

use App\Helpers\AppUtils;

class CollectionDropIds
{
  // collectionDropIds(tag: String!, ids: [ID!]!): JsonData!
  function resolve($root, array $input = [])
  {
    $error = null;

    try {
      $tag = (string) $input['tag'];
      $ids = (array)  $input['ids'];

      if (empty($ids))
        return AppUtils::res(null, null);

      if (empty($tag))
        throw new Exception("Collection name missing.");

      Docs::whereIn('id', $ids)
        ->whereHas(
          'tags',
          fn($q) =>
          $q->where('tags.tag', $tag)
        )
        ->delete();

    } catch (Throwable $e) {
      $error = $e;
    }

    return AppUtils::res(null, $error);
  }
}
