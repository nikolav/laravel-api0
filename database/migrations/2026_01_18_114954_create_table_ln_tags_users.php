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
        Schema::create('ln_users_tags', function (Blueprint $table) {
            // Auto-increment ID (optional)
            $table->id();

            // Foreign keys with constraints
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')  // Delete pivot when user is deleted
                ->onUpdate('cascade'); // Update pivot when user ID changes

            $table->foreignId('tag_id')
                ->constrained('tags')
                ->onDelete('cascade')  // Delete pivot when tag is deleted
                ->onUpdate('cascade'); // Update pivot when tag ID changes

            // Optional pivot metadata
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Composite unique constraint (prevents duplicate relationships)
            $table->unique(['user_id', 'tag_id']);

            // Indexes for performance
            $table->index('user_id');
            $table->index('tag_id');
            $table->index(['user_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ln_users_tags');
    }
};
