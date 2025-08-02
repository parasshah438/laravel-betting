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
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_token')->unique();
            $table->string('device_type')->nullable(); // mobile, desktop, tablet
            $table->string('device_name')->nullable();
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->string('location')->nullable(); // City, Country
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_activity');
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
            $table->index('session_token');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
