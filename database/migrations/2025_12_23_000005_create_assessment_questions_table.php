<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->json('content');
            $table->enum('pillar_group', ['heart', 'grit', 'wisdom']);
            $table->timestamps();
            $table->softDeletes();

            $table->index('pillar_group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_questions');
    }
};
