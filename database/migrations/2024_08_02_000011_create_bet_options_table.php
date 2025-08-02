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
        Schema::create('bet_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->onDelete('cascade');
            $table->foreignId('bet_market_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Home Win", "Draw", "Away Win"
            $table->string('value')->nullable(); // e.g., "1", "X", "2" or specific values
            $table->decimal('odds', 8, 4); // Betting odds
            $table->decimal('opening_odds', 8, 4)->nullable(); // Initial odds
            $table->boolean('is_active')->default(true);
            $table->boolean('is_suspended')->default(false);
            $table->integer('bet_count')->default(0); // Number of bets placed
            $table->decimal('total_stake', 20, 8)->default(0); // Total stake on this option
            $table->json('metadata')->nullable(); // Additional option data
            $table->timestamps();
            
            $table->unique(['match_id', 'bet_market_id', 'value']);
            $table->index(['match_id', 'bet_market_id']);
            $table->index(['is_active', 'is_suspended']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bet_options');
    }
};
