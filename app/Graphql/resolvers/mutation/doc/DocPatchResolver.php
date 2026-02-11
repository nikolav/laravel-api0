<?php

namespace App\Graphql\resolvers\mutation\doc;

use Exception;

use App\Helpers\AppUtils;
use App\Helpers\CacheUtils;

class DocPatchResolver

{
  // docCacheByKeyPatch(key: String!, patch: JsonData!): JsonData!
  function resolve($root, array $input = [])
  {
    $error = null;
    try {
      CacheUtils::mergedJson($input['key'], $input['patch']);
    } catch (Exception $e) {
      $error = $e;
    }

    return AppUtils::res(null, $error);
  }
}
