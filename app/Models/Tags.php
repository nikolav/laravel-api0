<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tags extends Model
{
  use HasFactory;

  protected $table = 'tags';

  protected $fillable = [
    'tag'
  ];

  /**
   * The users that belong to the tag.
   */
  public function users()
  {
    return $this->belongsToMany(User::class, 'ln_users_tags', 'tag_id', 'user_id')
      ->withTimestamps();
  }

  public function docs(): BelongsToMany
  {
    return $this->belongsToMany(
      Docs::class,
      'ln_main_tags',
      'tag_id',
      'main_id'
    );
  }

  public function assets(): BelongsToMany
  {
    return $this->belongsToMany(
      Assets::class,
      'ln_assets_tags',
      'tag_id',
      'asset_id'
    )->withTimestamps();
  }
}
