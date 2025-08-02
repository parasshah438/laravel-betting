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
        Schema::create('betting_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('limit_type', ['daily', 'weekly', 'monthly'])->default('daily');
            $table->enum('limit_category', ['deposit', 'bet', 'loss']);
            $table->decimal('limit_amount', 20, 8);
            $table->decimal('used_amount', 20, 8)->default(0);
            $table->datetime('period_start');
            $table->datetime('period_end');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_self_imposed')->default(true); // User set vs Admin set
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'limit_type', 'limit_category', 'period_start']);
            $table->index(['user_id', 'is_active']);
            $table->index(['period_start', 'period_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('betting_limits');
    }
};
