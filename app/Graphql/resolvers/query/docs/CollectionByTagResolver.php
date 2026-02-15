<?php

namespace App\Graphql\resolvers\query\docs;

use Throwable;

use App\Helpers\AppUtils;
use App\Models\Docs;

class CollectionByTagResolver
{
  // collectionByTag(tag: String!): JsonData!
  function resolve($root, array $input = [])
  {
    $docs  = null;
    $error = null;

    try {
      $docs = Docs::whereHas(
        'tags',
        fn($q) => $q->where('tags.tag', $input['tag'])
      )
        ->with('tags:tags.id,tag')
        ->get()
        ->map(fn($doc) => [
          ...$doc->toArray(),
          'tags' => $doc->tags->pluck('tag')->all(),
        ]);
    } catch (Throwable $e) {
      $error =  $e;
    }

    return AppUtils::res($docs, $error);
  }
}
