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
        Schema::create('users', function (Blueprint $table) {
            $table->string('two_factor_secret')->nullable();
            $table->string('two_factor_verified_at')->nullable();
            $table->boolean('has_otp_login')->nullable()->default(false);
            $table->boolean('has_login')->nullable()->default(false);
            $table->boolean('is_active')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_secret',
                'two_factor_verified_at',
                'has_otp_login',
                'has_login',
                'is_active'
            ]);
        });
    }
};
