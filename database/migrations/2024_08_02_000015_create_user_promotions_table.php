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
        Schema::create('user_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('promotion_id')->constrained()->onDelete('cascade');
            $table->decimal('bonus_amount', 20, 8); // Awarded bonus amount
            $table->decimal('wagering_requirement', 20, 8); // Total wagering needed
            $table->decimal('wagered_amount', 20, 8)->default(0); // Amount wagered so far
            $table->enum('status', ['active', 'completed', 'expired', 'cancelled'])->default('active');
            $table->datetime('claimed_at');
            $table->datetime('expires_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->json('metadata')->nullable(); // Additional promotion data
            $table->timestamps();
            
            $table->unique(['user_id', 'promotion_id']);
            $table->index(['user_id', 'status']);
            $table->index(['promotion_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_promotions');
    }
};
