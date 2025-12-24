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
        Schema::table('daily_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('daily_tasks', 'day') && !Schema::hasColumn('daily_tasks', 'day_number')) {
                $table->renameColumn('day', 'day_number');
            }

            if (Schema::hasColumn('daily_tasks', 'difficulty') && !Schema::hasColumn('daily_tasks', 'difficulty_level_int')) {
                $table->renameColumn('difficulty', 'difficulty_level_int');
            }

            if (!Schema::hasColumn('daily_tasks', 'content')) {
                $table->json('content')->nullable()->after('day_number');
            }

            if (!Schema::hasColumn('daily_tasks', 'pillar_tag')) {
                $table->string('pillar_tag')->nullable()->after('content');
            }

            if (!Schema::hasColumn('daily_tasks', 'difficulty')) {
                $table->string('difficulty')->default('easy')->after('pillar_tag');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('daily_tasks', 'difficulty')) {
                $table->dropColumn('difficulty');
            }
            if (Schema::hasColumn('daily_tasks', 'pillar_tag')) {
                $table->dropColumn('pillar_tag');
            }
            if (Schema::hasColumn('daily_tasks', 'content')) {
                $table->dropColumn('content');
            }

            if (Schema::hasColumn('daily_tasks', 'difficulty_level_int') && !Schema::hasColumn('daily_tasks', 'difficulty')) {
                $table->renameColumn('difficulty_level_int', 'difficulty');
            }

            if (Schema::hasColumn('daily_tasks', 'day_number') && !Schema::hasColumn('daily_tasks', 'day')) {
                $table->renameColumn('day_number', 'day');
            }
        });
    }
};
