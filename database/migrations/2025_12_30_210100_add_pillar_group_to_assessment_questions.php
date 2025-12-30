<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->enum('pillar_group', ['heart', 'grit', 'wisdom'])->nullable()->after('type');
            $table->boolean('is_negative')->default(false)->after('pillar_group');
            $table->json('related_pain_id')->nullable()->after('is_negative');
        });
    }

    public function down(): void
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->dropColumn(['pillar_group', 'is_negative', 'related_pain_id']);
        });
    }
};
