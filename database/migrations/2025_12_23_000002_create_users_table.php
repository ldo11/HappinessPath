<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->enum('role', ['member', 'volunteer', 'admin'])->default('member');

            $table->enum('spiritual_preference', ['buddhism', 'christianity', 'secular'])->nullable();
            $table->enum('onboarding_status', ['new', 'test_skipped', 'test_completed'])->default('new');

            $table->unsignedTinyInteger('start_pain_level')->nullable();

            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('country')->nullable();
            $table->boolean('geo_privacy')->default(true);

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('role');
            $table->index('spiritual_preference');
            $table->index('onboarding_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
