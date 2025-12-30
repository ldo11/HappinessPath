<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            // Add new columns for simplified scoring
            $table->enum('pillar_group_new', ['body', 'mind', 'wisdom'])->nullable()->after('type');
            $table->boolean('is_reversed')->default(false)->after('pillar_group_new');
            $table->string('related_pain_point_key')->nullable()->after('is_reversed');
        });
    }

    public function down(): void
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->dropColumn(['pillar_group_new', 'is_reversed', 'related_pain_point_key']);
        });
    }
};
