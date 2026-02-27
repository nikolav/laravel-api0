<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Mongo extends Model
{
  protected $connection = 'mongodb';
  protected $table      = null;

  protected $fillable = [
    'key',
    'data',
  ];

  protected $casts = [
    'data'       => 'array',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  public $timestamps = true;

  static function useCollection(string $collection): static
  {
    $instance = new static();
    $instance->table = $collection;
    return $instance;
  }
}
