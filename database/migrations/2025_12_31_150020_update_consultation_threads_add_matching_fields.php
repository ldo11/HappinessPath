<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultation_threads', function (Blueprint $table) {
            if (!Schema::hasColumn('consultation_threads', 'pain_point_id')) {
                $table->foreignId('pain_point_id')
                    ->nullable()
                    ->after('related_assessment_id')
                    ->constrained('pain_points')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('consultation_threads', 'assigned_consultant_id')) {
                $table->foreignId('assigned_consultant_id')
                    ->nullable()
                    ->after('pain_point_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('consultation_threads', 'closed_at')) {
                $table->timestamp('closed_at')->nullable()->after('status');
            }
        });

        if (Schema::hasColumn('consultation_threads', 'related_pain_point_id') && Schema::hasColumn('consultation_threads', 'pain_point_id')) {
            DB::table('consultation_threads')
                ->whereNull('pain_point_id')
                ->whereNotNull('related_pain_point_id')
                ->update(['pain_point_id' => DB::raw('related_pain_point_id')]);
        }
    }

    public function down(): void
    {
        Schema::table('consultation_threads', function (Blueprint $table) {
            if (Schema::hasColumn('consultation_threads', 'assigned_consultant_id')) {
                $table->dropForeign(['assigned_consultant_id']);
                $table->dropColumn('assigned_consultant_id');
            }

            if (Schema::hasColumn('consultation_threads', 'pain_point_id')) {
                $table->dropForeign(['pain_point_id']);
                $table->dropColumn('pain_point_id');
            }

            if (Schema::hasColumn('consultation_threads', 'closed_at')) {
                $table->dropColumn('closed_at');
            }
        });
    }
};
