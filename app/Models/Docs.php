<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Docs extends Model
{
  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'main';

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'key',
    'data',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'data'       => 'array',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

    // # automatically load tags
    // protected $with = ['tags'];

  /**
   * Get the tags associated with the main record.
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function tags(): BelongsToMany
  {
    return $this->belongsToMany(
      Tags::class,
      'ln_main_tags',
      'main_id',
      'tag_id'
    );
  }
}
