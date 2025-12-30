<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultation_threads', function (Blueprint $table) {
            $table->foreignId('related_assessment_id')->nullable()->after('related_pain_point_id')->constrained('assessments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('consultation_threads', function (Blueprint $table) {
            $table->dropForeign(['related_assessment_id']);
            $table->dropColumn('related_assessment_id');
        });
    }
};
