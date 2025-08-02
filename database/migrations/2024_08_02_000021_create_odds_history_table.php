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
        Schema::create('odds_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bet_option_id')->constrained()->onDelete('cascade');
            $table->decimal('odds', 8, 4);
            $table->datetime('recorded_at');
            $table->string('source')->nullable(); // odds provider source
            $table->timestamps();
            
            $table->index(['bet_option_id', 'recorded_at']);
            $table->index('recorded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odds_history');
    }
};
