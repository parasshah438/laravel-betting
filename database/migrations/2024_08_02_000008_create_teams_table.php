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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('slug');
            $table->string('country', 3)->nullable(); // ISO country code
            $table->string('logo')->nullable(); // Team logo path
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('stats')->nullable(); // Team statistics
            $table->string('external_id')->nullable(); // External API ID
            $table->timestamps();
            
            $table->unique(['sport_id', 'slug']);
            $table->index(['sport_id', 'is_active']);
            $table->index('external_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
