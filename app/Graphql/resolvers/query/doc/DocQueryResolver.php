<?php

namespace App\Graphql\resolvers\query\doc;

use Exception;

use App\Helpers\AppUtils;
use App\Helpers\CacheUtils;

class DocQueryResolver
{
  // docCacheByKey(key: String!): JsonData!
  function resolve($root, array $input = [])
  {
    $data  = null;
    $error = null;
    try {
      $data = AppUtils::deepToObject(CacheUtils::jsonData($input['key']));
    } catch (Exception $e) {
      $error = $e;
    }

    return AppUtils::res($data, $error);
  }
}
