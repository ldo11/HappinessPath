<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('daily_tasks', 'duration')) {
                $table->unsignedInteger('duration')->default(15)->after('pillar_tag');
            }
        });

        if (Schema::hasColumn('daily_tasks', 'pillar_tag')) {
            try {
                Schema::table('daily_tasks', function (Blueprint $table) {
                    $table->enum('pillar_tag', ['body', 'mind', 'wisdom'])->nullable()->change();
                });
            } catch (\Throwable $e) {
                // Fallback if doctrine/dbal isn't installed; keep as string.
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('daily_tasks', 'duration')) {
            Schema::table('daily_tasks', function (Blueprint $table) {
                $table->dropColumn('duration');
            });
        }

        if (Schema::hasColumn('daily_tasks', 'pillar_tag')) {
            try {
                Schema::table('daily_tasks', function (Blueprint $table) {
                    $table->string('pillar_tag')->nullable()->change();
                });
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }
};
