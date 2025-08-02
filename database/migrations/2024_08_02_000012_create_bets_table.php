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
        Schema::create('bets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('bet_id')->unique(); // Unique bet identifier
            $table->enum('bet_type', ['single', 'multiple', 'system'])->default('single');
            $table->decimal('stake', 20, 8); // Bet amount
            $table->decimal('potential_win', 20, 8); // Potential winnings
            $table->decimal('total_odds', 8, 4); // Combined odds for the bet
            $table->enum('status', ['pending', 'won', 'lost', 'void', 'cashout'])->default('pending');
            $table->decimal('payout', 20, 8)->nullable(); // Actual payout amount
            $table->string('currency', 3);
            $table->boolean('is_live_bet')->default(false);
            $table->boolean('is_system_bet')->default(false);
            $table->json('system_config')->nullable(); // System bet configuration
            $table->decimal('cashout_value', 20, 8)->nullable(); // Current cashout value
            $table->boolean('cashout_available')->default(false);
            $table->text('notes')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['bet_type', 'status']);
            $table->index('bet_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bets');
    }
};
