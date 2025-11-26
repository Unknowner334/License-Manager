<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apps', function (Blueprint $table) {
            $table->id();
            $table->uuid('edit_id')->unique();
            $table->uuid('app_id')->unique();
            $table->string('name')->unique();
            $table->integer('ppd_basic');
            $table->integer('ppd_premium');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->string('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('key_codes', function (Blueprint $table) {
            $table->id();
            $table->uuid('edit_id');
            $table->string('app_id');
            $table->string('owner');
            $table->string('key')->unique();
            $table->integer('max_devices')->default(1);
            $table->integer('duration')->default(30);
            $table->dateTime('expire_date')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->enum('rank', ['Premium', 'Basic'])->default('Basic');
            $table->string('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('key_history', function (Blueprint $table) {
            $table->id();
            $table->string('key_id')->nullable();
            $table->string('key');
            $table->string('device')->default('unknown-device');
            $table->string('ip_address');
            $table->string('app_id')->default('unknown-app');
            $table->enum('status', ['Success', 'Fail'])->default('Success');
            $table->enum('type', ['New Device', 'Old Device'])->default('New Device');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apps');
        Schema::dropIfExists('key_codes');
        Schema::dropIfExists('key_history');
    }
};
