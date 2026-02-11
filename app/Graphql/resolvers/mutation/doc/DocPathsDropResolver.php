<?php

namespace App\Graphql\resolvers\mutation\doc;

use App\Helpers\AppUtils;
use App\Helpers\CacheUtils;
use Exception;

class DocPathsDropResolver
{
  // docCacheByKeyPathsDrop(key: String!, paths: [String!]!): JsonData!
  static function resolve($root, array $input = [])
  {
    $error = null;
    try {
      CacheUtils::jsonForget($input['key'], $input['paths']);
    } catch (Exception $e) {
      $error = $e;
    }

    return AppUtils::res(null, $error);
  }
}
