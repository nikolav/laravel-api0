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
    Schema::create('ln_users_assets', function (Blueprint $table) {
      $table->id();

      $table->foreignId('user_id')
        ->constrained('users')
        ->cascadeOnDelete();

      $table->foreignId('asset_id')
        ->constrained('assets')
        ->cascadeOnDelete();

      $table->timestamps();

      // prevent duplicate user ↔ asset pairs
      $table->unique(['user_id', 'asset_id']);
      // optimize reverse lookups
      $table->index('asset_id');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('ln_users_assets');
  }
};
