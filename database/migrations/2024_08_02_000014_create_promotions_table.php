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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('code')->unique()->nullable(); // Promo code
            $table->enum('type', ['welcome_bonus', 'deposit_bonus', 'free_bet', 'cashback', 'loyalty', 'referral']);
            $table->json('conditions'); // Promotion conditions and rules
            $table->decimal('bonus_amount', 20, 8)->nullable(); // Fixed bonus amount
            $table->decimal('bonus_percentage', 5, 2)->nullable(); // Percentage bonus
            $table->decimal('max_bonus', 20, 8)->nullable(); // Maximum bonus amount
            $table->decimal('min_deposit', 20, 8)->nullable(); // Minimum deposit required
            $table->decimal('wagering_requirement', 5, 2)->nullable(); // Wagering multiplier
            $table->integer('usage_limit')->nullable(); // Total usage limit
            $table->integer('user_limit')->default(1); // Per user limit
            $table->integer('used_count')->default(0); // How many times used
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->boolean('is_active')->default(true);
            $table->string('image')->nullable(); // Promotion image
            $table->json('target_users')->nullable(); // User targeting criteria
            $table->timestamps();
            
            $table->index(['is_active', 'start_date', 'end_date']);
            $table->index('code');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
