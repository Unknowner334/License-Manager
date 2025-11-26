<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrable_codes', function (Blueprint $table) {
            $table->id();
            $table->uuid('edit_id');
            $table->string('code')->unique()->max(50);
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->string('created_by');
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->string('name')->max(100);
            $table->string('username')->unique()->max(50);
            $table->string('password')->max(50)->min(8);
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->enum('permissions', ['Owner', 'Admin'])->default('Admin');
            $table->string('reff')->nullable();
            $table->string('created_by')->nullable();
            $table->datetime('last_login')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('users_history', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();
            $table->string('username')->max(50);
            $table->enum('status', ['Success', 'Fail'])->default('Success');
            $table->enum('type', ['Login', 'Logout'])->default('Login');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('username')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrable_codes');
        Schema::dropIfExists('users');
        Schema::dropIfExists('users_history');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
