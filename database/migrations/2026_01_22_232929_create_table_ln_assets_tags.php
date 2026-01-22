<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('ln_assets_tags', function (Blueprint $table) {
      $table->id();

      $table->foreignId('asset_id')
        ->constrained('assets')
        ->cascadeOnDelete();

      $table->foreignId('tag_id')
        ->constrained('tags')
        ->cascadeOnDelete();

      $table->unique(['asset_id', 'tag_id']);
      $table->index(['tag_id', 'asset_id']);

      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('ln_assets_tags');
  }
};
