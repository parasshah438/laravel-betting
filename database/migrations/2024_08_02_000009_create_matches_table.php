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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_id')->constrained()->onDelete('cascade');
            $table->foreignId('league_id')->constrained()->onDelete('cascade');
            $table->foreignId('home_team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('away_team_id')->constrained('teams')->onDelete('cascade');
            $table->string('match_name'); // e.g., "Team A vs Team B"
            $table->datetime('start_time');
            $table->enum('status', ['scheduled', 'live', 'halftime', 'finished', 'postponed', 'cancelled'])->default('scheduled');
            $table->json('score')->nullable(); // Match score data
            $table->json('live_data')->nullable(); // Live match data (time, events, etc.)
            $table->json('statistics')->nullable(); // Match statistics
            $table->string('venue')->nullable();
            $table->string('referee')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('live_betting_enabled')->default(true);
            $table->string('external_id')->nullable(); // External API ID
            $table->json('metadata')->nullable(); // Additional match data
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
            
            $table->index(['sport_id', 'status']);
            $table->index(['league_id', 'start_time']);
            $table->index(['home_team_id', 'away_team_id']);
            $table->index('start_time');
            $table->index('external_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
