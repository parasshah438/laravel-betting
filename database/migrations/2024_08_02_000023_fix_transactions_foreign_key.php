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
        // Fix the foreign key reference in transactions table
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['related_bet_id']);
            $table->dropColumn('related_bet_id');
        });
        
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('related_bet_id')->nullable()->after('description')->constrained('bets')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['related_bet_id']);
            $table->dropColumn('related_bet_id');
        });
        
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('related_bet_id')->nullable()->after('description')->constrained('bets')->onDelete('set null');
        });
    }
};
