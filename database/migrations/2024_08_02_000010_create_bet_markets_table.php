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
        Schema::create('bet_markets', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Match Winner", "Over/Under", "Handicap"
            $table->string('key')->unique(); // e.g., "match_winner", "over_under", "handicap"
            $table->text('description')->nullable();
            $table->json('options_template')->nullable(); // Template for bet options
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bet_markets');
    }
};
