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
        Schema::create('risk_management', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('match_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('bet_option_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('risk_type'); // user_limit, match_limit, option_limit, suspicious_activity
            $table->string('trigger_event'); // high_stake, rapid_betting, unusual_pattern
            $table->decimal('amount', 20, 8)->nullable();
            $table->decimal('limit_amount', 20, 8)->nullable();
            $table->enum('action_taken', ['warning', 'limit_reduced', 'account_suspended', 'bet_rejected']);
            $table->text('description');
            $table->json('metadata')->nullable(); // Additional risk data
            $table->boolean('is_resolved')->default(false);
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'risk_type']);
            $table->index(['match_id', 'risk_type']);
            $table->index(['is_resolved', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_management');
    }
};
