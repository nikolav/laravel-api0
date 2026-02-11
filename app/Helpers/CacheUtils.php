<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class CacheUtils
{

  static function jsonData(string $key)
  {
    $data = json_decode((string) Cache::get($key, '{}'), true);
    if (!is_array($data)) $data = [];
    return $data;
  }

  // write patched json data
  static function mergedJson(string $key, array $patch, ?int $ttlSeconds = null): array
  {
    return Cache::lock("lock:$key", 10)->block(
      5,
      function () use ($key, $patch, $ttlSeconds) {
        $cached = CacheUtils::jsonData($key);

        // deep merge
        $data = array_replace_recursive($cached, $patch);

        // save
        Cache::put($key, json_encode($data, JSON_UNESCAPED_UNICODE), $ttlSeconds);

        return $data;
      }
    );
  }

  // docCacheByKeyPathsDrop(key: String!, paths: [String!]!): JsonData!
  static function jsonForget(string $key, array $paths, ?int $ttlSeconds = null): array
  {
    return Cache::lock("lock:$key", 10)->block(
      5,
      function () use ($key, $paths, $ttlSeconds) {
        $data = CacheUtils::jsonData($key);

        foreach ($paths as $path) {
          data_forget($data, $path);
        }

        Cache::put($key, json_encode($data, JSON_UNESCAPED_UNICODE), $ttlSeconds);

        return $data;
      }
    );
  }
}
