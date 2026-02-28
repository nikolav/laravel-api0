<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Str;

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

  protected static function booted()
  {
    static::creating(function ($model) {
      if (empty($model->key)) {
        $model->key = (string) Str::uuid();
      }
    });
  }

  public static function useCollection(string $collection): static
  {
    return (new static())->setTable($collection);
  }
}
