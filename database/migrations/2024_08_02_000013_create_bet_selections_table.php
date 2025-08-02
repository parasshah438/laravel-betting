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
        Schema::create('bet_selections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bet_id')->constrained()->onDelete('cascade');
            $table->foreignId('match_id')->constrained()->onDelete('cascade');
            $table->foreignId('bet_option_id')->constrained()->onDelete('cascade');
            $table->string('selection_name'); // Name of the selection at time of bet
            $table->decimal('odds', 8, 4); // Odds at time of bet
            $table->enum('status', ['pending', 'won', 'lost', 'void'])->default('pending');
            $table->json('match_info')->nullable(); // Match info snapshot at time of bet
            $table->json('result_info')->nullable(); // Result information
            $table->timestamps();
            
            $table->index(['bet_id', 'status']);
            $table->index(['match_id', 'status']);
            $table->index('bet_option_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bet_selections');
    }
};
