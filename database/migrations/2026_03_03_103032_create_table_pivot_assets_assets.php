<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('ln_assets_assets', function (Blueprint $table) {

      $table->foreignId('asset_id')
        ->constrained('assets')
        ->cascadeOnDelete();

      $table->foreignId('related_asset_id')
        ->constrained('assets')
        ->cascadeOnDelete();

      $table->timestamps();

      $table->primary(['asset_id', 'related_asset_id']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('ln_assets_assets');
  }
};
