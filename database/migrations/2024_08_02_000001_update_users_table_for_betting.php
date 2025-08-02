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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            $table->date('date_of_birth')->nullable()->after('phone_verified_at');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            $table->string('country', 3)->nullable()->after('gender'); // ISO country code
            $table->string('currency', 3)->default('USD')->after('country'); // ISO currency code
            $table->enum('status', ['active', 'inactive', 'suspended', 'banned'])->default('active')->after('currency');
            $table->enum('role', ['admin', 'agent', 'user'])->default('user')->after('status');
            $table->boolean('is_verified')->default(false)->after('role');
            $table->boolean('two_factor_enabled')->default(false)->after('is_verified');
            $table->string('two_factor_secret')->nullable()->after('two_factor_enabled');
            $table->json('preferences')->nullable()->after('two_factor_secret'); // User preferences
            $table->timestamp('last_login_at')->nullable()->after('preferences');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->softDeletes(); // Add soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'phone_verified_at', 'date_of_birth', 'gender', 
                'country', 'currency', 'status', 'role', 'is_verified',
                'two_factor_enabled', 'two_factor_secret', 'preferences',
                'last_login_at', 'last_login_ip', 'deleted_at'
            ]);
        });
    }
};
