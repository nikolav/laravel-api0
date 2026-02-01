<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::table('users', function (Blueprint $table) {

      // oauth provider name: google, github, facebook, etc.
      $table->string('provider')
        ->nullable();

      // provider user id (string, not int — google/facebook are not numeric)
      $table->string('provider_id')
        ->nullable();

      // avatar url from provider
      $table->string('avatar')
        ->nullable();

      // prevent the same provider account from being linked twice
      $table->unique(['provider', 'provider_id'], 'users_provider_provider_id_unique');

      // optional: helpful lookup indexes
      $table->index('provider');
      $table->index('provider_id');
    });
  }

  public function down(): void
  {
    Schema::table('users', function (Blueprint $table) {
      $table->dropUnique('users_provider_provider_id_unique');
      $table->dropIndex(['provider']);
      $table->dropIndex(['provider_id']);

      $table->dropColumn([
        'provider',
        'provider_id',
        'avatar',
      ]);
    });
  }
};
