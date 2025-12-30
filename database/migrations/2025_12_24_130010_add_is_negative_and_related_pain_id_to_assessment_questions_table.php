<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('assessment_questions')) {
            return;
        }

        Schema::table('assessment_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('assessment_questions', 'is_negative')) {
                $table->boolean('is_negative')->default(false)->after('pillar_group');
            }
            if (!Schema::hasColumn('assessment_questions', 'related_pain_id')) {
                $table->json('related_pain_id')->nullable()->after('is_negative');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('assessment_questions')) {
            return;
        }

        Schema::table('assessment_questions', function (Blueprint $table) {
            if (Schema::hasColumn('assessment_questions', 'related_pain_id')) {
                $table->dropColumn('related_pain_id');
            }
            if (Schema::hasColumn('assessment_questions', 'is_negative')) {
                $table->dropColumn('is_negative');
            }
        });
    }
};
