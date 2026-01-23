<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::table('assets', function (Blueprint $table) {
      $table->foreignId('parent_id')
        ->nullable()
        ->constrained('assets')
        ->nullOnDelete(); # delete parent, set children .parent_id=null
        // ->cascadeOnDelete();  # delete subtree
        // ->restrictOnDelete(); # prevent delete if children exist
    });
  }

  public function down(): void
  {
    Schema::table('assets', function (Blueprint $table) {
      $table->dropForeign(['parent_id']);
      $table->dropColumn('parent_id');
    });
  }
};
