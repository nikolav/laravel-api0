<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

use App\Enums\AssetsType;
use App\Casts\AsDotAccessData;
use App\Helpers\AppUtils;

class Assets extends Model
{
  use HasFactory;
  use SoftDeletes;
  use Notifiable;

  protected $table = 'assets';

  protected $fillable = [
    'key',
    'code',
    'name',
    'type',
    'status',
    'condition',
    'location',
    'notes',
    'data',
  ];

  protected function casts()
  {
    return [
      'type'       => AssetsType::class,
      'data'       => AsDotAccessData::class,
      'deleted_at' => 'datetime',
    ];
  }

  // key for route model binding
  function getRouteKeyName(): string
  {
    return 'key';
  }

  function getRouteKey()
  {
    return $this->key;
  }

  /**
   * Tags assigned to the asset (many-to-many)
   */
  function tags(): BelongsToMany
  {
    return $this->belongsToMany(
      Tags::class,
      'ln_assets_tags',
      'asset_id',
      'tag_id'
    )->withTimestamps();
  }

  // Child -> Parent (inverse)
  function parent(): BelongsTo
  {
    return $this->belongsTo(self::class, 'parent_id');
  }

  // Parent -> Children
  function children(): HasMany
  {
    return $this->hasMany(self::class, 'parent_id');
  }

  // Optional: recursive eager-loading helper
  function childrenRecursive($depth = 2): HasMany
  {
    if (0 === $depth) {
      return $this->children();
    }

    return $this->children()
      ->with(['childrenRecursive' => fn($a) => $a->childrenRecursive($depth - 1)]);
  }

  function users(): BelongsToMany
  {
    return $this->belongsToMany(
      User::class,
      'ln_users_assets',
      'asset_id',
      'user_id'
    )
      ->withTimestamps();
  }

  function assets_children(): BelongsToMany
  {
    return $this->belongsToMany(
      self::class,
      'ln_assets_assets',
      'asset_id',
      'related_asset_id'
    );
  }

  function assets_parents(): BelongsToMany
  {
    return $this->belongsToMany(
      self::class,
      'ln_assets_assets',
      'related_asset_id',
      'asset_id'
    );
  }

  protected static function booted()
  {
    static::creating(function ($a) {
      if (empty($a->key)) {
        $a->key = AppUtils::nanoid();
      }
    });
  }
}
