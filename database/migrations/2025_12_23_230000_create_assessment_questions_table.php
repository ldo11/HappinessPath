<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('assessment_questions')) {
            return;
        }

        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->json('content');
            $table->string('pillar_group'); // heart, grit, wisdom
            $table->integer('order');
            $table->timestamps();
            
            $table->index('pillar_group');
            $table->index('order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_questions');
    }
};
