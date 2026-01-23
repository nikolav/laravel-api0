<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Support\Str;
use App\Enums\AssetsType;

class Assets extends Model
{
  use HasFactory;
  use SoftDeletes;

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

  protected $casts = [
    'type'       => AssetsType::class,
    'data'       => 'array',
    'deleted_at' => 'datetime',
  ];

  /**
   * Tags assigned to the asset (many-to-many)
   */
  public function tags(): BelongsToMany
  {
    return $this->belongsToMany(
      Tags::class,
      'ln_assets_tags',
      'asset_id',
      'tag_id'
    )->withTimestamps();
  }

  // Optional: use key for route model binding (API-safe)
  public function getRouteKeyName(): string
  {
    return 'key';
  }
}
