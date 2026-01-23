<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('assets', function (Blueprint $table) {
      $table->id();

      // search key|slug
      $table->string('key')->unique();
      // domain identifier|sku
      $table->string('code')->unique()->nullable();

      // the category of the asset ("physical", "digital", "financial")
      $table->string('type')->nullable();
      // descriptive name for the asset ("laptop", "office space")
      $table->string('name');
      // indicates the current status ("active", "disposed", "maintenance", "sold")
      $table->string('status')->nullable();
      // condition of the asset ("new", "good", "needs repair")
      $table->string('condition')->nullable();
      // physical or digital location of the asset ("warehouse 1", "cloud server")
      $table->string('location')->nullable();

      // additional data
      $table->json('data')->nullable();
      // detailed description of the asset
      $table->text('notes')->nullable();

      $table->timestamps();
      $table->softDeletes(); // +deleted_at

      $table->index('name');
      $table->index('type');
      $table->index('status');
      $table->index('deleted_at'); // helps queries that exclude deleted
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('assets');
  }
};
