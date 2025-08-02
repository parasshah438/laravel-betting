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
        Schema::create('live_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->onDelete('cascade');
            $table->string('event_type'); // goal, yellow_card, red_card, substitution, etc.
            $table->integer('minute')->nullable(); // Match minute
            $table->string('team')->nullable(); // home, away
            $table->string('player')->nullable();
            $table->text('description');
            $table->json('data')->nullable(); // Additional event data
            $table->timestamps();
            
            $table->index(['match_id', 'minute']);
            $table->index(['match_id', 'event_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_events');
    }
};
