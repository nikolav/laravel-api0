<?php

namespace App\Graphql\resolvers\mutation\docs;

use Throwable;
use Exception;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Docs;

use App\Helpers\AppUtils;
use App\Models\Tags;

class CollectionBatchUpsertResolver
{

  // collectionBatchUpsert(tag: String!, patches: [JsonData!]!): JsonData!
  function resolve($root, array $input = [])
  {
    $error = null;

    try {
      $tag     = $input['tag'];
      $patches = collect($input['patches'] ?? []);

      if ($patches->isEmpty())
        return AppUtils::res(null, null);

      if (empty($tag))
        throw new Exception("No collection name.");

      $withId = $patches
        ->filter(fn($p) => !empty($p['id']))
        ->keyBy('id');

      $withoutId = $patches
        ->filter(fn($p) => empty($p['id']))
        ->values();

      DB::transaction(
        function () use ($tag, $withId, $withoutId) {
          $now = now();
          $ids = $withId->keys()->all();

          // ==updates
          $docsExisting = Docs::query()
            ->whereHas(
              'tags',
              fn($q) => $q->where('tags.tag', $tag)
            )
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

          $updates =
            $docsExisting->map(
              function ($doc, $id) use ($withId, $now) {
                $patch = (array) $withId->get($id);
                return [
                  'id'   => $id,
                  'key'  => (string) $doc->key,
                  'data' => AppUtils::encodeJson(
                    array_replace_recursive(
                      (array) $doc->data,
                      (array) Arr::except($patch, ['id', 'key'])
                    )
                  ),
                  'updated_at' => $now,
                ];
              }
            )->values()->all();

          if (!empty($updates)) {
            Docs::upsert($updates, ['id'], ['key', 'data', 'updated_at']);
          }

          // ==inserts
          if ($withoutId->isNotEmpty()) {
            // tag:id to insert docs under
            $tid = Tags::firstOrCreate(['tag' => $tag])->id;

            $inserts = $withoutId->map(function ($p) use ($now) {
              $p = (array) $p;
              return [
                'key'  => !empty($p['key']) ? (string) $p['key'] : (string) Str::uuid(),
                'data' => AppUtils::encodeJson(Arr::except($p, ['id', 'key'])),
                'created_at' => $now,
                'updated_at' => $now,
              ];
            })->all();

            // add
            Docs::insert($inserts);
            $keys = collect($inserts)->pluck('key')->all();
            $docsAdded = Docs::query()
              ->whereIn('key', $keys)
              ->get(['id']);

            // update pivot
            $rowsPivot = $docsAdded->map(
              fn($doc) => [
                'main_id' => $doc->id,
                'tag_id'  => $tid,
              ]
            )->all();

            // $tbl_prefix = config('database.connections')[DB::getDefaultConnection()]['prefix'] ?? '';
            DB::table('ln_main_tags')->insert($rowsPivot);
          }

          // eot
        }
      );
    } catch (Throwable $e) {
      $error = $e;
    }

    return AppUtils::res(null, $error);
  }
}
