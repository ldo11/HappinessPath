<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('users', 'start_pain_level')) {
                $table->integer('start_pain_level')->default(5);
            }
            if (!Schema::hasColumn('users', 'onboarding_completed')) {
                $table->boolean('onboarding_completed')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['start_pain_level', 'onboarding_completed']);
        });
    }
};
